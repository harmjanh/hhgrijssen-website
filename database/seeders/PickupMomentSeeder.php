<?php

namespace Database\Seeders;

use App\Models\PickupMoment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PickupMomentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start from Saturday, January 10, 2026
        $startDate = Carbon::create(2026, 1, 10);
        $endDate = Carbon::create(2026, 12, 31);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Only create pickup moments on Saturdays
            if ($currentDate->isSaturday()) {
                PickupMoment::firstOrCreate([
                    'date' => $currentDate->toDateString(),
                    'active' => true,
                ]);
            }

            // Move to next Saturday (add 2 weeks = 14 days)
            $currentDate->addDays(14);
        }
    }
}
