<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\PerformanceMonitoringService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemMetricsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(PerformanceMonitoringService::class);
        $metrics = $service->getSystemMetrics();

        return [
            Stat::make('Masa Respons', number_format($metrics['response_time'] ?? 0, 0).'ms')
                ->description('Purata masa respons')
                ->color($this->getColor('response_time', $metrics['response_time'] ?? 0)),

            Stat::make('Kadar Hit Cache', number_format($metrics['cache_hit_rate'] ?? 0, 1).'%')
                ->description('Keberkesanan cache')
                ->color($this->getColor('cache_hit_rate', $metrics['cache_hit_rate'] ?? 0)),

            Stat::make('Penggunaan Memori', number_format($metrics['memory_usage'] ?? 0, 1).'%')
                ->description('Memori sistem')
                ->color($this->getColor('memory_usage', $metrics['memory_usage'] ?? 0)),
        ];
    }

    private function getColor(string $metric, float|int $value): string
    {
        return match ($metric) {
            'response_time' => $value > 2000 ? 'danger' : ($value > 1000 ? 'warning' : 'success'),
            'cache_hit_rate' => $value < 80 ? 'danger' : ($value < 90 ? 'warning' : 'success'),
            'memory_usage' => $value > 85 ? 'danger' : ($value > 70 ? 'warning' : 'success'),
            default => 'primary',
        };
    }
}
