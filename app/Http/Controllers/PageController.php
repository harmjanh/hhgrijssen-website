<?php

namespace App\Http\Controllers;

use App\Actions\News\LoadNewsItemsAction;
use App\Models\Page;
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

    public function live(Page $page)
    {
        return Inertia::render('Page/Live', [
            'page' => $this->getPageData($page),
            'pages' => $this->getPages(),
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
