<?php

namespace Database\Seeders;

use App\Models\CatechesisSeason;
use Illuminate\Database\Seeder;

class CatechesisSeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatechesisSeason::query()->firstOrCreate(
            ['name' => '2026-2027'],
            ['is_open' => true],
        );
    }
}
