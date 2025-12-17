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
