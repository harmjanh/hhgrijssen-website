<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agenda;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agenda::firstOrCreate([
            'title' => 'Erediensten',
        ], [
            'ical_url' => 'https://calendar.google.com/calendar/ical/agenda%40hhgrijssen.nl/public/basic.ics',
        ]);

        Agenda::firstOrCreate([
            'title' => 'Verenigingen',
        ], [
            'ical_url' => 'https://calendar.google.com/calendar/ical/6sn8u1evd31q2s76eqv20c8q9k%40group.calendar.google.com/public/basic.ics',
        ]);

        Agenda::firstOrCreate([
            'title' => 'Overige',
        ], [
            'ical_url' => 'https://calendar.google.com/calendar/ical/0umajs375su1soaadikjau50u8%40group.calendar.google.com/public/basic.ics',
        ]);
    }
}
