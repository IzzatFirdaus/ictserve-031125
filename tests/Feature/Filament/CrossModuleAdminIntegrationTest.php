<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Pages\ViewAsset;
use App\Filament\Resources\Loans\Pages\ListLoanApplications;
use App\Filament\Resources\Loans\Pages\ViewLoanApplication;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cross-Module Admin Integration Tests
 *
 * Tests cross-module integration functionality in the admin panel,
 * including asset-loan linking, helpdesk integration, and unified views.
 *
 * @see D03-FR-016.1 Cross-module integration
 * @see D03-FR-016.2 Shared organizational data
 * @see D03-FR-016.3 Asset history integration
 * @see D03-FR-016.4 Unified search
 */
class CrossModuleAdminIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
    }

    // ========================================
    // Asset-Loan Integration Tests
    // ========================================

    #[Test]
    public function asset_view_displays_current_loan_status(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::ISSUED,
        ]);

        // Link asset to loan application
        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
            'condition_before' => AssetCondition::EXCELLENT,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($application->application_number)
            ->assertSee($application->applicant_name)
            ->assertSee('Loaned');
    }

    #[Test]
    public function asset_view_displays_complete_loan_history(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();

        // Create multiple loan applications for the same asset
        $app1 = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::COMPLETED,
        ]);
        $app2 = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::ISSUED,
        ]);

        // Link asset to both applications
        $app1->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);
        $app2->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($app1->application_number)
            ->assertSee($app2->application_number);
    }

    #[Test]
    public function loan_application_view_displays_asset_details(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'asset_tag' => 'AST-2025-001',
            'name' => 'Test Laptop',
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee('AST-2025-001')
            ->assertSee('Test Laptop');
    }

    #[Test]
    public function loan_application_displays_multiple_assets(): void
    {
        $category = AssetCategory::factory()->create();
        $asset1 = Asset::factory()->create([
            'category_id' => $category->id,
            'name' => 'Laptop 1',
        ]);
        $asset2 = Asset::factory()->create([
            'category_id' => $category->id,
            'name' => 'Projector 1',
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Link multiple assets to application
        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset1->current_value,
            'total_value' => $asset1->current_value,
        ]);
        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset2->current_value,
            'total_value' => $asset2->current_value,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee('Laptop 1')
            ->assertSee('Projector 1');
    }

    // ========================================
    // Helpdesk Integration Tests
    // ========================================

    #[Test]
    public function asset_view_displays_related_helpdesk_tickets(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::MAINTENANCE,
        ]);

        // Create ticket category
        $ticketCategory = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        // Create helpdesk ticket for asset maintenance
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Asset Maintenance Required',
            'category_id' => $ticketCategory->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($ticket->ticket_number)
            ->assertSee('Asset Maintenance Required');
    }

    #[Test]
    public function asset_with_damage_report_shows_helpdesk_ticket_link(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'condition' => AssetCondition::DAMAGED,
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Create loan item with damage report
        $loanItem = $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
            'condition_after' => AssetCondition::DAMAGED,
            'damage_report' => 'Screen cracked during use',
        ]);

        // Create ticket category and related helpdesk ticket
        $ticketCategory = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);
        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $ticketCategory->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee('Screen cracked during use')
            ->assertSee($ticket->ticket_number);
    }

    // ========================================
    // Transaction History Tests
    // ========================================

    #[Test]
    public function asset_view_displays_transaction_history(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Create loan item
        $loanItem = $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        // Create transactions
        LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'issue',
            'processed_by' => $this->admin->id,
        ]);

        LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'return',
            'processed_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee('issue')
            ->assertSee('return');
    }

    #[Test]
    public function loan_application_view_displays_transaction_timeline(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        // Create transaction
        $transaction = LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'issue',
            'processed_by' => $this->admin->id,
            'notes' => 'Asset issued to applicant',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee('Asset issued to applicant')
            ->assertSee($this->admin->name);
    }

    // ========================================
    // Shared Organizational Data Tests
    // ========================================

    #[Test]
    public function division_data_is_consistent_across_modules(): void
    {
        $division = Division::factory()->create(['name' => 'ICT Division']);

        // Create loan application with division
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        // Create helpdesk ticket with same division
        $ticket = HelpdeskTicket::factory()->create([
            'division_id' => $division->id,
        ]);

        $this->actingAs($this->admin);

        // Check loan application shows correct division
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee('ICT Division');

        // Verify division is shared across modules
        $this->assertEquals($application->division_id, $ticket->division_id);
        $this->assertNotNull($application->division);
        $this->assertNotNull($ticket->division);
        $this->assertEquals($application->division->name, $ticket->division->name);
    }

    #[Test]
    public function user_data_is_consistent_across_modules(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $division = Division::factory()->create();

        // Create loan application for user
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'division_id' => $division->id,
        ]);

        // Create helpdesk ticket for same user
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($this->admin);

        // Verify user data is consistent
        $this->assertEquals($application->user_id, $ticket->user_id);
        $this->assertNotNull($application->user);
        $this->assertNotNull($ticket->user);
        $this->assertEquals($application->user->email, $ticket->user->email);
    }

    // ========================================
    // Unified Search Tests
    // ========================================

    #[Test]
    public function asset_search_includes_loan_application_data(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'asset_tag' => 'SEARCH-TEST-001',
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'application_number' => 'LA202511001',
        ]);

        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Search by asset tag should find the asset
        Livewire::test(ListAssets::class)
            ->searchTable('SEARCH-TEST-001')
            ->assertCanSeeTableRecords([$asset]);
    }

    #[Test]
    public function loan_application_search_includes_asset_data(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'name' => 'Unique Laptop Model',
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Search should work across related data
        Livewire::test(ListLoanApplications::class)
            ->searchTable($application->application_number)
            ->assertCanSeeTableRecords([$application]);
    }

    // ========================================
    // Data Integrity Tests
    // ========================================

    #[Test]
    public function deleting_asset_preserves_loan_history(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $loanItem = $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Soft delete the asset
        $asset->delete();

        // Loan item should still exist with asset reference
        $this->assertDatabaseHas('loan_items', [
            'id' => $loanItem->id,
        ]);

        // Asset should be soft deleted
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    #[Test]
    public function cross_module_referential_integrity_is_maintained(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create(['category_id' => $category->id]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $loanItem = $application->loanItems()->create([
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        // Verify relationships are properly established
        $this->assertEquals($asset->id, $loanItem->asset_id);
        $this->assertEquals($application->id, $loanItem->loan_application_id);

        // Verify reverse relationships work
        $this->assertTrue($asset->loanItems->contains($loanItem));
        $this->assertTrue($application->loanItems->contains($loanItem));
    }
}
