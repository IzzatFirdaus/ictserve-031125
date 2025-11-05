<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\DualApprovalService;
use App\Services\LoanApplicationService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Loan Application Service Tests
 *
 * Tests business logic for loan application creation and management.
 *
 * @see D03-FR-001.1 Hybrid application creation
 * @see D03-FR-001.2 Guest and authenticated submissions
 * Requirements: 2.3, 9.2, 16.1, 7.2
 */
class LoanApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    private LoanApplicationService $service;

    private DualApprovalService $approvalService;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->approvalService = $this->createMock(DualApprovalService::class);
        $this->notificationService = $this->createMock(NotificationService::class);

        $this->service = new LoanApplicationService(
            $this->approvalService,
            $this->notificationService
        );
    }

    #[Test]
    public function it_creates_guest_loan_application_successfully(): void
    {
        // Arrange
        $division = Division::factory()->create();
        $asset = Asset::factory()->available()->create(['current_value' => 1000]);

        $data = [
            'applicant_name' => 'John Doe',
            'applicant_email' => 'john@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF001',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Project presentation',
            'location' => 'Meeting Room A',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(3)->format('Y-m-d'),
            'items' => [$asset->id],
        ];

        $this->notificationService->expects($this->once())
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $application = $this->service->createHybridApplication($data, null);

        // Assert
        $this->assertInstanceOf(LoanApplication::class, $application);
        $this->assertNull($application->user_id);
        $this->assertEquals('John Doe', $application->applicant_name);
        $this->assertEquals('john@example.com', $application->applicant_email);
        $this->assertEquals(LoanStatus::SUBMITTED, $application->status);
        $this->assertEquals(1000, $application->total_value);
        $this->assertCount(1, $application->loanItems);
    }

    #[Test]
    public function it_creates_authenticated_loan_application_successfully(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'grade' => '44',
        ]);
        $division = Division::factory()->create();
        $asset = Asset::factory()->available()->create(['current_value' => 2000]);

        $data = [
            'applicant_name' => $user->name,
            'applicant_email' => $user->email,
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF002',
            'grade' => '44',
            'division_id' => $division->id,
            'purpose' => 'Training session',
            'location' => 'Training Room',
            'loan_start_date' => now()->addDays(2)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(5)->format('Y-m-d'),
            'items' => [$asset->id],
        ];

        $this->notificationService->expects($this->once())
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $application = $this->service->createHybridApplication($data, $user);

        // Assert
        $this->assertInstanceOf(LoanApplication::class, $application);
        $this->assertEquals($user->id, $application->user_id);
        $this->assertEquals($user->name, $application->applicant_name);
        $this->assertEquals(LoanStatus::SUBMITTED, $application->status);
    }

    #[Test]
    public function it_creates_loan_items_with_correct_values(): void
    {
        // Arrange
        $division = Division::factory()->create();
        $asset1 = Asset::factory()->available()->create(['current_value' => 1500]);
        $asset2 = Asset::factory()->available()->create(['current_value' => 2500]);

        $data = [
            'applicant_name' => 'Jane Smith',
            'applicant_email' => 'jane@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF003',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Conference',
            'location' => 'Conference Hall',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [
                ['asset_id' => $asset1->id, 'quantity' => 1],
                ['asset_id' => $asset2->id, 'quantity' => 2],
            ],
        ];

        $this->notificationService->expects($this->once())
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $application = $this->service->createHybridApplication($data, null);

        // Assert
        $this->assertCount(2, $application->loanItems);
        $this->assertEquals(6500, $application->total_value); // 1500 + (2500 * 2)
    }

    #[Test]
    public function it_generates_unique_application_number(): void
    {
        // Arrange
        $division = Division::factory()->create();
        $asset = Asset::factory()->available()->create();

        $data = [
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF004',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Testing',
            'location' => 'Test Location',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [$asset->id],
        ];

        $this->notificationService->expects($this->exactly(2))
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->exactly(2))
            ->method('sendApprovalRequest');

        // Act
        $app1 = $this->service->createHybridApplication($data, null);
        $app2 = $this->service->createHybridApplication($data, null);

        // Assert
        $this->assertNotEquals($app1->application_number, $app2->application_number);
        $this->assertMatchesRegularExpression('/^LA\d{6}\d{4}$/', $app1->application_number);
        $this->assertMatchesRegularExpression('/^LA\d{6}\d{4}$/', $app2->application_number);
    }

    #[Test]
    public function it_rolls_back_transaction_on_failure(): void
    {
        // Arrange
        $division = Division::factory()->create();

        $data = [
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF005',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Testing',
            'location' => 'Test Location',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [999999], // Non-existent asset
        ];

        // Act & Assert
        $this->expectException(\Exception::class);

        $initialCount = LoanApplication::count();
        $this->service->createHybridApplication($data, null);

        // Verify rollback
        $this->assertEquals($initialCount, LoanApplication::count());
    }

    #[Test]
    public function it_updates_loan_status_successfully(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->notificationService->expects($this->once())
            ->method('sendLoanStatusUpdate')
            ->with($application, LoanStatus::SUBMITTED->value);

        // Act
        $this->service->updateStatus($application, LoanStatus::APPROVED, 'Approved by manager');

        // Assert
        $freshApplication = $application->fresh();
        $this->assertNotNull($freshApplication);
        $this->assertEquals(LoanStatus::APPROVED, $freshApplication->status);
    }

    #[Test]
    public function it_requests_loan_extension_successfully(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::IN_USE,
            'loan_end_date' => now()->addDays(5),
        ]);

        $newEndDate = now()->addDays(10)->format('Y-m-d');
        $justification = 'Project extended';

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $this->service->requestExtension($application, $newEndDate, $justification);

        // Assert
        $application->refresh();
        $this->assertEquals($newEndDate, $application->loan_end_date->format('Y-m-d'));
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $application->status);
        $this->assertStringContainsString($justification, $application->special_instructions);
    }

    #[Test]
    public function it_claims_guest_application_successfully(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => 'guest@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'guest@example.com',
        ]);

        $this->notificationService->expects($this->once())
            ->method('sendLoanStatusUpdate');

        // Act
        $result = $this->service->claimGuestApplication($application, $user);

        // Assert
        $this->assertTrue($result);
        $freshApplication = $application->fresh();
        $this->assertNotNull($freshApplication);
        $this->assertEquals($user->id, $freshApplication->user_id);
    }

    #[Test]
    public function it_throws_exception_when_claiming_non_guest_application(): void
    {
        // Arrange
        $existingUser = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $existingUser->id,
            'applicant_email' => 'existing@example.com',
        ]);

        $newUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('already linked to an account');

        $this->service->claimGuestApplication($application, $newUser);
    }

    #[Test]
    public function it_throws_exception_when_email_mismatch_on_claim(): void
    {
        // Arrange
        $application = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => 'guest@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'different@example.com',
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email does not match');

        $this->service->claimGuestApplication($application, $user);
    }

    #[Test]
    public function it_sets_default_priority_when_not_provided(): void
    {
        // Arrange
        $division = Division::factory()->create();
        $asset = Asset::factory()->available()->create();

        $data = [
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF006',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Testing',
            'location' => 'Test Location',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [$asset->id],
            // No priority specified
        ];

        $this->notificationService->expects($this->once())
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $application = $this->service->createHybridApplication($data, null);

        // Assert
        $this->assertEquals(LoanPriority::NORMAL, $application->priority);
    }

    #[Test]
    public function it_uses_location_as_return_location_when_not_specified(): void
    {
        // Arrange
        $division = Division::factory()->create();
        $asset = Asset::factory()->available()->create();

        $data = [
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@example.com',
            'applicant_phone' => '0123456789',
            'staff_id' => 'STAFF007',
            'grade' => '41',
            'division_id' => $division->id,
            'purpose' => 'Testing',
            'location' => 'Office A',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [$asset->id],
            // No return_location specified
        ];

        $this->notificationService->expects($this->once())
            ->method('sendLoanApplicationConfirmation');

        $this->approvalService->expects($this->once())
            ->method('sendApprovalRequest');

        // Act
        $application = $this->service->createHybridApplication($data, null);

        // Assert
        $this->assertEquals('Office A', $application->return_location);
    }
}
