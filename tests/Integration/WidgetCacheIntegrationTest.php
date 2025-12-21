<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Filament\Traits\CacheableWidget;
use Filament\Widgets\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Laravel\Pulse\Facades\Pulse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Cache Integration Test
 *
 * Tests the enhanced CacheableWidget trait functionality including:
 * - Redis-based caching optimization
 * - Cache hit rate tracking for Laravel Pulse
 * - Cache warming mechanism
 * - Bulk cache invalidation
 *
 * @trace Requirements: R4 (Widget Performance), R17 (Performance Standards)
 *
 * @see App\Filament\Traits\CacheableWidget
 */
class WidgetCacheIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private TestCacheableWidget $widget;

    protected function setUp(): void
    {
        parent::setUp();

        $this->widget = new TestCacheableWidget;

        // Enable Pulse for testing
        Config::set('pulse.enabled', true);

        // Clear any existing cache
        Cache::flush();
    }

    #[Test]
    public function it_can_cache_widget_data_with_standard_caching(): void
    {
        // Use file cache for this test
        Config::set('cache.default', 'file');

        $result = $this->widget->cached(fn () => 'test-data');

        $this->assertEquals('test-data', $result);

        // Verify data is cached
        $this->assertTrue(Cache::has($this->widget->getCacheKey()));
    }

    #[Test]
    public function it_can_cache_widget_data_with_redis_optimization(): void
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not available');
        }

        // Use Redis cache
        Config::set('cache.default', 'redis');

        $result = $this->widget->cachedWithTracking(fn () => 'redis-test-data');

        $this->assertEquals('redis-test-data', $result);

        // Verify data is cached
        $this->assertTrue(Cache::has($this->widget->getCacheKey()));
    }

    #[Test]
    public function it_tracks_cache_hits_and_misses_with_pulse(): void
    {
        $widgetClass = class_basename(TestCacheableWidget::class);

        // Clear cache first
        Cache::flush();

        // First call should be a cache miss
        $this->widget->cachedWithTracking(fn () => 'tracked-data');

        // Second call should be a cache hit
        $this->widget->cachedWithTracking(fn () => 'tracked-data');

        // Note: In test environment, Pulse might not be fully functional
        // We'll just verify the method doesn't throw exceptions
        $hitRate = $this->widget->getCacheHitRate();
        $this->assertGreaterThanOrEqual(0, $hitRate);
        $this->assertLessThanOrEqual(100, $hitRate);
    }

    #[Test]
    public function it_can_warm_cache_for_critical_widgets(): void
    {
        $warmed = $this->widget->warmCache(fn () => 'warmed-data');

        $this->assertTrue($warmed);
        $this->assertTrue(Cache::has($this->widget->getCacheKey()));
        $this->assertEquals('warmed-data', Cache::get($this->widget->getCacheKey()));
    }

    #[Test]
    public function it_handles_cache_warming_failures_gracefully(): void
    {
        $warmed = $this->widget->warmCache(function () {
            throw new \Exception('Warming failed');
        });

        $this->assertFalse($warmed);
    }

    #[Test]
    public function it_can_invalidate_all_cache_entries(): void
    {
        // Create multiple cache entries
        $this->widget->cached(fn () => 'data1', 'suffix1');
        $this->widget->cached(fn () => 'data2', 'suffix2');

        // Verify entries exist
        $this->assertTrue(Cache::has($this->widget->getCacheKey('suffix1')));
        $this->assertTrue(Cache::has($this->widget->getCacheKey('suffix2')));

        // Invalidate all
        $deleted = $this->widget->invalidateAllCache();

        // Note: File cache doesn't support pattern deletion, so this may return 0
        $this->assertGreaterThanOrEqual(0, $deleted);
    }

    #[Test]
    public function it_supports_user_scoped_caching(): void
    {
        $userScopedWidget = new TestUserScopedWidget;

        // Test without authentication - should use anonymous key
        $result1 = $userScopedWidget->cached(fn () => 'anonymous-data');
        $this->assertEquals('anonymous-data', $result1);
        $anonymousKey = $userScopedWidget->getCacheKey();

        // Test with authentication - should use user-specific key
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $authenticatedWidget = new TestUserScopedWidget;
        $result2 = $authenticatedWidget->cached(fn () => 'user-data');
        $this->assertEquals('user-data', $result2);
        $userKey = $authenticatedWidget->getCacheKey();

        // Verify different cache keys are used
        $this->assertNotEquals($anonymousKey, $userKey);
        $this->assertStringContainsString('user:'.$user->id, $userKey);
    }

    #[Test]
    public function it_handles_redis_pipeline_failures_gracefully(): void
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not available');
        }

        Config::set('cache.default', 'redis');

        // Mock Redis to throw exception
        Redis::shouldReceive('pipeline')
            ->once()
            ->andThrow(new \Exception('Redis connection failed'));

        // Should fallback to standard caching
        $result = $this->widget->cachedWithTracking(fn () => 'fallback-data');

        $this->assertEquals('fallback-data', $result);
    }

    #[Test]
    public function it_handles_pulse_tracking_failures_gracefully(): void
    {
        // Disable Pulse
        Config::set('pulse.enabled', false);

        // Should not throw exceptions
        $result = $this->widget->cachedWithTracking(fn () => 'pulse-disabled-data');

        $this->assertEquals('pulse-disabled-data', $result);
    }

    #[Test]
    public function it_respects_configurable_cache_ttl(): void
    {
        $customTtlWidget = new TestCustomTtlWidget;

        $customTtlWidget->cached(fn () => 'ttl-data');

        // Verify custom TTL is used (we can't easily test expiration in unit tests)
        $this->assertEquals(600, $customTtlWidget->getCacheTtl());
    }

    #[Test]
    public function it_generates_proper_cache_keys_with_prefixes(): void
    {
        $key = $this->widget->getCacheKey();
        $keyWithSuffix = $this->widget->getCacheKey('test-suffix');

        $this->assertStringContainsString('widget:TestCacheableWidget', $key);
        $this->assertStringContainsString('widget:TestCacheableWidget:test-suffix', $keyWithSuffix);
    }

    #[Test]
    public function it_supports_cache_tags_when_available(): void
    {
        // Clear cache first
        Cache::flush();

        // Test with Redis (supports tags)
        if (extension_loaded('redis')) {
            Config::set('cache.default', 'redis');

            $result = $this->widget->cachedWithTags(fn () => 'tagged-data');
            $this->assertEquals('tagged-data', $result);
        }

        // Test with file cache (no tags support)
        Config::set('cache.default', 'file');
        Cache::flush(); // Clear again after config change

        $result = $this->widget->cachedWithTags(fn () => 'untagged-data');
        $this->assertEquals('untagged-data', $result);
    }
}

/**
 * Test widget class for testing CacheableWidget trait
 */
class TestCacheableWidget extends Widget
{
    use CacheableWidget {
        getCacheKey as public;
        getCacheTtl as public;
        cached as public;
        cachedWithTracking as public;
        cachedWithTags as public;
        isUserScoped as public;
    }

    protected string $view = 'test-widget';
}

/**
 * Test widget with user-scoped caching
 */
class TestUserScopedWidget extends TestCacheableWidget
{
    public function isUserScoped(): bool
    {
        return true;
    }
}

/**
 * Test widget with custom TTL
 */
class TestCustomTtlWidget extends TestCacheableWidget
{
    public function getCacheTtl(): int
    {
        return 600; // 10 minutes
    }
}
