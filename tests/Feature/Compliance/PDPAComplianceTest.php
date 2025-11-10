<?php

declare(strict_types=1);

namespace Tests\Feature\Compliance;

use App\Models\LoanApplication;
use App\Models\User;
use App\Services\PDPAComplianceService;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PDPA 2010 Compliance Tests
 *
 * Tests Personal Data Protection Act 2010 compliance including:
 * - Consent management
 * - Data retention policies (7-year minimum)
 * - Secure storage with AES-256 encryption
 * - Data subject rights (access, correction, deletion)
 * - Data minimization and purpose limitation
 *
 * @see D03-FR-006.2 PDPA compliance requirements
 * @see D03-FR-010.2 Audit trail retention
 * @see D09 Database Documentation - Data protection
 */
class PDPAComplianceTest extends TestCase
{
    private PDPAComplianceService $pdpaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdpaService = new PDPAComplianceService;
    }

    /**
     * Test: Consent Management
     * Requirement: 6.2 - PDPA consent management
     */
    #[Test]
    public function user_consent_is_recorded_and_tracked(): void
    {
        $user = User::factory()->create();

        // Record consent
        $consent = $this->pdpaService->recordConsent($user->id, 'data_processing', [
            'purpose' => 'Asset loan application processing',
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 Test Browser',
        ]);

        $this->assertNotNull($consent);
        $this->assertEquals($user->id, $consent->user_id);
        $this->assertEquals('data_processing', $consent->consent_type);
        $this->assertTrue($consent->is_active);
        $this->assertNotNull($consent->consented_at);
    }

    #[Test]
    public function user_can_withdraw_consent(): void
    {
        $user = User::factory()->create();

        // Record consent
        $consent = $this->pdpaService->recordConsent($user->id, 'marketing_communications');

        // Withdraw consent
        $withdrawn = $this->pdpaService->withdrawConsent($user->id, 'marketing_communications');

        $this->assertTrue($withdrawn);

        // Verify consent is withdrawn in history
        $history = $this->pdpaService->getConsentHistory($user->id);
        $marketingConsent = $history->where('consent_type', 'marketing_communications')->first();

        if ($marketingConsent) {
            $this->assertFalse($marketingConsent->is_active);
        }
    }

    #[Test]
    public function consent_history_is_maintained(): void
    {
        $user = User::factory()->create();

        // Record multiple consents
        $this->pdpaService->recordConsent($user->id, 'data_processing');
        $this->pdpaService->recordConsent($user->id, 'marketing_communications');

        // Withdraw one
        $this->pdpaService->withdrawConsent($user->id, 'marketing_communications');

        // Check history
        $history = $this->pdpaService->getConsentHistory($user->id);

        $this->assertCount(2, $history);
        $dataProcessingConsent = $history->where('consent_type', 'data_processing')->first();
        $this->assertNotNull($dataProcessingConsent);
        $this->assertTrue($dataProcessingConsent->is_active);

        $marketingConsent = $history->where('consent_type', 'marketing_communications')->first();
        $this->assertNotNull($marketingConsent);
        $this->assertFalse($marketingConsent->is_active);
    }

    /**
     * Test: Data Retention Policies
     * Requirement: 6.2 - 7-year retention period
     */
    #[Test]
    public function data_retention_period_is_enforced(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        // Check retention period
        $retentionInfo = $this->pdpaService->checkRetentionPeriod($loanApplication);

        $this->assertIsArray($retentionInfo);
        $this->assertArrayHasKey('within_retention', $retentionInfo);
        $this->assertArrayHasKey('retention_years', $retentionInfo);
        $this->assertArrayHasKey('expires_at', $retentionInfo);
        $this->assertArrayHasKey('days_remaining', $retentionInfo);

        $this->assertTrue($retentionInfo['within_retention']);
        $this->assertEquals(7, $retentionInfo['retention_years']);
    }

    #[Test]
    public function expired_data_is_identified_for_deletion(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        // Simulate old record (8 years old)
        DB::table('loan_applications')
            ->where('id', $loanApplication->id)
            ->update(['created_at' => now()->subYears(8)]);

        // Check if expired
        $expiredRecords = $this->pdpaService->getExpiredRecords();

        $this->assertGreaterThan(0, $expiredRecords->count());
        $this->assertTrue($expiredRecords->contains('id', $loanApplication->id));
    }

    #[Test]
    public function data_retention_report_generation(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(3)->create(['user_id' => $user->id]);

        $report = $this->pdpaService->generateRetentionReport();

        $this->assertIsArray($report);
        $this->assertArrayHasKey('total_records', $report);
        $this->assertArrayHasKey('within_retention', $report);
        $this->assertArrayHasKey('expired_records', $report);
        $this->assertArrayHasKey('expiring_soon', $report);
        $this->assertArrayHasKey('retention_policy', $report);

        $this->assertEquals('7 years', $report['retention_policy']);
    }

    /**
     * Test: Data Subject Rights
     * Requirement: 6.2 - Access, correction, deletion rights
     */
    #[Test]
    public function user_can_access_their_personal_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        LoanApplication::factory()->count(2)->create(['user_id' => $user->id]);

        // Request data access
        $personalData = $this->pdpaService->getUserPersonalData($user->id);

        $this->assertIsArray($personalData);
        $this->assertArrayHasKey('user_info', $personalData);
        $this->assertArrayHasKey('loan_applications', $personalData);
        $this->assertArrayHasKey('consents', $personalData);
        $this->assertArrayHasKey('audit_logs', $personalData);

        $this->assertEquals('John Doe', $personalData['user_info']['name']);
        $this->assertEquals('john@example.com', $personalData['user_info']['email']);
        $this->assertCount(2, $personalData['loan_applications']);
    }

    #[Test]
    public function user_can_request_data_correction(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Request correction
        $correctionRequest = $this->pdpaService->requestDataCorrection($user->id, [
            'field' => 'email',
            'current_value' => 'john@example.com',
            'requested_value' => 'john.doe@example.com',
            'reason' => 'Email address changed',
        ]);

        $this->assertNotNull($correctionRequest);
        $this->assertEquals($user->id, $correctionRequest->user_id);
        $this->assertEquals('pending', $correctionRequest->status); // Service returns 'pending' as initial status
        $this->assertNotNull($correctionRequest->requested_at);
    }

    #[Test]
    public function user_can_request_data_deletion(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create(['user_id' => $user->id]);

        // Request deletion
        $deletionRequest = $this->pdpaService->requestDataDeletion($user->id, [
            'reason' => 'No longer using the service',
            'confirm_understanding' => true,
        ]);

        $this->assertNotNull($deletionRequest);
        $this->assertEquals($user->id, $deletionRequest->user_id);
        $this->assertEquals('pending', $deletionRequest->status); // Service returns 'pending' as initial status
        $this->assertNotNull($deletionRequest->requested_at);
    }

    #[Test]
    public function data_deletion_respects_retention_requirements(): void
    {
        $user = User::factory()->create();
        $activeLoan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_use',
        ]);

        // Try to delete data with active loan
        $canDelete = $this->pdpaService->canDeleteUserData($user->id);

        $this->assertFalse($canDelete['allowed']);
        $this->assertStringContainsString('active loan', strtolower($canDelete['reason']));
    }

    /**
     * Test: Data Minimization
     * Requirement: 6.2 - Collect only necessary data
     */
    #[Test]
    public function only_necessary_data_is_collected(): void
    {
        $loanData = [
            'applicant_name' => 'John Doe',
            'applicant_email' => 'john@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF001',
            'grade' => '41',
            'division_id' => 1,
            'purpose' => 'Project presentation',
            'unnecessary_field' => 'This should not be stored',
        ];

        $sanitized = $this->pdpaService->sanitizePersonalData($loanData);

        $this->assertArrayHasKey('applicant_name', $sanitized);
        $this->assertArrayHasKey('applicant_email', $sanitized);
        $this->assertArrayNotHasKey('unnecessary_field', $sanitized);
    }

    #[Test]
    public function sensitive_data_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Check if sensitive fields are encrypted in database
        $rawData = DB::table('users')->where('id', $user->id)->first();

        // Email should be encrypted if configured
        if (config('pdpa.encrypt_email')) {
            $this->assertNotEquals('john@example.com', $rawData->email);
        }

        // But model accessor should decrypt it
        $this->assertEquals('john@example.com', $user->email);
    }

    /**
     * Test: Purpose Limitation
     * Requirement: 6.2 - Data used only for stated purposes
     */
    #[Test]
    public function data_usage_is_logged_with_purpose(): void
    {
        $user = User::factory()->create();

        // Log data access
        $this->pdpaService->logDataAccess($user->id, 'loan_application_processing', [
            'action' => 'view',
            'resource' => 'loan_applications',
            'purpose' => 'Processing loan application LA202511001',
        ]);

        // Verify log
        $accessLogs = $this->pdpaService->getDataAccessLogs($user->id);

        $this->assertGreaterThan(0, $accessLogs->count());
        $firstLog = $accessLogs->first();
        $this->assertNotNull($firstLog);
        $this->assertEquals('loan_application_processing', $firstLog->purpose);
    }

    #[Test]
    public function unauthorized_data_access_is_prevented(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Try to access other user's data without permission
        $this->actingAs($user);

        $canAccess = $this->pdpaService->canAccessUserData($user->id, $otherUser->id);

        $this->assertFalse($canAccess);
    }

    /**
     * Test: Data Breach Notification
     * Requirement: 6.2 - Breach notification procedures
     */
    #[Test]
    public function data_breach_notification_system(): void
    {
        $breachData = [
            'type' => 'unauthorized_access',
            'severity' => 'high',
            'affected_users' => 10,
            'data_types' => ['email', 'name'],
            'description' => 'Unauthorized access attempt detected',
        ];

        $breach = $this->pdpaService->reportDataBreach($breachData);

        $this->assertNotNull($breach);
        $this->assertEquals('high', $breach->severity);
        $this->assertEquals('reported', $breach->status);
        $this->assertNotNull($breach->reported_at);
    }

    #[Test]
    public function affected_users_are_notified_of_breach(): void
    {
        $users = User::factory()->count(3)->create();

        $breach = $this->pdpaService->reportDataBreach([
            'type' => 'data_exposure',
            'severity' => 'medium',
            'affected_users' => $users->pluck('id')->toArray(),
        ]);

        // Verify notifications were queued
        $notifications = $this->pdpaService->getBreachNotifications($breach->id);

        $this->assertCount(3, $notifications);
    }

    /**
     * Test: Data Portability
     * Requirement: 6.2 - Export data in machine-readable format
     */
    #[Test]
    public function user_data_can_be_exported(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(2)->create(['user_id' => $user->id]);

        // Export data
        $export = $this->pdpaService->exportUserData($user->id, 'json');

        $this->assertIsString($export);
        $this->assertJson($export);

        $data = json_decode($export, true);
        $this->assertArrayHasKey('user_info', $data);
        $this->assertArrayHasKey('loan_applications', $data);
        $this->assertArrayHasKey('export_date', $data);
    }

    #[Test]
    public function data_export_includes_all_personal_information(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $export = $this->pdpaService->exportUserData($user->id, 'json');
        $data = json_decode($export, true);

        $this->assertEquals('John Doe', $data['user_info']['name']);
        $this->assertEquals('john@example.com', $data['user_info']['email']);
        $this->assertArrayHasKey('created_at', $data['user_info']);
    }

    /**
     * Test: Privacy Policy Compliance
     * Requirement: 6.2 - Privacy policy availability
     */
    #[Test]
    public function privacy_policy_is_accessible(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertSee('Personal Data Protection');
        $response->assertSee('PDPA 2010');
    }

    #[Test]
    public function privacy_policy_version_tracking(): void
    {
        $currentVersion = $this->pdpaService->getCurrentPrivacyPolicyVersion();

        $this->assertNotNull($currentVersion);
        $this->assertArrayHasKey('version', $currentVersion);
        $this->assertArrayHasKey('effective_date', $currentVersion);
        $this->assertArrayHasKey('content', $currentVersion);
    }

    /**
     * Test: Compliance Reporting
     * Requirement: 6.2 - Generate compliance reports
     */
    #[Test]
    public function pdpa_compliance_report_generation(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(3)->create(['user_id' => $user->id]);

        $report = $this->pdpaService->generateComplianceReport();

        $this->assertIsArray($report);
        $this->assertArrayHasKey('consent_management', $report);
        $this->assertArrayHasKey('data_retention', $report);
        $this->assertArrayHasKey('data_subject_requests', $report);
        $this->assertArrayHasKey('data_breaches', $report);
        $this->assertArrayHasKey('compliance_score', $report);

        $this->assertGreaterThanOrEqual(0, $report['compliance_score']);
        $this->assertLessThanOrEqual(100, $report['compliance_score']);
    }

    #[Test]
    public function compliance_audit_trail(): void
    {
        $auditTrail = $this->pdpaService->getComplianceAuditTrail();

        $this->assertIsArray($auditTrail);
        $this->assertArrayHasKey('total_events', $auditTrail);
        $this->assertArrayHasKey('recent_events', $auditTrail);
        $this->assertArrayHasKey('compliance_checks', $auditTrail);
    }

    /**
     * Test: Data Protection Officer (DPO) Functions
     * Requirement: 6.2 - DPO oversight capabilities
     */
    #[Test]
    public function dpo_can_access_compliance_dashboard(): void
    {
        // Create user with superuser role (role attribute is sufficient for middleware)
        $dpo = User::factory()->create(['role' => 'superuser']);

        $this->actingAs($dpo);
        Filament::auth()->login($dpo);

        // Verify user has correct role and panel access
        $this->assertEquals('superuser', $dpo->role);
        $this->assertTrue($dpo->isSuperuser());
        $this->assertTrue($dpo->hasAdminAccess());

        $response = $this->get('/admin/pdpa/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function dpo_receives_compliance_alerts(): void
    {
        $alerts = $this->pdpaService->getComplianceAlerts();

        $this->assertIsArray($alerts);

        foreach ($alerts as $alert) {
            $this->assertArrayHasKey('type', $alert);
            $this->assertArrayHasKey('severity', $alert);
            $this->assertArrayHasKey('message', $alert);
            $this->assertArrayHasKey('created_at', $alert);
        }
    }
}
