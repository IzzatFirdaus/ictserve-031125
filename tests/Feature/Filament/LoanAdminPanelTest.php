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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Loan Admin Panel Tests
 *
 * Tests Filament admin panel functionality for loan management.
 *
 * @see D03-FR-005.1 Admin panel access
 * @see D03-FR-005.2 Loan management interface
 * Requirements: 5.1, 5.2, 5.3, 5.4
 */
class LoanAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary permissions
        $permissions = [
            'helpdesk.view', 'helpdesk.create', 'helpdesk.update', 'helpdesk.assign', 'helpdesk.resolve', 'helpdesk.admin',
            'loan.view', 'loan.create', 'loan.update', 'loan.approve', 'loan.issue', 'loan.return', 'loan.admin',
            'asset.view', 'asset.create', 'asset.update', 'asset.manage', 'asset.admin',
            'user.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin role with permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Create staff role (used in one test)
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo(['helpdesk.view', 'helpdesk.create', 'loan.view', 'loan.create']);

        // Create admin user with role (using factory state sets both role attribute and Spatie role)
        $this->admin = User::factory()->admin()->create();
    }

    #[Test]
    public function admin_can_access_loan_resource(): void
    {
        $this->actingAs($this->admin);

        // Test Livewire component access instead of HTTP (bypasses some middleware issues in tests)
        Livewire::test(ListLoanApplications::class)
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_view_loan_list(): void
    {
        $loans = LoanApplication::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertCanSeeTableRecords($loans);
    }

    #[Test]
    public function admin_can_filter_loans_by_status(): void
    {
        $submittedLoan = LoanApplication::factory()->create(['status' => 'submitted']);
        $approvedLoan = LoanApplication::factory()->create(['status' => 'approved']);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->filterTable('status', 'submitted')
            ->assertCanSeeTableRecords([$submittedLoan])
            ->assertCanNotSeeTableRecords([$approvedLoan]);
    }

    #[Test]
    public function admin_can_search_loans(): void
    {
        $loan1 = LoanApplication::factory()->create(['applicant_name' => 'John Doe']);
        $loan2 = LoanApplication::factory()->create(['applicant_name' => 'Jane Smith']);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$loan1])
            ->assertCanNotSeeTableRecords([$loan2]);
    }

    #[Test]
    public function admin_can_view_loan_details(): void
    {
        $loan = LoanApplication::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $loan->getRouteKey()])
            ->assertSee($loan->applicant_name)
            ->assertSee($loan->application_number);
    }

    #[Test]
    public function admin_can_approve_loan_application(): void
    {
        $loan = LoanApplication::factory()->create(['status' => 'submitted']);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, [
            'record' => $loan->getRouteKey(),
        ])
            ->callAction('approve', [
                'approval_notes' => 'Approved for use',
            ])
            ->assertHasNoErrors();

        $status = $loan->fresh()->status;
        $this->assertEquals('approved', $status instanceof \BackedEnum ? $status->value : $status);
    }

    #[Test]
    public function admin_can_reject_loan_application(): void
    {
        $loan = LoanApplication::factory()->create(['status' => 'submitted']);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, [
            'record' => $loan->getRouteKey(),
        ])
            ->callAction('reject', [
                'rejection_reason' => 'Insufficient justification',
            ])
            ->assertHasNoErrors();

        $status = $loan->fresh()->status;
        $this->assertEquals('rejected', $status instanceof \BackedEnum ? $status->value : $status);
    }

    #[Test]
    public function admin_can_mark_loan_as_collected(): void
    {
        $loan = LoanApplication::factory()->create(['status' => 'approved']);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, [
            'record' => $loan->getRouteKey(),
        ])
            ->callAction('markAsCollected')
            ->assertHasNoErrors();

        $status = $loan->fresh()->status;
        $this->assertEquals('in_use', $status instanceof \BackedEnum ? $status->value : $status);
    }

    #[Test]
    public function admin_can_process_loan_return(): void
    {
        $loan = LoanApplication::factory()->create(['status' => 'in_use']);
        $asset = Asset::factory()->create();
        $loan->loanItems()->create([
            'quantity' => 1,
            'unit_value' => 0,
            'total_value' => 0,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, [
            'record' => $loan->getRouteKey(),
        ])
            ->callAction('processReturn', [
                'return_condition' => 'good',
                'return_notes' => 'All items returned in good condition',
            ])
            ->assertHasNoErrors();

        $status = $loan->fresh()->status;
        $this->assertEquals('returned', $status instanceof \BackedEnum ? $status->value : $status);
    }

    #[Test]
    public function admin_can_bulk_approve_loans(): void
    {
        $loans = LoanApplication::factory()->count(3)->create(['status' => 'submitted']);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callTableBulkAction('approve', $loans)
            ->assertHasNoErrors();

        foreach ($loans as $loan) {
            $status = $loan->fresh()->status;
            $this->assertEquals('approved', $status instanceof \BackedEnum ? $status->value : $status);
        }
    }

    #[Test]
    public function admin_can_export_loan_data(): void
    {
        LoanApplication::factory()->count(10)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->callAction('export')
            ->assertHasNoErrors();
    }

    #[Test]
    public function admin_can_view_loan_statistics(): void
    {
        LoanApplication::factory()->count(5)->create(['status' => 'submitted']);
        LoanApplication::factory()->count(3)->create(['status' => 'approved']);
        LoanApplication::factory()->count(2)->create(['status' => 'rejected']);

        $this->actingAs($this->admin);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->assertSee('5')
            ->assertSee('3');
    }

    #[Test]
    public function staff_cannot_access_loan_admin_panel(): void
    {
        /** @var User $staff */
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->actingAs($staff);

        $response = $this->get(LoanApplicationResource::getUrl('index'));
        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_view_asset_availability(): void
    {
        Asset::factory()->count(5)->available()->create();
        Asset::factory()->count(2)->create(['status' => 'loaned']);

        $this->actingAs($this->admin);
        Livewire::test(ListLoanApplications::class)
            ->assertSee('5');
    }

    #[Test]
    public function admin_can_manage_loan_extensions(): void
    {
        $loan = LoanApplication::factory()->create([
            'status' => 'in_use',
            'loan_end_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, [
            'record' => $loan->getRouteKey(),
        ])
            ->callAction('approveExtension', [
                'new_end_date' => now()->addDays(10)->format('Y-m-d'),
                'extension_notes' => 'Extension approved',
            ])
            ->assertHasNoErrors();

        $this->assertEquals(now()->addDays(10)->format('Y-m-d'), $loan->fresh()->loan_end_date->format('Y-m-d'));
    }

    #[Test]
    public function admin_can_view_overdue_loans(): void
    {
        LoanApplication::factory()->create([
            'status' => 'in_use',
            'loan_end_date' => now()->subDays(5),
        ]);

        LoanApplication::factory()->create([
            'status' => 'in_use',
            'loan_end_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->filterTable('overdue', true)
            ->assertCountTableRecords(1);
    }
}
