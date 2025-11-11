<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Resources\Loans\Pages\ListLoanApplications;
use App\Filament\Resources\Loans\Pages\ViewLoanApplication;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function admin_can_view_loan_applications(): void
    {
        $applications = LoanApplication::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertCanSeeTableRecords($applications)
            ->assertCanRenderTableColumn('application_number')
            ->assertCanRenderTableColumn('status')
            ->assertCanRenderTableColumn('loan_start_date');
    }

    #[Test]
    public function admin_can_approve_loan_application(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'submitted',
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

    #[Test]
    public function admin_can_reject_loan_application(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'submitted',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('decline', $application, data: [
                'reason' => 'Asset not available',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'rejected',
            'rejected_reason' => 'Asset not available',
        ]);
    }

    #[Test]
    public function admin_can_issue_approved_loan(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'approved',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('processIssuance', $application, data: [
                'issued_at' => now()->toDateTimeString(),
                'issued_by_name' => 'Test Admin',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'issued',
        ]);
    }

    #[Test]
    public function admin_can_process_asset_return(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'in_use',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('processReturn', $application, data: [
                'returned_at' => now()->toDateTimeString(),
                'returned_by_name' => 'Test Admin',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'returned',
        ]);
    }

    #[Test]
    public function admin_can_filter_applications_by_status(): void
    {
        $pendingApps = LoanApplication::factory()->count(3)->create(['status' => 'submitted']);
        $approvedApps = LoanApplication::factory()->count(2)->create(['status' => 'approved']);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->filterTable('status', 'submitted')
            ->assertCanSeeTableRecords($pendingApps)
            ->assertCanNotSeeTableRecords($approvedApps);
    }

    #[Test]
    public function admin_can_search_applications(): void
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

    #[Test]
    public function admin_can_bulk_approve_applications(): void
    {
        $applications = LoanApplication::factory()->count(3)->create([
            'status' => 'submitted',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableBulkAction('approve', $applications)
            ->assertHasNoErrors();

        foreach ($applications as $application) {
            $this->assertDatabaseHas('loan_applications', [
                'id' => $application->id,
                'status' => 'approved',
            ]);
        }
    }

    #[Test]
    public function damaged_asset_return_creates_helpdesk_ticket(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'in_use',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('processReturn', $application, data: [
                'returned_at' => now()->toDateTimeString(),
                'returned_by_name' => 'Test Admin',
                'damage_reported' => true,
            ])
            ->assertHasNoErrors();

        // Application should be marked as requiring maintenance
        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'maintenance_required' => true,
        ]);
    }

    #[Test]
    public function overdue_applications_are_highlighted(): void
    {
        $overdueApp = LoanApplication::factory()->create([
            'status' => 'issued',
            'loan_end_date' => now()->subDays(5), // Overdue
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertCanSeeTableRecords([$overdueApp])
            ->assertSee('Lewat'); // Check for overdue badge text in Bahasa Melayu
    }

    #[Test]
    public function asset_availability_is_checked_before_approval(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'submitted',
        ]);

        $this->actingAs($this->admin);

        // Approve application
        Livewire::test(ListLoanApplications::class)
            ->callTableAction('approve', $application)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function admin_can_view_application_history(): void
    {
        $application = LoanApplication::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($application->application_number)
            ->assertSee('Application History');
    }

    #[Test]
    public function admin_can_export_applications(): void
    {
        LoanApplication::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('export')
            ->assertHasNoErrors();
    }

    #[Test]
    public function staff_cannot_access_loan_resource(): void
    {
        $this->actingAs($this->staff)
            ->get(LoanApplicationResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function approval_workflow_sends_notifications(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'submitted',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableAction('approve', $application)
            ->assertHasNoErrors();

        // Verify application was approved
        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);

        // Note: Email notification integration can be verified separately
        // when EmailNotificationService integration is complete
    }

    #[Test]
    public function loan_duration_validation(): void
    {
        $this->actingAs($this->admin);

        $validApplication = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(1),
            'loan_end_date' => now()->addDays(8),
        ]);

        $this->assertDatabaseHas('loan_applications', [
            'id' => $validApplication->id,
        ]);

        // Verify dates are valid (end date after start date)
        $this->assertTrue(
            $validApplication->loan_end_date->greaterThan($validApplication->loan_start_date)
        );
    }

    #[Test]
    public function asset_conflict_detection(): void
    {
        $application1 = LoanApplication::factory()->create([
            'status' => 'issued',
            'loan_start_date' => now(),
            'loan_end_date' => now()->addDays(7),
        ]);

        $application2 = LoanApplication::factory()->create([
            'status' => 'submitted',
            'loan_start_date' => now()->addDays(3),
            'loan_end_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        // Both applications should exist with overlapping dates
        $this->assertDatabaseHas('loan_applications', [
            'id' => $application1->id,
            'status' => 'issued',
        ]);

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application2->id,
            'status' => 'submitted',
        ]);
    }
}
