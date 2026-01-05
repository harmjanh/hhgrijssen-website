<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceTitleService;
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

                // Skip events before the minimum date for 'Kerktijden' agenda
                if ($minDate && $start && $start < $minDate) {
                    continue;
                }

                $importedKeys[] = $uid . '|'  . ($start ? $start->format('YmdHis') : '');

                AgendaItem::updateOrCreate(
                    ['uid' => $uid, 'start_date' => $start, 'agenda_id' => $agenda->id],
                    [
                        'title'       => $event->summary ?? 'No title',
                        'description' => $event->description ?? '',
                        'location'    => $event->location ?? '',
                        'end_date'         => $end,
                        // Add other fields as needed
                    ]
                );
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
}
