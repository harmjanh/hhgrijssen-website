<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER_HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
    ];

    public function test_news_index_visit_is_tracked(): void
    {
        $this->get(route('news.index'), self::BROWSER_HEADERS)->assertOk();

        $this->assertDatabaseHas('page_views', [
            'path' => '/nieuws',
            'viewable_id' => null,
        ]);
    }

    public function test_news_article_visit_is_tracked_with_viewable(): void
    {
        $news = $this->createNews();

        $this->get(route('news.show', $news->slug), self::BROWSER_HEADERS)->assertOk();

        $this->assertDatabaseHas('page_views', [
            'path' => '/nieuws/' . $news->slug,
            'viewable_type' => News::class,
            'viewable_id' => $news->id,
        ]);

        $this->assertSame(1, $news->pageViews()->count());
    }

    public function test_bot_visits_are_not_tracked(): void
    {
        $this->get(route('news.index'), [
            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ])->assertOk();

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_visits_without_user_agent_are_not_tracked(): void
    {
        $this->get(route('news.index'), ['User-Agent' => ''])->assertOk();

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_inertia_partial_reloads_are_not_tracked(): void
    {
        $this->get(route('news.index'), self::BROWSER_HEADERS + [
            'X-Inertia' => 'true',
            'X-Inertia-Partial-Component' => 'News/Index',
            'X-Inertia-Partial-Data' => 'news',
        ]);

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_not_found_pages_are_not_tracked(): void
    {
        $this->get('/nieuws/bestaat-niet', self::BROWSER_HEADERS)->assertNotFound();

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_visitor_hash_is_anonymous_and_stable_per_day(): void
    {
        $this->get(route('news.index'), self::BROWSER_HEADERS)->assertOk();
        $this->get(route('news.index'), self::BROWSER_HEADERS)->assertOk();

        $hashes = PageView::query()->pluck('visitor_hash');

        $this->assertCount(2, $hashes);
        $this->assertSame(1, $hashes->unique()->count());
        $this->assertStringNotContainsString('127.0.0.1', $hashes->first());
    }

    private function createNews(): News
    {
        return News::create([
            'title' => 'Testartikel',
            'description' => 'Beschrijving',
            'content' => 'Inhoud van het artikel',
            'is_published' => 1,
            'visible_from' => now()->subDay(),
        ]);
    }
}
