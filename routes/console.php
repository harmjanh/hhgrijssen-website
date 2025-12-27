<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('youtube:sync')
    ->hourly()
    ->between('06:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

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
