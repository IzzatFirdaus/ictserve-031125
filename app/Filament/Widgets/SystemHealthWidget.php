<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\PerformanceMonitoringService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * System Health Widget for Filament Dashboard
 *
 * Displays server health metrics including CPU usage, memory consumption,
 * and disk space utilization with color-coded status indicators.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Requirements 36.5
 *
 * @trace Requirements 36.5
 */
class SystemHealthWidget extends BaseWidget
{
    /**
     * Widget sort order on dashboard
     */
    protected static ?int $sort = 11;

    /**
     * Polling interval for real-time updates (30 seconds)
     */
    protected ?string $pollingInterval = '30s';

    /**
     * Widget heading
     */
    protected ?string $heading = 'System Health';

    /**
     * Widget description
     */
    protected ?string $description = 'Server resource utilization';

    /**
     * Health status thresholds
     */
    private const THRESHOLDS = [
        'cpu' => [
            'warning' => 70,
            'danger' => 85,
        ],
        'memory' => [
            'warning' => 70,
            'danger' => 85,
        ],
        'disk' => [
            'warning' => 75,
            'danger' => 90,
        ],
    ];

    /**
     * Get the health statistics for display
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $service = app(PerformanceMonitoringService::class);
        $serverHealth = $service->getServerHealthMetrics();

        // Get the first server's metrics (typically localhost)
        $serverMetrics = reset($serverHealth['servers']) ?: $this->getDefaultMetrics();
        $serverName = array_key_first($serverHealth['servers']) ?? 'localhost';

        return [
            $this->buildCpuStat($serverMetrics, $serverName),
            $this->buildMemoryStat($serverMetrics),
            $this->buildDiskStat($serverMetrics),
            $this->buildOverallHealthStat($serverHealth['overall_health'] ?? 'unknown'),
        ];
    }

    /**
     * Build CPU usage statistic
     *
     * @param  array<string, mixed>  $metrics
     */
    private function buildCpuStat(array $metrics, string $serverName): Stat
    {
        $cpuPercent = $metrics['cpu_percent'] ?? 0;
        $color = $this->getStatusColor('cpu', $cpuPercent);
        $status = $this->getStatusLabel($color);

        return Stat::make('CPU Usage', number_format($cpuPercent, 1).'%')
            ->description("{$status} - {$serverName}")
            ->descriptionIcon($this->getStatusIcon($color))
            ->color($color)
            ->chart($this->getCpuChartData())
            ->extraAttributes([
                'title' => __('Current CPU utilization'),
                'class' => 'system-health-cpu',
            ]);
    }

    /**
     * Build memory usage statistic
     *
     * @param  array<string, mixed>  $metrics
     */
    private function buildMemoryStat(array $metrics): Stat
    {
        $memoryPercent = $metrics['memory_percent'] ?? 0;
        $memoryUsedMb = $metrics['memory_used_mb'] ?? 0;
        $memoryTotalMb = $metrics['memory_total_mb'] ?? 0;
        $color = $this->getStatusColor('memory', $memoryPercent);

        $description = sprintf(
            '%.0f MB / %.0f MB',
            $memoryUsedMb,
            $memoryTotalMb
        );

        return Stat::make('Memory Usage', number_format($memoryPercent, 1).'%')
            ->description($description)
            ->descriptionIcon($this->getStatusIcon($color))
            ->color($color)
            ->chart($this->getMemoryChartData())
            ->extraAttributes([
                'title' => __('Current memory consumption'),
                'class' => 'system-health-memory',
            ]);
    }

    /**
     * Build disk usage statistic
     *
     * @param  array<string, mixed>  $metrics
     */
    private function buildDiskStat(array $metrics): Stat
    {
        $diskPercent = $metrics['disk_percent'] ?? 0;
        $diskUsedGb = $metrics['disk_used_gb'] ?? 0;
        $diskTotalGb = $metrics['disk_total_gb'] ?? 0;
        $color = $this->getStatusColor('disk', $diskPercent);

        $description = sprintf(
            '%.1f GB / %.1f GB',
            $diskUsedGb,
            $diskTotalGb
        );

        return Stat::make('Disk Space', number_format($diskPercent, 1).'%')
            ->description($description)
            ->descriptionIcon($this->getStatusIcon($color))
            ->color($color)
            ->chart($this->getDiskChartData())
            ->extraAttributes([
                'title' => __('Current disk space utilization'),
                'class' => 'system-health-disk',
            ]);
    }

    /**
     * Build overall health status statistic
     */
    private function buildOverallHealthStat(string $overallHealth): Stat
    {
        $color = match ($overallHealth) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };

        $icon = match ($overallHealth) {
            'healthy' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical' => 'heroicon-o-exclamation-circle',
            default => 'heroicon-o-question-mark-circle',
        };

        $label = match ($overallHealth) {
            'healthy' => 'All Systems Operational',
            'warning' => 'Some Issues Detected',
            'critical' => 'Critical Issues',
            default => 'Status Unknown',
        };

        return Stat::make('Overall Status', ucfirst($overallHealth))
            ->description($label)
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'title' => __('Overall system health status'),
                'class' => 'system-health-overall',
            ]);
    }

    /**
     * Get status color based on metric type and value
     */
    private function getStatusColor(string $metric, float $value): string
    {
        $thresholds = self::THRESHOLDS[$metric] ?? ['warning' => 70, 'danger' => 85];

        if ($value >= $thresholds['danger']) {
            return 'danger';
        }

        if ($value >= $thresholds['warning']) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get status label from color
     */
    private function getStatusLabel(string $color): string
    {
        return match ($color) {
            'success' => 'Normal',
            'warning' => 'Warning',
            'danger' => 'Critical',
            default => 'Unknown',
        };
    }

    /**
     * Get status icon based on color
     */
    private function getStatusIcon(string $color): string
    {
        return match ($color) {
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'danger' => 'heroicon-o-exclamation-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    /**
     * Get default metrics when service data is unavailable
     *
     * @return array<string, mixed>
     */
    private function getDefaultMetrics(): array
    {
        return [
            'cpu_percent' => 0,
            'memory_used_mb' => 0,
            'memory_total_mb' => 0,
            'memory_percent' => 0,
            'disk_used_gb' => 0,
            'disk_total_gb' => 0,
            'disk_percent' => 0,
            'last_seen_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get chart data for CPU usage trend
     *
     * @return array<int>
     */
    private function getCpuChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [25, 35, 28, 42, 38, 45, 32, 40, 35, 38, 30, 35];
    }

    /**
     * Get chart data for memory usage trend
     *
     * @return array<int>
     */
    private function getMemoryChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [55, 58, 60, 62, 58, 65, 63, 60, 58, 62, 60, 58];
    }

    /**
     * Get chart data for disk usage trend
     *
     * @return array<int>
     */
    private function getDiskChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [45, 45, 46, 46, 47, 47, 48, 48, 48, 49, 49, 50];
    }
}
