<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\ApiTokenService;
use App\Services\SecurityComplianceService;
use App\Services\SecurityMonitoringService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for SecurityComplianceService.
 *
 * Tests security compliance reporting, PDPA compliance, and audit trail metrics.
 *
 * @see D03 §12.1-12.5 - Security Requirements
 * @see Requirements 5.1, 5.2, 5.3, 5.4, 12.1, 12.2, 12.4, 12.5
 */
#[CoversClass(SecurityComplianceService::class)]
class SecurityComplianceServiceTest extends TestCase
{
    private SecurityComplianceService $service;

    /** @var SecurityMonitoringService&MockInterface */
    private MockInterface $securityMonitoringMock;

    /** @var ApiTokenService&MockInterface */
    private MockInterface $apiTokenServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityMonitoringMock = Mockery::mock(SecurityMonitoringService::class);
        $this->apiTokenServiceMock = Mockery::mock(ApiTokenService::class);

        $this->service = new SecurityComplianceService(
            $this->securityMonitoringMock,
            $this->apiTokenServiceMock
        );

        // Clear cache before each test
        Cache::flush();

        // Set up default mock expectations
        $this->securityMonitoringMock
            ->shouldReceive('getFailedLoginsCount')
            ->andReturn(0)
            ->byDefault();

        $this->securityMonitoringMock
            ->shouldReceive('getDashboardStats')
            ->andReturn([
                'suspicious_activities_24h' => 0,
                'blocked_ips' => 0,
                'critical_alerts' => 0,
            ])
            ->byDefault();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test compliance report generation returns expected structure.
     */
    #[Test]
    public function generate_compliance_report_returns_expected_structure(): void
    {
        $report = $this->service->generateComplianceReport(30);

        $this->assertArrayHasKey('report_generated_at', $report);
        $this->assertArrayHasKey('period_days', $report);
        $this->assertArrayHasKey('period_start', $report);
        $this->assertArrayHasKey('period_end', $report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('authentication', $report);
        $this->assertArrayHasKey('api_security', $report);
        $this->assertArrayHasKey('data_access', $report);
        $this->assertArrayHasKey('audit_trail', $report);
        $this->assertArrayHasKey('threat_detection', $report);
        $this->assertArrayHasKey('compliance_score', $report);
        $this->assertArrayHasKey('recommendations', $report);

        $this->assertEquals(30, $report['period_days']);
    }

    /**
     * Test compliance report is cached.
     */
    #[Test]
    public function generate_compliance_report_is_cached(): void
    {
        // First call - should hit the service
        $report1 = $this->service->generateComplianceReport(30);

        // Second call - should use cache
        $report2 = $this->service->generateComplianceReport(30);

        $this->assertEquals($report1, $report2);
    }

    /**
     * Test compliance summary contains expected fields.
     */
    #[Test]
    public function compliance_summary_contains_expected_fields(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $summary = $report['summary'];

        $this->assertArrayHasKey('total_security_events', $summary);
        $this->assertArrayHasKey('critical_incidents', $summary);
        $this->assertArrayHasKey('resolved_incidents', $summary);
        $this->assertArrayHasKey('pending_reviews', $summary);
        $this->assertArrayHasKey('compliance_status', $summary);
    }

    /**
     * Test authentication metrics contains expected fields.
     */
    #[Test]
    public function authentication_metrics_contains_expected_fields(): void
    {
        $this->securityMonitoringMock
            ->shouldReceive('getFailedLoginsCount')
            ->andReturn(10);

        // Clear cache to force new report generation
        Cache::flush();

        $report = $this->service->generateComplianceReport(30);
        $auth = $report['authentication'];

        $this->assertArrayHasKey('total_logins', $auth);
        $this->assertArrayHasKey('failed_logins', $auth);
        $this->assertArrayHasKey('blocked_accounts', $auth);
        $this->assertArrayHasKey('mfa_enabled_users', $auth);
        $this->assertArrayHasKey('sso_logins', $auth);
        $this->assertArrayHasKey('password_resets', $auth);
        $this->assertArrayHasKey('session_timeouts', $auth);

        $this->assertEquals(10, $auth['failed_logins']);
    }

    /**
     * Test API security metrics contains expected fields.
     */
    #[Test]
    public function api_security_metrics_contains_expected_fields(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $api = $report['api_security'];

        $this->assertArrayHasKey('total_api_requests', $api);
        $this->assertArrayHasKey('authenticated_requests', $api);
        $this->assertArrayHasKey('rate_limited_requests', $api);
        $this->assertArrayHasKey('invalid_token_attempts', $api);
        $this->assertArrayHasKey('active_api_tokens', $api);
        $this->assertArrayHasKey('expired_tokens_cleaned', $api);
    }

    /**
     * Test data access metrics for PDPA compliance.
     */
    #[Test]
    public function data_access_metrics_for_pdpa_compliance(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $dataAccess = $report['data_access'];

        $this->assertArrayHasKey('personal_data_access', $dataAccess);
        $this->assertArrayHasKey('data_exports', $dataAccess);
        $this->assertArrayHasKey('data_modifications', $dataAccess);
        $this->assertArrayHasKey('data_deletions', $dataAccess);
        $this->assertArrayHasKey('consent_updates', $dataAccess);
        $this->assertArrayHasKey('data_breach_incidents', $dataAccess);
    }

    /**
     * Test audit trail metrics contains dual audit system data.
     */
    #[Test]
    public function audit_trail_metrics_contains_dual_audit_data(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $audit = $report['audit_trail'];

        $this->assertArrayHasKey('compliance_audit_entries', $audit);
        $this->assertArrayHasKey('operational_log_entries', $audit);
        $this->assertArrayHasKey('total_audit_entries', $audit);
        $this->assertArrayHasKey('audit_integrity_verified', $audit);
        $this->assertArrayHasKey('retention_compliance', $audit);

        // Total should be sum of compliance and operational
        $this->assertEquals(
            $audit['compliance_audit_entries'] + $audit['operational_log_entries'],
            $audit['total_audit_entries']
        );
    }

    /**
     * Test threat detection metrics from security monitoring.
     */
    #[Test]
    public function threat_detection_metrics_from_security_monitoring(): void
    {
        $this->securityMonitoringMock
            ->shouldReceive('getDashboardStats')
            ->andReturn([
                'suspicious_activities_24h' => 5,
                'blocked_ips' => 10,
                'critical_alerts' => 2,
            ]);

        // Clear cache to force new report generation
        Cache::flush();

        $report = $this->service->generateComplianceReport(30);
        $threats = $report['threat_detection'];

        $this->assertArrayHasKey('suspicious_activities', $threats);
        $this->assertArrayHasKey('blocked_ips', $threats);
        $this->assertArrayHasKey('brute_force_attempts', $threats);
        $this->assertArrayHasKey('sql_injection_attempts', $threats);
        $this->assertArrayHasKey('xss_attempts', $threats);
        $this->assertArrayHasKey('critical_alerts', $threats);

        $this->assertEquals(5, $threats['suspicious_activities']);
        $this->assertEquals(10, $threats['blocked_ips']);
        $this->assertEquals(2, $threats['critical_alerts']);
    }

    /**
     * Test compliance score is within valid range.
     */
    #[Test]
    public function compliance_score_is_within_valid_range(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $score = $report['compliance_score'];

        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    /**
     * Test compliance status is valid value.
     */
    #[Test]
    public function compliance_status_is_valid_value(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $status = $report['summary']['compliance_status'];

        $this->assertContains($status, ['compliant', 'partially_compliant', 'non_compliant']);
    }

    /**
     * Test recommendations is array.
     */
    #[Test]
    public function recommendations_is_array(): void
    {
        $report = $this->service->generateComplianceReport(30);
        $recommendations = $report['recommendations'];

        $this->assertIsArray($recommendations);
    }

    /**
     * Test export report returns valid report.
     */
    #[Test]
    public function export_report_returns_valid_report(): void
    {
        $user = User::factory()->create(['name' => 'Test Admin']);
        $this->actingAs($user);

        $report = $this->service->exportReport(30);

        $this->assertArrayHasKey('compliance_score', $report);
        $this->assertArrayHasKey('period_days', $report);
    }

    /**
     * Test report with different period days.
     */
    #[Test]
    public function report_with_different_period_days(): void
    {
        $report7 = $this->service->generateComplianceReport(7);
        Cache::flush();
        $report90 = $this->service->generateComplianceReport(90);

        $this->assertEquals(7, $report7['period_days']);
        $this->assertEquals(90, $report90['period_days']);

        // Period start should be different
        $this->assertNotEquals($report7['period_start'], $report90['period_start']);
    }

    /**
     * Test blocked accounts count from database.
     */
    #[Test]
    public function blocked_accounts_count_from_database(): void
    {
        // Create active and inactive users
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        // Clear cache to force new report generation
        Cache::flush();

        $report = $this->service->generateComplianceReport(30);

        $this->assertEquals(2, $report['authentication']['blocked_accounts']);
    }

    /**
     * Test retention compliance check.
     */
    #[Test]
    public function retention_compliance_check(): void
    {
        $report = $this->service->generateComplianceReport(30);

        // Should be true when no audits exist or all within retention period
        $this->assertTrue($report['audit_trail']['retention_compliance']);
    }

    /**
     * Test audit integrity verification.
     */
    #[Test]
    public function audit_integrity_verification(): void
    {
        $report = $this->service->generateComplianceReport(30);

        // Should be true for basic integrity check
        $this->assertTrue($report['audit_trail']['audit_integrity_verified']);
    }
}
