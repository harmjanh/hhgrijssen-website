<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgendaItem;
use App\Models\Service;
use App\Models\YouTubeVideo;

class CreateServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-services-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create services for all agenda items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting service creation and YouTube video linking...');

        $nonServiceTitles = [
            'Jeugd (<12 jaar) en contactdag',
            'Jeugddag (12+)',
            'Jeugdvereniging Het Kompas',
            'Jeugdvereniging Jedid-Jah',
            'Jeugdvereniging Jedid',
            'Jeugdvereniging TomTom',
            'Jeugdvereniging De Wegwijzer',
            'Kinderappel',
            'KiBi club',
            'KiBi',
            'KiBi club (kerst)',
            'Afsluitingsavond catechisatie',
            'Jeugdvereniging 16+',
            'Opening catechisatie seizoen - Presentatie SDOK',
            'Jeugdvereniging De Wegwijzer - Gaat niet door!',
            'Gemeenteavond',
            'Bezinningsuur Heilig Avondmaal',
            'Winterwandeling PZC',
            'Instroomcatechese',
            'Psalmzangavond',
            'Landelijke zendingsdag HHK',
            'Inloop + koffiedrinken Bijbelkring',
            'Bijbelkring (NGB)',
            'Avondmaalsbijeenkomst',
            'BBQ 16+',
            'Starten fietstocht PZC',
            'Vrouwenvereniging \'Martha\'',
            'Mannenvereniging \'Onderzoekt de Schriften\'',
            'Lidmatenkring',
            'Contactmiddag',
            'Jeugdvereniging \'Jedid-Jah\'',
            'Jeugdvereniging \'De Wegwijzer\'',
            'Jeugdvereniging \'Het Kompas\'',
            'Jeugdvereniging \'Tom-Tom\'',
            'Doopzitting',
            'Jeugdvereniging \'16+\'',
            'Jeugdvereniging 16+: Diner met kerkenraad',
            'Inloop + koffiedrinken',
            'Afsluitingsavond MV+VV',
            'Evangelisatieavond',
            'Toerustingsavond, thema: Mediaopvoeding',
            'Avond voor leidinggevenden jeugdwerk en KiBi-club',
            'Toerustingsavond, thema: Gewetensvorming',
            'Toerustingsavond, thema: Het gebed en geloofsgesprek met kinderen',
            'Actiedag / kerkpleinmarkt',
            'BBQ JV \'Tom-Tom\' & JV \'16+\'',
            'JV \'16+\'',
            'Afsluiting catechisaties',
            'Actiedag',
            'Toerustingsavond \'Huisgodsdienst\'',
            'Toerustingsavond',
            'Bijbelkring NGB',
            'Actiedag 2017',
            '\'Jeugdvereniging\' de Wegwijzer',
            '\'Jeugdvereniging\' het Kompas',
            '\'Jeugdvereniging\' Jedid-Jah',
            'Bijbelkring (Ziekentroost)',
            'Bijbelkring',
            'Jeugdvereniging \'+16\'',
            'Kinderappèl (Jedid-Jah en de Wegwijzer)',
            'Sportieve buitenactiviteit 16+',
            'Oefenen kerstfeest',
            'HHJO 16-',
            'Psalmzanguur',
            'Kerstfeest met de kinderen',
            'Uitzending over Ziekentroost',
            'Voorleesmoment',
            'Bunyankring',
            'HHG Rijssen Familiequiz',
            'Online familieavond',
            'Bidstond',
            'PZC - Winterwandeling',
            'Vrijwilligersactiviteit',
            'Fietstocht PZC',
            'Jeugd en contactdag',
            'Jeugdavond',
            'Start zomerfietstocht',
            'Instroomcatechese - nieuwe leden',
            'Jeugdvereniging Jedid-jah + de Wegwijzer',
            'Bijbelstudieconferentie 18+',
            'Jongerendag 16+',
            'Bijbelstudieconfentie 23+',
            'Jongerendag 16-',
            'Kinderappel Lunteren',
        ];

        $agendaItems = AgendaItem::all();
        $totalItems = $agendaItems->count();
        $processed = 0;
        $linked = 0;
        $skipped = 0;

        $this->info("Found {$totalItems} agenda items to process.");

        foreach ($agendaItems as $agendaItem) {
            // Skip if this is not a service
            if (in_array(strtolower($agendaItem->title), array_map('strtolower', $nonServiceTitles), true)) {
                $skipped++;
                continue;
            }

            // Extract pastor from title
            $pastor = $this->extractPastorFromTitle($agendaItem->title);

            // Find matching YouTube video by date and time in title
            $youtubeVideo = $this->findYouTubeVideoByServiceDate($agendaItem);

            $serviceData = [
                'pastor' => $pastor,
            ];

            // Add YouTube video ID if found
            if ($youtubeVideo) {
                $serviceData['youtube_video_id'] = $youtubeVideo->id;
                $linked++;
            }

            Service::updateOrCreate(
                [
                    'agenda_item_id' => $agendaItem->id,
                ],
                $serviceData
            );

            $processed++;
        }

        $this->info("Completed!");
        $this->info("Processed: {$processed} services");
        $this->info("Linked YouTube videos: {$linked}");
        $this->info("Skipped (non-services): {$skipped}");
    }

    /**
     * Extract pastor name from agenda item title.
     */
    private function extractPastorFromTitle(string $title): ?string
    {
        // Remove "(aangepaste dienst)"
        $title = str_replace('(aangepaste dienst)', '', $title);

        // Remove everything after "-" (including the dash)
        if (strpos($title, '-') !== false) {
            $title = trim(substr($title, 0, strpos($title, '-')));
        }

        // Trim whitespace
        $title = trim($title);

        return $title ?: null;
    }

    /**
     * Find YouTube video that matches the agenda item date and time
     */
    private function findYouTubeVideoByServiceDate(AgendaItem $agendaItem): ?YouTubeVideo
    {
        $startDate = $agendaItem->start_date;

        if (!$startDate) {
            return null;
        }

        // Extract date and time components
        $dateOnly = $startDate->format('d-m-Y');  // 19-11-2025
        $timeOnly = $startDate->format('H:i');    // 19:30
        $dateWithTime = $startDate->format('d-m-Y H:i');  // 19-11-2025 19:30

        // Also try alternative date formats
        $dateOnlyAlt = $startDate->format('d/m/Y');  // 19/11/2025
        $dateWithTimeAlt = $startDate->format('d/m/Y H:i');  // 19/11/2025 19:30

        // Search for videos that contain both the date and time
        // This handles formats like "Woensdag 19-11-2025 19:30" or "Zondag 23-11-2025 10:00"
        $videos = YouTubeVideo::where(function ($query) use ($dateOnly, $timeOnly) {
            $query->where('title', 'LIKE', '%' . $dateOnly . '%')
                  ->where('title', 'LIKE', '%' . $timeOnly . '%');
        })->orWhere(function ($query) use ($dateOnlyAlt, $timeOnly) {
            $query->where('title', 'LIKE', '%' . $dateOnlyAlt . '%')
                  ->where('title', 'LIKE', '%' . $timeOnly . '%');
        })->orWhere('title', 'LIKE', '%' . $dateWithTime . '%')
          ->orWhere('title', 'LIKE', '%' . $dateWithTimeAlt . '%')
          ->get();

        // Return the first match (should be unique based on date+time)
        return $videos->first();
    }
}
