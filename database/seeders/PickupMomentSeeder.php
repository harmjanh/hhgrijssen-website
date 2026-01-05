<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\AgendaItem;
use App\Models\PickupMoment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Observers\PickupMomentObserver;

class PickupMomentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $observer = app(PickupMomentObserver::class);

        // Get or create the "Collectemunten" agenda
        $agenda = Agenda::firstOrCreate([
            'title' => 'Collectemunten',
        ]);

        // Start from Saturday, January 10, 2026
        $startDate = Carbon::create(2026, 1, 10);
        $endDate = Carbon::create(2026, 12, 31);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Only create pickup moments on Saturdays
            if ($currentDate->isSaturday()) {
                $pickupMoment = PickupMoment::firstOrCreate([
                    'date' => $currentDate->toDateString()
                ], [
                    'active' => true,
                ]);

                $observer->created($pickupMoment);
            }

            // Move to next Saturday (add 2 weeks = 14 days)
            $currentDate->addDays(14);
        }
    }
}
