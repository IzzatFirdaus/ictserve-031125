<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalMatrixService;
use App\Services\DualApprovalService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

/**
 * Dual Approval Service Test
 *
 * Comprehensive tests for email-based and portal-based approval workflows.
 *
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-002.3 Secure token processing
 * @see D04 §2.2 Email approval workflow service
 */
class DualApprovalServiceTest extends TestCase
{
    use RefreshDatabase;

    private DualApprovalService $service;

    private ApprovalMatrixService $approvalMatrix;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure no lingering transactions
        try {
            DB::rollBack();
        } catch (\Throwable $e) {
            // OK if there's no transaction
        }

        $this->approvalMatrix = Mockery::mock(ApprovalMatrixService::class);
        $this->notificationService = Mockery::mock(NotificationService::class);

        $this->service = new DualApprovalService(
            $this->approvalMatrix,
            $this->notificationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }public function test_it_sends_approval_request_with_dual_options(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'grade' => '44',
            'total_value' => 5000.00,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $approver = [
            'email' => 'approver@motac.gov.my',
            'name' => 'Dato\' Ahmad bin Ali',
        ];

        $this->approvalMatrix->shouldReceive('determineApprover')
            ->once()
            ->with('44', 5000.00)
            ->andReturn($approver);

        $this->notificationService->shouldReceive('sendApprovalRequest')
            ->once()
            ->with(
                Mockery::type(LoanApplication::class),
                $approver,
                Mockery::type('string')
            );

        // Act
        $this->service->sendApprovalRequest($application);

        // Assert
        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
        $this->assertEquals('approver@motac.gov.my', $application->approver_email);
        $this->assertEquals('Dato\' Ahmad bin Ali', $application->approved_by_name);
        $this->assertNotNull($application->approval_token);
        $this->assertNotNull($application->approval_token_expires_at);
    }public function test_it_processes_email_approval_successfully(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $result = $this->service->processEmailApproval($token, true, 'Approved for official use');

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('approved', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertNotNull($application->approved_at);
        $this->assertNull($application->approval_token);
        $this->assertNull($application->approval_token_expires_at);
    }public function test_it_processes_email_rejection_successfully(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldNotReceive('notifyAdminForAssetPreparation');

        // Act
        $result = $this->service->processEmailApproval($token, false, 'Insufficient justification');

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('declined', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::REJECTED, $application->status);
        $this->assertNull($application->approved_at);
        $this->assertEquals('Insufficient justification', $application->rejected_reason);
        $this->assertNull($application->approval_token);
    }public function test_it_rejects_invalid_approval_token(): void
    {
        // Arrange
        $invalidToken = 'invalid-token-12345';

        // Act
        $result = $this->service->processEmailApproval($invalidToken, true);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalid', strtolower($result['message']));
    }public function test_it_rejects_expired_approval_token(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'expired-token',
            'approval_token_expires_at' => now()->subDays(1), // Expired
        ]);

        // Act
        $result = $this->service->processEmailApproval('expired-token', true);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expired', strtolower($result['message']));
    }public function test_it_processes_portal_approval_successfully(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '44', // Grade 41+ can approve
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $result = $this->service->processPortalApproval(
            $application,
            $approver,
            true,
            'Approved via portal'
        );

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('approved', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertNotNull($application->approved_at);
        $this->assertEquals($approver->email, $application->approver_email);
        $this->assertEquals($approver->name, $application->approved_by_name);
    }public function test_it_processes_portal_rejection_successfully(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '44',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldNotReceive('notifyAdminForAssetPreparation');

        // Act
        $result = $this->service->processPortalApproval(
            $application,
            $approver,
            false,
            'Budget constraints'
        );

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('declined', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::REJECTED, $application->status);
        $this->assertNull($application->approved_at);
        $this->assertEquals('Budget constraints', $application->rejected_reason);
    }public function test_it_prevents_portal_approval_by_unauthorized_user(): void
    {
        // Arrange
        $unauthorizedUser = User::factory()->create([
            'grade' => '32', // Below Grade 41
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Act
        $result = $this->service->processPortalApproval(
            $application,
            $unauthorizedUser,
            true
        );

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('permission', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
    }public function test_it_logs_approval_decision_metadata(): void
    {
        // Arrange
        Log::spy();

        $approver = User::factory()->create([
            'grade' => '44',
            'name' => 'Dato\' Ahmad',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Act
        $this->service->logApprovalDecision(
            $application,
            true,
            'portal',
            'Approved for official use',
            $approver
        );

        // Assert
        $application->refresh();
        $this->assertEquals('portal', $application->approval_method);
        $this->assertEquals('Approved for official use', $application->approval_remarks);
        $this->assertEquals('Dato\' Ahmad', $application->approved_by_name);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Approval decision logged', Mockery::on(function ($context) use ($application) {
                return $context['application_number'] === $application->application_number
                    && $context['method'] === 'portal';
            }));
    }public function test_it_handles_email_approval_processing_failure_with_rollback(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        // Force an exception during processing
        $this->notificationService->shouldReceive('sendApprovalDecision')
            ->andThrow(new \Exception('Email service unavailable'));

        // Act
        $result = $this->service->processEmailApproval($token, true);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('failed', strtolower($result['message']));

        // Verify rollback - status should remain unchanged
        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
        $this->assertNotNull($application->approval_token); // Token not cleared due to rollback
    }public function test_it_handles_portal_approval_processing_failure_with_rollback(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '44',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Force an exception during processing
        $this->notificationService->shouldReceive('sendApprovalDecision')
            ->andThrow(new \Exception('Database error'));

        // Act
        $result = $this->service->processPortalApproval($application, $approver, true);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('failed', strtolower($result['message']));

        // Verify rollback - status should remain unchanged
        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
    }public function test_it_clears_approval_token_after_successful_email_approval(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $this->service->processEmailApproval($token, true);

        // Assert
        $application->refresh();
        $this->assertNull($application->approval_token);
        $this->assertNull($application->approval_token_expires_at);
    }public function test_it_clears_approval_token_after_successful_portal_approval(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '44',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'some-token',
            'approval_token_expires_at' => now()->addDays(7),
        ]);

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $this->service->processPortalApproval($application, $approver, true);

        // Assert
        $application->refresh();
        $this->assertNull($application->approval_token);
        $this->assertNull($application->approval_token_expires_at);
    }public function test_it_notifies_admin_only_on_approval_not_rejection(): void
    {
        // Arrange - Email approval
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldNotReceive('notifyAdminForAssetPreparation');

        // Act - Reject
        $this->service->processEmailApproval($token, false, 'Not approved');

        // Assert - Admin notification should not be sent for rejection
        $this->assertTrue(true); // Assertion is in the mock expectations
    }public function test_it_logs_email_approval_events(): void
    {
        // Arrange
        Log::spy();

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $token = $application->generateApprovalToken();

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $this->service->processEmailApproval($token, true);

        // Assert
        Log::shouldHaveReceived('info')
            ->with('Email approval processed', Mockery::on(function ($context) use ($application) {
                return $context['application_number'] === $application->application_number
                    && $context['approved'] === true
                    && $context['method'] === 'email';
            }));

        $this->assertTrue(true); // Assert Mockery expectations are checked
    }

    public function test_it_logs_portal_approval_events(): void
    {
        // Arrange
        Log::spy();

        $approver = User::factory()->create([
            'grade' => '44',
            'role' => 'approver', // Must have approver role to approve
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->notificationService->shouldReceive('sendApprovalDecision')->once();
        $this->notificationService->shouldReceive('sendApprovalConfirmation')->once();
        $this->notificationService->shouldReceive('notifyAdminForAssetPreparation')->once();

        // Act
        $this->service->processPortalApproval($application, $approver, true);

        // Assert
        Log::shouldHaveReceived('info')
            ->with('Portal approval processed', Mockery::on(function ($context) use ($application, $approver) {
                return $context['application_number'] === $application->application_number
                    && $context['approved'] === true
                    && $context['approver_id'] === $approver->id
                    && $context['method'] === 'portal';
            }));

        $this->assertTrue(true); // Assert Mockery expectations are checked
    }
}

