<?php

namespace App\Http\Controllers;

use App\Actions\News\LoadNewsItemsAction;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        // Check if this is a live page and redirect to the live method
        if ($page->pageType->name === 'live') {
            return $this->live($page);
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

        // Get upcoming services with their agenda items, ordered by start date
        $upcomingServices = Service::with('agendaItem')
            ->whereHas('agendaItem', function ($query) {
                // $query->where('start_date', '>=', now());
            })
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->orderBy('agenda_items.start_date', 'asc')
            ->limit(3)
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
            ->with('children')
            ->active()
            ->whereNull('parent_id')
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
}
