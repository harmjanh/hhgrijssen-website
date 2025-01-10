<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $map = [
            [
                'key'   => 'homepage_number_articles',
                'value' => '5',
            ],
        ];

        foreach ($map as $row) {
            \App\Models\Setting::query()
                ->firstOrCreate([
                    'key' => $row['key'],
                ], [
                    'value' => $row['value'],
                ]);
        }
    }
}
