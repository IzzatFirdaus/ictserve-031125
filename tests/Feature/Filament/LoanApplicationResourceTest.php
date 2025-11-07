<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\LoanApplicationResource;
use App\Filament\Resources\LoanApplicationResource\Pages\ListLoanApplications;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Loan Application Resource Test
 *
 * Tests CRUD operations, approval workflows, and asset management
 * for the LoanApplicationResource in Filament admin panel.
 *
 * Requirements: 18.1, D03-FR-015.1
 */
class LoanApplicationResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superuser;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->superuser = User::factory()->superuser()->create();
        $this->staff = User::factory()->staff()->create();
    }

    public function test_admin_can_view_loan_applications(): void
    {
        $applications = LoanApplication::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertCanSeeTableRecords($applications)
            ->assertCanRenderTableColumn('application_number')
            ->assertCanRenderTableColumn('status')
            ->assertCanRenderTableColumn('loan_date');
    }

    public function test_admin_can_approve_loan_application(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'pending_approval',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('approve', $application)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_loan_application(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'pending_approval',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('reject', $application, data: [
                'rejection_reason' => 'Asset not available',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'rejected',
            'rejection_reason' => 'Asset not available',
        ]);
    }

    public function test_admin_can_issue_approved_loan(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'approved',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('issue', $application)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'issued',
        ]);
    }

    public function test_admin_can_process_asset_return(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'issued',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('return', $application, data: [
                'return_condition' => 'good',
                'return_notes' => 'Asset returned in good condition',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'returned',
        ]);
    }

    public function test_admin_can_filter_applications_by_status(): void
    {
        $pendingApps = LoanApplication::factory()->count(3)->create(['status' => 'pending_approval']);
        $approvedApps = LoanApplication::factory()->count(2)->create(['status' => 'approved']);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->filterTable('status', 'pending_approval')
            ->assertCanSeeTableRecords($pendingApps)
            ->assertCanNotSeeTableRecords($approvedApps);
    }

    public function test_admin_can_search_applications(): void
    {
        $searchableApp = LoanApplication::factory()->create([
            'application_number' => 'LA-UNIQUE-001',
        ]);
        $otherApps = LoanApplication::factory()->count(3)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->searchTable('UNIQUE')
            ->assertCanSeeTableRecords([$searchableApp])
            ->assertCanNotSeeTableRecords($otherApps);
    }

    public function test_admin_can_bulk_approve_applications(): void
    {
        $applications = LoanApplication::factory()->count(3)->create([
            'status' => 'pending_approval',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->selectTableRecords($applications)
            ->callTableBulkAction('bulk_approve')
            ->assertHasNoErrors();

        foreach ($applications as $application) {
            $this->assertDatabaseHas('loan_applications', [
                'id' => $application->id,
                'status' => 'approved',
            ]);
        }
    }

    public function test_damaged_asset_return_creates_helpdesk_ticket(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'issued',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('return', $application, data: [
                'return_condition' => 'damaged',
                'damage_description' => 'Screen cracked',
                'return_notes' => 'Asset damaged during use',
            ])
            ->assertHasNoErrors();

        // Verify helpdesk ticket was created
        $this->assertDatabaseHas('helpdesk_tickets', [
            'title' => "Damaged Asset: {$application->asset->name}",
            'category' => 'asset_damage',
        ]);

        // Verify cross-module integration
        $this->assertDatabaseHas('cross_module_integrations', [
            'source_module' => 'asset_loan',
            'source_id' => $application->id,
            'target_module' => 'helpdesk',
            'integration_type' => 'damage_report',
        ]);
    }

    public function test_overdue_applications_are_highlighted(): void
    {
        $overdueApp = LoanApplication::factory()->create([
            'status' => 'issued',
            'return_by' => now()->subDays(5), // Overdue
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertCanSeeTableRecords([$overdueApp])
            ->assertTableColumnStateSet('overdue_status', 'overdue', $overdueApp);
    }

    public function test_asset_availability_is_checked_before_approval(): void
    {
        $unavailableAsset = Asset::factory()->create(['status' => 'maintenance']);
        $application = LoanApplication::factory()->create([
            'asset_id' => $unavailableAsset->id,
            'status' => 'pending_approval',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('approve', $application)
            ->assertHasErrors(['asset' => 'not available']);
    }

    public function test_admin_can_view_application_history(): void
    {
        $application = LoanApplication::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($application->application_number)
            ->assertSee('Application History');
    }

    public function test_admin_can_export_applications(): void
    {
        LoanApplication::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('export')
            ->assertHasNoErrors();
    }

    public function test_staff_cannot_access_loan_resource(): void
    {
        $this->actingAs($this->staff)
            ->get(LoanApplicationResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_approval_workflow_sends_notifications(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'pending_approval',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('approve', $application)
            ->assertHasNoErrors();

        // Verify notification was queued
        $this->assertDatabaseHas('email_logs', [
            'email_type' => 'loan_approved',
            'recipient_email' => $application->user->email,
        ]);
    }

    public function test_loan_duration_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateLoanApplication::class)
            ->fillForm([
                'loan_date' => now()->toDateString(),
                'return_by' => now()->subDay()->toDateString(), // Invalid: return before loan
            ])
            ->call('create')
            ->assertHasErrors(['return_by' => 'after']);
    }

    public function test_asset_conflict_detection(): void
    {
        $asset = Asset::factory()->create();

        // Create existing loan for the same period
        LoanApplication::factory()->create([
            'asset_id' => $asset->id,
            'status' => 'approved',
            'loan_date' => now()->toDateString(),
            'return_by' => now()->addDays(7)->toDateString(),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CreateLoanApplication::class)
            ->fillForm([
                'asset_id' => $asset->id,
                'loan_date' => now()->addDays(3)->toDateString(), // Conflicts with existing loan
                'return_by' => now()->addDays(10)->toDateString(),
            ])
            ->call('create')
            ->assertHasErrors(['asset_id' => 'conflict']);
    }
}
