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

        return Inertia::render('Page', [
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
