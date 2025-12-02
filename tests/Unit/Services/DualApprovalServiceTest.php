<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalMatrixService;
use App\Services\DualApprovalService;
use App\Services\Notifications\LoanNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for DualApprovalService.
 *
 * Tests dual approval workflow (email + portal) for loan applications.
 */
#[CoversClass(DualApprovalService::class)]
class DualApprovalServiceTest extends TestCase
{
    use RefreshDatabase;

    private DualApprovalService $service;

    /** @var ApprovalMatrixService&MockInterface */
    private MockInterface $approvalMatrixMock;

    /** @var LoanNotificationService&MockInterface */
    private MockInterface $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->approvalMatrixMock = Mockery::mock(ApprovalMatrixService::class);
        $this->notificationServiceMock = Mockery::mock(LoanNotificationService::class);

        $this->service = new DualApprovalService(
            $this->approvalMatrixMock,
            $this->notificationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test sending approval request routes to correct approver.
     */
    public function test_send_approval_request_routes_to_correct_approver(): void
    {
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'grade' => 'N41',
            'total_value' => 5000.00,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $expectedApprover = [
            'email' => 'approver@motac.gov.my',
            'name' => 'Test Approver',
        ];

        $this->approvalMatrixMock
            ->shouldReceive('determineApprover')
            ->once()
            ->with('N41', 5000.00)
            ->andReturn($expectedApprover);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalRequest')
            ->once();

        $this->service->sendApprovalRequest($application);

        $application->refresh();

        $this->assertEquals($expectedApprover['email'], $application->approver_email);
        $this->assertEquals($expectedApprover['name'], $application->approved_by_name);
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
    }

    /**
     * Test email approval with valid token approves application.
     */
    public function test_process_email_approval_with_valid_token_approves_application(): void
    {
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'valid-token-123',
            'approval_token_expires_at' => now()->addDays(7),
        ]);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalDecision')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalConfirmation')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('notifyAdminForAssetPreparation')
            ->once();

        $result = $this->service->processEmailApproval('valid-token-123', true, 'Approved for official use');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('approved', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertNotNull($application->approved_at);
        $this->assertNull($application->approval_token);
    }

    /**
     * Test email approval with valid token rejects application.
     */
    public function test_process_email_approval_with_valid_token_rejects_application(): void
    {
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'valid-token-456',
            'approval_token_expires_at' => now()->addDays(7),
        ]);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalDecision')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalConfirmation')
            ->once();

        $rejectionReason = 'Asset not available for requested dates';
        $result = $this->service->processEmailApproval('valid-token-456', false, $rejectionReason);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('declined', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::REJECTED, $application->status);
        $this->assertEquals($rejectionReason, $application->rejected_reason);
        $this->assertNull($application->approved_at);
    }

    /**
     * Test email approval with invalid token fails.
     */
    public function test_process_email_approval_with_invalid_token_fails(): void
    {
        $result = $this->service->processEmailApproval('non-existent-token', true);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalid', strtolower($result['message']));
    }

    /**
     * Test email approval with expired token fails.
     */
    public function test_process_email_approval_with_expired_token_fails(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'expired-token-789',
            'approval_token_expires_at' => now()->subDays(1),
        ]);

        $result = $this->service->processEmailApproval('expired-token-789', true);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expired', strtolower($result['message']));
    }

    /**
     * Test portal approval by authorized approver succeeds.
     */
    public function test_process_portal_approval_by_authorized_approver_succeeds(): void
    {
        $approver = User::factory()->create([
            'grade' => 'N48',
            'role' => 'approver',
        ]);

        $applicant = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $applicant->id,
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalDecision')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalConfirmation')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('notifyAdminForAssetPreparation')
            ->once();

        $result = $this->service->processPortalApproval(
            $application,
            $approver,
            true,
            'Approved via portal'
        );

        $this->assertTrue($result['success']);

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertEquals($approver->email, $application->approver_email);
        $this->assertEquals($approver->name, $application->approved_by_name);
    }

    /**
     * Test portal approval by unauthorized user fails.
     */
    public function test_process_portal_approval_by_unauthorized_user_fails(): void
    {
        $unauthorizedUser = User::factory()->create([
            'grade' => 'N29',
            'role' => 'staff',
        ]);

        $applicant = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $applicant->id,
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $result = $this->service->processPortalApproval(
            $application,
            $unauthorizedUser,
            true
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('permission', strtolower($result['message']));

        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
    }

    /**
     * Test portal rejection records reason correctly.
     */
    public function test_process_portal_rejection_records_reason(): void
    {
        $approver = User::factory()->create([
            'grade' => 'N48',
            'role' => 'approver',
        ]);

        $applicant = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $applicant->id,
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalDecision')
            ->once();

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalConfirmation')
            ->once();

        $rejectionReason = 'Budget constraints for this quarter';
        $result = $this->service->processPortalApproval(
            $application,
            $approver,
            false,
            $rejectionReason
        );

        $this->assertTrue($result['success']);

        $application->refresh();
        $this->assertEquals(LoanStatus::REJECTED, $application->status);
        $this->assertEquals($rejectionReason, $application->rejected_reason);
    }

    /**
     * Test log approval decision records metadata correctly.
     */
    public function test_log_approval_decision_records_metadata(): void
    {
        $approver = User::factory()->create(['name' => 'Test Approver']);
        $applicant = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $applicant->id,
            'status' => LoanStatus::APPROVED,
        ]);

        $this->service->logApprovalDecision(
            $application,
            true,
            'portal',
            'Test remarks',
            $approver
        );

        $application->refresh();
        $this->assertEquals('portal', $application->approval_method);
        $this->assertEquals('Test remarks', $application->approval_remarks);
        $this->assertEquals('Test Approver', $application->approved_by_name);
    }

    /**
     * Test route for email approval is alias for send approval request.
     */
    public function test_route_for_email_approval_is_alias(): void
    {
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'grade' => 'N41',
            'total_value' => 3000.00,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->approvalMatrixMock
            ->shouldReceive('determineApprover')
            ->once()
            ->andReturn(['email' => 'test@motac.gov.my', 'name' => 'Test']);

        $this->notificationServiceMock
            ->shouldReceive('sendApprovalRequest')
            ->once();

        $this->service->routeForEmailApproval($application);

        $application->refresh();
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
    }
}
