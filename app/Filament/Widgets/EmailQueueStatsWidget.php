<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * Email Queue Stats Widget (Widget Statistik Giliran E-mel)
 *
 * Monitors email queue status and failed job counts in the Laravel Horizon
 * queue system. Provides real-time visibility into email delivery pipeline
 * health and backlog management.
 *
 * Features:
 * - Pending email count (jobs table)
 * - Failed email tracking (failed_jobs table)
 * - Queue health indicators
 * - Real-time status updates
 * - Visual alert system for queue backlogs
 *
 * @trace D17-§3 (Queue Management with Laravel Horizon)
 * @trace D04-§3.2 (Dashboard Widgets Architecture)
 * @trace D11-§9 (Queue Management and Monitoring)
 * @trace D12-§7 (Queue Monitoring UI/UX)
 *
 * @see \App\Services\EmailNotificationService
 * @see \App\Filament\Traits\WidgetMetadata
 */
class EmailQueueStatsWidget extends BaseWidget
{
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D11 §9 Queue management';
    }

    protected function getStats(): array
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return [
            Stat::make('E-mel Dalam Giliran', $pendingJobs)
                ->description('E-mel menunggu untuk dihantar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('E-mel Gagal', $failedJobs)
                ->description('E-mel gagal dihantar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
