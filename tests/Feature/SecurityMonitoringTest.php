<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\SecurityMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Security Monitoring System Tests
 *
 * Tests failed login monitoring, suspicious activity detection,
 * security event logging, and automated security scanning.
 *
 * @see D03-FR-010.1 Security monitoring requirements
 * @see D03-FR-010.2 Security event logging
 */
class SecurityMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private SecurityMonitoringService $securityMonitoring;

    protected function setUp(): void
    {
        parent::setUp();
        $this->securityMonitoring = new SecurityMonitoringService();
        Cache::flush(); // Clear cache before each test
    }

    public function test_failed_login_attempt_logging(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Failed login attempt', \Mockery::type('array'));

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $request->headers->set('User-Agent', 'Mozilla/5.0 Test Browser');

        $this->securityMonitoring->logFailedLogin('test@example.com', $request);

        // Check that failed attempts are tracked
        $this->assertEquals(1, $this->securityMonitoring->getFailedLoginAttempts('192.168.1.100'));
        $this->assertEquals(1, $this->securityMonitoring->getFailedEmailAttempts('test@example.com'));
    }

    public function test_failed_login_threshold_detection(): void
    {
        Log::shouldReceive('warning')->times(5);
        Log::shouldReceive('critical')
            ->times(2) // Both IP and email thresholds will be breached
            ->with('Failed login threshold breached', \Mockery::type('array'));

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Simulate 5 failed attempts to trigger threshold
        for ($i = 0; $i < 5; $i++) {
            $this->securityMonitoring->logFailedLogin('test@example.com', $request);
        }

        $this->assertTrue($this->securityMonitoring->isIpBlocked('192.168.1.100'));
        $this->assertTrue($this->securityMonitoring->isEmailBlocked('test@example.com'));
    }

    public function test_successful_login_clears_failed_attempts(): void
    {
        Log::shouldReceive('warning')->times(3);
        Log::shouldReceive('info')
            ->once()
            ->with('Successful login', \Mockery::type('array'));

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Log some failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->securityMonitoring->logFailedLogin('test@example.com', $request);
        }

        $this->assertEquals(3, $this->securityMonitoring->getFailedEmailAttempts('test@example.com'));

        // Log successful login
        $this->securityMonitoring->logSuccessfulLogin('test@example.com', $request);

        // Failed attempts for email should be cleared
        $this->assertEquals(0, $this->securityMonitoring->getFailedEmailAttempts('test@example.com'));
    }

    public function test_suspicious_activity_logging(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Suspicious activity detected', \Mockery::type('array'));

        $request = Request::create('/admin', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        $this->securityMonitoring->logSuspiciousActivity(
            'Unauthorized admin access attempt',
            ['user_id' => null, 'path' => '/admin'],
            $request
        );

        $this->assertTrue(true); // Test passes if no exceptions thrown
    }

    public function test_suspicious_activity_threshold_detection(): void
    {
        Log::shouldReceive('warning')->times(10);
        Log::shouldReceive('critical')
            ->once()
            ->with('Suspicious activity threshold breached', \Mockery::type('array'));

        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Simulate 10 suspicious activities to trigger threshold
        for ($i = 0; $i < 10; $i++) {
            $this->securityMonitoring->logSuspiciousActivity(
                'Test suspicious activity',
                ['attempt' => $i + 1],
                $request
            );
        }

        $this->assertTrue(true); // Test passes if threshold alert is triggered
    }

    public function test_security_event_logging(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security event', \Mockery::type('array'));

        $this->securityMonitoring->logSecurityEvent('Test security event', [
            'severity' => 'high',
            'component' => 'authentication',
        ]);

        $this->assertTrue(true); // Test passes if no exceptions thrown
    }

    public function test_api_rate_limiting(): void
    {
        // First 60 requests should pass
        for ($i = 0; $i < 60; $i++) {
            $this->assertTrue($this->securityMonitoring->monitorApiRateLimit('test_user'));
        }

        // 61st request should fail
        Log::shouldReceive('warning')
            ->once()
            ->with('Suspicious activity detected', \Mockery::type('array'));

        $this->assertFalse($this->securityMonitoring->monitorApiRateLimit('test_user'));
    }

    public function test_clear_failed_attempts(): void
    {
        Log::shouldReceive('warning')->times(3);

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Log failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->securityMonitoring->logFailedLogin('test@example.com', $request);
        }

        $this->assertEquals(3, $this->securityMonitoring->getFailedLoginAttempts('192.168.1.100'));

        // Clear attempts
        $this->securityMonitoring->clearFailedAttempts('192.168.1.100', 'ip');

        $this->assertEquals(0, $this->securityMonitoring->getFailedLoginAttempts('192.168.1.100'));
    }

    public function test_security_statistics(): void
    {
        $stats = $this->securityMonitoring->getSecurityStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('failed_logins_last_hour', $stats);
        $this->assertArrayHasKey('suspicious_activities_last_hour', $stats);
        $this->assertArrayHasKey('blocked_ips_count', $stats);
        $this->assertArrayHasKey('security_alerts_today', $stats);
        $this->assertArrayHasKey('last_security_scan', $stats);
    }

    public function test_security_scan(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Security scan completed', \Mockery::type('array'));

        $results = $this->securityMonitoring->runSecurityScan();

        $this->assertIsArray($results);
        $this->assertArrayHasKey('timestamp', $results);
        $this->assertArrayHasKey('checks', $results);

        $checks = $results['checks'];
        $this->assertArrayHasKey('failed_login_patterns', $checks);
        $this->assertArrayHasKey('suspicious_user_agents', $checks);
        $this->assertArrayHasKey('unusual_access_patterns', $checks);
        $this->assertArrayHasKey('security_configuration', $checks);

        // Each check should have status and message
        foreach ($checks as $check) {
            $this->assertArrayHasKey('status', $check);
            $this->assertArrayHasKey('message', $check);
        }
    }

    public function test_data_access_logging(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Data access logged', \Mockery::type('array'));

        $this->securityMonitoring->logDataAccess('User', 1, 'read', 123);

        $this->assertTrue(true); // Test passes if no exceptions thrown
    }

    public function test_security_scan_command(): void
    {
        $this->artisan('security:scan')
            ->expectsOutput('Starting security scan...')
            ->expectsOutput('Security scan completed successfully.')
            ->assertExitCode(0);
    }

    public function test_security_scan_with_report(): void
    {
        $this->artisan('security:scan --report')
            ->expectsOutput('Starting security scan...')
            ->expectsOutput('Generating detailed security report...')
            ->expectsOutput('Security scan completed successfully.')
            ->assertExitCode(0);
    }

    public function test_ip_blocking_functionality(): void
    {
        // Initially not blocked
        $this->assertFalse($this->securityMonitoring->isIpBlocked('192.168.1.100'));

        // Simulate failed attempts to trigger blocking
        Log::shouldReceive('warning')->times(5);
        Log::shouldReceive('critical')->times(2); // Both IP and email thresholds

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        for ($i = 0; $i < 5; $i++) {
            $this->securityMonitoring->logFailedLogin('test@example.com', $request);
        }

        // Should now be blocked
        $this->assertTrue($this->securityMonitoring->isIpBlocked('192.168.1.100'));
    }

    public function test_email_blocking_functionality(): void
    {
        // Initially not blocked
        $this->assertFalse($this->securityMonitoring->isEmailBlocked('test@example.com'));

        // Simulate failed attempts to trigger blocking
        Log::shouldReceive('warning')->times(5);
        Log::shouldReceive('critical')->times(2); // Both IP and email thresholds

        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        for ($i = 0; $i < 5; $i++) {
            $this->securityMonitoring->logFailedLogin('test@example.com', $request);
        }

        // Should now be blocked
        $this->assertTrue($this->securityMonitoring->isEmailBlocked('test@example.com'));
    }
}
