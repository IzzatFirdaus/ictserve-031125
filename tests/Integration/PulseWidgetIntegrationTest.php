<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Filament\Widgets\PulseOverviewWidget;
use App\Filament\Widgets\QueueStatsWidget;
use App\Services\PulseWidgetIntegration;
use Laravel\Pulse\Facades\Pulse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pulse Widget Integration Tests
 *
 * Tests the integration between Laravel Pulse and Filament widgets
 * including data retrieval, caching, error handling, and performance.
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R18 (Pulse Dashboard Integration)
 */
class PulseWidgetIntegrationTest extends TestCase
{
    protected PulseWidgetIntegration $pulseIntegration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pulseIntegration = app(PulseWidgetIntegration::class);
    }

    #[Test]
    public function it_can_get_performance_metrics(): void
    {
        $metrics = $this->pulseIntegration->getPerformanceMetrics('1 hour');

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('response_time', $metrics);
        $this->assertArrayHasKey('slow_queries', $metrics);
        $this->assertArrayHasKey('error_rate', $metrics);
        $this->assertArrayHasKey('queue_health', $metrics);
        $this->assertArrayHasKey('server_metrics', $metrics);
        $this->assertArrayHasKey('cache_performance', $metrics);
    }

    #[Test]
    public function it_handles_pulse_errors_gracefully(): void
    {
        // Test with invalid period to trigger error handling
        $responseTime = $this->pulseIntegration->getAverageResponseTime(now()->subCentury());

        $this->assertIsFloat($responseTime);
        $this->assertGreaterThanOrEqual(0.0, $responseTime);
    }

    #[Test]
    public function it_can_check_alert_thresholds(): void
    {
        $alerts = $this->pulseIntegration->checkAlertThresholds('1 hour');

        $this->assertIsArray($alerts);
        // Should be empty array when no thresholds are exceeded
        $this->assertEmpty($alerts);
    }

    #[Test]
    public function it_can_get_formatted_metrics(): void
    {
        $formattedMetrics = $this->pulseIntegration->getFormattedMetrics('1 hour');

        $this->assertIsArray($formattedMetrics);
        $this->assertArrayHasKey('response_time', $formattedMetrics);
        $this->assertArrayHasKey('error_rate', $formattedMetrics);
        $this->assertArrayHasKey('slow_queries', $formattedMetrics);
        $this->assertArrayHasKey('queue_health', $formattedMetrics);

        // Check format structure
        foreach ($formattedMetrics as $metric) {
            $this->assertArrayHasKey('value', $metric);
            $this->assertArrayHasKey('color', $metric);
            $this->assertArrayHasKey('raw', $metric);
        }
    }

    #[Test]
    public function it_can_get_slow_queries_details(): void
    {
        $slowQueries = $this->pulseIntegration->getSlowQueriesDetails(now()->subHour(), 5);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $slowQueries);
        // Should be empty collection when no slow queries exist
        $this->assertTrue($slowQueries->isEmpty());
    }

    #[Test]
    public function it_can_update_and_get_thresholds(): void
    {
        $originalThresholds = $this->pulseIntegration->getThresholds();

        $this->assertIsArray($originalThresholds);
        $this->assertArrayHasKey('response_time', $originalThresholds);
        $this->assertArrayHasKey('error_rate', $originalThresholds);

        // Update thresholds
        $newThresholds = [
            'response_time' => [
                'warning' => 600,
                'critical' => 1200,
            ],
        ];

        $this->pulseIntegration->updateThresholds($newThresholds);
        $updatedThresholds = $this->pulseIntegration->getThresholds();

        $this->assertEquals(600, $updatedThresholds['response_time']['warning']);
        $this->assertEquals(1200, $updatedThresholds['response_time']['critical']);
    }

    #[Test]
    public function it_can_clear_cache(): void
    {
        // Get metrics to populate cache
        $this->pulseIntegration->getPerformanceMetrics('1 hour');

        // Clear cache should not throw errors
        $this->pulseIntegration->clearCache();

        $this->assertTrue(true); // If we reach here, clearCache() worked
    }

    #[Test]
    public function pulse_overview_widget_can_render(): void
    {
        $widget = new PulseOverviewWidget;

        $this->assertInstanceOf(PulseOverviewWidget::class, $widget);
        $this->assertEquals('header', $widget::getWidgetCategory());
        $this->assertEquals(['admin', 'superuser'], $widget::getWidgetRoles());
    }

    #[Test]
    public function pulse_overview_widget_has_correct_metadata(): void
    {
        $this->assertEquals('header', PulseOverviewWidget::getWidgetCategory());
        $this->assertEquals(['admin', 'superuser'], PulseOverviewWidget::getWidgetRoles());
        $this->assertStringContainsString('D04 §3.2', PulseOverviewWidget::getDocumentationReference());
    }

    #[Test]
    public function queue_stats_widget_can_render(): void
    {
        $widget = new QueueStatsWidget;

        $this->assertInstanceOf(QueueStatsWidget::class, $widget);
        $this->assertEquals('content', $widget::getWidgetCategory());
        $this->assertEquals(['admin', 'superuser'], $widget::getWidgetRoles());
    }

    #[Test]
    public function queue_stats_widget_has_correct_metadata(): void
    {
        $this->assertEquals('content', QueueStatsWidget::getWidgetCategory());
        $this->assertEquals(['admin', 'superuser'], QueueStatsWidget::getWidgetRoles());
        $this->assertStringContainsString('D04 §3.2', QueueStatsWidget::getDocumentationReference());
    }

    #[Test]
    public function widgets_have_proper_caching_configuration(): void
    {
        $pulseWidget = new PulseOverviewWidget;
        $queueWidget = new QueueStatsWidget;

        // Both widgets should use CacheableWidget trait
        $this->assertTrue(method_exists($pulseWidget, 'cached'));
        $this->assertTrue(method_exists($queueWidget, 'cached'));

        // Both should have getCacheTtl method
        $this->assertTrue(method_exists($pulseWidget, 'getCacheTtl'));
        $this->assertTrue(method_exists($queueWidget, 'getCacheTtl'));
    }

    #[Test]
    public function widgets_have_proper_polling_intervals(): void
    {
        $pulseWidget = new PulseOverviewWidget;
        $queueWidget = new QueueStatsWidget;

        // Check polling intervals are set (using reflection to access protected property)
        $pulseReflection = new \ReflectionClass($pulseWidget);
        $queueReflection = new \ReflectionClass($queueWidget);

        $pulsePollingProperty = $pulseReflection->getProperty('pollingInterval');
        $queuePollingProperty = $queueReflection->getProperty('pollingInterval');

        $pulsePollingProperty->setAccessible(true);
        $queuePollingProperty->setAccessible(true);

        $this->assertEquals('120s', $pulsePollingProperty->getValue($pulseWidget));
        $this->assertEquals('120s', $queuePollingProperty->getValue($queueWidget));
    }

    #[Test]
    public function pulse_integration_service_is_properly_bound(): void
    {
        $service = app(PulseWidgetIntegration::class);

        $this->assertInstanceOf(PulseWidgetIntegration::class, $service);
    }

    #[Test]
    public function pulse_configuration_is_valid(): void
    {
        $pulseConfig = config('pulse');

        $this->assertIsArray($pulseConfig);
        $this->assertArrayHasKey('enabled', $pulseConfig);
        $this->assertArrayHasKey('recorders', $pulseConfig);
        $this->assertArrayHasKey('ai_monitoring', $pulseConfig);
    }

    #[Test]
    public function pulse_recorders_are_configured(): void
    {
        $recorders = config('pulse.recorders');

        $this->assertIsArray($recorders);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\SlowQueries', $recorders);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\SlowRequests', $recorders);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\Queues', $recorders);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\Exceptions', $recorders);

        // Check AI monitoring recorders
        $this->assertArrayHasKey('App\\Pulse\\Recorders\\AIServiceMetrics', $recorders);
        $this->assertArrayHasKey('App\\Pulse\\Recorders\\TicketProcessingRecorder', $recorders);
    }

    #[Test]
    public function ai_monitoring_is_configured(): void
    {
        $aiMonitoring = config('pulse.ai_monitoring');

        $this->assertIsArray($aiMonitoring);
        $this->assertTrue($aiMonitoring['enabled']);
        $this->assertEquals(1, $aiMonitoring['sample_rate']);
        $this->assertEquals(2000, $aiMonitoring['slow_threshold']);
        $this->assertTrue($aiMonitoring['track_cache_performance']);
        $this->assertTrue($aiMonitoring['track_model_usage']);
    }
}
