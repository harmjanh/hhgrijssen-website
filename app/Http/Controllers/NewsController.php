<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Page;

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
            ->with('children')
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }
}
