<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Query Optimization Trait v3.6.0
 *
 * Provides database query optimization patterns:
 * - Eager loading to prevent N+1 queries
 * - Query result caching
 * - Index hints for complex queries
 * - Chunked processing for large datasets
 *
 * @see D12 §9 Performance optimization patterns
 * @see Requirements 13.3 - Database query optimization
 *
 * @version 3.6.0
 */
trait QueryOptimization
{
    /**
     * Default cache TTL in seconds
     */
    protected int $queryCacheTtl = 300;

    /**
     * Apply eager loading to prevent N+1 queries
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<int, string>  $relations
     * @return Builder<TModel>
     */
    

/**
 * @param array<string, mixed> $relations
 */
protected function withEagerLoading(Builder $query, array $relations): Builder
    {
        if (! empty($relations)) {
            return $query->with($relations);
        }

        return $query;
    }

    /**
     * Apply select optimization to reduce data transfer
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<int, string>  $columns
     * @return Builder<TModel>
     */
    

/**
 * @param array<string, mixed> $columns
 */
protected function withSelectOptimization(Builder $query, array $columns): Builder
    {
        if (! empty($columns) && $columns !== ['*']) {
            return $query->select($columns);
        }

        return $query;
    }

    /**
     * Get cached query results
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     */
    protected function getCachedResults(Builder $query, string $cacheKey, ?int $ttl = null): mixed
    {
        $ttl ??= $this->queryCacheTtl;

        return Cache::remember($cacheKey, $ttl, fn () => $query->get());
    }

    /**
     * Get cached count result
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     */
    protected function getCachedCount(Builder $query, string $cacheKey, ?int $ttl = null): int
    {
        $ttl ??= $this->queryCacheTtl;

        $count = Cache::remember($cacheKey, $ttl, fn () => $query->count());

        return \is_int($count) ? $count : 0;
    }

    /**
     * Process large dataset in chunks
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     */
    protected function processInChunks(Builder $query, int $chunkSize, callable $callback): void
    {
        $query->chunk($chunkSize, $callback);
    }

    /**
     * Apply index hint for complex queries (MySQL specific)
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function withIndexHint(Builder $query, string $indexName): Builder
    {
        // Only apply for MySQL
        if (DB::getDriverName() === 'mysql') {
            $table = $query->getModel()->getTable();

            return $query->from(DB::raw("`{$table}` USE INDEX (`{$indexName}`)"));
        }

        return $query;
    }

    /**
     * Invalidate query cache
     */
    protected function invalidateQueryCache(string $cacheKey): void
    {
        Cache::forget($cacheKey);
    }

    /**
     * Invalidate multiple query caches by pattern
     *
     * @param  array<int, string>  $cacheKeys
     */
    

/**
 * @param array<string, mixed> $cacheKeys
 */
protected function invalidateMultipleQueryCaches(array $cacheKeys): void
    {
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Build optimized query with all optimizations applied
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<int, string>  $columns
     * @param  array<int, string>  $relations
     * @return Builder<TModel>
     */
    

/**
 * @param array<string, mixed> $relations
 */
protected function buildOptimizedQuery(
        Builder $query,
        array $columns = ['*'],
        array $relations = []
    ): Builder {
        $query = $this->withSelectOptimization($query, $columns);
        $query = $this->withEagerLoading($query, $relations);

        return $query;
    }

    /**
     * Get query execution time in milliseconds
     *
     * @return array{result: mixed, time_ms: float}
     */
    protected function measureQueryTime(callable $queryCallback): array
    {
        $startTime = microtime(true);
        $result = $queryCallback();
        $endTime = microtime(true);

        return [
            'result' => $result,
            'time_ms' => round(($endTime - $startTime) * 1000, 2),
        ];
    }
}
