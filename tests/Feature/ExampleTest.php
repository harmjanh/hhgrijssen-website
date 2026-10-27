<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        Setting::create(['key' => 'homepage_number_articles', 'value' => '3']);

        $pageType = PageType::create(['name' => 'Standaard']);

        Page::create([
            'page_type_id' => $pageType->id,
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Welkom',
            'sort_order' => 1,
            'is_active' => true,
            'exclude_from_navigation' => false,
            'requires_authentication' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
