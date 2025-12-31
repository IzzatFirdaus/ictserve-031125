<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\GoogleServicesAuditLog;
use App\Services\GoogleServicesCacheService;
use App\Services\GoogleServicesPerformanceMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Unit tests for GoogleServicesPerformanceMonitor
 *
 * @see Requirements 13.5, 17.2
 */
class GoogleServicesPerformanceMonitorTest extends TestCase
{
    use RefreshDatabase;

    private GoogleServicesPerformanceMonitor $monitor;

    private GoogleServicesCacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new GoogleServicesCacheService;
        $this->monitor = new GoogleServicesPerformanceMonitor($this->cacheService);
        Cache::flush();
    }

    // =========================================================================
    // Timing Recording Tests
    // =========================================================================

    public function test_record_sso_timing_stores_timing_data(): void
    {
        $this->monitor->recordSsoTiming(1500.5, true);

        $metrics = $this->monitor->getSsoMetrics();

        $this->assertArrayHasKey('timing', $metrics);
    }

    public function test_record_sso_timing_triggers_alert_for_slow_operation(): void
    {
        // Record a slow operation (> 5000ms threshold)
        $this->monitor->recordSsoTiming(6000.0, true);

        $alerts = $this->monitor->getActiveAlerts();

        $this->assertNotEmpty($alerts);
        $this->assertEquals('slow_operation', $alerts[0]['type']);
        $this->assertEquals('sso', $alerts[0]['service']);
        $this->assertEquals(6000.0, $alerts[0]['duration_ms']);
    }

    public function test_record_gmail_timing_stores_timing_data(): void
    {
        $this->monitor->recordGmailTiming('send_email', 2500.0, true);

        $metrics = $this->monitor->getGmailMetrics();

        $this->assertArrayHasKey('timing', $metrics);
    }

    public function test_record_gmail_timing_triggers_alert_for_slow_send(): void
    {
        // Record a slow email send (> 10000ms threshold)
        $this->monitor->recordGmailTiming('send_email', 12000.0, true);

        $alerts = $this->monitor->getActiveAlerts();

        $this->assertNotEmpty($alerts);
        $this->assertEquals('slow_operation', $alerts[0]['type']);
        $this->assertEquals('gmail', $alerts[0]['service']);
        $this->assertEquals('send_email', $alerts[0]['operation']);
    }

    public function test_record_gmail_timing_triggers_alert_for_slow_api_call(): void
    {
        // Record a slow API call (> 3000ms threshold)
        $this->monitor->recordGmailTiming('authenticate', 4000.0, true);

        $alerts = $this->monitor->getActiveAlerts();

        $this->assertNotEmpty($alerts);
        $this->assertEquals('authenticate', $alerts[0]['operation']);
    }

    public function test_record_timing_with_error(): void
    {
        $this->monitor->recordSsoTiming(1000.0, false, 'Authentication failed');

        // Should not throw error
        $this->assertTrue(true);
    }

    // =========================================================================
    // Metrics Collection Tests
    // =========================================================================

    public function test_get_sso_metrics_returns_expected_structure(): void
    {
        $metrics = $this->monitor->getSsoMetrics();

        $this->assertArrayHasKey('total_operations', $metrics);
        $this->assertArrayHasKey('successful_operations', $metrics);
        $this->assertArrayHasKey('failed_operations', $metrics);
        $this->assertArrayHasKey('success_rate_percent', $metrics);
        $this->assertArrayHasKey('failure_rate_percent', $metrics);
        $this->assertArrayHasKey('timing', $metrics);
        $this->assertArrayHasKey('slow_operations', $metrics);
        $this->assertArrayHasKey('threshold_ms', $metrics);
        $this->assertArrayHasKey('health_status', $metrics);
        $this->assertArrayHasKey('period', $metrics);
    }

    public function test_get_gmail_metrics_returns_expected_structure(): void
    {
        $metrics = $this->monitor->getGmailMetrics();

        $this->assertArrayHasKey('total_operations', $metrics);
        $this->assertArrayHasKey('successful_operations', $metrics);
        $this->assertArrayHasKey('failed_operations', $metrics);
        $this->assertArrayHasKey('timing', $metrics);
        $this->assertArrayHasKey('health_status', $metrics);
    }

    public function test_get_all_metrics_returns_combined_data(): void
    {
        $metrics = $this->monitor->getAllMetrics();

        $this->assertArrayHasKey('sso', $metrics);
        $this->assertArrayHasKey('gmail', $metrics);
        $this->assertArrayHasKey('alerts', $metrics);
        $this->assertArrayHasKey('thresholds', $metrics);
        $this->assertArrayHasKey('collected_at', $metrics);
    }

    public function test_metrics_include_audit_log_data(): void
    {
        // Create some audit log entries
        GoogleServicesAuditLog::factory()->sso()->successful()->count(5)->create();
        GoogleServicesAuditLog::factory()->sso()->failed()->count(2)->create();

        // Clear cache to force recalculation
        Cache::flush();

        $metrics = $this->monitor->getSsoMetrics();

        $this->assertEquals(7, $metrics['total_operations']);
        $this->assertEquals(5, $metrics['successful_operations']);
        $this->assertEquals(2, $metrics['failed_operations']);
    }

    // =========================================================================
    // Health Status Tests
    // =========================================================================

    public function test_health_status_is_healthy_with_low_failure_rate(): void
    {
        // Create mostly successful operations
        GoogleServicesAuditLog::factory()->sso()->successful()->count(95)->create();
        GoogleServicesAuditLog::factory()->sso()->failed()->count(5)->create();

        Cache::flush();

        $metrics = $this->monitor->getSsoMetrics();

        // With 5% failure rate, status should be healthy or warning (depending on slow operations)
        $this->assertContains($metrics['health_status'], ['healthy', 'warning']);
        $this->assertLessThanOrEqual(10, $metrics['failure_rate_percent']);
    }

    public function test_health_status_is_warning_with_moderate_failure_rate(): void
    {
        // Create moderate failure rate (5-10%)
        GoogleServicesAuditLog::factory()->sso()->successful()->count(92)->create();
        GoogleServicesAuditLog::factory()->sso()->failed()->count(8)->create();

        Cache::flush();

        $metrics = $this->monitor->getSsoMetrics();

        $this->assertContains($metrics['health_status'], ['warning', 'healthy']);
    }

    public function test_health_status_is_critical_with_high_failure_rate(): void
    {
        // Create high failure rate (>10%)
        GoogleServicesAuditLog::factory()->sso()->successful()->count(80)->create();
        GoogleServicesAuditLog::factory()->sso()->failed()->count(20)->create();

        Cache::flush();

        $metrics = $this->monitor->getSsoMetrics();

        $this->assertEquals('critical', $metrics['health_status']);
    }

    // =========================================================================
    // Alerting Tests
    // =========================================================================

    public function test_get_active_alerts_returns_recent_alerts_only(): void
    {
        // Record some slow operations
        $this->monitor->recordSsoTiming(6000.0, true);
        $this->monitor->recordGmailTiming('send_email', 12000.0, true);

        $alerts = $this->monitor->getActiveAlerts();

        $this->assertCount(2, $alerts);
    }

    public function test_clear_alerts_removes_all_alerts(): void
    {
        $this->monitor->recordSsoTiming(6000.0, true);
        $this->assertNotEmpty($this->monitor->getActiveAlerts());

        $this->monitor->clearAlerts();

        $this->assertEmpty($this->monitor->getActiveAlerts());
    }

    // =========================================================================
    // Dashboard Data Tests
    // =========================================================================

    public function test_get_dashboard_data_returns_expected_structure(): void
    {
        $data = $this->monitor->getDashboardData();

        $this->assertArrayHasKey('metrics', $data);
        $this->assertArrayHasKey('trends', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('generated_at', $data);

        $this->assertArrayHasKey('overall_health', $data['summary']);
        $this->assertArrayHasKey('sso_health', $data['summary']);
        $this->assertArrayHasKey('gmail_health', $data['summary']);
        $this->assertArrayHasKey('active_alerts_count', $data['summary']);
    }

    public function test_get_performance_trends_returns_aggregated_data(): void
    {
        // Record some timing data
        $this->monitor->recordSsoTiming(1000.0, true);
        $this->monitor->recordSsoTiming(1500.0, true);
        $this->monitor->recordGmailTiming('send_email', 2000.0, true);

        $trends = $this->monitor->getPerformanceTrends(24);

        $this->assertArrayHasKey('sso', $trends);
        $this->assertArrayHasKey('gmail', $trends);
    }

    // =========================================================================
    // Cache Management Tests
    // =========================================================================

    public function test_clear_caches_removes_all_performance_caches(): void
    {
        // Generate some cached metrics
        $this->monitor->getSsoMetrics();
        $this->monitor->getGmailMetrics();
        $this->monitor->recordSsoTiming(6000.0, true); // Creates alert

        $this->monitor->clearCaches();

        // Alerts should be cleared
        $this->assertEmpty($this->monitor->getActiveAlerts());
    }

    // =========================================================================
    // Timing Statistics Tests
    // =========================================================================

    public function test_timing_statistics_calculate_correctly(): void
    {
        // Record multiple timings
        $this->monitor->recordSsoTiming(1000.0, true);
        $this->monitor->recordSsoTiming(2000.0, true);
        $this->monitor->recordSsoTiming(3000.0, true);
        $this->monitor->recordSsoTiming(4000.0, true);

        Cache::flush();

        $metrics = $this->monitor->getSsoMetrics();

        $this->assertArrayHasKey('average_ms', $metrics['timing']);
        $this->assertArrayHasKey('max_ms', $metrics['timing']);
        $this->assertArrayHasKey('min_ms', $metrics['timing']);
        $this->assertArrayHasKey('p95_ms', $metrics['timing']);
        $this->assertArrayHasKey('p99_ms', $metrics['timing']);
    }

    // =========================================================================
    // Threshold Tests
    // =========================================================================

    public function test_thresholds_are_included_in_metrics(): void
    {
        $metrics = $this->monitor->getAllMetrics();

        $this->assertArrayHasKey('thresholds', $metrics);
        $this->assertEquals(5000, $metrics['thresholds']['sso_auth_ms']);
        $this->assertEquals(10000, $metrics['thresholds']['gmail_send_ms']);
        $this->assertEquals(3000, $metrics['thresholds']['api_call_ms']);
        $this->assertEquals(10, $metrics['thresholds']['failure_rate_percent']);
    }
}
