<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Admin Panel Resource Tests
 *
 * Tests Filament resource CRUD operations, role-based access control,
 * and cross-module integration functionality.
 *
 * @see D03-FR-003.1 Asset Management CRUD
 * @see D03-FR-004.4 Role-based access control
 * @see D03-FR-010.1 Authorization
 * @see D03-FR-013.1 Analytics Dashboard
 */
class AdminPanelResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected User $approver;

    protected User $superuser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->staff->assignRole('staff');

        $this->approver = User::factory()->create(['role' => 'approver']);
        $this->approver->assignRole('approver');

        $this->superuser = User::factory()->create(['role' => 'superuser']);
        $this->superuser->assignRole('superuser');
    }

    // ========================================
    // Asset Resource CRUD Tests
    // ========================================

    public function test_admin_can_view_asset_list(): void
    {
        $category = AssetCategory::factory()->create();
        $assets = Asset::factory()->count(3)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\ListAssets::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($assets);
    }

    public function test_staff_cannot_access_asset_list(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(AssetResource::getUrl('index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_asset(): void
    {
        $category = AssetCategory::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\CreateAsset::class)
            ->fillForm([
                'asset_tag' => 'AST-2025-001',
                'name' => 'Test Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'serial_number' => 'SN123456789',
                'category_id' => $category->id,
                'purchase_date' => now()->subYear()->format('Y-m-d'),
                'purchase_value' => 3500.00,
                'current_value' => 3000.00,
                'status' => AssetStatus::AVAILABLE->value,
                'location' => 'ICT Office',
                'condition' => AssetCondition::EXCELLENT->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'AST-2025-001',
            'name' => 'Test Laptop',
            'brand' => 'Dell',
        ]);
    }

    public function test_admin_can_edit_asset(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\EditAsset::class, ['record' => $asset->id])
            ->fillForm([
                'name' => 'Updated Asset Name',
                'condition' => AssetCondition::GOOD->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Updated Asset Name',
            'condition' => AssetCondition::GOOD->value,
        ]);
    }

    public function test_admin_can_view_asset_details(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag)
            ->assertSee($asset->name);
    }

    public function test_admin_can_delete_asset(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\EditAsset::class, ['record' => $asset->id])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    // ========================================
    // Loan Application Resource CRUD Tests
    // ========================================

    public function test_admin_can_view_loan_application_list(): void
    {
        $division = Division::factory()->create();
        $applications = LoanApplication::factory()->count(3)->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\ListLoanApplications::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($applications);
    }

    public function test_staff_cannot_access_loan_application_admin(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(LoanApplicationResource::getUrl('index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_loan_application_details(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number)
            ->assertSee($application->applicant_name);
    }

    public function test_admin_can_edit_loan_application(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\EditLoanApplication::class, ['record' => $application->id])
            ->fillForm([
                'status' => LoanStatus::APPROVED->value,
                'priority' => LoanPriority::HIGH->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => LoanStatus::APPROVED->value,
            'priority' => LoanPriority::HIGH->value,
        ]);
    }

    // ========================================
    // Role-Based Access Control Tests
    // ========================================

    public function test_superuser_has_full_access_to_all_resources(): void
    {
        $this->actingAs($this->superuser);

        // Test Asset Resource access
        $response = $this->get(AssetResource::getUrl('index'));
        $response->assertSuccessful();

        // Test Loan Application Resource access
        $response = $this->get(LoanApplicationResource::getUrl('index'));
        $response->assertSuccessful();
    }

    public function test_approver_can_view_but_not_manage_resources(): void
    {
        $this->actingAs($this->approver);

        // Approvers should not have admin access to resources
        $response = $this->get(AssetResource::getUrl('index'));
        $response->assertForbidden();

        $response = $this->get(LoanApplicationResource::getUrl('index'));
        $response->assertForbidden();
    }

    public function test_resource_authorization_checks_are_enforced(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        // Staff user should not be able to edit assets
        $this->actingAs($this->staff);

        $response = $this->get(AssetResource::getUrl('edit', ['record' => $asset]));
        $response->assertForbidden();

        // Admin user should be able to edit assets
        $this->actingAs($this->admin);

        $response = $this->get(AssetResource::getUrl('edit', ['record' => $asset]));
        $response->assertSuccessful();
    }

    // ========================================
    // Cross-Module Integration Tests
    // ========================================

    public function test_asset_resource_displays_loan_history(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Create loan item linking asset to application
        $application->loanItems()->create([
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($application->application_number);
    }

    public function test_loan_application_resource_displays_asset_details(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $application->loanItems()->create([
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag)
            ->assertSee($asset->name);
    }

    // ========================================
    // Table Filtering and Search Tests
    // ========================================

    public function test_asset_table_can_be_filtered_by_status(): void
    {
        $category = AssetCategory::factory()->create();
        $availableAsset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);
        $loanedAsset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\ListAssets::class)
            ->filterTable('status', AssetStatus::AVAILABLE->value)
            ->assertCanSeeTableRecords([$availableAsset])
            ->assertCanNotSeeTableRecords([$loanedAsset]);
    }

    public function test_loan_application_table_can_be_searched(): void
    {
        $division = Division::factory()->create();
        $application1 = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'applicant_name' => 'John Doe',
        ]);
        $application2 = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'applicant_name' => 'Jane Smith',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\ListLoanApplications::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$application1])
            ->assertCanNotSeeTableRecords([$application2]);
    }

    public function test_loan_application_table_can_be_filtered_by_status(): void
    {
        $division = Division::factory()->create();
        $submittedApp = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);
        $approvedApp = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::APPROVED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanApplicationResource\Pages\ListLoanApplications::class)
            ->filterTable('status', LoanStatus::SUBMITTED->value)
            ->assertCanSeeTableRecords([$submittedApp])
            ->assertCanNotSeeTableRecords([$approvedApp]);
    }

    // ========================================
    // Bulk Actions Tests
    // ========================================

    public function test_admin_can_perform_bulk_delete_on_assets(): void
    {
        $category = AssetCategory::factory()->create();
        $assets = Asset::factory()->count(3)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\ListAssets::class)
            ->callTableBulkAction('delete', $assets);

        foreach ($assets as $asset) {
            $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        }
    }

    // ========================================
    // Validation Tests
    // ========================================

    public function test_asset_creation_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\CreateAsset::class)
            ->fillForm([
                'asset_tag' => '', // Required field left empty
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['asset_tag', 'name']);
    }

    public function test_asset_creation_validates_unique_asset_tag(): void
    {
        $category = AssetCategory::factory()->create();
        $existingAsset = Asset::factory()->create([
            'category_id' => $category->id,
            'asset_tag' => 'AST-2025-001',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AssetResource\Pages\CreateAsset::class)
            ->fillForm([
                'asset_tag' => 'AST-2025-001', // Duplicate asset tag
                'name' => 'Test Asset',
                'brand' => 'Test Brand',
                'model' => 'Test Model',
                'category_id' => $category->id,
                'purchase_date' => now()->format('Y-m-d'),
                'purchase_value' => 1000.00,
                'current_value' => 900.00,
                'status' => AssetStatus::AVAILABLE->value,
                'location' => 'Test Location',
                'condition' => AssetCondition::GOOD->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['asset_tag']);
    }
}
