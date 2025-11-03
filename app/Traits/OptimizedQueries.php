<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * OptimizedQueries Trait
 *
 * Provides query optimization patterns for Eloquent models.
 * Implements caching, eager loading, and N+1 query prevention.
 *
 * @see D11 Technical Design Documentation - Performance Optimization
 * @see D04 Software Design Document - Database Performance
 */
trait OptimizedQueries
{
    /**
     * Cache duration in seconds (5 minutes default)
     */
    protected int $cacheSeconds = 300;

    /**
     * Get relationships to eager load
     */
    protected function getEagerLoadRelationships(): array
    {
        return [];
    }

    /**
     * Scope to eager load common relationships
     */
    public function scopeWithCommonRelations(Builder $query): Builder
    {
        $relationships = $this->getEagerLoadRelationships();

        if (! empty($relationships)) {
            return $query->with($relationships);
        }

        return $query;
    }

    /**
     * Get cached query result
     *
     * @return mixed
     */
    protected function getCachedQuery(string $key, \Closure $callback, ?int $seconds = null)
    {
        $seconds = $seconds ?? $this->cacheSeconds;
        $cacheKey = $this->getCacheKey($key);

        return Cache::remember($cacheKey, $seconds, $callback);
    }

    /**
     * Clear cached query
     */
    protected function clearCachedQuery(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);

        return Cache::forget($cacheKey);
    }

    /**
     * Get cache key for this model
     */
    protected function getCacheKey(string $suffix): string
    {
        $modelClass = class_basename($this);
        $modelId = $this->getKey() ?? 'all';

        return sprintf('%s.%s.%s', strtolower($modelClass), $modelId, $suffix);
    }

    /**
     * Clear all cached queries for this model instance
     */
    public function clearAllCachedQueries(): void
    {
        $modelClass = class_basename($this);
        $modelId = $this->getKey();

        if ($modelId) {
            $pattern = sprintf('%s.%s.*', strtolower($modelClass), $modelId);
            Cache::flush(); // In production, use more specific cache tagging
        }
    }

    /**
     * Scope to optimize pagination queries
     */
    public function scopeOptimizedPagination(Builder $query, int $perPage = 25): Builder
    {
        return $query->select($this->getTable().'.*');
    }

    /**
     * Get optimized count query
     */
    public function scopeOptimizedCount(Builder $query): int
    {
        return $this->getCachedQuery('count', function () use ($query) {
            return $query->count();
        }, 60); // Cache count for 1 minute
    }

    /**
     * Boot the trait
     */
    public static function bootOptimizedQueries(): void
    {
        // Clear cache on model events
        static::saved(function ($model) {
            $model->clearAllCachedQueries();
        });

        static::deleted(function ($model) {
            $model->clearAllCachedQueries();
        });
    }
}
