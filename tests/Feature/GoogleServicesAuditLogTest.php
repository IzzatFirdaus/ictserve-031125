<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\GoogleServicesAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleServicesAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sso_success_log(): void
    {
        $user = User::factory()->create();

        $log = GoogleServicesAuditLog::logSsoSuccess([
            'user_id' => $user->id,
            'email' => 'test@motac.gov.my',
            'google_id' => '123456789',
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0',
            'verification_status' => GoogleServicesAuditLog::VERIFICATION_VERIFIED,
        ]);

        $this->assertDatabaseHas('google_services_audit_logs', [
            'id' => $log->id,
            'user_id' => $user->id,
            'email' => 'test@motac.gov.my',
            'service_type' => GoogleServicesAuditLog::SERVICE_SSO,
            'operation_type' => GoogleServicesAuditLog::OPERATION_AUTHENTICATE,
            'success' => true,
        ]);

        $this->assertTrue($log->wasSuccessful());
        $this->assertTrue($log->isSsoOperation());
    }

    public function test_can_create_sso_failure_log(): void
    {
        $log = GoogleServicesAuditLog::logSsoFailure([
            'email' => 'invalid@gmail.com',
            'ip_address' => '192.168.1.1',
            'error_type' => GoogleServicesAuditLog::ERROR_DOMAIN,
            'error_message' => 'Invalid email domain',
        ]);

        $this->assertDatabaseHas('google_services_audit_logs', [
            'id' => $log->id,
            'email' => 'invalid@gmail.com',
            'success' => false,
            'error_type' => GoogleServicesAuditLog::ERROR_DOMAIN,
        ]);

        $this->assertTrue($log->wasFailed());
    }

    public function test_can_create_gmail_success_log(): void
    {
        $user = User::factory()->create();

        $log = GoogleServicesAuditLog::logGmailSuccess([
            'user_id' => $user->id,
            'email' => 'sender@motac.gov.my',
            'operation_type' => GoogleServicesAuditLog::OPERATION_SEND_EMAIL,
            'authentication_method' => GoogleServicesAuditLog::AUTH_OAUTH,
            'metadata' => ['recipient' => 'recipient@example.com', 'subject' => 'Test'],
        ]);

        $this->assertDatabaseHas('google_services_audit_logs', [
            'id' => $log->id,
            'service_type' => GoogleServicesAuditLog::SERVICE_GMAIL,
            'operation_type' => GoogleServicesAuditLog::OPERATION_SEND_EMAIL,
            'success' => true,
        ]);

        $this->assertTrue($log->isGmailOperation());
        $this->assertEquals('recipient@example.com', $log->getMetadata('recipient'));
    }

    public function test_can_create_gmail_failure_log(): void
    {
        $log = GoogleServicesAuditLog::logGmailFailure([
            'email' => 'sender@motac.gov.my',
            'error_type' => GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED,
            'error_message' => 'Daily quota exceeded',
            'authentication_method' => GoogleServicesAuditLog::AUTH_OAUTH,
        ]);

        $this->assertDatabaseHas('google_services_audit_logs', [
            'id' => $log->id,
            'service_type' => GoogleServicesAuditLog::SERVICE_GMAIL,
            'success' => false,
            'error_type' => GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED,
        ]);
    }

    public function test_scope_successful_filters_correctly(): void
    {
        GoogleServicesAuditLog::factory()->count(3)->successful()->create();
        GoogleServicesAuditLog::factory()->count(2)->failed()->create();

        $successfulLogs = GoogleServicesAuditLog::successful()->get();

        $this->assertCount(3, $successfulLogs);
        $this->assertTrue($successfulLogs->every(fn ($log) => $log->success === true));
    }

    public function test_scope_failed_filters_correctly(): void
    {
        GoogleServicesAuditLog::factory()->count(3)->successful()->create();
        GoogleServicesAuditLog::factory()->count(2)->failed()->create();

        $failedLogs = GoogleServicesAuditLog::failed()->get();

        $this->assertCount(2, $failedLogs);
        $this->assertTrue($failedLogs->every(fn ($log) => $log->success === false));
    }

    public function test_scope_sso_filters_correctly(): void
    {
        GoogleServicesAuditLog::factory()->count(3)->sso()->create();
        GoogleServicesAuditLog::factory()->count(2)->gmail()->create();

        $ssoLogs = GoogleServicesAuditLog::sso()->get();

        $this->assertCount(3, $ssoLogs);
        $this->assertTrue($ssoLogs->every(fn ($log) => $log->service_type === GoogleServicesAuditLog::SERVICE_SSO));
    }

    public function test_scope_gmail_filters_correctly(): void
    {
        GoogleServicesAuditLog::factory()->count(3)->sso()->create();
        GoogleServicesAuditLog::factory()->count(2)->gmail()->create();

        $gmailLogs = GoogleServicesAuditLog::gmail()->get();

        $this->assertCount(2, $gmailLogs);
        $this->assertTrue($gmailLogs->every(fn ($log) => $log->service_type === GoogleServicesAuditLog::SERVICE_GMAIL));
    }

    public function test_scope_quota_errors_filters_correctly(): void
    {
        GoogleServicesAuditLog::factory()->quotaExceeded()->create();
        GoogleServicesAuditLog::factory()->rateLimited()->create();
        GoogleServicesAuditLog::factory()->failed(GoogleServicesAuditLog::ERROR_NETWORK)->create();

        $quotaErrors = GoogleServicesAuditLog::quotaErrors()->get();

        $this->assertCount(2, $quotaErrors);
    }

    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $log = GoogleServicesAuditLog::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_get_service_statistics(): void
    {
        GoogleServicesAuditLog::factory()->count(7)->sso()->successful()->create();
        GoogleServicesAuditLog::factory()->count(3)->sso()->failed()->create();

        $stats = GoogleServicesAuditLog::getServiceStatistics(GoogleServicesAuditLog::SERVICE_SSO);

        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(7, $stats['successful']);
        $this->assertEquals(3, $stats['failed']);
        $this->assertEquals(70.0, $stats['success_rate']);
    }

    public function test_get_error_breakdown(): void
    {
        GoogleServicesAuditLog::factory()->count(3)->failed(GoogleServicesAuditLog::ERROR_DOMAIN)->sso()->create();
        GoogleServicesAuditLog::factory()->count(2)->failed(GoogleServicesAuditLog::ERROR_OAUTH)->sso()->create();
        GoogleServicesAuditLog::factory()->count(1)->failed(GoogleServicesAuditLog::ERROR_NETWORK)->sso()->create();

        $breakdown = GoogleServicesAuditLog::getErrorBreakdown(GoogleServicesAuditLog::SERVICE_SSO);

        $this->assertEquals(3, $breakdown[GoogleServicesAuditLog::ERROR_DOMAIN]);
        $this->assertEquals(2, $breakdown[GoogleServicesAuditLog::ERROR_OAUTH]);
        $this->assertEquals(1, $breakdown[GoogleServicesAuditLog::ERROR_NETWORK]);
    }

    public function test_factory_states_work_correctly(): void
    {
        $ssoLog = GoogleServicesAuditLog::factory()->sso()->successful()->verified()->create();
        $this->assertEquals(GoogleServicesAuditLog::SERVICE_SSO, $ssoLog->service_type);
        $this->assertTrue($ssoLog->success);
        $this->assertEquals(GoogleServicesAuditLog::VERIFICATION_VERIFIED, $ssoLog->verification_status);

        $gmailLog = GoogleServicesAuditLog::factory()->gmail()->smtpFallback()->create();
        $this->assertEquals(GoogleServicesAuditLog::SERVICE_GMAIL, $gmailLog->service_type);
        $this->assertEquals(GoogleServicesAuditLog::AUTH_SMTP_FALLBACK, $gmailLog->authentication_method);

        $quotaLog = GoogleServicesAuditLog::factory()->quotaExceeded()->create();
        $this->assertFalse($quotaLog->success);
        $this->assertEquals(GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED, $quotaLog->error_type);
    }
}
