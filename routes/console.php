<?php

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
