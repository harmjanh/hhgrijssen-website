<?php

namespace App\Http\Controllers;

use App\Actions\News\LoadNewsItemsAction;
use App\Models\Page;
use App\Models\Service;
use App\Models\YouTubeVideo;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        // Check if page requires authentication
        if ($page->requires_authentication && !auth()->check()) {
            abort(403, 'Deze pagina is alleen toegankelijk voor ingelogde gebruikers.');
        }

        // Check if this is a live page and redirect to the live method
        if ($page->pageType->name === 'live') {
            return $this->live($page);
        }

        // Check if this is an archive page and redirect to the archive method
        if ($page->pageType->name === 'archive') {
            return $this->archive($page);
        }

        // If page requires authentication, use AuthenticatedLayout
        if ($page->requires_authentication) {
            return Inertia::render('Page/Authenticated', [
                'page' => $this->getPageData($page),
            ]);
        }

        return Inertia::render('Page', [
            'page' => $this->getPageData($page),
            'pages' => $this->getPages(),
        ]);
    }

    public function live(?Page $page = null)
    {
        // Get the live page if not provided
        if (!$page) {
            $page = Page::query()
                ->join('page_types', 'pages.page_type_id', '=', 'page_types.id')
                ->where('page_types.name', 'live')
                ->firstOrFail();
        }

        // Get services from yesterday to 7 days from now
        $yesterday = now()->subDay()->startOfDay();
        $sevenDaysFromNow = now()->addDays(7)->endOfDay();

        // Get upcoming services with their agenda items, ordered by start date
        $upcomingServices = Service::with('agendaItem')
            ->whereHas('agendaItem', function ($query) use ($yesterday, $sevenDaysFromNow) {
                $query->whereBetween('agenda_items.start_date', [$yesterday, $sevenDaysFromNow]);
            })
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->orderBy('agenda_items.start_date', 'asc')
            ->get()
            ->map(function ($service) {
                // Calculate end_date: use agenda item's end_date or default to 1 hour after start
                $endDate = $service->agendaItem->end_date
                    ? $service->agendaItem->end_date->timestamp
                    : $service->agendaItem->start_date->copy()->addHours(1)->timestamp;

                return [
                    'id' => $service->id,
                    'pastor' => $service->pastor,
                    'liturgy' => $service->liturgy,
                    'start_date' => $service->agendaItem->start_date->format('d-m-Y'),
                    'start_time' => $service->agendaItem->start_date->format('H:i'),
                    'end_date' => $endDate,
                    'title' => $service->agendaItem->title,
                    'youtube_url' => $service->youtube_url,
                ];
            });

        return Inertia::render('Page/Live', [
            'page' => $this->getPageData($page),
            'pages' => $this->getPages(),
            'upcomingServices' => $upcomingServices,
        ]);
    }

    public function agenda()
    {
        $page = Page::query()
            ->join('page_types', 'pages.page_type_id', '=', 'page_types.id')
            ->where('page_types.name', 'agenda')
            ->firstOrFail();

        return Inertia::render('Agenda/Index', [
            'page' => $this->getPageData($page),
            'pages' => $this->getPages(),
        ]);
    }

    public function archive(?Page $page = null)
    {
        // Get the archive page if not provided
        if (!$page) {
            $page = Page::query()
                ->join('page_types', 'pages.page_type_id', '=', 'page_types.id')
                ->where('page_types.name', 'archive')
                ->firstOrFail();
        }

        // Get filter parameters from request
        $pastorFilter = request()->get('pastor');
        $dateFilter = request()->get('date');
        $yearFilter = request()->get('year');

        // Get available years from services
        // Only include years from services that started at least 3 hours ago
        $availableYears = Service::join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->whereRaw('DATE_ADD(agenda_items.start_date, INTERVAL 3 HOUR) <= ?', [now()])
            ->selectRaw('YEAR(agenda_items.start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no year is specified, use the most recent year
        if (!$yearFilter && !empty($availableYears)) {
            $yearFilter = $availableYears[0];
        }

        // Build query for services
        // Only show services that started at least 3 hours ago
        $servicesQuery = Service::with('agendaItem')
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->whereRaw('DATE_ADD(agenda_items.start_date, INTERVAL 3 HOUR) <= ?', [now()])
            ->orderBy('agenda_items.start_date', 'desc');

        // Apply year filter if provided
        if ($yearFilter) {
            $servicesQuery->whereYear('agenda_items.start_date', $yearFilter);
        }

        // Apply pastor filter if provided
        if ($pastorFilter) {
            $servicesQuery->where('services.pastor', $pastorFilter);
        }

        // Apply date filter if provided
        if ($dateFilter) {
            $servicesQuery->whereDate('agenda_items.start_date', $dateFilter);
        }

        // Get all services matching the filters
        $services = $servicesQuery->get()->map(function ($service) {
            // Find matching YouTube video by date and time in title

            $serviceData = [
                'id' => $service->id,
                'pastor' => $service->pastor,
                'date' => $service->agendaItem->start_date->format('d-m-Y'),
                'time' => $service->agendaItem->start_date->format('H:i'),
                'start_date' => $service->agendaItem->start_date->format('Y-m-d'),
                'year' => $service->agendaItem->start_date->format('Y'),
            ];

            if ($service->youtube_video_id) {
                $serviceData['youtube_video'] = [
                    'id' => $service->youtubeVideo->id,
                    'url' => $service->youtubeVideo->url,
                    'has_audio' => !empty($service->youtubeVideo->audio_file_path),
                    'download_status' => $service->youtubeVideo->download_status,
                ];
            }

            return $serviceData;
        });

        // Get distinct pastors for filter dropdown
        // Only include pastors from services that started at least 3 hours ago
        // Filter by year if year filter is applied
        $pastorsQuery = Service::join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->whereRaw('DATE_ADD(agenda_items.start_date, INTERVAL 3 HOUR) <= ?', [now()])
            ->whereNotNull('services.pastor');

        if ($yearFilter) {
            $pastorsQuery->whereYear('agenda_items.start_date', $yearFilter);
        }

        $pastors = $pastorsQuery->distinct()
            ->orderBy('services.pastor')
            ->pluck('services.pastor')
            ->toArray();

        return Inertia::render('Page/Archive', [
            'page' => $this->getPageData($page),
            'pages' => $this->getPages(),
            'services' => $services,
            'pastors' => $pastors,
            'availableYears' => $availableYears,
            'isAdmin' => auth()->check() && auth()->user()->hasRole('admin'),
            'filters' => [
                'pastor' => $pastorFilter,
                'date' => $dateFilter,
                'year' => $yearFilter,
            ],
        ]);
    }

    /**
     * Find YouTube video that matches the service date and time
     */
    private function findYouTubeVideoByServiceDate(Service $service): ?YouTubeVideo
    {
        $startDate = $service->agendaItem->start_date;

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

    /**
     * Trigger download of YouTube video for a service
     */
    public function downloadVideo(Service $service)
    {
        // Check if user is admin
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Alleen beheerders kunnen video\'s downloaden.');
        }

        // Check if service has a YouTube video
        if (!$service->youtube_video_id) {
            abort(404, 'Geen YouTube video gevonden voor deze dienst.');
        }

        $youtubeVideo = YouTubeVideo::find($service->youtube_video_id);

        if (!$youtubeVideo) {
            abort(404, 'YouTube video niet gevonden.');
        }

        // Dispatch the download job
        \App\Jobs\DownloadYouTubeVideo::dispatch($youtubeVideo);

        return response()->json([
            'message' => 'Download gestart. Het audio bestand zal binnenkort beschikbaar zijn.',
        ]);
    }

    /**
     * Stream audio file for a service
     */
    public function streamAudio(Service $service)
    {
        // First check if service has a direct youtube_video_id
        if ($service->youtube_video_id) {
            $youtubeVideo = YouTubeVideo::find($service->youtube_video_id);
            if ($youtubeVideo && !empty($youtubeVideo->audio_file_path)) {
                return $this->streamAudioFile($youtubeVideo);
            }
        }

        // Fallback: Find matching YouTube video by date and time
        $youtubeVideo = $this->findYouTubeVideoByServiceDate($service);

        if (!$youtubeVideo || empty($youtubeVideo->audio_file_path)) {
            abort(404, 'Audio niet gevonden');
        }

        return $this->streamAudioFile($youtubeVideo);
    }

    /**
     * Stream audio file from YouTube video (works with both local and S3)
     */
    private function streamAudioFile(YouTubeVideo $youtubeVideo)
    {
        if (empty($youtubeVideo->audio_file_path)) {
            abort(404, 'Audio niet gevonden');
        }

        $disk = Storage::disk('youtube');
        $audioPath = $youtubeVideo->audio_file_path;

        // Check if file exists
        if (!$disk->exists($audioPath)) {
            abort(404, 'Audio bestand niet gevonden');
        }

        // If using S3, stream directly from S3
        if (config('filesystems.disks.youtube.driver') === 's3') {
            return response()->streamDownload(function () use ($disk, $audioPath) {
                $stream = $disk->readStream($audioPath);
                while (!feof($stream)) {
                    echo fread($stream, 8192);
                    flush();
                }
                fclose($stream);
            }, basename($audioPath), [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'inline; filename="' . basename($audioPath) . '"',
            ]);
        }

        // Local storage fallback
        $localPath = $disk->path($audioPath);
        if (!file_exists($localPath)) {
            abort(404, 'Audio bestand niet gevonden');
        }

        return response()->streamDownload(function () use ($localPath) {
            $stream = fopen($localPath, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        }, basename($audioPath), [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="' . basename($audioPath) . '"',
        ]);
    }

    public function home(LoadNewsItemsAction $loadNewsItemsAction)
    {
        $page = Page::where('slug', 'home')->firstOrFail();

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'pages' => $this->getPages(),
            'page' => $this->getPageData($page),
            'newsItems' => $loadNewsItemsAction->execute(),
        ]);
    }

    private function getPages()
    {
        return Page::select(['id', 'title', 'slug'])
            ->with(['children' => function ($query) {
                $query->where('exclude_from_navigation', false)
                    ->active()
                    ->orderBy('sort_order');
            }])
            ->active()
            ->whereNull('parent_id')
            ->where('exclude_from_navigation', false)
            ->where('requires_authentication', false)
            ->orderBy('sort_order')
            ->get();
    }

    private function getPageData(Page $page)
    {
        return [
            'title' => $page->title,
            'content' => $page->content,
            'type' => $page->pageType->name,
            'header_image' => $page->header_image,
        ];
    }

    /**
     * Get pages that require authentication for authenticated users
     */
    public static function getAuthenticatedPages()
    {
        return Page::select(['id', 'title', 'slug'])
            ->with(['children' => function ($query) {
                $query->where('requires_authentication', true)
                    ->active()
                    ->orderBy('sort_order');
            }])
            ->active()
            ->where('requires_authentication', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }
}
