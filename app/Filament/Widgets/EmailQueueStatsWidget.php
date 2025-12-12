<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EmailQueueStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return [
            Stat::make('Pending Emails', $pendingJobs)
                ->description('Emails waiting in queue')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Failed Emails', $failedJobs)
                ->description('Emails failed to send')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
