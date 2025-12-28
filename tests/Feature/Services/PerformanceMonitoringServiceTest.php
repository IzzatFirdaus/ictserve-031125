<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Contracts\PerformanceMonitoringServiceInterface;
use App\Models\User;
use App\Notifications\PerformanceAlertNotification;
use App\Services\PerformanceMonitoringService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for PerformanceMonitoringService
 *
 * Validates Laravel Pulse integration for performance monitoring.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Requirements 36.2, 36.3, 36.4, 36.5, 36.7, 36.8
 */
class PerformanceMonitoringServiceTest extends TestCase
{
    private PerformanceMonitoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PerformanceMonitoringServiceInterface::class);
    }

    #[Test]
    public function it_resolves_from_container(): void
    {
        $service = app(PerformanceMonitoringServiceInterface::class);

        $this->assertInstanceOf(PerformanceMonitoringService::class, $service);
    }

    #[Test]
    public function it_returns_slow_queries_as_collection(): void
    {
        $result = $this->service->getSlowQueries();

        $this->assertInstanceOf(Collection::class, $result);
    }

    #[Test]
    public function it_accepts_custom_threshold_for_slow_queries(): void
    {
        $result = $this->service->getSlowQueries(1000);

        $this->assertInstanceOf(Collection::class, $result);
    }

    #[Test]
    public function it_returns_queue_job_metrics_with_expected_structure(): void
    {
        $result = $this->service->getQueueJobMetrics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_jobs', $result);
        $this->assertArrayHasKey('processed_jobs', $result);
        $this->assertArrayHasKey('failed_jobs', $result);
        $this->assertArrayHasKey('pending_jobs', $result);
        $this->assertArrayHasKey('average_processing_time_ms', $result);
        $this->assertArrayHasKey('failure_rate_percent', $result);
        $this->assertArrayHasKey('jobs_by_queue', $result);
        $this->assertArrayHasKey('slow_jobs', $result);
    }

    #[Test]
    public function it_returns_request_metrics_with_expected_structure(): void
    {
        $result = $this->service->getRequestMetrics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_requests', $result);
        $this->assertArrayHasKey('average_response_time_ms', $result);
        $this->assertArrayHasKey('slow_requests_count', $result);
        $this->assertArrayHasKey('requests_by_user', $result);
        $this->assertArrayHasKey('cache_hit_rate_percent', $result);
        $this->assertArrayHasKey('memory_usage_mb', $result);
    }

    #[Test]
    public function it_returns_server_health_metrics_with_expected_structure(): void
    {
        $result = $this->service->getServerHealthMetrics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('servers', $result);
        $this->assertArrayHasKey('overall_health', $result);
        $this->assertIsArray($result['servers']);
    }

    #[Test]
    public function it_returns_server_metrics_with_required_fields(): void
    {
        $result = $this->service->getServerHealthMetrics();

        foreach ($result['servers'] as $serverName => $metrics) {
            $this->assertArrayHasKey('cpu_percent', $metrics);
            $this->assertArrayHasKey('memory_used_mb', $metrics);
            $this->assertArrayHasKey('memory_total_mb', $metrics);
            $this->assertArrayHasKey('memory_percent', $metrics);
            $this->assertArrayHasKey('disk_used_gb', $metrics);
            $this->assertArrayHasKey('disk_total_gb', $metrics);
            $this->assertArrayHasKey('disk_percent', $metrics);
            $this->assertArrayHasKey('last_seen_at', $metrics);
        }
    }

    #[Test]
    public function it_returns_exceeded_thresholds_as_array(): void
    {
        $result = $this->service->checkPerformanceThresholds();

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_returns_threshold_alerts_with_expected_structure(): void
    {
        $result = $this->service->checkPerformanceThresholds();

        foreach ($result as $alert) {
            $this->assertArrayHasKey('metric', $alert);
            $this->assertArrayHasKey('current_value', $alert);
            $this->assertArrayHasKey('threshold', $alert);
            $this->assertArrayHasKey('severity', $alert);
            $this->assertArrayHasKey('message', $alert);
        }
    }

    #[Test]
    public function it_sends_notification_to_superusers_on_alert(): void
    {
        Notification::fake();

        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->service->triggerPerformanceAlert('response_time_ms', 3000.0, 2000.0);

        Notification::assertSentTo(
            $superuser,
            PerformanceAlertNotification::class,
            function ($notification) {
                return $notification->metric === 'response_time_ms'
                    && $notification->currentValue === 3000.0
                    && $notification->threshold === 2000.0;
            }
        );
    }

    #[Test]
    public function it_prunes_old_pulse_data(): void
    {
        $oldTimestamp = now()->subDays(10)->timestamp;
        $recentTimestamp = now()->subDays(3)->timestamp;

        DB::table('pulse_entries')->insert([
            ['timestamp' => $oldTimestamp, 'type' => 'test', 'key' => 'old_entry', 'value' => 100],
            ['timestamp' => $recentTimestamp, 'type' => 'test', 'key' => 'recent_entry', 'value' => 200],
        ]);

        $prunedCount = $this->service->pruneOldData(7);

        $this->assertGreaterThanOrEqual(1, $prunedCount);
        $this->assertDatabaseHas('pulse_entries', ['key' => 'recent_entry']);
    }

    #[Test]
    public function it_respects_retention_days_parameter(): void
    {
        $timestamp = now()->subDays(5)->timestamp;

        DB::table('pulse_entries')->insert([
            'timestamp' => $timestamp,
            'type' => 'test',
            'key' => 'five_day_old_entry',
            'value' => 100,
        ]);

        $this->service->pruneOldData(7);
        $this->assertDatabaseHas('pulse_entries', ['key' => 'five_day_old_entry']);

        $this->service->pruneOldData(3);
        $this->assertDatabaseMissing('pulse_entries', ['key' => 'five_day_old_entry']);
    }

    #[Test]
    public function it_provides_legacy_system_metrics(): void
    {
        $result = $this->service->getSystemMetrics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('response_time', $result);
        $this->assertArrayHasKey('database_query_time', $result);
        $this->assertArrayHasKey('cache_hit_rate', $result);
        $this->assertArrayHasKey('memory_usage', $result);
        $this->assertArrayHasKey('disk_usage', $result);
    }

    #[Test]
    public function it_provides_legacy_performance_trends(): void
    {
        $result = $this->service->getPerformanceTrends('24h');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('response_times', $result);
        $this->assertArrayHasKey('query_times', $result);
        $this->assertArrayHasKey('cache_rates', $result);
        $this->assertArrayHasKey('memory_usage', $result);
        $this->assertArrayHasKey('error_counts', $result);
    }

    #[Test]
    public function it_provides_legacy_integration_health(): void
    {
        $result = $this->service->getIntegrationHealth();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('database', $result);
        $this->assertArrayHasKey('redis', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('queue', $result);
    }
}
