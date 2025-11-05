<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Performance Optimization Service
 *
 * Provides centralized performance optimization utilities for ICTServe:
 * - Query optimization and monitoring
 * - Cache management strategies
 * - Image lazy loading helpers
 * - JavaScript bundle optimization
 *
 * @trace D07 System Integration Plan - Performance Optimization
 * @trace D11 Technical Design - Performance Standards
 *
 * @requirements 7.1, 7.2, 15.4
 */
class PerformanceOptimizationService
{
    /**
     * Cache duration constants (in minutes)
     */
    private const CACHE_DASHBOARD_STATS = 5;

    private const CACHE_ASSET_AVAILABILITY = 5;

    private const CACHE_USER_PREFERENCES = 60;

    private const CACHE_SYSTEM_CONFIG = 1440; // 24 hours

    /**
     * Query performance threshold (in milliseconds)
     */
    private const SLOW_QUERY_THRESHOLD = 1000;

    /**
     * Cache data with automatic key generation and TTL
     *
     * @param  string  $key  Cache key
     * @param  callable  $callback  Data retrieval callback
     * @param  int  $minutes  Cache duration in minutes
     * @return mixed Cached or fresh data
     */
    public function cacheData(string $key, callable $callback, int $minutes = self::CACHE_DASHBOARD_STATS): mixed
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Cache dashboard statistics with user-specific key
     *
     * @param  int  $userId  User ID
     * @param  callable  $callback  Statistics retrieval callback
     * @return mixed Dashboard statistics
     */
    public function cacheDashboardStats(int $userId, callable $callback): mixed
    {
        $key = "dashboard_stats_user_{$userId}";

        return $this->cacheData($key, $callback, self::CACHE_DASHBOARD_STATS);
    }

    /**
     * Cache asset availability data
     *
     * @param  int  $assetId  Asset ID
     * @param  callable  $callback  Availability retrieval callback
     * @return mixed Asset availability data
     */
    public function cacheAssetAvailability(int $assetId, callable $callback): mixed
    {
        $key = "asset_availability_{$assetId}";

        return $this->cacheData($key, $callback, self::CACHE_ASSET_AVAILABILITY);
    }

    /**
     * Invalidate cache for specific patterns
     *
     * @param  string  $pattern  Cache key pattern (e.g., 'dashboard_stats_*')
     */
    public function invalidateCache(string $pattern): void
    {
        // For Redis cache driver
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->connection();
            $keys = $redis->keys($pattern);

            if (! empty($keys)) {
                $redis->del($keys);
                Log::info("Invalidated cache keys matching pattern: {$pattern}", ['count' => count($keys)]);
            }
        } else {
            // For other cache drivers, use tags if supported
            try {
                Cache::tags([$pattern])->flush();
            } catch (\Exception $e) {
                Log::warning("Cache invalidation failed for pattern: {$pattern}", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Optimize query with eager loading and select optimization
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Query builder instance
     * @param  array  $selectColumns  Columns to select
     * @param  array  $eagerLoadRelations  Relations to eager load
     * @return \Illuminate\Database\Eloquent\Builder Optimized query
     */
    public function optimizeQuery($query, array $selectColumns = ['*'], array $eagerLoadRelations = [])
    {
        // Apply select optimization
        if ($selectColumns !== ['*']) {
            $query->select($selectColumns);
        }

        // Apply eager loading
        if (! empty($eagerLoadRelations)) {
            $query->with($eagerLoadRelations);
        }

        return $query;
    }

    /**
     * Monitor query performance and log slow queries
     *
     * @param  callable  $callback  Query execution callback
     * @param  string  $queryName  Query identifier for logging
     * @return mixed Query result
     */
    public function monitorQueryPerformance(callable $callback, string $queryName): mixed
    {
        $startTime = microtime(true);

        $result = $callback();

        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        if ($duration > self::SLOW_QUERY_THRESHOLD) {
            Log::warning("Slow query detected: {$queryName}", [
                'duration_ms' => round($duration, 2),
                'threshold_ms' => self::SLOW_QUERY_THRESHOLD,
            ]);
        }

        return $result;
    }

    /**
     * Get image lazy loading attributes for Blade components
     *
     * @param  string  $src  Image source URL
     * @param  string  $alt  Alt text
     * @param  bool  $priority  Whether image is above the fold (no lazy loading)
     * @return array Image attributes
     */
    public function getImageAttributes(string $src, string $alt, bool $priority = false): array
    {
        $attributes = [
            'src' => $src,
            'alt' => $alt,
        ];

        if (! $priority) {
            $attributes['loading'] = 'lazy';
            $attributes['decoding'] = 'async';
        } else {
            $attributes['fetchpriority'] = 'high';
        }

        return $attributes;
    }

    /**
     * Generate responsive image srcset
     *
     * @param  string  $baseUrl  Base image URL
     * @param  array  $sizes  Array of sizes [width => suffix]
     * @return string Srcset attribute value
     */
    public function generateResponsiveImageSrcset(string $baseUrl, array $sizes): string
    {
        $srcset = [];

        foreach ($sizes as $width => $suffix) {
            $url = str_replace('.', $suffix.'.', $baseUrl);
            $srcset[] = "{$url} {$width}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Get Livewire optimization attributes
     *
     * @param  bool  $lazy  Whether to use lazy loading
     * @param  int  $debounce  Debounce delay in milliseconds
     * @return array Livewire wire: attributes
     */
    public function getLivewireOptimizationAttributes(bool $lazy = false, int $debounce = 300): array
    {
        $attributes = [];

        if ($lazy) {
            $attributes['wire:init'] = 'loadData';
        }

        if ($debounce > 0) {
            $attributes['wire:model.live.debounce.'.$debounce.'ms'] = 'search';
        }

        return $attributes;
    }

    /**
     * Preload critical assets
     *
     * @param  array  $assets  Array of assets to preload ['url' => 'type']
     * @return string HTML link preload tags
     */
    public function generatePreloadTags(array $assets): string
    {
        $tags = [];

        foreach ($assets as $url => $type) {
            $as = match ($type) {
                'css' => 'style',
                'js' => 'script',
                'font' => 'font',
                'image' => 'image',
                default => 'fetch',
            };

            $crossorigin = $type === 'font' ? ' crossorigin' : '';
            $tags[] = "<link rel=\"preload\" href=\"{$url}\" as=\"{$as}\"{$crossorigin}>";
        }

        return implode("\n", $tags);
    }

    /**
     * Get performance metrics for monitoring
     *
     * @return array Performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_rate' => $this->getCacheHitRate(),
            'average_query_time' => $this->getAverageQueryTime(),
            'slow_queries_count' => $this->getSlowQueriesCount(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
        ];
    }

    /**
     * Get cache hit rate (placeholder - implement with actual cache metrics)
     *
     * @return float Cache hit rate percentage
     */
    private function getCacheHitRate(): float
    {
        // This would require cache driver-specific implementation
        // For now, return a placeholder value
        return 85.0;
    }

    /**
     * Get average query execution time
     *
     * @return float Average query time in milliseconds
     */
    private function getAverageQueryTime(): float
    {
        // Get query log from last request
        $queries = DB::getQueryLog();

        if (empty($queries)) {
            return 0.0;
        }

        $totalTime = array_sum(array_column($queries, 'time'));

        return $totalTime / count($queries);
    }

    /**
     * Get count of slow queries
     *
     * @return int Number of slow queries
     */
    private function getSlowQueriesCount(): int
    {
        $queries = DB::getQueryLog();

        return count(array_filter($queries, function ($query) {
            return $query['time'] > self::SLOW_QUERY_THRESHOLD;
        }));
    }

    /**
     * Clear all performance-related caches
     */
    public function clearAllCaches(): void
    {
        $patterns = [
            'dashboard_stats_*',
            'asset_availability_*',
            'user_preferences_*',
            'system_config_*',
        ];

        foreach ($patterns as $pattern) {
            $this->invalidateCache($pattern);
        }

        Log::info('All performance caches cleared');
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCaches(): void
    {
        // Warm up system configuration cache
        $this->cacheData('system_config', function () {
            return config('app');
        }, self::CACHE_SYSTEM_CONFIG);

        Log::info('Critical caches warmed up');
    }
}
