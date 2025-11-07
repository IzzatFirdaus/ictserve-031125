<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule system alert checks
Schedule::command('alerts:check')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/alerts.log'));

// Schedule daily alert summary
Schedule::command('alerts:check --type=system_health')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/alerts.log'));

// Cleanup portal activities older than 7 years (monthly on 1st at 02:00 AM)
// Per D03-NFR-004 and D11 §14.5: 7-year retention policy
Schedule::command('portal:cleanup-activities')
    ->monthlyOn(1, '02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/portal-cleanup.log'));
