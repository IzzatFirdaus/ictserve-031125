<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Widgets\WidgetPerformanceWidget;
use App\Models\User;
use App\Services\PulseWidgetIntegration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Performance Tests
 *
 * Tests the WidgetPerformanceWidget functionality including
 * performance metrics display, caching, and role-based access.
 *
 * @trace Requirements: R17 (Performance Standards), R4 (Widget Performance)
 */
class WidgetPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();
    }

    #[Test]
    public function widget_performance_widget_can_be_instantiated(): void
    {
        $widget = new WidgetPerformanceWidget;

        $this->assertInstanceOf(WidgetPerformanceWidget::class, $widget);
    }

    #[Test]
    public function widget_has_correct_metadata(): void
    {
        $this->assertEquals('content', WidgetPerformanceWidget::getWidgetCategory());
        $this->assertEquals(['admin', 'superuser'], WidgetPerformanceWidget::getWidgetRoles());
        $this->assertStringContainsString('D04 §3.2', WidgetPerformanceWidget::getDocumentationReference());
    }

    #[Test]
    public function widget_uses_cacheable_trait(): void
    {
        $widget = new WidgetPerformanceWidget;

        $this->assertTrue(method_exists($widget, 'cached'));
        $this->assertTrue(method_exists($widget, 'getCacheTtl'));
    }

    #[Test]
    public function widget_has_proper_polling_interval(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected property
        $reflection = new \ReflectionClass($widget);
        $pollingProperty = $reflection->getProperty('pollingInterval');
        $pollingProperty->setAccessible(true);

        $this->assertEquals('120s', $pollingProperty->getValue($widget));
    }

    #[Test]
    public function widget_is_lazy_loaded(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected property
        $reflection = new \ReflectionClass($widget);
        $lazyProperty = $reflection->getProperty('isLazy');
        $lazyProperty->setAccessible(true);

        $this->assertTrue($lazyProperty->getValue($widget));
    }

    #[Test]
    public function widget_has_correct_cache_ttl(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getCacheTtl');
        $method->setAccessible(true);

        $this->assertEquals(120, $method->invoke($widget));
    }

    #[Test]
    public function widget_can_get_stats_with_fallback_data(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);

        $stats = $method->invoke($widget);

        $this->assertIsArray($stats);
        $this->assertCount(4, $stats); // Should have 4 stats
    }

    #[Test]
    public function widget_handles_missing_pulse_data_gracefully(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected methods
        $reflection = new \ReflectionClass($widget);

        $renderTimeMethod = $reflection->getMethod('getAverageWidgetRenderTime');
        $renderTimeMethod->setAccessible(true);

        $cacheHitRateMethod = $reflection->getMethod('getWidgetCacheHitRate');
        $cacheHitRateMethod->setAccessible(true);

        $queryCountMethod = $reflection->getMethod('getAverageWidgetQueryCount');
        $queryCountMethod->setAccessible(true);

        // Should return 0.0 when no data is available
        $this->assertEquals(0.0, $renderTimeMethod->invoke($widget));
        $this->assertEquals(0.0, $cacheHitRateMethod->invoke($widget));
        $this->assertEquals(0.0, $queryCountMethod->invoke($widget));
    }

    #[Test]
    public function widget_calculates_performance_score_correctly(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('calculatePerformanceScore');
        $method->setAccessible(true);

        // Test excellent performance
        $score = $method->invoke($widget, 50.0, 95.0, 2.0);
        $this->assertGreaterThanOrEqual(90, $score);

        // Test poor performance
        $score = $method->invoke($widget, 1000.0, 30.0, 25.0);
        $this->assertLessThan(50, $score);
    }

    #[Test]
    public function widget_formats_render_time_correctly(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('formatRenderTime');
        $method->setAccessible(true);

        $this->assertEquals('<1ms', $method->invoke($widget, 0.5));
        $this->assertEquals('100ms', $method->invoke($widget, 100.0));
        $this->assertEquals('1.50s', $method->invoke($widget, 1500.0));
    }

    #[Test]
    public function widget_provides_correct_color_coding(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected methods
        $reflection = new \ReflectionClass($widget);

        $renderTimeColorMethod = $reflection->getMethod('getRenderTimeColor');
        $renderTimeColorMethod->setAccessible(true);

        $cacheHitRateColorMethod = $reflection->getMethod('getCacheHitRateColor');
        $cacheHitRateColorMethod->setAccessible(true);

        $queryCountColorMethod = $reflection->getMethod('getQueryCountColor');
        $queryCountColorMethod->setAccessible(true);

        // Test render time colors
        $this->assertEquals('success', $renderTimeColorMethod->invoke($widget, 50.0));
        $this->assertEquals('warning', $renderTimeColorMethod->invoke($widget, 200.0));
        $this->assertEquals('danger', $renderTimeColorMethod->invoke($widget, 500.0));

        // Test cache hit rate colors
        $this->assertEquals('success', $cacheHitRateColorMethod->invoke($widget, 90.0));
        $this->assertEquals('warning', $cacheHitRateColorMethod->invoke($widget, 75.0));
        $this->assertEquals('danger', $cacheHitRateColorMethod->invoke($widget, 50.0));

        // Test query count colors
        $this->assertEquals('success', $queryCountColorMethod->invoke($widget, 3.0));
        $this->assertEquals('warning', $queryCountColorMethod->invoke($widget, 10.0));
        $this->assertEquals('danger', $queryCountColorMethod->invoke($widget, 20.0));
    }

    #[Test]
    public function widget_provides_performance_recommendations(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getPerformanceRecommendation');
        $method->setAccessible(true);

        $this->assertEquals('Prestasi cemerlang', $method->invoke($widget, 95));
        $this->assertEquals('Prestasi baik', $method->invoke($widget, 80));
        $this->assertEquals('Perlu penambahbaikan', $method->invoke($widget, 65));
        $this->assertEquals('Perlu optimisasi segera', $method->invoke($widget, 50));
        $this->assertEquals('Prestasi kritikal - tindakan diperlukan', $method->invoke($widget, 30));
    }

    #[Test]
    public function widget_caches_performance_data(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);

        // First call should cache the data
        $stats1 = $method->invoke($widget);

        // Second call should use cached data
        $stats2 = $method->invoke($widget);

        $this->assertEquals($stats1, $stats2);

        // Check that cache key exists (the widget uses cached() method which creates a unique key)
        // We can't predict the exact key, but we can verify caching behavior by checking the stats are identical
        $this->assertIsArray($stats1);
        $this->assertIsArray($stats2);
        $this->assertCount(4, $stats1); // Should have 4 stats
        $this->assertCount(4, $stats2); // Should have 4 stats
    }

    #[Test]
    public function admin_user_can_access_widget(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $widget = new WidgetPerformanceWidget;

        // Widget should be accessible to admin
        $this->assertTrue($widget->canView());
    }

    #[Test]
    public function superuser_can_access_widget(): void
    {
        $superuser = User::factory()->create();
        $superuser->assignRole('superuser');

        $this->actingAs($superuser);

        $widget = new WidgetPerformanceWidget;

        // Widget should be accessible to superuser
        $this->assertTrue($widget->canView());
    }

    #[Test]
    public function staff_user_cannot_access_widget(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->actingAs($staff);

        $widget = new WidgetPerformanceWidget;

        // Widget should not be accessible to staff
        $this->assertFalse($widget->canView());
    }

    #[Test]
    public function widget_integrates_with_pulse_service(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getWidgetCacheHitRate');
        $method->setAccessible(true);

        // Should not throw exception when PulseWidgetIntegration is not available
        $result = $method->invoke($widget);
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0.0, $result);
    }

    #[Test]
    public function widget_handles_cache_data_correctly(): void
    {
        // Set some test data in cache
        Cache::put('widget_render_times', [100.0, 200.0, 150.0], now()->addMinutes(5));
        Cache::put('widget_query_counts', [3.0, 5.0, 4.0], now()->addMinutes(5));

        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected methods
        $reflection = new \ReflectionClass($widget);

        $renderTimeMethod = $reflection->getMethod('getAverageWidgetRenderTime');
        $renderTimeMethod->setAccessible(true);

        $queryCountMethod = $reflection->getMethod('getAverageWidgetQueryCount');
        $queryCountMethod->setAccessible(true);

        // Should calculate averages correctly
        $this->assertEquals(150.0, $renderTimeMethod->invoke($widget));
        $this->assertEquals(4.0, $queryCountMethod->invoke($widget));
    }

    #[Test]
    public function widget_performance_scores_are_calculated_with_proper_weights(): void
    {
        $widget = new WidgetPerformanceWidget;

        // Use reflection to access protected methods
        $reflection = new \ReflectionClass($widget);

        $renderScoreMethod = $reflection->getMethod('getRenderTimeScore');
        $renderScoreMethod->setAccessible(true);

        $cacheScoreMethod = $reflection->getMethod('getCacheScore');
        $cacheScoreMethod->setAccessible(true);

        $queryScoreMethod = $reflection->getMethod('getQueryScore');
        $queryScoreMethod->setAccessible(true);

        // Test individual score calculations
        $this->assertEquals(100, $renderScoreMethod->invoke($widget, 50.0));
        $this->assertEquals(80, $renderScoreMethod->invoke($widget, 75.0));
        $this->assertEquals(60, $renderScoreMethod->invoke($widget, 150.0));

        $this->assertEquals(100, $cacheScoreMethod->invoke($widget, 95.0));
        $this->assertEquals(80, $cacheScoreMethod->invoke($widget, 85.0));
        $this->assertEquals(60, $cacheScoreMethod->invoke($widget, 70.0));

        $this->assertEquals(100, $queryScoreMethod->invoke($widget, 2.0));
        $this->assertEquals(80, $queryScoreMethod->invoke($widget, 5.0));
        $this->assertEquals(60, $queryScoreMethod->invoke($widget, 10.0));
    }
}
