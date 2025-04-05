<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\AgendaItem;
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
        $agenda = Agenda::first();

        // $feeds = [
        //     'feed1' => 'https://example.com/feed1.ics',
        //     'feed2' => 'https://example.com/feed2.ics',
        //     // Add your feed URLs here
        // ];

        $url = $agenda->ical_url;

        // foreach ($feeds as $source => $url) {
        try {
            $ical = new ICal($url, [
                // 'defaultSpan'                 => 2,     // Default value
                // 'defaultTimeZone'            => 'Europe/Amsterdam',
                // // 'defaultWeekStart'           => 'SU',  // Default value
                // 'disableCharacterReplacement' => false, // Default value
                // 'skipRecurrence'             => false, // Default value
                // 'useTimeZoneWithRRules'      => false, // Default value
            ]);
            $events = $ical->events();
            // Log::info($events);
            // die;
            foreach ($events as $event) {
                $recurrenceId = $event->additionalProperties['recurrence_id'] ?? '';
                $eventId = $event->uid . '_' . $recurrenceId;
                $agendaItem = AgendaItem::query()
                    ->where('uid', $eventId)
                    ->where('agenda_id', $agenda->id)
                    ->first();

                if ($agendaItem) {
                    dd($agendaItem, $event);
                    $agendaItem->update([
                        'title' => $event->summary ?? '',
                        'description' => $event->description ?? '',
                        'start_date' => date('Y-m-d H:i:s', strtotime($event->dtstart)),
                        'end_date' => $event->dtend ? date('Y-m-d H:i:s', strtotime($event->dtend)) : null,
                        'location' => $event->location ?? '',
                    ]);
                } else {
                    AgendaItem::create(
                        [
                            'uid' => $eventId,
                            'agenda_id' => $agenda->id,
                            'title' => $event->summary ?? '',
                            'description' => $event->description ?? '',
                            'start_date' => date('Y-m-d H:i:s', strtotime($event->dtstart)),
                            'end_date' => $event->dtend ? date('Y-m-d H:i:s', strtotime($event->dtend)) : null,
                            'location' => $event->location ?? '',
                        ]
                    );
                }
            }

            $this->info("Successfully imported events from {$agenda->title}");
        } catch (Exception $e) {
            $this->error("Failed to import {$agenda->title}: " . $e->getMessage());
        }
        // }
    }
}
