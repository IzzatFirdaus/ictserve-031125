<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Pages\ViewAsset;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Resources\Loans\Pages\EditLoanApplication;
use App\Filament\Resources\Loans\Pages\ListLoanApplications;
use App\Filament\Resources\Loans\Pages\ViewLoanApplication;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function admin_can_view_asset_list(): void
    {
        $category = AssetCategory::factory()->create();
        $assets = Asset::factory()->count(3)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(ListAssets::class)
            ->loadTable()
            ->assertSuccessful()
            ->assertCanSeeTableRecords($assets);
    }

    #[Test]
    public function staff_cannot_access_asset_list(): void
    {
        $this->actingAsForFilament($this->staff);

        $response = $this->get(AssetResource::getUrl('index'));

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_create_asset(): void
    {
        $category = AssetCategory::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(CreateAsset::class)
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

    #[Test]
    public function admin_can_edit_asset(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(EditAsset::class, ['record' => $asset->id])
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

    #[Test]
    public function admin_can_view_asset_details(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag)
            ->assertSee($asset->name);
    }

    #[Test]
    public function admin_can_delete_asset(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(EditAsset::class, ['record' => $asset->id])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    // ========================================
    // Loan Application Resource CRUD Tests
    // ========================================

    #[Test]
    public function admin_can_view_loan_application_list(): void
    {
        $division = Division::factory()->create();
        $applications = LoanApplication::factory()->count(3)->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        Livewire::test(ListLoanApplications::class)
            ->loadTable()
            ->assertSuccessful()
            ->assertCanSeeTableRecords($applications);
    }

    #[Test]
    public function staff_cannot_access_loan_application_admin(): void
    {
        $this->actingAsForFilament($this->staff);

        $response = $this->get(LoanApplicationResource::getUrl('index'));

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_view_loan_application_details(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number)
            ->assertSee($application->applicant_name);
    }

    #[Test]
    public function admin_can_edit_loan_application(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditLoanApplication::class, ['record' => $application->id])
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

    #[Test]
    public function superuser_has_full_access_to_all_resources(): void
    {
        $this->actingAsForFilament($this->superuser);

        // Test Asset Resource access
        $response = $this->get(AssetResource::getUrl('index'));
        $response->assertSuccessful();

        // Test Loan Application Resource access
        $response = $this->get(LoanApplicationResource::getUrl('index'));
        $response->assertSuccessful();
    }

    #[Test]
    public function approver_can_view_but_not_manage_resources(): void
    {
        $this->actingAsForFilament($this->approver);

        // Approvers should not have admin access to resources
        $response = $this->get(AssetResource::getUrl('index'));
        $response->assertForbidden();

        $response = $this->get(LoanApplicationResource::getUrl('index'));
        $response->assertForbidden();
    }

    #[Test]
    public function resource_authorization_checks_are_enforced(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        // Staff user should not be able to edit assets
        $this->actingAsForFilament($this->staff);

        $response = $this->get(AssetResource::getUrl('edit', ['record' => $asset]));
        $response->assertForbidden();

        // Admin user should be able to edit assets
        $this->actingAsForFilament($this->admin);

        $response = $this->get(AssetResource::getUrl('edit', ['record' => $asset]));
        $response->assertSuccessful();
    }

    // ========================================
    // Cross-Module Integration Tests
    // ========================================

    #[Test]
    public function asset_resource_displays_loan_history(): void
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

        // Verify ViewAsset page loads successfully with loan history relation manager
        $component = Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful();

        // Verify the asset has the loan relationship loaded
        $this->assertTrue($asset->loanItems()->exists());
        $this->assertEquals(1, $asset->loanItems()->count());

        // Verify LoanHistoryRelationManager is registered
        $this->assertContains(
            \App\Filament\Resources\Assets\RelationManagers\LoanHistoryRelationManager::class,
            AssetResource::getRelations()
        );
    }

    #[Test]
    public function loan_application_resource_displays_asset_details(): void
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

        // Verify ViewLoanApplication page loads successfully
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful();

        // Verify the loan application has asset relationships loaded via eager loading
        $loadedApplication = LoanApplication::with('loanItems.asset')->find($application->id);
        $this->assertTrue($loadedApplication->loanItems->isNotEmpty());
        $this->assertEquals($asset->id, $loadedApplication->loanItems->first()->asset->id);

        // Verify eager loading is configured in the resource query
        $query = LoanApplicationResource::getEloquentQuery();
        $eagerLoads = $query->getEagerLoads();
        $this->assertArrayHasKey('loanItems.asset', $eagerLoads);
    }

    // ========================================
    // Table Filtering and Search Tests
    // ========================================

    #[Test]
    public function asset_table_can_be_filtered_by_status(): void
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

        Livewire::test(ListAssets::class)
            ->loadTable()
            ->filterTable('status', AssetStatus::AVAILABLE->value)
            ->assertCanSeeTableRecords([$availableAsset])
            ->assertCanNotSeeTableRecords([$loanedAsset]);
    }

    #[Test]
    public function loan_application_table_can_be_searched(): void
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

        Livewire::test(ListLoanApplications::class)
            ->loadTable()
            ->searchTable('John')
            ->assertCanSeeTableRecords([$application1])
            ->assertCanNotSeeTableRecords([$application2]);
    }

    #[Test]
    public function loan_application_table_can_be_filtered_by_status(): void
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

        Livewire::test(ListLoanApplications::class)
            ->loadTable()
            ->filterTable('status', LoanStatus::SUBMITTED->value)
            ->assertCanSeeTableRecords([$submittedApp])
            ->assertCanNotSeeTableRecords([$approvedApp]);
    }

    // ========================================
    // Bulk Actions Tests
    // ========================================

    #[Test]
    public function admin_can_perform_bulk_delete_on_assets(): void
    {
        $category = AssetCategory::factory()->create();
        $assets = Asset::factory()->count(3)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin);

        Livewire::test(ListAssets::class)
            ->loadTable()
            ->callTableBulkAction('delete', $assets);

        foreach ($assets as $asset) {
            $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        }
    }

    // ========================================
    // Validation Tests
    // ========================================

    #[Test]
    public function asset_creation_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateAsset::class)
            ->fillForm([
                'asset_tag' => '', // Required field left empty
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['asset_tag', 'name']);
    }

    #[Test]
    public function asset_creation_validates_unique_asset_tag(): void
    {
        $category = AssetCategory::factory()->create();
        $existingAsset = Asset::factory()->create([
            'category_id' => $category->id,
            'asset_tag' => 'AST-2025-001',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CreateAsset::class)
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

    private function actingAsForFilament(User $user): void
    {
        $this->actingAs($user);
        Filament::auth()->login($user);
    }
}
