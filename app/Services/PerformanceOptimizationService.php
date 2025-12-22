<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Performance Optimization Service v3.6.0
 *
 * Centralized service for Core Web Vitals optimization:
 * - LCP (Largest Contentful Paint): <2.5s
 * - FID (First Input Delay): <100ms
 * - CLS (Cumulative Layout Shift): <0.1
 * - TTFB (Time to First Byte): <600ms
 *
 * @see D12 §9 Performance optimization patterns
 * @see D13 §6 Performance monitoring
 * @see Requirements 13.1, 13.2, 13.3, 13.4
 *
 * @version 3.6.0
 */
class PerformanceOptimizationService
{
    /**
     * Cache TTL constants (in seconds)
     */
    public const CACHE_TTL_DASHBOARD = 300;    // 5 minutes

    public const CACHE_TTL_USER_DATA = 600;    // 10 minutes

    public const CACHE_TTL_STATISTICS = 60;    // 1 minute

    public const CACHE_TTL_WIDGETS = 300;      // 5 minutes

    /**
     * Core Web Vitals targets
     */
    public const TARGET_LCP = 2500;   // 2.5 seconds

    public const TARGET_FID = 100;    // 100ms

    public const TARGET_CLS = 0.1;    // 0.1 ratio

    public const TARGET_TTFB = 600;   // 600ms

    /**
     * Get optimized image attributes for lazy loading
     *
     * @param  string  $src  Image source URL
     * @param  string  $alt  Alt text for accessibility
     * @param  int|null  $width  Image width
     * @param  int|null  $height  Image height
     * @param  bool  $critical  Whether image is above the fold
     * @return array<string, string|int|null>
     */
    public function getImageAttributes(
        string $src,
        string $alt,
        ?int $width = null,
        ?int $height = null,
        bool $critical = false
    ): array {
        $attributes = [
            'src' => $src,
            'alt' => $alt,
            'decoding' => 'async',
        ];

        // Add dimensions to prevent CLS
        if ($width !== null) {
            $attributes['width'] = $width;
        }
        if ($height !== null) {
            $attributes['height'] = $height;
        }

        // Critical images load immediately, others lazy load
        if ($critical) {
            $attributes['fetchpriority'] = 'high';
            $attributes['loading'] = 'eager';
        } else {
            $attributes['loading'] = 'lazy';
            $attributes['fetchpriority'] = 'low';
        }

        return $attributes;
    }

    /**
     * Get WebP image source with JPEG fallback
     *
     * @param  string  $imagePath  Original image path
     * @return array{webp: string, fallback: string}
     */
    public function getOptimizedImageSources(string $imagePath): array
    {
        $directory = dirname($imagePath);
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);

        $webpPath = ($directory === '.' ? '' : $directory.DIRECTORY_SEPARATOR).$filename.'.webp';

        return [
            'webp' => $webpPath,
            'fallback' => $imagePath,
        ];
    }

    /**
     * Cache dashboard statistics with Redis
     *
     * @param  string  $userId  User ID for cache key
     * @param  \Closure(): mixed  $callback  Data generation callback
     * @return mixed Cached or fresh data
     */
    public function cacheDashboardStats(string $userId, \Closure $callback): mixed
    {
        $cacheKey = "dashboard.stats.{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_DASHBOARD, $callback);
    }

    /**
     * Cache user data with longer TTL
     *
     * @param  string  $userId  User ID for cache key
     * @param  \Closure(): mixed  $callback  Data generation callback
     * @return mixed Cached or fresh data
     */
    public function cacheUserData(string $userId, \Closure $callback): mixed
    {
        $cacheKey = "user.data.{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_USER_DATA, $callback);
    }

    /**
     * Invalidate user-related caches
     *
     * @param  string  $userId  User ID
     */
    public function invalidateUserCache(string $userId): void
    {
        Cache::forget("dashboard.stats.{$userId}");
        Cache::forget("user.data.{$userId}");
        Cache::forget("user.notifications.{$userId}");
    }

    /**
     * Get preload link tags for critical resources
     *
     * @return string HTML preload link tags
     */
    public function getCriticalResourcePreloads(): string
    {
        $preloads = [];

        // Preload critical fonts
        $preloads[] = '<link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600|poppins:400,500,600" as="style" crossorigin>';

        // DNS prefetch for external resources
        $preloads[] = '<link rel="dns-prefetch" href="https://fonts.bunny.net">';
        $preloads[] = '<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>';

        return implode("\n", $preloads);
    }

    /**
     * Get inline critical CSS for above-the-fold content
     *
     * @return string Critical CSS
     */
    public function getCriticalCSS(): string
    {
        return <<<'CSS'
        /* Critical CSS for LCP optimization */
        body{font-family:Inter,system-ui,sans-serif;margin:0;padding:0}
        .skeleton-pulse{animation:pulse 2s cubic-bezier(.4,0,.6,1) infinite}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
        [x-cloak]{display:none!important}
        .min-h-screen{min-height:100vh}
        .bg-white{background-color:#fff}
        .dark .bg-white{background-color:#1e293b}
        CSS;
    }

    /**
     * Monitor and log slow queries
     *
     * @param  float  $threshold  Threshold in milliseconds
     */
    public function enableSlowQueryLogging(float $threshold = 500): void
    {
        DB::listen(function ($query) use ($threshold): void {
            if ($query->time > $threshold) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'time_ms' => $query->time,
                    'bindings' => $query->bindings,
                ]);
            }
        });
    }

    /**
     * Get performance metrics for monitoring
     *
     * @return array{
     *   cache_hit_rate: float,
     *   average_query_time: float,
     *   slow_queries_count: int,
     *   memory_usage: int,
     *   peak_memory_usage: int
     * }
     */
    public function getPerformanceMetrics(): array
    {
        /** @var array<int, array<string, mixed>> $queries */
        $queries = DB::getQueryLog();

        $queryTimes = array_map(
            static fn (array $query): float => is_numeric($query['time'] ?? null) ? (float) $query['time'] : 0.0,
            $queries
        );

        $averageQueryTime = count($queryTimes) > 0 ? array_sum($queryTimes) / count($queryTimes) : 0.0;
        $slowQueriesCount = count(array_filter($queryTimes, static fn (float $time): bool => $time > 1000));

        $cacheHits = Cache::get('performance.cache.hits');
        $cacheMisses = Cache::get('performance.cache.misses');

        $cacheHitRate = 0.0;
        if (is_numeric($cacheHits) && is_numeric($cacheMisses)) {
            $total = (float) $cacheHits + (float) $cacheMisses;
            if ($total > 0) {
                $cacheHitRate = ((float) $cacheHits / $total) * 100;
            }
        }

        return [
            'cache_hit_rate' => round($cacheHitRate, 1),
            'average_query_time' => round($averageQueryTime, 2),
            'slow_queries_count' => $slowQueriesCount,
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
        ];
    }

    public function clearAllCaches(): void
    {
        Cache::flush();
    }

    public function warmUpCaches(): void
    {
        Cache::add('performance.cache.hits', 0, self::CACHE_TTL_STATISTICS);
        Cache::add('performance.cache.misses', 0, self::CACHE_TTL_STATISTICS);
    }

    /**
     * Generate skeleton loader HTML
     *
     * @param  string  $type  Type: 'card', 'list', 'table', 'stats'
     * @param  int  $count  Number of skeleton items
     * @return string HTML skeleton loader
     */
    public function getSkeletonLoader(string $type = 'card', int $count = 3): string
    {
        $items = '';

        for ($i = 0; $i < $count; $i++) {
            $items .= match ($type) {
                'card' => $this->getCardSkeleton(),
                'list' => $this->getListSkeleton(),
                'table' => $this->getTableRowSkeleton(),
                'stats' => $this->getStatsSkeleton(),
                default => $this->getCardSkeleton(),
            };
        }

        return sprintf(
            '<div class="space-y-4" role="status" aria-label="Memuatkan...">%s<span class="sr-only">Memuatkan...</span></div>',
            $items
        );
    }

    private function getCardSkeleton(): string
    {
        return <<<'HTML'
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-3"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full mb-2"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
        </div>
        HTML;
    }

    private function getListSkeleton(): string
    {
        return <<<'HTML'
        <div class="animate-pulse flex items-center space-x-4 p-3">
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            <div class="flex-1">
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
            </div>
        </div>
        HTML;
    }

    private function getTableRowSkeleton(): string
    {
        return <<<'HTML'
        <tr class="animate-pulse">
            <td class="p-3"><div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-20"></div></td>
            <td class="p-3"><div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
            <td class="p-3"><div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
            <td class="p-3"><div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
        </tr>
        HTML;
    }

    private function getStatsSkeleton(): string
    {
        return <<<'HTML'
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-6 shadow-card min-h-[140px]">
            <div class="flex items-center justify-between mb-4">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16 mb-2"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
        </div>
        HTML;
    }

    /**
     * Check if Core Web Vitals targets are met
     *
     * @param  array{lcp?: float, fid?: float, cls?: float, ttfb?: float}  $metrics
     * @return array{passed: bool, details: array<string, array{value: float, target: float, passed: bool}>}
     */
    

/**
 * @param array<string, mixed> $metrics
 */
public function checkCoreWebVitals(array $metrics): array
    {
        $details = [];

        if (isset($metrics['lcp'])) {
            $passed = $metrics['lcp'] <= self::TARGET_LCP;
            $details['lcp'] = ['value' => $metrics['lcp'], 'target' => self::TARGET_LCP, 'passed' => $passed];
        }

        if (isset($metrics['fid'])) {
            $passed = $metrics['fid'] <= self::TARGET_FID;
            $details['fid'] = ['value' => $metrics['fid'], 'target' => self::TARGET_FID, 'passed' => $passed];
        }

        if (isset($metrics['cls'])) {
            $passed = $metrics['cls'] <= self::TARGET_CLS;
            $details['cls'] = ['value' => $metrics['cls'], 'target' => self::TARGET_CLS, 'passed' => $passed];
        }

        if (isset($metrics['ttfb'])) {
            $passed = $metrics['ttfb'] <= self::TARGET_TTFB;
            $details['ttfb'] = ['value' => $metrics['ttfb'], 'target' => self::TARGET_TTFB, 'passed' => $passed];
        }

        $allPassed = array_reduce(
            $details,
            static fn (bool $carry, array $detail): bool => $carry && $detail['passed'],
            true
        );

        return ['passed' => $allPassed, 'details' => $details];
    }
}
