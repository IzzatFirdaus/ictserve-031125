<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Livewire\GuestLoanApplication;
use App\Livewire\Loans\ApprovalQueue;
use App\Livewire\Loans\AuthenticatedLoanDashboard;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Comprehensive Workflow Integration Tests
 *
 * Tests complete user workflows from application submission to asset return,
 * covering guest access, authenticated portal, and admin processing.
 *
 * @see D03-FR-001.1 Hybrid architecture support
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-016.1 Cross-module integration
 * @see Task 11.1 - Comprehensive integration testing
 */
class ComprehensiveWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $approver;
    protected User $admin;
    protected Asset $asset;
    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different roles
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->approver = User::factory()->create(['role' => 'approver']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create test data
        $this->division = Division::factory()->create();
        $category = AssetCategory::factory()->create();
        $this->asset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::EXCELLENT,
        ]);

        Mail::fake();
    }

    /**
     * Test complete guest workflow: submission → approval → issuance → return
     *
     * @see D03-FR-001.2 Guest form submission
     * @see D03-FR-002.1 Email approval workflow
     */
    public function test_complete_guest_loan_workflow(): void
    {
        // Step 1: Guest submits loan application
        $applicationData = [
            'applicant_name' => 'Ahmad Bin Ali',
            'applicant_email' => 'ahmad@motac.gov.my',
            'applicant_phone' => '03-12345678',
            'staff_id' => 'MOTAC001',
            'grade' => '41',
            'division_id' => $this->division->id,
            'purpose' => 'Mesyuarat dengan agensi luar',
            'location' => 'Putrajaya',
            'return_location' => 'Putrajaya',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(3)->format('Y-m-d'),
            'selected_assets' => [$this->asset->id],
        ];

        Livewire::test(GuestLoanApplication::class)
            ->set('applicant_name', $applicationData['applicant_name'])
            ->set('applicant_email', $applicationData['applicant_email'])
            ->set('applicant_phone', $applicationData['applicant_phone'])
            ->set('staff_id', $applicationData['staff_id'])
            ->set('grade', $applicationData['grade'])
            ->set('division_id', $applicationData['division_id'])
            ->set('purpose', $applicationData['purpose'])
            ->set('location', $applicationData['location'])
            ->set('return_location', $applicationData['return_location'])
            ->set('loan_start_date', $applicationData['loan_start_date'])
            ->set('loan_end_date', $applicationData['loan_end_date'])
            ->set('selected_assets', $applicationData['selected_assets'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('application-submitted');

        // Verify application was created
        $application = LoanApplication::where('applicant_email', $applicationData['applicant_email'])->first();
        $this->assertNotNull($application);
        $this->assertNull($application->user_id); // Guest submission
        $this->assertEquals(LoanStatus::SUBMITTED, $application->status);
        $this->assertNotNull($application->application_number);
        $this->assertMatchesRegularExpression('/^LA\d{4}\d{2}\d{4}$/', $application->application_number);

        // Verify email notifications sent
        Mail::assertSent(\App\Mail\LoanApplicationSubmitted::class);
        Mail::assertSent(\App\Mail\ApprovalRequest::class);

        // Step 2: Approver processes via email workflow
        $this->assertNotNull($application->approval_token);
        $this->assertNotNull($application->approval_token_expires_at);
        $this->assertTrue($application->approval_token_expires_at > now());

        // Simulate email approval
        $response = $this->get(route('loan.approve', [
            'token' => $application->approval_token,
            'action' => 'approve'
        ]));

        $response->assertOk();

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertNotNull($application->approved_at);

        // Step 3: Admin processes asset issuance
        $this->actingAs($this->admin);

        // Simulate admin issuing assets through Filament
        $application->update(['status' => LoanStatus::ISSUED]);
        $application->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => $this->asset->current_value,
            'total_value' => $this->asset->current_value,
            'condition_before' => $this->asset->condition,
        ]);

        // Update asset status
        $this->asset->update(['status' => AssetStatus::LOANED]);

        // Step 4: Asset return processing
        $application->update(['status' => LoanStatus::RETURNED]);
        $loanItem = $application->loanItems()->first();
        $loanItem->update([
            'condition_after' => AssetCondition::GOOD,
        ]);

        // Create return transaction
        $application->transactions()->create([
            'asset_id' => $this->asset->id,
            'transaction_type' => 'return',
            'processed_by' => $this->admin->id,
            'processed_at' => now(),
            'condition_before' => AssetCondition::EXCELLENT,
            'condition_after' => AssetCondition::GOOD,
            'notes' => 'Asset returned in good condition',
        ]);

        // Update asset status back to available
        $this->asset->update([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
        ]);

        // Final verification
        $application->refresh();
        $this->assertEquals(LoanStatus::RETURNED, $application->status);
        $this->assertEquals(AssetStatus::AVAILABLE, $this->asset->fresh()->status);

        // Verify audit trail was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => LoanApplication::class,
            'auditable_id' => $application->id,
            'event' => 'created',
        ]);
    }

    /**
     * Test complete authenticated user workflow
     *
     * @see D03-FR-001.3 Authenticated portal features
     * @see D03-FR-011.1 User dashboard
     */
    public function test_complete_authenticated_loan_workflow(): void
    {
        $this->actingAs($this->staff);

        // Step 1: View dashboard
        Livewire::test(AuthenticatedLoanDashboard::class)
            ->assertSee('My Active Loans')
            ->assertSee('My Pending Applications')
            ->assertSee('Available Assets');

        // Step 2: Submit application through authenticated portal
        $applicationData = [
            'purpose' => 'Training session',
            'location' => 'Kuala Lumpur',
            'return_location' => 'Kuala Lumpur',
            'loan_start_date' => now()->addDays(2)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(4)->format('Y-m-d'),
            'selected_assets' => [$this->asset->id],
        ];

        Livewire::test(\App\Livewire\Loans\SubmitApplication::class)
            ->set('purpose', $applicationData['purpose'])
            ->set('location', $applicationData['location'])
            ->set('return_location', $applicationData['return_location'])
            ->set('loan_start_date', $applicationData['loan_start_date'])
            ->set('loan_end_date', $applicationData['loan_end_date'])
            ->set('selected_assets', $applicationData['selected_assets'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('application-submitted');

        // Verify authenticated application
        $application = LoanApplication::where('user_id', $this->staff->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals($this->staff->id, $application->user_id);
        $this->assertEquals($this->staff->name, $application->applicant_name);
        $this->assertEquals($this->staff->email, $application->applicant_email);

        // Step 3: Approver processes via portal
        $this->actingAs($this->approver);

        Livewire::test(ApprovalQueue::class)
            ->assertSee($application->application_number)
            ->set("remarks.{$application->id}", 'Approved for training purposes')
            ->call('approve', $application->id);

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertEquals('portal', $application->approval_method);
        $this->assertEquals('Approved for training purposes', $application->approval_remarks);

        // Verify notifications sent
        Mail::assertSent(\App\Mail\LoanApplicationApproved::class);
    }

    /**
     * Test admin workflow with asset management
     *
     * @see D03-FR-003.1 Asset management
     * @see D03-FR-004.1 Admin dashboard
     */
    public function test_admin_asset_management_workflow(): void
    {
        $this->actingAs($this->admin);

        // Create approved application
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        // Test asset issuance process
        $response = $this->post(route('admin.loans.issue', $application->id), [
            'assets' => [
                [
                    'asset_id' => $this->asset->id,
                    'condition_before' => AssetCondition::EXCELLENT->value,
                    'accessories' => ['Power adapter', 'Mouse'],
                ]
            ],
            'notes' => 'Asset issued for approved loan',
        ]);

        $response->assertRedirect();

        // Verify loan item created
        $this->assertDatabaseHas('loan_items', [
            'loan_application_id' => $application->id,
            'asset_id' => $this->asset->id,
        ]);

        // Verify asset status updated
        $this->asset->refresh();
        $this->assertEquals(AssetStatus::LOANED, $this->asset->status);

        // Verify transaction recorded
        $this->assertDatabaseHas('loan_transactions', [
            'loan_application_id' => $application->id,
            'asset_id' => $this->asset->id,
            'transaction_type' => 'issue',
            'processed_by' => $this->admin->id,
        ]);

        // Test asset return process
        $response = $this->post(route('admin.loans.return', $application->id), [
            'assets' => [
                [
                    'asset_id' => $this->asset->id,
                    'condition_after' => AssetCondition::GOOD->value,
                    'accessories_returned' => ['Power adapter', 'Mouse'],
                    'damage_report' => null,
                ]
            ],
            'notes' => 'Asset returned in good condition',
        ]);

        $response->assertRedirect();

        // Verify return processed
        $application->refresh();
        $this->assertEquals(LoanStatus::RETURNED, $application->status);

        $this->asset->refresh();
        $this->assertEquals(AssetStatus::AVAILABLE, $this->asset->status);
        $this->assertEquals(AssetCondition::GOOD, $this->asset->condition);
    }

    /**
     * Test error handling and edge cases
     */
    public function test_workflow_error_handling(): void
    {
        // Test expired approval token
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'expired-token',
            'approval_token_expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('loan.approve', [
            'token' => 'expired-token',
            'action' => 'approve'
        ]));

        $response->assertStatus(410); // Gone - token expired

        // Test invalid token
        $response = $this->get(route('loan.approve', [
            'token' => 'invalid-token',
            'action' => 'approve'
        ]));

        $response->assertStatus(404);

        // Test asset unavailability
        $this->asset->update(['status' => AssetStatus::MAINTENANCE]);

        Livewire::test(GuestLoanApplication::class)
            ->set('selected_assets', [$this->asset->id])
            ->call('checkAvailability')
            ->assertHasErrors(['selected_assets']);
    }

    /**
     * Test RBAC enforcement across workflows
     *
     * @see D03-FR-010.1 Role-based access control
     */
    public function test_rbac_enforcement_in_workflows(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Test staff cannot access admin functions
        $this->actingAs($this->staff);

        $response = $this->post(route('admin.loans.issue', $application->id), []);
        $response->assertStatus(403);

        // Test approver can access approval queue
        $this->actingAs($this->approver);

        Livewire::test(ApprovalQueue::class)
            ->assertSuccessful();

        // Test admin can access all functions
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.loans.index'));
        $response->assertSuccessful();
    }
}
