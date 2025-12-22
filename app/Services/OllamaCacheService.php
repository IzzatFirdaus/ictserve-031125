<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Perkhidmatan Cache Ollama untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menguruskan caching untuk operasi AI Ollama termasuk:
 * - Cache bertag untuk pertanyaan FAQ (TTL 1 jam)
 * - Cache embedding untuk dokumen (TTL 24 jam)
 * - Logik invalidasi cache
 * - Pemanasan cache untuk 50 pertanyaan FAQ teratas
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 Technical Design Documentation v3.6.0
 *
 * @requirements 8.4, 8.5
 */
class OllamaCacheService
{
    public const TAG_FAQ = 'ollama:faq';

    public const TAG_EMBEDDING = 'ollama:embedding';

    public const TAG_DOCUMENT = 'ollama:document';

    public const TAG_COMMON = 'ollama:common';

    private const FAQ_TTL = 3600;

    private const EMBEDDING_TTL = 86400;

    private const COMMON_TTL = 7200;

    private const WARM_UP_COUNT = 50;

    private array $config;

    private array $stats = [
        'hits' => 0,
        'misses' => 0,
        'writes' => 0,
        'invalidations' => 0,
        'warm_ups' => 0,
    ];

    public function __construct()
    {
        $this->config = config('ollama.cache', []);
        $this->loadStatsFromCache();
    }

    

/**
 * @param array<string, mixed> $response
 */
public function cacheFaqResponse(string $query, array $response, ?int $ttl = null): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        try {
            $cacheKey = $this->generateFaqCacheKey($query);
            $ttl = $ttl ?? $this->getFaqTtl();

            Cache::tags([self::TAG_FAQ])->put($cacheKey, $response, $ttl);

            $this->stats['writes']++;
            $this->saveStatsToCache();

            Log::debug('FAQ response cached', [
                'cache_key' => $cacheKey,
                'ttl' => $ttl,
                'query_hash' => md5($query),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cache FAQ response', [
                'error' => $e->getMessage(),
                'query_hash' => md5($query),
            ]);

            return false;
        }
    }

    public function getFaqResponse(string $query): ?array
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        try {
            $cacheKey = $this->generateFaqCacheKey($query);
            $response = Cache::tags([self::TAG_FAQ])->get($cacheKey);

            if ($response !== null) {
                $this->stats['hits']++;
            } else {
                $this->stats['misses']++;
            }

            $this->saveStatsToCache();

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to get FAQ response from cache', [
                'error' => $e->getMessage(),
                'query_hash' => md5($query),
            ]);

            return null;
        }
    }

    

/**
 * @param array<string, mixed> $embedding
 */
public function cacheEmbedding(int $documentId, int $chunkIndex, array $embedding, ?int $ttl = null): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        try {
            $cacheKey = $this->generateEmbeddingCacheKey($documentId, $chunkIndex);
            $ttl = $ttl ?? $this->getEmbeddingTtl();

            Cache::tags([self::TAG_EMBEDDING, self::TAG_DOCUMENT])->put($cacheKey, $embedding, $ttl);

            $this->stats['writes']++;
            $this->saveStatsToCache();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cache embedding', [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
                'chunk_index' => $chunkIndex,
            ]);

            return false;
        }
    }

    public function getEmbedding(int $documentId, int $chunkIndex): ?array
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        try {
            $cacheKey = $this->generateEmbeddingCacheKey($documentId, $chunkIndex);
            $embedding = Cache::tags([self::TAG_EMBEDDING])->get($cacheKey);

            if ($embedding !== null) {
                $this->stats['hits']++;
            } else {
                $this->stats['misses']++;
            }

            $this->saveStatsToCache();

            return $embedding;
        } catch (\Exception $e) {
            Log::error('Failed to get embedding from cache', [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
                'chunk_index' => $chunkIndex,
            ]);

            return null;
        }
    }

    public function invalidateFaqCache(?string $query = null): bool
    {
        try {
            if ($query !== null) {
                $cacheKey = $this->generateFaqCacheKey($query);
                Cache::tags([self::TAG_FAQ])->forget($cacheKey);
            } else {
                Cache::tags([self::TAG_FAQ])->flush();
            }

            $this->stats['invalidations']++;
            $this->saveStatsToCache();

            Log::info('FAQ cache invalidated', [
                'query_specific' => $query !== null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to invalidate FAQ cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function invalidateEmbeddingCache(?int $documentId = null): bool
    {
        try {
            if ($documentId !== null) {
                $pattern = "ollama:embedding:{$documentId}:*";
                $this->deleteByPattern($pattern);
            } else {
                Cache::tags([self::TAG_EMBEDDING])->flush();
            }

            $this->stats['invalidations']++;
            $this->saveStatsToCache();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to invalidate embedding cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function invalidateAllCache(): bool
    {
        try {
            Cache::tags([self::TAG_FAQ])->flush();
            Cache::tags([self::TAG_EMBEDDING])->flush();
            Cache::tags([self::TAG_DOCUMENT])->flush();
            Cache::tags([self::TAG_COMMON])->flush();

            $this->stats['invalidations']++;
            $this->saveStatsToCache();

            Log::info('All Ollama cache invalidated');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to invalidate all cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Pemanasan cache untuk 50 pertanyaan FAQ teratas
     * Warm up cache with top 50 FAQ queries
     *
     * @return array{warmed: int, failed: int, duration_ms: float}
     */
    public function warmUpFaqCache(): array
    {
        $startTime = microtime(true);
        $warmed = 0;
        $failed = 0;

        try {
            $topFaqs = Faq::query()
                ->orderByDesc('match_score')
                ->limit(self::WARM_UP_COUNT)
                ->get();

            foreach ($topFaqs as $faq) {
                $response = [
                    'answer' => $faq->answer,
                    'question' => $faq->question,
                    'tags' => $faq->tags ?? [],
                    'confidence' => $faq->match_score ?? 0.8,
                    'source' => 'faq_database',
                    'cached_at' => now()->toIso8601String(),
                ];

                if ($this->cacheFaqResponse($faq->question, $response)) {
                    $warmed++;
                } else {
                    $failed++;
                }
            }

            $this->stats['warm_ups']++;
            $this->saveStatsToCache();

            Log::info('FAQ cache warm-up completed', [
                'warmed' => $warmed,
                'failed' => $failed,
                'total_faqs' => $topFaqs->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('FAQ cache warm-up failed', ['error' => $e->getMessage()]);
        }

        return [
            'warmed' => $warmed,
            'failed' => $failed,
            'duration_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ];
    }

    /**
     * Dapatkan statistik cache
     * Get cache statistics
     *
     * @return array{hits: int, misses: int, writes: int, invalidations: int, warm_ups: int, hit_rate: float, size_bytes: int}
     */
    public function getStats(): array
    {
        $totalRequests = $this->stats['hits'] + $this->stats['misses'];
        $hitRate = $totalRequests > 0 ? round(($this->stats['hits'] / $totalRequests) * 100, 2) : 0.0;

        return array_merge($this->stats, [
            'hit_rate' => $hitRate,
            'size_bytes' => $this->getCacheSize(),
            'enabled' => $this->isCacheEnabled(),
            'driver' => $this->getCacheDriver(),
        ]);
    }

    /**
     * Reset statistik cache
     * Reset cache statistics
     */
    public function resetStats(): void
    {
        $this->stats = [
            'hits' => 0,
            'misses' => 0,
            'writes' => 0,
            'invalidations' => 0,
            'warm_ups' => 0,
        ];
        $this->saveStatsToCache();

        Log::info('Cache statistics reset');
    }

    /**
     * Semak sama ada cache diaktifkan
     * Check if cache is enabled
     */
    public function isCacheEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? config('ollama.cache.enabled', true));
    }

    /**
     * Dapatkan TTL untuk FAQ cache
     * Get TTL for FAQ cache
     */
    public function getFaqTtl(): int
    {
        return (int) ($this->config['faq_ttl'] ?? config('ollama.cache.faq_ttl', self::FAQ_TTL));
    }

    /**
     * Dapatkan TTL untuk embedding cache
     * Get TTL for embedding cache
     */
    public function getEmbeddingTtl(): int
    {
        return (int) ($this->config['embedding_ttl'] ?? config('ollama.cache.embedding_ttl', self::EMBEDDING_TTL));
    }

    /**
     * Dapatkan driver cache
     * Get cache driver
     */
    public function getCacheDriver(): string
    {
        return (string) ($this->config['driver'] ?? config('ollama.cache.driver', 'redis'));
    }

    /**
     * Jana kunci cache untuk FAQ
     * Generate cache key for FAQ
     */
    protected function generateFaqCacheKey(string $query): string
    {
        $normalizedQuery = mb_strtolower(trim($query));
        $hash = hash('sha256', $normalizedQuery);

        return self::TAG_FAQ.':'.$hash;
    }

    /**
     * Jana kunci cache untuk embedding
     * Generate cache key for embedding
     */
    protected function generateEmbeddingCacheKey(int $documentId, int $chunkIndex): string
    {
        return self::TAG_EMBEDDING.":{$documentId}:{$chunkIndex}";
    }

    /**
     * Padam cache mengikut corak
     * Delete cache by pattern
     */
    protected function deleteByPattern(string $pattern): int
    {
        $deleted = 0;

        try {
            if ($this->getCacheDriver() === 'redis') {
                $prefix = config('database.redis.options.prefix', '');
                $keys = Redis::keys($prefix.$pattern);

                foreach ($keys as $key) {
                    $keyWithoutPrefix = str_replace($prefix, '', $key);
                    if (Cache::forget($keyWithoutPrefix)) {
                        $deleted++;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete cache by pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);
        }

        return $deleted;
    }

    /**
     * Dapatkan saiz cache dalam bytes
     * Get cache size in bytes
     */
    protected function getCacheSize(): int
    {
        try {
            if ($this->getCacheDriver() === 'redis') {
                $info = Redis::info('memory');

                return (int) ($info['used_memory'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::debug('Failed to get cache size', ['error' => $e->getMessage()]);
        }

        return 0;
    }

    /**
     * Muat statistik dari cache
     * Load statistics from cache
     */
    protected function loadStatsFromCache(): void
    {
        try {
            $cachedStats = Cache::get('ollama:cache:stats');
            if (is_array($cachedStats)) {
                $this->stats = array_merge($this->stats, $cachedStats);
            }
        } catch (\Exception $e) {
            Log::debug('Failed to load cache stats', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Simpan statistik ke cache
     * Save statistics to cache
     */
    protected function saveStatsToCache(): void
    {
        try {
            Cache::put('ollama:cache:stats', $this->stats, 86400);
        } catch (\Exception $e) {
            Log::debug('Failed to save cache stats', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Cache common/shared response
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to cache
     * @param  int|null  $ttl  Time to live in seconds
     */
    public function cacheCommon(string $key, mixed $value, ?int $ttl = null): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        try {
            $cacheKey = self::TAG_COMMON.':'.$key;
            $ttl = $ttl ?? self::COMMON_TTL;

            Cache::tags([self::TAG_COMMON])->put($cacheKey, $value, $ttl);

            $this->stats['writes']++;
            $this->saveStatsToCache();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cache common value', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get common/shared cached value
     *
     * @param  string  $key  Cache key
     * @return mixed|null
     */
    public function getCommon(string $key): mixed
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        try {
            $cacheKey = self::TAG_COMMON.':'.$key;
            $value = Cache::tags([self::TAG_COMMON])->get($cacheKey);

            if ($value !== null) {
                $this->stats['hits']++;
            } else {
                $this->stats['misses']++;
            }

            $this->saveStatsToCache();

            return $value;
        } catch (\Exception $e) {
            Log::error('Failed to get common value from cache', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Remember a value in cache (get or set pattern)
     *
     * @param  string  $tag  Cache tag
     * @param  string  $key  Cache key
     * @param  int  $ttl  Time to live
     * @param  callable  $callback  Callback to generate value if not cached
     */
    public function remember(string $tag, string $key, int $ttl, callable $callback): mixed
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        try {
            $cacheKey = $tag.':'.$key;

            return Cache::tags([$tag])->remember($cacheKey, $ttl, function () use ($callback) {
                $this->stats['misses']++;
                $this->stats['writes']++;
                $this->saveStatsToCache();

                return $callback();
            });
        } catch (\Exception $e) {
            Log::error('Cache remember failed', [
                'tag' => $tag,
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return $callback();
        }
    }

    /**
     * Check if a key exists in cache
     *
     * @param  string  $tag  Cache tag
     * @param  string  $key  Cache key
     */
    public function has(string $tag, string $key): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        try {
            $cacheKey = $tag.':'.$key;

            return Cache::tags([$tag])->has($cacheKey);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get cache health status for monitoring
     *
     * @return array{status: string, driver: string, enabled: bool, hit_rate: float, memory_usage: int}
     */
    public function getHealthStatus(): array
    {
        $stats = $this->getStats();

        $status = 'healthy';
        if (! $this->isCacheEnabled()) {
            $status = 'disabled';
        } elseif ($stats['hit_rate'] < 50 && ($stats['hits'] + $stats['misses']) > 100) {
            $status = 'degraded';
        }

        return [
            'status' => $status,
            'driver' => $this->getCacheDriver(),
            'enabled' => $this->isCacheEnabled(),
            'hit_rate' => $stats['hit_rate'],
            'memory_usage' => $stats['size_bytes'],
            'total_operations' => $stats['hits'] + $stats['misses'] + $stats['writes'],
        ];
    }
}
