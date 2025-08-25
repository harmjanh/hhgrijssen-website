<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\AgendaItem;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create agenda items first
        $pastAgendaItems = AgendaItem::factory()
            ->count(10)
            ->past()
            ->create();

        $upcomingAgendaItems = AgendaItem::factory()
            ->count(5)
            ->upcoming()
            ->create();

        // Create services for past agenda items (with recordings)
        foreach ($pastAgendaItems as $agendaItem) {
            Service::factory()
                ->withRecording()
                ->create([
                    'agenda_item_id' => $agendaItem->id,
                ]);
        }

        // Create services for some upcoming agenda items (without recordings)
        foreach ($upcomingAgendaItems->take(3) as $agendaItem) {
            Service::factory()
                ->create([
                    'agenda_item_id' => $agendaItem->id,
                ]);
        }
    }
}
