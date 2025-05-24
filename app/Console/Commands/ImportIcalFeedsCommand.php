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
        $url = $agenda->ical_url;
        $this->info("Fetching iCal from: $url");

        try {
            $ical = new ICal($url, [
                'skipRecurrence' => false,
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to parse iCal: " . $e->getMessage());
            return 1;
        }

        $events = $ical->events();
        $importedKeys = [];

        foreach ($events as $event) {
            $uid = $event->uid;
            $start = $ical->iCalDateToDateTime($event->dtstart_array[3]);
            $end = $ical->iCalDateToDateTime($event->dtend_array[3] ?? $event->dtstart_array[3]);

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


        // Delete events not present in the current feed
        $existing = AgendaItem::all();
        $deleted = 0;
        foreach ($existing as $item) {
            $key = $item->uid . '|' . ($item->start_date ? $item->start_date->format('YmdHis') : '');
            if (!in_array($key, $importedKeys)) {
                $item->delete();
                $deleted++;
            }
        }

        $this->info("Imported/updated " . count($importedKeys) . " events.");
        $this->info("Deleted $deleted events not present in the feed.");
        return 0;
    }
}
