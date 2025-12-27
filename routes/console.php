<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule YouTube video sync to run daily at 6 AM
Schedule::command('youtube:sync')
    ->daily()
    ->at('06:00')
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
