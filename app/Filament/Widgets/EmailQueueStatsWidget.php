<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\EmailQueueMonitoringService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailQueueStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(EmailQueueMonitoringService::class);
        $stats = $service->getQueueStats();

        return [
            Stat::make('Pending', $stats['total_pending'] ?? 0)
                ->description('Jobs waiting in queue')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Processing', $stats['total_processing'] ?? 0)
                ->description('Currently being processed')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Failed', $stats['total_failed'] ?? 0)
                ->description('Failed jobs requiring attention')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Health', ucfirst($stats['overall_health'] ?? 'unknown'))
                ->description('Overall queue health')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color(match ($stats['overall_health'] ?? 'unknown') {
                    'healthy' => 'success',
                    'warning' => 'warning',
                    'critical' => 'danger',
                    default => 'gray',
                }),
        ];
    }
}
