<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('News/Index', [
            'news' => News::query()
                ->visible()
                ->orderByDesc('updated_at')
                ->paginate(10),
            'pages' => $this->getPages(),
        ]);
    }

    public function show(News $news)
    {
        return Inertia::render('News/Show', [
            'newsItem' => $news,
            'pages' => $this->getPages(),
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
}
