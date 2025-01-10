<?php

namespace Database\Seeders;

use App\Models\PageType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pageTypes = [
            ['name' => 'homepage', 'max_count' => 1],
            ['name' => 'news_overview', 'max_count' => 1],
            ['name' => 'content', 'max_count' => null],
            ['name' => 'contact', 'max_count' => 1],
        ];

        foreach ($pageTypes as $type) {
            PageType::query()->updateOrCreate([
                'name' => $type['name'],
            ],
                [
                    'max_count' => $type['max_count'],
                ]
            );
        }
    }
}
