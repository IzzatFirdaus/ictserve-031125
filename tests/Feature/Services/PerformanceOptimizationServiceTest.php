<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\PerformanceOptimizationService;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerformanceOptimizationServiceTest extends TestCase
{
    #[Test]
    public function get_performance_metrics_includes_expected_keys(): void
    {
        Cache::put('performance.cache.hits', 8);
        Cache::put('performance.cache.misses', 2);

        $service = app(PerformanceOptimizationService::class);
        $metrics = $service->getPerformanceMetrics();

        $this->assertSame(80.0, $metrics['cache_hit_rate']);
        $this->assertIsFloat($metrics['average_query_time']);
        $this->assertIsInt($metrics['slow_queries_count']);
        $this->assertIsInt($metrics['memory_usage']);
        $this->assertIsInt($metrics['peak_memory_usage']);
    }

    #[Test]
    public function warm_up_caches_initializes_counters(): void
    {
        Cache::forget('performance.cache.hits');
        Cache::forget('performance.cache.misses');

        $service = app(PerformanceOptimizationService::class);
        $service->warmUpCaches();

        $this->assertTrue(Cache::has('performance.cache.hits'));
        $this->assertTrue(Cache::has('performance.cache.misses'));
    }

    #[Test]
    public function clear_all_caches_flushes_store(): void
    {
        Cache::put('performance.cache.hits', 1);
        Cache::put('performance.cache.misses', 1);

        $service = app(PerformanceOptimizationService::class);
        $service->clearAllCaches();

        $this->assertFalse(Cache::has('performance.cache.hits'));
        $this->assertFalse(Cache::has('performance.cache.misses'));
    }
}
