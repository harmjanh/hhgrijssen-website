<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsContactFormRequest;
use App\Models\News;
use App\Models\Page;
use App\Notifications\NewsContactFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
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
            'newsItem' => $news->only([
                'id', 'title', 'slug', 'content', 'description', 'image',
                'created_at', 'updated_at', 'visible_from', 'visible_until',
                'contact_form_enabled',
            ]),
            'pages' => $this->getPages(),
        ]);
    }

    public function storeContact(NewsContactFormRequest $request, News $news): RedirectResponse
    {
        abort_unless($news->contact_form_enabled, 404);
        abort_if(empty($news->contact_form_recipient), 500);

        $data = $request->safe()->except('website');

        Notification::route('mail', $news->contact_form_recipient)
            ->notify(new NewsContactFormSubmitted($news, $data));

        return redirect()
            ->route('news.show', $news->slug)
            ->with('success', 'Uw bericht is verzonden. We nemen zo spoedig mogelijk contact met u op.');
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
