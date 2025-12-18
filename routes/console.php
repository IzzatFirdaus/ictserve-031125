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

// Memory auto-sync: imports markdown files into Memory Graph daily at 03:00
Schedule::command('memory:sync-markdown')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/memory-sync.log'));

// Laravel Horizon metrics snapshot collection
// Requirement 23.4: Real-time metrics with 60-second refresh intervals
// Requirement 23.8: Integration with Laravel Pulse for unified monitoring
Schedule::command('horizon:snapshot')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Laravel Horizon health monitoring
// Requirement 23.5: Automated alerting for queue issues
Schedule::command('horizon:monitor-health --alert')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/horizon-health.log'));

// Clean up old failed jobs weekly
// Requirement 23.6: Failed job cleanup and retry mechanisms
Schedule::command('horizon:forget --all')
    ->weeklyOn(1, '02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/horizon-cleanup.log'));

// Production monitoring with comprehensive health checks
// Requirement 23.4: Production deployment configuration with health monitoring
Schedule::command('horizon:monitor-production --alert')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->environments(['production', 'staging'])
    ->appendOutputTo(storage_path('logs/horizon-production.log'));

// Daily detailed metrics collection for analysis
Schedule::command('horizon:monitor-production --detailed --json')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/horizon-daily-metrics.log'));
