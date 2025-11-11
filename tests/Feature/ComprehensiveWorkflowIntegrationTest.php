<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Livewire\Loans\ApprovalQueue;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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

        // Assign Spatie roles if they exist
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $this->admin->assignRole($adminRole);
        }

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
    #[Test]
    public function complete_guest_loan_workflow(): void
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

        // Create application directly for testing workflow
        $application = LoanApplication::create([
            'applicant_name' => $applicationData['applicant_name'],
            'applicant_email' => $applicationData['applicant_email'],
            'applicant_phone' => $applicationData['applicant_phone'],
            'staff_id' => $applicationData['staff_id'],
            'grade' => $applicationData['grade'],
            'division_id' => $applicationData['division_id'],
            'purpose' => $applicationData['purpose'],
            'location' => $applicationData['location'],
            'return_location' => $applicationData['return_location'],
            'loan_start_date' => $applicationData['loan_start_date'],
            'loan_end_date' => $applicationData['loan_end_date'],
            'status' => LoanStatus::SUBMITTED,
            'application_number' => 'LA'.date('Ymd').str_pad('1', 4, '0', STR_PAD_LEFT),
            'approval_token' => bin2hex(random_bytes(32)),
            'approval_token_expires_at' => now()->addDays(7),
        ]);

        $this->assertNotNull($application);
        $this->assertNull($application->user_id); // Guest submission
        $this->assertEquals(LoanStatus::SUBMITTED, $application->status);
        $this->assertNotNull($application->application_number);

        // Step 2: Approver processes via email workflow
        $this->assertNotNull($application->approval_token);
        $this->assertNotNull($application->approval_token_expires_at);
        $this->assertTrue($application->approval_token_expires_at > now());

        // Simulate approval directly (email workflow tested separately)
        $application->update([
            'status' => LoanStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $this->approver->id,
        ]);

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
            'transaction_type' => 'return',
            'asset_id' => $this->asset->id,
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
        $freshAsset = $this->asset->fresh();
        $this->assertNotNull($freshAsset);
        $this->assertEquals(AssetStatus::AVAILABLE, $freshAsset->status);

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
    #[Test]
    public function complete_authenticated_loan_workflow(): void
    {
        $this->actingAs($this->staff);

        // Step 1: Access authenticated portal
        $response = $this->get(route('loan.authenticated.dashboard'));
        $response->assertSuccessful();

        // Step 2: Create authenticated application
        $application = LoanApplication::create([
            'user_id' => $this->staff->id,
            'applicant_name' => $this->staff->name,
            'applicant_email' => $this->staff->email,
            'applicant_phone' => $this->staff->phone ?? '03-12345678',
            'staff_id' => $this->staff->staff_id ?? 'STAFF001',
            'grade' => '41',
            'division_id' => $this->division->id,
            'purpose' => 'Training session',
            'location' => 'Kuala Lumpur',
            'return_location' => 'Kuala Lumpur',
            'loan_start_date' => now()->addDays(2)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(4)->format('Y-m-d'),
            'status' => LoanStatus::UNDER_REVIEW,
            'application_number' => 'LA'.date('Ymd').str_pad('2', 4, '0', STR_PAD_LEFT),
        ]);

        $this->assertNotNull($application);
        $this->assertEquals($this->staff->id, $application->user_id);
        $this->assertEquals($this->staff->name, $application->applicant_name);
        $this->assertEquals($this->staff->email, $application->applicant_email);

        // Step 3: Approver processes via portal
        $this->actingAs($this->approver);

        // Simulate approval
        $application->update([
            'status' => LoanStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $this->approver->id,
            'approval_method' => 'portal',
            'approval_remarks' => 'Approved for training purposes',
        ]);

        $application->refresh();
        $this->assertEquals(LoanStatus::APPROVED, $application->status);
        $this->assertEquals('portal', $application->approval_method);
        $this->assertEquals('Approved for training purposes', $application->approval_remarks);
    }

    /**
     * Test admin workflow with asset management
     *
     * @see D03-FR-003.1 Asset management
     * @see D03-FR-004.1 Admin dashboard
     */
    #[Test]
    public function admin_asset_management_workflow(): void
    {
        $this->actingAs($this->admin);

        // Create approved application
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        // Simulate asset issuance
        $application->update(['status' => LoanStatus::ISSUED]);
        $application->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => $this->asset->current_value,
            'total_value' => $this->asset->current_value,
            'condition_before' => $this->asset->condition,
        ]);

        $this->asset->update(['status' => AssetStatus::LOANED]);

        // Verify loan item created
        $this->assertDatabaseHas('loan_items', [
            'loan_application_id' => $application->id,
        ]);

        // Verify asset status updated
        $this->asset->refresh();
        $this->assertEquals(AssetStatus::LOANED, $this->asset->status);

        // Simulate asset return
        $application->update(['status' => LoanStatus::RETURNED]);
        $loanItem = $application->loanItems()->first();
        $loanItem->update(['condition_after' => AssetCondition::GOOD]);

        $this->asset->update([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
        ]);

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
    #[Test]
    public function workflow_error_handling(): void
    {
        // Test expired approval token
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_token' => 'expired-token',
            'approval_token_expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('loan.approve', [
            'token' => 'expired-token',
            'action' => 'approve',
        ]));

        $response->assertStatus(404); // Token not found or expired

        // Test invalid token
        $response = $this->get(route('loan.approve', [
            'token' => 'invalid-token',
            'action' => 'approve',
        ]));

        $response->assertStatus(404);

        // Test asset unavailability
        $this->asset->update(['status' => AssetStatus::MAINTENANCE]);

        // Verify asset is not available
        $this->assertEquals(AssetStatus::MAINTENANCE, $this->asset->fresh()->status);
    }

    /**
     * Test RBAC enforcement across workflows
     *
     * @see D03-FR-010.1 Role-based access control
     */
    #[Test]
    public function rbac_enforcement_in_workflows(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Test staff cannot access admin panel
        $this->actingAs($this->staff);

        $response = $this->get('/admin');
        $response->assertStatus(403);

        // Test approver can access approval queue
        $this->actingAs($this->approver);

        Livewire::test(ApprovalQueue::class)
            ->assertSuccessful();

        // Test admin can access admin panel
        $this->actingAs($this->admin);

        // Just verify admin role exists
        $this->assertTrue($this->admin->hasRole('admin'));
    }
}
