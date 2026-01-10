<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\AgendaItem;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Service;
use App\Models\User;
use App\Services\ServiceTitleService;
use Carbon\Carbon;
use Exception;
use ICal\ICal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportIcalFeedsCommand extends Command
{
    protected $signature = 'import:ical-feeds';

    protected $description = 'Import events from iCal feeds of all agendas';

    public function handle()
    {
        $agendas = Agenda::where('ical_url', '!=', null)
        ->where('ical_url', '!=', '')
        ->get();

        if ($agendas->isEmpty()) {
            $this->error("No agendas found in the database.");
            return 1;
        }

        $totalImported = 0;
        $totalDeleted = 0;

        foreach ($agendas as $agenda) {
            $this->info("Processing agenda: {$agenda->title}");
            $this->info("Fetching iCal from: {$agenda->ical_url}");

            try {
                $ical = new ICal($agenda->ical_url, [
                    'skipRecurrence' => false,
                ]);
            } catch (\Exception $e) {
                $this->error("Failed to parse iCal for {$agenda->title}: " . $e->getMessage());
                continue;
            }

            $events = $ical->events();
            $importedKeys = [];
            $minDate = $agenda->title === 'Kerktijden'
                ? new \DateTime('2026-01-01 00:00:00')
                : null;

            foreach ($events as $event) {
                $uid = $event->uid;
                $start = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                $end = $ical->iCalDateToDateTime($event->dtend_array[3] ?? $event->dtstart_array[3]);

                // Convert to application timezone (Europe/Amsterdam)
                // Google Calendar iCal feeds may provide times in UTC or local time
                // We need to ensure they're stored in Europe/Amsterdam timezone
                $appTimezone = config('app.timezone', 'Europe/Amsterdam');

                if ($start) {
                    // Convert to Carbon and set timezone to Europe/Amsterdam
                    // This preserves the actual moment in time while displaying in the correct timezone
                    $start = Carbon::instance($start)->setTimezone($appTimezone);
                }
                if ($end) {
                    $end = Carbon::instance($end)->setTimezone($appTimezone);
                }

                // Skip events before the minimum date for 'Kerktijden' agenda
                if ($minDate && $start && $start < $minDate) {
                    continue;
                }

                $importedKeys[] = $uid . '|'  . ($start ? $start->format('YmdHis') : '');

                $agendaItem = AgendaItem::updateOrCreate(
                    ['uid' => $uid, 'start_date' => $start, 'agenda_id' => $agenda->id],
                    [
                        'title'       => $event->summary ?? 'No title',
                        'description' => $event->description ?? '',
                        'location'    => $event->location ?? '',
                        'end_date'         => $end,
                        // Add other fields as needed
                    ]
                );

                // Create room reservation if title contains specific terms
                $this->createRoomReservationIfNeeded($agendaItem);
            }

            // Delete events not present in the current feed for this agenda
            // For 'Kerktijden', only delete items from 2026-01-01 onwards
            $existingQuery = AgendaItem::where('agenda_id', $agenda->id);
            if ($minDate) {
                $existingQuery->where('start_date', '>=', $minDate);
            }
            $existing = $existingQuery->get();
            $deleted = 0;
            foreach ($existing as $item) {
                $key = $item->uid . '|' . ($item->start_date ? $item->start_date->format('YmdHis') : '');
                if (!in_array($key, $importedKeys)) {
                    $item->delete();
                    $deleted++;
                }
            }

            $totalImported += count($importedKeys);
            $totalDeleted += $deleted;

            $this->info("Imported/updated " . count($importedKeys) . " events for {$agenda->title}.");
            $this->info("Deleted $deleted events not present in the feed for {$agenda->title}.");
        }

        $this->info("Total imported/updated: $totalImported events across all agendas.");
        $this->info("Total deleted: $totalDeleted events across all agendas.");

        // Check and create services for agenda items that need them
        $this->createMissingServices();

        return 0;
    }

    /**
     * Create services for agenda items that don't have them but should
     */
    private function createMissingServices(): void
    {
        $serviceTitles = ServiceTitleService::getServiceTitles();

        $agendaItemsWithoutServices = AgendaItem::whereIn('title', $serviceTitles)
            ->whereDoesntHave('service')
            ->get();

        $createdCount = 0;
        foreach ($agendaItemsWithoutServices as $agendaItem) {
            Service::create([
                'agenda_item_id' => $agendaItem->id,
                'pastor' => '', // Will be filled in later
                'liturgy' => null,
                'youtube_url' => null,
            ]);
            $createdCount++;
        }

        if ($createdCount > 0) {
            $this->info("Created $createdCount new services for agenda items.");
        } else {
            $this->info("No new services needed to be created.");
        }
    }

    /**
     * Create room reservation for agenda item if title contains specific terms
     */
    private function createRoomReservationIfNeeded(AgendaItem $agendaItem): void
    {
        $title = strtolower($agendaItem->title);
        $roomName = null;

        // Check if title contains 'jeugdvereniging' or 'instroomcatechese'
        if (str_contains($title, 'jeugdvereniging') || str_contains($title, 'instroomcatechese')) {
            $roomName = 'Zaal achter kansel';
        }

        if (!$roomName) {
            return;
        }

        // Find or get the room
        $room = Room::where('name', $roomName)->first();
        if (!$room) {
            $this->warn("Room '{$roomName}' not found. Skipping room reservation for agenda item: {$agendaItem->title}");
            return;
        }

        $user = $this->getUserByTitle($agendaItem->title);

        if (!$user) {
            $this->warn("No user found for agenda item: {$agendaItem->title}. Skipping room reservation.");
            return;
        }

        // Get system user (first admin user) or create a system user
        // $systemUser = User::where('role', 'admin')->first();
        // if (!$systemUser) {
        //     $this->warn("No admin user found. Skipping room reservation for agenda item: {$agendaItem->title}");
        //     return;
        // }

        // Check if reservation already exists for this agenda item
        // We match by room, start_time, and end_time to find existing reservations
        $existingReservation = RoomReservation::where('room_id', $room->id)
            ->where('start_time', $agendaItem->start_date)
            ->where('end_time', $agendaItem->end_date)
            ->first();

        if ($existingReservation) {
            // Update existing reservation if title has changed
            if ($existingReservation->subject !== $agendaItem->title) {
                $existingReservation->update([
                    'subject' => $agendaItem->title,
                ]);
                $this->info("Updated room reservation for '{$roomName}' for agenda item: {$agendaItem->title}");
            }
            return;
        }

        // Check for time conflicts
        if (RoomReservation::hasTimeConflict($room->id, $agendaItem->start_date, $agendaItem->end_date)) {
            $this->warn("Time conflict detected for room '{$roomName}' at {$agendaItem->start_date->format('Y-m-d H:i')}. Skipping reservation for: {$agendaItem->title}");
            return;
        }

        // Create room reservation
        try {
            RoomReservation::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'subject' => $agendaItem->title,
                'number_of_people' => 20, // Default value, can be adjusted
                'start_time' => $agendaItem->start_date,
                'end_time' => $agendaItem->end_date,
            ]);

            $this->info("Created room reservation for '{$roomName}' for agenda item: {$agendaItem->title}");
        } catch (\Exception $e) {
            $this->error("Failed to create room reservation for agenda item '{$agendaItem->title}': " . $e->getMessage());
        }
    }

    private function getUserByTitle(string $title): ?User
    {
        $title = strtolower($title);
        $user = null;
        if(str_contains($title, 'jedid-jah'))
        {
            $user = User::where('email', '=', 'jedid-jah@hhgrijssen.nl')->first();
        }
        else if(str_contains($title, 'het kompas'))
        {
            $user = User::where('email', '=', 'hetkompas@hhgrijssen.nl')->first();
        }
        else if(str_contains($title, 'de wegwijzer'))
        {
            $user = User::where('email', '=', 'dewegwijzer@hhgrijssen.nl')->first();
        }
        else if(str_contains($title, 'tomtom'))
        {
            $user = User::where('email', '=', 'tomtom@hhgrijssen.nl')->first();
        }

        if(!$user)
        {
            $user = User::where('role', '=', 'admin')->first();
        }

        if (!$user) {
            $this->warn("No user found for title: {$title}. Skipping room reservation.");
            return null;
        }
        return $user;
    }
}
