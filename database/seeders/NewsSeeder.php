<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Database\Factories\NewsFactory;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old images
        Storage::disk('public')->deleteDirectory('news');
        Storage::disk('public')->createDirectory('news');

        // Create 150 news items
        NewsFactory::new()
            ->count(150)
            ->sequence(fn ($sequence) => [
                'visible_from' => now()->subDays(rand(0, 365)),
                'visible_until' => rand(0, 1) ? null : now()->addDays(rand(0, 365)),
            ])
            ->create();
    }
}
