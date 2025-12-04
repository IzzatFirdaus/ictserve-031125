<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Cacheable Widget Trait
 *
 * Provides caching functionality for Filament widgets to optimize
 * dashboard load time. Target: <3s with caching per Requirement 10.5.
 *
 * Features:
 * - Configurable cache TTL per widget
 * - Automatic cache key generation
 * - Cache invalidation on data changes
 * - User-specific caching support
 *
 * @trace Requirements: 10.5 (Filament dashboard <3s with caching)
 *
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
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
}
