<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Laravel\Pulse\Facades\Pulse;

/**
 * Cacheable Widget Trait
 *
 * Provides advanced caching functionality for Filament widgets to optimize
 * dashboard load time. Target: <3s with caching per Requirement 10.5.
 *
 * Features:
 * - Configurable cache TTL per widget
 * - Automatic cache key generation
 * - Cache invalidation on data changes
 * - User-specific caching support
 * - Redis-based optimization for production
 * - Cache hit rate tracking for Laravel Pulse integration
 * - Cache warming mechanism for critical widgets
 *
 * @trace Requirements: R4 (Widget Performance), R17 (Performance Standards)
 * @trace Requirements: 10.5 (Filament dashboard <3s with caching)
 *
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
 * @see D18 §4.2 AI monitoring integration
 */
trait CacheableWidget
{
    /**
     * Get the cache TTL in seconds
     * Override in widget class to customize
     */
    protected function getCacheTtl(): int
    {
        return config('performance.cache.widget_ttl', 300); // 5 minutes default
    }

    /**
     * Get the cache key prefix for this widget
     * Override in widget class to customize
     */
    protected function getCacheKeyPrefix(): string
    {
        return 'widget:'.class_basename(static::class);
    }

    /**
     * Generate a unique cache key for this widget instance
     *
     * @param  string|null  $suffix  Additional suffix for the cache key
     */
    protected function getCacheKey(?string $suffix = null): string
    {
        $key = $this->getCacheKeyPrefix();

        // Add user-specific key if widget is user-scoped
        if ($this->isUserScoped() && Auth::check()) {
            $key .= ':user:'.Auth::id();
        }

        if ($suffix) {
            $key .= ':'.$suffix;
        }

        return $key;
    }

    /**
     * Check if widget data should be cached per user
     * Override in widget class to enable user-specific caching
     */
    protected function isUserScoped(): bool
    {
        return false;
    }

    /**
     * Get cached data or execute callback and cache result
     *
     * @param  callable  $callback  Function to execute if cache miss
     * @param  string|null  $suffix  Additional cache key suffix
     */
    protected function cached(callable $callback, ?string $suffix = null): mixed
    {
        $key = $this->getCacheKey($suffix);
        $ttl = $this->getCacheTtl();

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear the widget's cache
     *
     * @param  string|null  $suffix  Additional cache key suffix
     */
    protected function clearCache(?string $suffix = null): void
    {
        Cache::forget($this->getCacheKey($suffix));
    }

    /**
     * Clear all cache entries for this widget type
     */
    protected function clearAllCache(): void
    {
        $prefix = $this->getCacheKeyPrefix();
        // Note: This requires cache driver that supports tags or pattern deletion
        // For Redis: Cache::getRedis()->keys($prefix . '*')
        // For file/database: Manual cleanup needed
        Cache::forget($prefix);
    }

    /**
     * Get cache tags for this widget
     * Useful for grouped cache invalidation
     *
     * @return array<string>
     */
    protected function getCacheTags(): array
    {
        return [
            'widgets',
            'dashboard',
            class_basename(static::class),
        ];
    }

    /**
     * Cache data with tags (requires Redis or Memcached)
     *
     * @param  callable  $callback  Function to execute if cache miss
     * @param  string|null  $suffix  Additional cache key suffix
     */
    protected function cachedWithTags(callable $callback, ?string $suffix = null): mixed
    {
        $key = $this->getCacheKey($suffix);
        $ttl = $this->getCacheTtl();
        $tags = $this->getCacheTags();

        // Check if cache driver supports tags
        if (method_exists(Cache::getStore(), 'tags')) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        // Fallback to regular caching
        return $this->cached($callback, $suffix);
    }

    /**
     * Check if Redis is available and configured
     */
    protected function isRedisAvailable(): bool
    {
        return config('cache.default') === 'redis' && extension_loaded('redis');
    }

    /**
     * Get cached data with Redis optimization and hit rate tracking
     *
     * This method provides enhanced caching with:
     * - Redis pipeline optimization for batch operations
     * - Cache hit rate tracking for Laravel Pulse
     * - Automatic fallback to standard caching
     *
     * @param  callable  $callback  Function to execute if cache miss
     * @param  string|null  $suffix  Additional cache key suffix
     *
     * @trace Requirements: R4 (Widget Performance), R17 (Performance Standards)
     */
    protected function cachedWithTracking(callable $callback, ?string $suffix = null): mixed
    {
        $key = $this->getCacheKey($suffix);
        $ttl = $this->getCacheTtl();
        $widgetClass = class_basename(static::class);

        // Track cache attempt
        $this->trackCacheAttempt($widgetClass);

        // Check if value exists in cache
        if (Cache::has($key)) {
            // Track cache hit
            $this->trackCacheHit($widgetClass);

            return Cache::get($key);
        }

        // Cache miss - execute callback and store result
        $this->trackCacheMiss($widgetClass);

        $value = $callback();

        // Use Redis pipeline for optimized storage if available
        if ($this->isRedisAvailable()) {
            $this->storeInRedisPipeline($key, $value, $ttl);
        } else {
            Cache::put($key, $value, $ttl);
        }

        return $value;
    }

    /**
     * Store value in Redis using pipeline for better performance
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to cache
     * @param  int  $ttl  Time to live in seconds
     */
    protected function storeInRedisPipeline(string $key, mixed $value, int $ttl): void
    {
        try {
            Redis::pipeline(function ($pipe) use ($key, $value, $ttl) {
                $serialized = serialize($value);
                $pipe->setex($key, $ttl, $serialized);

                // Store metadata for cache warming
                $metaKey = $key.':meta';
                $metadata = json_encode([
                    'widget' => class_basename(static::class),
                    'created_at' => now()->timestamp,
                    'ttl' => $ttl,
                ]);
                $pipe->setex($metaKey, $ttl, $metadata);
            });
        } catch (\Exception $e) {
            // Fallback to standard caching on Redis error
            Cache::put($key, $value, $ttl);

            // Log error for monitoring
            logger()->warning('Redis pipeline failed for widget cache', [
                'widget' => class_basename(static::class),
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track cache attempt for Laravel Pulse
     *
     * @param  string  $widgetClass  Widget class name
     */
    protected function trackCacheAttempt(string $widgetClass): void
    {
        if (! config('pulse.enabled', true)) {
            return;
        }

        try {
            Pulse::record(
                type: 'widget_cache_attempt',
                key: $widgetClass,
                value: 1
            )->count();
        } catch (\Exception $e) {
            // Silently fail if Pulse is not available
            logger()->debug('Pulse tracking failed for cache attempt', [
                'widget' => $widgetClass,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track cache hit for Laravel Pulse
     *
     * @param  string  $widgetClass  Widget class name
     */
    protected function trackCacheHit(string $widgetClass): void
    {
        if (! config('pulse.enabled', true)) {
            return;
        }

        try {
            Pulse::record(
                type: 'widget_cache_hit',
                key: $widgetClass,
                value: 1
            )->count();
        } catch (\Exception $e) {
            // Silently fail if Pulse is not available
            logger()->debug('Pulse tracking failed for cache hit', [
                'widget' => $widgetClass,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track cache miss for Laravel Pulse
     *
     * @param  string  $widgetClass  Widget class name
     */
    protected function trackCacheMiss(string $widgetClass): void
    {
        if (! config('pulse.enabled', true)) {
            return;
        }

        try {
            Pulse::record(
                type: 'widget_cache_miss',
                key: $widgetClass,
                value: 1
            )->count();
        } catch (\Exception $e) {
            // Silently fail if Pulse is not available
            logger()->debug('Pulse tracking failed for cache miss', [
                'widget' => $widgetClass,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get cache hit rate for this widget
     *
     * @return float Cache hit rate as percentage (0-100)
     */
    public function getCacheHitRate(): float
    {
        $widgetClass = class_basename(static::class);

        try {
            // Get metrics from Pulse (last hour)
            $attempts = Pulse::values('widget_cache_attempt')
                ->where('key', $widgetClass)
                ->sum('value');

            $hits = Pulse::values('widget_cache_hit')
                ->where('key', $widgetClass)
                ->sum('value');

            if ($attempts === 0) {
                return 0.0;
            }

            return round(($hits / $attempts) * 100, 2);
        } catch (\Exception $e) {
            logger()->debug('Failed to calculate cache hit rate', [
                'widget' => $widgetClass,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Warm cache for critical widgets
     *
     * This method pre-loads widget data into cache to ensure
     * fast initial page loads. Should be called during deployment
     * or scheduled maintenance windows.
     *
     * @param  callable  $callback  Function to execute for cache warming
     * @param  string|null  $suffix  Additional cache key suffix
     * @return bool Success status
     *
     * @trace Requirements: R4 (Widget Performance)
     */
    public function warmCache(callable $callback, ?string $suffix = null): bool
    {
        try {
            $key = $this->getCacheKey($suffix);
            $ttl = $this->getCacheTtl();

            // Execute callback and store result
            $value = $callback();

            // Use Redis pipeline if available
            if ($this->isRedisAvailable()) {
                $this->storeInRedisPipeline($key, $value, $ttl);
            } else {
                Cache::put($key, $value, $ttl);
            }

            logger()->info('Cache warmed successfully', [
                'widget' => class_basename(static::class),
                'key' => $key,
            ]);

            return true;
        } catch (\Exception $e) {
            logger()->error('Cache warming failed', [
                'widget' => class_basename(static::class),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get all cache keys for this widget type
     *
     * Useful for bulk operations like cache warming or invalidation
     *
     * @return array<string>
     */
    protected function getAllCacheKeys(): array
    {
        if (! $this->isRedisAvailable()) {
            return [];
        }

        try {
            $prefix = $this->getCacheKeyPrefix();
            $pattern = config('cache.prefix').$prefix.'*';

            return Redis::keys($pattern);
        } catch (\Exception $e) {
            logger()->warning('Failed to get cache keys', [
                'widget' => class_basename(static::class),
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Invalidate all cache entries for this widget type
     *
     * @return int Number of keys deleted
     */
    public function invalidateAllCache(): int
    {
        $keys = $this->getAllCacheKeys();

        if (empty($keys)) {
            return 0;
        }

        try {
            if ($this->isRedisAvailable()) {
                // Use Redis pipeline for bulk deletion
                $deleted = 0;
                Redis::pipeline(function ($pipe) use ($keys, &$deleted) {
                    foreach ($keys as $key) {
                        $pipe->del($key);
                        $deleted++;
                    }
                });

                logger()->info('Bulk cache invalidation completed', [
                    'widget' => class_basename(static::class),
                    'keys_deleted' => $deleted,
                ]);

                return $deleted;
            }

            // Fallback to individual deletion
            $deleted = 0;
            foreach ($keys as $key) {
                if (Cache::forget($key)) {
                    $deleted++;
                }
            }

            return $deleted;
        } catch (\Exception $e) {
            logger()->error('Bulk cache invalidation failed', [
                'widget' => class_basename(static::class),
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }
}
