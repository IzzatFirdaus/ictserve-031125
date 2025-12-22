<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use App\Services\PulseWidgetIntegration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Widget Performance Monitoring Widget
 *
 * Displays comprehensive widget performance metrics including
 * rendering times, cache hit rates, query counts, and optimization
 * recommendations based on OptimizedLivewireComponent data.
 *
 * Features:
 * - Widget rendering performance tracking
 * - Cache hit rate monitoring
 * - Query count and N+1 detection alerts
 * - Memory usage tracking
 * - Performance optimization recommendations
 * - Integration with Laravel Pulse metrics
 * - Role-based access control (admin and superuser only)
 * - 2-minute cache TTL for performance optimization
 *
 * @trace Requirements: R17 (Performance Standards), R4 (Widget Performance)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D11 §12.1 Performance standards
 *
 * @version 3.6.1
 */
class WidgetPerformanceWidget extends BaseWidget
{
    use CacheableWidget;
    use WidgetMetadata;

    protected static ?int $sort = 12;

    protected static bool $isLazy = true; // Performance monitoring - lazy load

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
        return 'D04 §3.2 Dashboard widgets, D11 §12.1 Performance standards';
    }

    /**
     * Get widget performance stats with caching
     */
    protected function getStats(): array
    {
        return $this->cached(function () {
            $stats = [];

            // Average Widget Render Time
            $avgRenderTime = $this->getAverageWidgetRenderTime();
            $stats[] = Stat::make('Masa Render Widget Purata', $this->formatRenderTime($avgRenderTime))
                ->description('Dalam 1 jam terakhir')
                ->descriptionIcon('heroicon-m-clock')
                ->color($this->getRenderTimeColor($avgRenderTime))
                ->url(route('pulse').'#slow-requests');

            // Widget Cache Hit Rate
            $cacheHitRate = $this->getWidgetCacheHitRate();
            $stats[] = Stat::make('Kadar Cache Hit Widget', number_format($cacheHitRate, 1).'%')
                ->description('Prestasi cache widget')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($this->getCacheHitRateColor($cacheHitRate))
                ->url(route('pulse').'#cache');

            // Widget Query Count
            $avgQueryCount = $this->getAverageWidgetQueryCount();
            $stats[] = Stat::make('Purata Query Per Widget', number_format($avgQueryCount, 1))
                ->description('Kecekapan query')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($this->getQueryCountColor($avgQueryCount))
                ->url(route('pulse').'#slow-queries');

            // Performance Score
            $performanceScore = $this->calculatePerformanceScore($avgRenderTime, $cacheHitRate, $avgQueryCount);
            $stats[] = Stat::make('Skor Prestasi Widget', $performanceScore.'%')
                ->description($this->getPerformanceRecommendation($performanceScore))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($this->getPerformanceScoreColor($performanceScore))
                ->url(route('filament.admin.pages.performance-monitoring'));

            return $stats;
        }, 'widget-performance-stats');
    }

    /**
     * Get average widget render time from cache metrics
     */
    protected function getAverageWidgetRenderTime(): float
    {
        try {
            // Get widget render times from cache (stored by OptimizedLivewireComponent)
            $renderTimes = Cache::get('widget_render_times', []);

            if (empty($renderTimes)) {
                return 0.0;
            }

            return array_sum($renderTimes) / count($renderTimes);
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Get widget cache hit rate
     */
    protected function getWidgetCacheHitRate(): float
    {
        try {
            $pulseIntegration = app(PulseWidgetIntegration::class);
            $cacheMetrics = $pulseIntegration->getCachePerformance(now()->subHour());

            return $cacheMetrics['hit_rate'] ?? 0.0;
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Get average widget query count
     */
    protected function getAverageWidgetQueryCount(): float
    {
        try {
            // Get query counts from cache (stored by OptimizedLivewireComponent)
            $queryCounts = Cache::get('widget_query_counts', []);

            if (empty($queryCounts)) {
                return 0.0;
            }

            return array_sum($queryCounts) / count($queryCounts);
        } catch (\Exception) {
            return 0.0;
        }
    }

    /**
     * Calculate overall performance score
     */
    protected function calculatePerformanceScore(float $renderTime, float $cacheHitRate, float $queryCount): int
    {
        $renderScore = $this->getRenderTimeScore($renderTime);
        $cacheScore = $this->getCacheScore($cacheHitRate);
        $queryScore = $this->getQueryScore($queryCount);

        // Weighted average: render time (40%), cache hit rate (30%), query count (30%)
        $totalScore = ($renderScore * 0.4) + ($cacheScore * 0.3) + ($queryScore * 0.3);

        return (int) round($totalScore);
    }

    /**
     * Get render time score (0-100)
     */
    protected function getRenderTimeScore(float $renderTime): int
    {
        if ($renderTime <= 50) {
            return 100; // Excellent
        }

        if ($renderTime <= 100) {
            return 80; // Good
        }

        if ($renderTime <= 200) {
            return 60; // Fair
        }

        if ($renderTime <= 500) {
            return 40; // Poor
        }

        return 20; // Very poor
    }

    /**
     * Get cache score (0-100)
     */
    protected function getCacheScore(float $hitRate): int
    {
        if ($hitRate >= 95) {
            return 100; // Excellent
        }

        if ($hitRate >= 85) {
            return 80; // Good
        }

        if ($hitRate >= 70) {
            return 60; // Fair
        }

        if ($hitRate >= 50) {
            return 40; // Poor
        }

        return 20; // Very poor
    }

    /**
     * Get query score (0-100)
     */
    protected function getQueryScore(float $queryCount): int
    {
        if ($queryCount <= 2) {
            return 100; // Excellent
        }

        if ($queryCount <= 5) {
            return 80; // Good
        }

        if ($queryCount <= 10) {
            return 60; // Fair
        }

        if ($queryCount <= 20) {
            return 40; // Poor
        }

        return 20; // Very poor (N+1 problem likely)
    }

    /**
     * Get performance recommendation based on score
     */
    protected function getPerformanceRecommendation(int $score): string
    {
        if ($score >= 90) {
            return 'Prestasi cemerlang';
        }

        if ($score >= 75) {
            return 'Prestasi baik';
        }

        if ($score >= 60) {
            return 'Perlu penambahbaikan';
        }

        if ($score >= 40) {
            return 'Perlu optimisasi segera';
        }

        return 'Prestasi kritikal - tindakan diperlukan';
    }

    /**
     * Format render time for display
     */
    protected function formatRenderTime(float $time): string
    {
        if ($time < 1) {
            return '<1ms';
        }

        if ($time < 1000) {
            return number_format($time, 0).'ms';
        }

        return number_format($time / 1000, 2).'s';
    }

    /**
     * Get color for render time based on thresholds
     */
    protected function getRenderTimeColor(float $time): string
    {
        if ($time <= 100) {
            return 'success'; // Green - fast rendering
        }

        if ($time <= 300) {
            return 'warning'; // Yellow - acceptable
        }

        return 'danger'; // Red - slow rendering
    }

    /**
     * Get color for cache hit rate
     */
    protected function getCacheHitRateColor(float $rate): string
    {
        if ($rate >= 85) {
            return 'success'; // Green - excellent cache performance
        }

        if ($rate >= 70) {
            return 'warning'; // Yellow - good cache performance
        }

        return 'danger'; // Red - poor cache performance
    }

    /**
     * Get color for query count
     */
    protected function getQueryCountColor(float $count): string
    {
        if ($count <= 5) {
            return 'success'; // Green - efficient queries
        }

        if ($count <= 15) {
            return 'warning'; // Yellow - moderate queries
        }

        return 'danger'; // Red - too many queries (N+1 problem)
    }

    /**
     * Get color for performance score
     */
    protected function getPerformanceScoreColor(int $score): string
    {
        if ($score >= 80) {
            return 'success'; // Green - excellent performance
        }

        if ($score >= 60) {
            return 'warning'; // Yellow - good performance
        }

        return 'danger'; // Red - poor performance
    }
}
