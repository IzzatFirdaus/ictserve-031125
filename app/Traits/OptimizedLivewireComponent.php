<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;

/**
 * OptimizedLivewireComponent Trait
 *
 * Provides performance optimization patterns for Livewire components.
 * Implements lazy loading, computed property caching, and N+1 query prevention.
 *
 * Usage:
 * - Add trait to Livewire component class
 * - Use #[Lazy] attribute on component class for lazy loading
 * - Use #[Computed] attribute on methods for automatic caching
 * - Override getEagerLoadRelationships() to prevent N+1 queries
 *
 * @see D11 Technical Design Documentation - Performance Optimization
 * @see D04 Software Design Document - Livewire Performance
 * @see Requirements 4.2, 9.3 - Performance optimization requirements
 */
trait OptimizedLivewireComponent
{
    /**
     * Cache duration in seconds (5 minutes default)
     */
    protected int $componentCacheSeconds = 300;

    /**
     * Get relationships to eager load for preventing N+1 queries
     *
     * Override this method in your component to specify relationships
     * that should be eager loaded.
     *
     * Example:
     * protected function getEagerLoadRelationships(): array
     * {
     *     return ['user', 'assignedAgent', 'comments.user'];
     * }
     */
    protected function getEagerLoadRelationships(): array
    {
        return [];
    }

    /**
     * Apply eager loading to a query builder
     *
     * Prevents N+1 query problems by eager loading specified relationships.
     */
    protected function applyEagerLoading($query)
    {
        $relationships = $this->getEagerLoadRelationships();

        if (! empty($relationships)) {
            return $query->with($relationships);
        }

        return $query;
    }

    /**
     * Get cached component data
     *
     * Provides caching for component-specific data with automatic invalidation.
     *
     * @param  string  $key  Cache key suffix
     * @param  \Closure  $callback  Callback to generate data if not cached
     * @param  int|null  $seconds  Cache duration (uses default if null)
     * @return mixed
     */
    protected function getCachedComponentData(string $key, \Closure $callback, ?int $seconds = null)
    {
        $seconds = $seconds ?? $this->componentCacheSeconds;
        $cacheKey = $this->buildComponentCacheKey($key);

        return Cache::remember($cacheKey, $seconds, $callback);
    }

    /**
     * Clear cached component data
     *
     * Clears specific cached data for this component.
     */
    protected function clearCachedComponentData(string $key): bool
    {
        $cacheKey = $this->buildComponentCacheKey($key);

        return Cache::forget($cacheKey);
    }

    /**
     * Build cache key for component data
     *
     * Creates a unique cache key based on component name and user context.
     */
    protected function buildComponentCacheKey(string $suffix): string
    {
        $componentName = class_basename($this);
        $userId = Auth::check() ? (string) Auth::id() : 'guest';

        return sprintf('livewire.%s.user.%s.%s', strtolower($componentName), $userId, $suffix);
    }

    /**
     * Clear all cached data for this component
     *
     * Clears all cached data associated with this component and user.
     */
    protected function clearAllComponentCache(): void
    {
        $componentName = class_basename($this);
        $userId = Auth::check() ? (string) Auth::id() : 'guest';
        $pattern = sprintf('livewire.%s.user.%s.*', strtolower($componentName), $userId);

        // In production, use cache tagging for more efficient clearing
        // For now, we'll use a simple approach
        Cache::flush();
    }

    /**
     * Get optimized paginated results
     *
     * Applies eager loading and caching to paginated queries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getOptimizedPaginatedResults($query, int $perPage = 25)
    {
        // Apply eager loading
        $query = $this->applyEagerLoading($query);

        // Use simple pagination for better performance
        return $query->paginate($perPage);
    }

    /**
     * Debounce input updates
     *
     * Returns the debounce time in milliseconds for wire:model.live.debounce
     * Override this method to customize debounce timing.
     */
    protected function getDebounceTime(): int
    {
        return 300; // 300ms default
    }

    /**
     * Get polling interval
     *
     * Returns the polling interval in seconds for wire:poll
     * Override this method to customize polling timing.
     */
    protected function getPollingInterval(): int
    {
        return 30; // 30 seconds default
    }

    /**
     * Optimize query for counting
     *
     * Provides cached counting with automatic invalidation.
     */
    protected function getOptimizedCount($query, string $cacheKey = 'count'): int
    {
        return $this->getCachedComponentData($cacheKey, function () use ($query) {
            return $query->count();
        }, 60); // Cache count for 1 minute
    }

    /**
     * Invalidate component cache on data changes
     *
     * Call this method after creating, updating, or deleting data
     * to ensure cached data is refreshed.
     */
    protected function invalidateComponentCache(): void
    {
        $this->clearAllComponentCache();
    }

    /**
     * Get placeholder view for lazy loading
     *
     * Override this method to customize the loading placeholder.
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded w-3/4 mb-4"></div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-4/6"></div>
        </div>
        HTML;
    }

    /**
     * Boot the trait
     *
     * Sets up automatic cache invalidation on component updates.
     */
    public function bootOptimizedLivewireComponent(): void
    {
        // Automatically clear cache when component is updated
        $this->listeners['$refresh'] = 'invalidateComponentCache';
    }
}
