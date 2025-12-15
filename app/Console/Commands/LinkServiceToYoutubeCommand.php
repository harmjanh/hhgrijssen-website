<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YouTubeVideo;
use App\Models\AgendaItem;
use App\Models\Service;
use Carbon\Carbon;

class LinkServiceToYoutubeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:link-service-to-youtube-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link a service to a youtube video';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $youtubeVideos = YouTubeVideo::query()->whereNull('service_id')->get();

        foreach ($youtubeVideos as $youtubeVideo) {
            $title = $youtubeVideo->title;

            // Check if the title contains a date pattern (DD-MM-YYYY)
            if (!preg_match('/\d{2}-\d{2}-\d{4}/', $title, $dateMatches)) {
                // No date found, skip this video
                continue;
            }

            // Extract the date (DD-MM-YYYY format)
            $date = $dateMatches[0];

            // Extract the time (HH:MM format)
            $time = null;
            if (preg_match('/\d{2}:\d{2}/', $title, $timeMatches)) {
                $time = $timeMatches[0];
            }

            $dateTimeString = $date . ' ' . $time;
            try{
                $dateTime = Carbon::createFromFormat('d-m-Y H:i', $dateTimeString);
                $dateTime->setMilliseconds(0);
                // first get an agenda item for the date and time based on start_date
                $agendaItem = AgendaItem::query()->where('start_date', '=', $dateTime->format('Y-m-d H:i:s'))->first();
                if ($agendaItem) {
                    $service = $agendaItem->service;
                    if(!$service) {
                        $service = Service::create([
                            'agenda_item_id' => $agendaItem->id,
                            'pastor' => $agendaItem->title,
                            'liturgy' => null,
                            'youtube_url' => $youtubeVideo->url,
                        ]);
                    }
                    $youtubeVideo->service_id = $service->id;
                    $youtubeVideo->save();
                }
            } catch (\Exception $e) {
                $this->error('Error creating date time: ' . $dateTimeString . ' - ' . $e->getMessage());
                continue;
            }
        }
    }
}
