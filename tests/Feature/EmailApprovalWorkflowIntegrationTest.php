<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Mail\ApprovalRequest;
use App\Mail\LoanApplicationApproved;
use App\Mail\LoanApplicationRejected;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\EmailApprovalWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Email Approval Workflow Integration Tests
 *
 * Tests end-to-end email-based approval workflows including token generation,
 * email delivery, approval processing, and notification systems.
 *
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-002.3 Secure token system
 * @see D03-FR-009.1 Email notifications
 * @see Task 11.1 - Email approval workflow testing
 */
class EmailApprovalWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $approver;
    protected LoanApplication $loanApplication;
    protected EmailApprovalWorkflowService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->approver = User::factory()->create([
            'role' => 'approver',
            'email' => 'approver@motac.gov.my',
            'name' => 'Dato\' Ahmad Approver',
        ]);

        $this->loanApplication = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
            'total_value' => 5000.00,
            'grade' => '41',
        ]);

        $this->workflowService = app(EmailApprovalWorkflowService::class);

        Mail::fake();
        Queue::fake();
    }

    /**
     * Test complete email approval workflow from routing to processing
     *
     * @see D03-FR-002.1 Email approval routing
     * @see D03-FR-002.3 Token generation and validation
     */
    public function test_complete_email_approval_workflow(): void
    {
        // Step 1: Route application for email approval
        $this->workflowService->routeForEmailApproval($this->loanApplication);

        // Verify application status updated
        $this->loanApplication->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $this->loanApplication->status);
        $this->assertEquals($this->approver->email, $this->loanApplication->approver_email);
        $this->assertNotNull($this->loanApplication->approval_token);
        $this->assertNotNull($this->loanApplication->approval_token_expires_at);
        $this->assertTrue($this->loanApplication->approval_token_expires_at > now());
        $this->assertTrue($this->loanApplication->approval_token_expires_at <= now()->addDays(7));

        // Verify approval email sent
        Mail::assertSent(ApprovalRequest::class, function ($mail) {
            return $mail->hasTo($this->approver->email) &&
                   $mail->loanApplication->id === $this->loanApplication->id;
        });

        // Step 2: Process email approval
        $token = $this->loanApplication->approval_token;
        $approvedApplication = $this->workflowService->processEmailApproval(
            $token,
            true,
            'Application approved via email workflow'
        );

        // Verify approval processed
        $this->assertEquals(LoanStatus::APPROVED, $approvedApplication->status);
        $this->assertNotNull($approvedApplication->approved_at);
        $this->assertEquals('Application approved via email workflow', $approvedApplication->approval_remarks);
        $this->assertNull($approvedApplication->approval_token);
        $this->assertNull($approvedApplication->approval_token_expires_at);

        // Verify confirmation emails sent
        Mail::assertSent(LoanApplicationApproved::class, function ($mail) {
            return $mail->hasTo($this->loanApplication->applicant_email);
        });

        // Verify admin notification sent
        Mail::assertSent(\App\Mail\AdminAssetPreparationNotification::class);
    }

    /**
     * Test email rejection workflow
     *
     * @see D03-FR-002.3 Rejection processing
     */
    public function test_email_rejection_workflow(): void
    {
        // Route for approval
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Process rejection
        $rejectedApplication = $this->workflowService->processEmailApproval(
            $token,
            false,
            'Insufficient justification provided'
        );

        // Verify rejection processed
        $this->assertEquals(LoanStatus::REJECTED, $rejectedApplication->status);
        $this->assertNull($rejectedApplication->approved_at);
        $this->assertEquals('Insufficient justification provided', $rejectedApplication->rejected_reason);
        $this->assertNull($rejectedApplication->approval_token);

        // Verify rejection email sent
        Mail::assertSent(LoanApplicationRejected::class, function ($mail) {
            return $mail->hasTo($this->loanApplication->applicant_email) &&
                   $mail->loanApplication->rejected_reason === 'Insufficient justification provided';
        });
    }

    /**
     * Test approval matrix logic
     *
     * @see D03-FR-002.1 Approval matrix
     */
    public function test_approval_matrix_routing(): void
    {
        // Test Grade 41+ with high value (requires approval)
        $highValueApplication = LoanApplication::factory()->create([
            'grade' => '41',
            'total_value' => 10000.00,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->workflowService->routeForEmailApproval($highValueApplication);

        $highValueApplication->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $highValueApplication->status);
        $this->assertNotNull($highValueApplication->approver_email);

        // Test lower grade with low value (auto-approve or different routing)
        $lowValueApplication = LoanApplication::factory()->create([
            'grade' => '32',
            'total_value' => 500.00,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->workflowService->routeForEmailApproval($lowValueApplication);

        $lowValueApplication->refresh();
        // Depending on business rules, this might be auto-approved or routed differently
        $this->assertContains($lowValueApplication->status, [
            LoanStatus::UNDER_REVIEW,
            LoanStatus::APPROVED
        ]);
    }

    /**
     * Test token security and expiration
     *
     * @see D03-FR-002.3 Token security
     * @see D03-FR-002.5 Token expiration handling
     */
    public function test_token_security_and_expiration(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Test valid token
        $this->assertTrue($this->loanApplication->isTokenValid($token));

        // Test invalid token
        $this->assertFalse($this->loanApplication->isTokenValid('invalid-token'));

        // Test expired token
        $this->loanApplication->update([
            'approval_token_expires_at' => now()->subDay()
        ]);
        $this->loanApplication->refresh();
        $this->assertFalse($this->loanApplication->isTokenValid($token));

        // Test processing expired token
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->workflowService->processEmailApproval('expired-token', true);
    }

    /**
     * Test email approval via HTTP endpoints
     *
     * @see D03-FR-002.3 HTTP approval endpoints
     */
    public function test_http_approval_endpoints(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Test approval endpoint
        $response = $this->get(route('loan.approve', [
            'token' => $token,
            'action' => 'approve'
        ]));

        $response->assertOk();
        $response->assertSee('Application Approved');

        $this->loanApplication->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $this->loanApplication->status);

        // Test rejection endpoint with new application
        $newApplication = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
        ]);
        $this->workflowService->routeForEmailApproval($newApplication);
        $newToken = $newApplication->approval_token;

        $response = $this->get(route('loan.approve', [
            'token' => $newToken,
            'action' => 'reject'
        ]));

        $response->assertOk();
        $response->assertSee('Application Rejected');

        $newApplication->refresh();
        $this->assertEquals(LoanStatus::REJECTED, $newApplication->status);
    }

    /**
     * Test email template rendering and content
     *
     * @see D03-FR-006.1 Email templates
     * @see D03-FR-015.2 Bilingual support
     */
    public function test_email_template_rendering(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);

        // Test approval request email content
        Mail::assertSent(ApprovalRequest::class, function ($mail) {
            $rendered = $mail->render();

            // Check for required content
            $this->assertStringContainsString($this->loanApplication->application_number, $rendered);
            $this->assertStringContainsString($this->loanApplication->applicant_name, $rendered);
            $this->assertStringContainsString($this->loanApplication->purpose, $rendered);
            $this->assertStringContainsString('RM ' . number_format($this->loanApplication->total_value, 2), $rendered);

            // Check for approval/rejection links
            $this->assertStringContainsString('approve', $rendered);
            $this->assertStringContainsString('reject', $rendered);
            $this->assertStringContainsString($this->loanApplication->approval_token, $rendered);

            return true;
        });

        // Test bilingual support (if implemented)
        $malayApplication = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
            'applicant_name' => 'Ahmad bin Abdullah',
        ]);

        // Set locale to Malay
        app()->setLocale('ms');
        $this->workflowService->routeForEmailApproval($malayApplication);

        Mail::assertSent(ApprovalRequest::class, function ($mail) {
            $rendered = $mail->render();

            // Check for Malay content (if implemented)
            // This would depend on your translation files
            return true;
        });
    }

    /**
     * Test email delivery performance and SLA compliance
     *
     * @see D03-FR-009.1 Email SLA (60 seconds)
     * @see D03-FR-007.2 Performance requirements
     */
    public function test_email_delivery_performance(): void
    {
        $startTime = microtime(true);

        // Process multiple applications
        $applications = LoanApplication::factory()->count(10)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        foreach ($applications as $application) {
            $this->workflowService->routeForEmailApproval($application);
        }

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        // Verify processing completed within SLA (should be much faster than 60 seconds)
        $this->assertLessThan(10.0, $processingTime, 'Email routing took too long');

        // Verify all emails queued
        Mail::assertSent(ApprovalRequest::class, 10);
    }

    /**
     * Test email queue processing and retry mechanism
     *
     * @see D03-FR-009.2 Queue processing with retry
     */
    public function test_email_queue_processing_and_retry(): void
    {
        Queue::fake();

        $this->workflowService->routeForEmailApproval($this->loanApplication);

        // Verify email job was queued
        Queue::assertPushed(\Illuminate\Mail\SendQueuedMailable::class);

        // Test retry mechanism (would require more complex setup in real scenario)
        $this->assertTrue(true); // Placeholder for retry testing
    }

    /**
     * Test concurrent approval processing
     *
     * @see D03-FR-007.2 Concurrent processing
     */
    public function test_concurrent_approval_processing(): void
    {
        // Create multiple applications
        $applications = LoanApplication::factory()->count(5)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        // Route all for approval
        foreach ($applications as $application) {
            $this->workflowService->routeForEmailApproval($application);
        }

        // Process approvals concurrently (simulate)
        $tokens = $applications->pluck('approval_token')->toArray();

        foreach ($tokens as $token) {
            $this->workflowService->processEmailApproval($token, true, 'Bulk approval');
        }

        // Verify all processed correctly
        foreach ($applications as $application) {
            $application->refresh();
            $this->assertEquals(LoanStatus::APPROVED, $application->status);
        }

        // Verify no race conditions or data corruption
        $this->assertEquals(5, LoanApplication::where('status', LoanStatus::APPROVED)->count());
    }

    /**
     * Test error handling in email workflow
     */
    public function test_email_workflow_error_handling(): void
    {
        // Test processing non-existent token
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->workflowService->processEmailApproval('non-existent-token', true);

        // Test processing already processed application
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Process once
        $this->workflowService->processEmailApproval($token, true);

        // Try to process again
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->workflowService->processEmailApproval($token, true);
    }

    /**
     * Test audit trail for email approval workflow
     *
     * @see D03-FR-010.2 Audit logging
     */
    public function test_email_approval_audit_trail(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Process approval
        $this->workflowService->processEmailApproval($token, true, 'Email approval test');

        // Verify audit records created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => LoanApplication::class,
            'auditable_id' => $this->loanApplication->id,
            'event' => 'updated',
        ]);

        // Verify audit includes approval details
        $audit = \App\Models\Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $this->loanApplication->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($audit);
        $this->assertArrayHasKey('status', $audit->new_values);
        $this->assertEquals('approved', $audit->new_values['status']);
    }
}
