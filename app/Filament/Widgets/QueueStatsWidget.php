<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Laravel\Pulse\Facades\Pulse;

/**
 * Queue Statistics Widget
 *
 * Displays detailed queue performance metrics including job processing
 * times, failure rates, and throughput statistics from Laravel Pulse.
 *
 * Features:
 * - Real-time queue metrics display
 * - Job processing throughput tracking
 * - Failure rate monitoring with alerts
 * - Average processing time calculation
 * - Integration with Laravel Horizon dashboard
 * - Role-based access control (admin and superuser only)
 * - 2-minute cache TTL for performance optimization
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R17 (Performance Standards)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D17 Queue Management - Laravel Horizon integration
 *
 * @version 3.6.1
 */
class QueueStatsWidget extends BaseWidget
{
    use CacheableWidget;
    use WidgetMetadata;

    protected static ?int $sort = 11;

    protected static bool $isLazy = true; // Non-critical - lazy load

    /**
     * 2-minute polling for queue metrics
     */
    protected ?string $pollingInterval = '120s';

    /**
     * Cache TTL for queue metrics (2 minutes)
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
        return 'content';
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
        return 'D04 §3.2 Dashboard widgets, D17 Queue Management - Laravel Horizon integration';
    }

    /**
     * Get queue statistics with caching
     */
    protected function getStats(): array
    {
        return $this->cached(function () {
            $stats = [];

            // Jobs Processed (last hour)
            $jobsProcessed = $this->getJobsProcessed();
            $stats[] = Stat::make('Job Diproses', number_format($jobsProcessed))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url($this->getHorizonUrl());

            // Jobs Failed (last hour)
            $jobsFailed = $this->getJobsFailed();
            $stats[] = Stat::make('Job Gagal', number_format($jobsFailed))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($this->getFailedJobsColor($jobsFailed))
                ->url($this->getHorizonUrl().'/failed');

            // Average Processing Time
            $avgProcessingTime = $this->getAverageProcessingTime();
            $stats[] = Stat::make('Masa Pemprosesan Purata', $this->formatProcessingTime($avgProcessingTime))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-clock')
                ->color($this->getProcessingTimeColor($avgProcessingTime))
                ->url($this->getHorizonUrl().'/monitoring');

            // Queue Throughput (jobs per minute)
            $throughput = $this->getQueueThroughput();
            $stats[] = Stat::make('Throughput', number_format($throughput, 1).' job/min')
                ->description('Purata dalam 1 jam')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($this->getThroughputColor($throughput))
                ->url($this->getHorizonUrl().'/monitoring');

            return $stats;
        }, 'queue-stats');
    }

    /**
     * Get jobs processed count from Pulse data
     */
    protected function getJobsProcessed(): int
    {
        try {
            return Pulse::aggregate('queue', 'count', now()->subHour())->sum('count') ?? 0;
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Get jobs failed count from Pulse data
     */
    protected function getJobsFailed(): int
    {
        try {
            return Pulse::aggregate('slow_job', 'count', now()->subHour())->sum('count') ?? 0;
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Get average processing time from Pulse data
     */
    protected function getAverageProcessingTime(): float
    {
        try {
            return Pulse::aggregate('slow_job', 'avg', now()->subHour())->avg('avg') ?? 0.0;
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Get queue throughput (jobs per minute)
     */
    protected function getQueueThroughput(): float
    {
        try {
            $jobsProcessed = $this->getJobsProcessed();

            return $jobsProcessed / 60; // Convert to jobs per minute
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Format processing time for display
     */
    protected function formatProcessingTime(float $time): string
    {
        if ($time < 1000) {
            return number_format($time, 0).'ms';
        }

        return number_format($time / 1000, 2).'s';
    }

    /**
     * Get color for failed jobs based on count
     */
    protected function getFailedJobsColor(int $count): string
    {
        if ($count === 0) {
            return 'success'; // Green - no failures
        }

        if ($count < 5) {
            return 'warning'; // Yellow - few failures
        }

        return 'danger'; // Red - many failures
    }

    /**
     * Get color for processing time based on thresholds
     */
    protected function getProcessingTimeColor(float $time): string
    {
        if ($time < 1000) {
            return 'success'; // Green - fast processing
        }

        if ($time < 5000) {
            return 'warning'; // Yellow - moderate processing
        }

        return 'danger'; // Red - slow processing
    }

    /**
     * Get color for throughput based on performance
     */
    protected function getThroughputColor(float $throughput): string
    {
        if ($throughput > 10) {
            return 'success'; // Green - high throughput
        }

        if ($throughput > 5) {
            return 'warning'; // Yellow - moderate throughput
        }

        return 'info'; // Blue - low throughput (not necessarily bad)
    }

    /**
     * Get Laravel Horizon URL safely
     */
    protected function getHorizonUrl(): string
    {
        return config('horizon.path', '/admin/horizon');
    }
}
