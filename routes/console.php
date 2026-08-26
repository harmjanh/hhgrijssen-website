<?php

use Illuminate\Support\Facades\Schedule;

// Schedule::command('youtube:sync')
//     ->hourly()
//     ->between('06:00', '23:00')
//     ->withoutOverlapping()
//     ->runInBackground();

// hourly sync agenda items
Schedule::command('import:ical-feeds')
    ->hourlyAt(5)
    ->between('06:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('app:create-services-command')
    ->hourlyAt(15)
    ->between('06:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

// Audio van diensten (4 uur na aanvang) naar het prekenarchief;
// venster tot 23:59 zodat een avonddienst dezelfde avond verwerkt wordt.
Schedule::command('services:process-audio --missing --queue --limit=3')
    ->everyThirtyMinutes()
    ->between('06:00', '23:59')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('mollie:check-payment-status')
    ->hourlyAt(20)
    ->between('06:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('mollie:check-payment-status --type=treat')
    ->hourlyAt(25)
    ->between('06:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule reservation reminders to run daily at 8 AM
Schedule::command('reservations:send-reminders')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule weekly reservations overview to run every Saturday at 8 AM
Schedule::command('reservations:send-weekly-overview')
    ->saturdays()
    ->at('08:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('telescope:prune --hours=168')
    ->daily()
    ->at('03:00');
