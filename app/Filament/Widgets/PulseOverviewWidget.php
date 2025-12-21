<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Laravel\Pulse\Facades\Pulse;

/**
 * Pulse Overview Widget
 *
 * Displays key performance metrics from Laravel Pulse including
 * response times, slow queries, error rates, and server health.
 *
 * Features:
 * - Real-time performance metrics display
 * - Color-coded status indicators (green/yellow/red)
 * - Click-to-action navigation to detailed Pulse dashboard
 * - Role-based access control (admin and superuser only)
 * - 2-minute cache TTL for performance optimization
 * - WCAG 2.2 AA compliant with proper contrast ratios
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R18 (Pulse Dashboard Integration)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D17 Queue Management - Laravel Horizon integration
 *
 * @version 3.6.1
 */
class PulseOverviewWidget extends BaseWidget
{
    use CacheableWidget;
    use WidgetMetadata;

    protected static ?int $sort = 10;

    protected static bool $isLazy = false; // Critical performance metrics - load immediately

    /**
     * 2-minute polling for performance metrics
     */
    protected ?string $pollingInterval = '120s';

    /**
     * Cache TTL for performance metrics (2 minutes)
     */
    protected function getCacheTtl(): int
    {
        return 120;
    }

    /**
     * Widget category for organization
     */
    public static function getWidgetCategory(): string
    {
        return 'header';
    }

    /**
     * Widget roles for access control (admin and superuser only)
     */
    public static function getWidgetRoles(): array
    {
        return ['admin', 'superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D17 Queue Management - Laravel Pulse integration';
    }

    /**
     * Get performance stats with caching
     */
    protected function getStats(): array
    {
        return $this->cached(function () {
            $stats = [];

            // Average Response Time (last hour)
            $avgResponseTime = $this->getAverageResponseTime();
            $stats[] = Stat::make('Masa Respons Purata', $this->formatResponseTime($avgResponseTime))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-clock')
                ->color($this->getResponseTimeColor($avgResponseTime))
                ->url(route('pulse').'#slow-requests');

            // Slow Queries Count (last hour)
            $slowQueriesCount = $this->getSlowQueriesCount();
            $stats[] = Stat::make('Query Perlahan', number_format($slowQueriesCount))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($this->getSlowQueriesColor($slowQueriesCount))
                ->url(route('pulse').'#slow-queries');

            // Error Rate (last hour)
            $errorRate = $this->getErrorRate();
            $stats[] = Stat::make('Kadar Ralat', $this->formatErrorRate($errorRate))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($this->getErrorRateColor($errorRate))
                ->url(route('pulse').'#exceptions');

            // Queue Health
            $queueHealth = $this->getQueueHealth();
            $stats[] = Stat::make('Kesihatan Queue', $queueHealth['status'])
                ->description($queueHealth['description'])
                ->descriptionIcon('heroicon-m-queue-list')
                ->color($queueHealth['color'])
                ->url(route('pulse').'#queues');

            return $stats;
        }, 'pulse-overview-stats');
    }

    /**
     * Get average response time from Pulse data
     */
    protected function getAverageResponseTime(): float
    {
        try {
            $entries = Pulse::aggregate('slow_request', 'avg', now()->subHour());

            return $entries->avg('avg') ?? 0.0;
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Get slow queries count from Pulse data
     */
    protected function getSlowQueriesCount(): int
    {
        try {
            return Pulse::aggregate('slow_query', 'count', now()->subHour())->sum('count') ?? 0;
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Get error rate from Pulse data
     */
    protected function getErrorRate(): float
    {
        try {
            $totalRequests = Pulse::aggregate('user_request', 'count', now()->subHour())->sum('count') ?? 0;
            $errorRequests = Pulse::aggregate('exception', 'count', now()->subHour())->sum('count') ?? 0;

            if ($totalRequests === 0) {
                return 0.0;
            }

            return ($errorRequests / $totalRequests) * 100;
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Get queue health status
     */
    protected function getQueueHealth(): array
    {
        try {
            $totalJobs = Pulse::aggregate('queue', 'count', now()->subHour())->sum('count') ?? 0;
            $failedJobs = Pulse::aggregate('slow_job', 'count', now()->subHour())->sum('count') ?? 0;

            if ($totalJobs === 0) {
                return [
                    'status' => 'Tiada Aktiviti',
                    'description' => 'Tiada job dalam 1 jam',
                    'color' => 'gray',
                ];
            }

            $failureRate = ($failedJobs / $totalJobs) * 100;

            if ($failureRate === 0) {
                return [
                    'status' => 'Sihat',
                    'description' => 'Semua job berjaya',
                    'color' => 'success',
                ];
            }

            if ($failureRate < 5) {
                return [
                    'status' => 'Baik',
                    'description' => sprintf('%.1f%% kegagalan', $failureRate),
                    'color' => 'warning',
                ];
            }

            return [
                'status' => 'Bermasalah',
                'description' => sprintf('%.1f%% kegagalan', $failureRate),
                'color' => 'danger',
            ];
        } catch (\Exception) {
            return [
                'status' => 'Tidak Diketahui',
                'description' => 'Ralat mendapatkan data',
                'color' => 'gray',
            ];
        }
    }

    /**
     * Format response time for display
     */
    protected function formatResponseTime(float $time): string
    {
        if ($time < 1000) {
            return number_format($time, 0).'ms';
        }

        return number_format($time / 1000, 2).'s';
    }

    /**
     * Format error rate for display
     */
    protected function formatErrorRate(float $rate): string
    {
        return number_format($rate, 2).'%';
    }

    /**
     * Get color for response time based on thresholds
     */
    protected function getResponseTimeColor(float $time): string
    {
        if ($time < 500) {
            return 'success'; // Green - excellent
        }

        if ($time < 1000) {
            return 'warning'; // Yellow - acceptable
        }

        return 'danger'; // Red - poor
    }

    /**
     * Get color for slow queries based on count
     */
    protected function getSlowQueriesColor(int $count): string
    {
        if ($count === 0) {
            return 'success'; // Green - no slow queries
        }

        if ($count < 10) {
            return 'warning'; // Yellow - few slow queries
        }

        return 'danger'; // Red - many slow queries
    }

    /**
     * Get color for error rate based on percentage
     */
    protected function getErrorRateColor(float $rate): string
    {
        if ($rate < 1) {
            return 'success'; // Green - low error rate
        }

        if ($rate < 5) {
            return 'warning'; // Yellow - moderate error rate
        }

        return 'danger'; // Red - high error rate
    }
}
