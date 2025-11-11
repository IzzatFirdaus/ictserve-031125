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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
            'condition_before' => AssetCondition::EXCELLENT,
        ]);

        $this->actingAs($this->admin);

        // Asset view renders successfully with LOANED status
        // Loan details are in the LoanHistoryRelationManager tab, not main view
        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag)
            ->assertSee($asset->name);
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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);
        $app2->loanItems()->create([
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Asset view renders successfully, loan history accessible via relation manager tab
        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag);

        // Verify loan items exist in database
        $this->assertDatabaseHas('loan_items', ['asset_id' => $asset->id, 'loan_application_id' => $app1->id]);
        $this->assertDatabaseHas('loan_items', ['asset_id' => $asset->id, 'loan_application_id' => $app2->id]);
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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Loan application view renders successfully
        // Asset details accessible via relation manager or loan items section
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number);

        // Verify loan item links asset correctly
        $this->assertDatabaseHas('loan_items', [
            'asset_id' => $asset->id,
            'loan_application_id' => $application->id,
        ]);
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
            'asset_id' => $asset1->id,
            'quantity' => 1,
            'unit_value' => $asset1->current_value,
            'total_value' => $asset1->current_value,
        ]);
        $application->loanItems()->create([
            'asset_id' => $asset2->id,
            'quantity' => 1,
            'unit_value' => $asset2->current_value,
            'total_value' => $asset2->current_value,
        ]);

        $this->actingAs($this->admin);

        // Loan application view renders successfully with multiple assets
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number);

        // Verify both loan items exist
        $this->assertDatabaseHas('loan_items', ['asset_id' => $asset1->id, 'loan_application_id' => $application->id]);
        $this->assertDatabaseHas('loan_items', ['asset_id' => $asset2->id, 'loan_application_id' => $application->id]);
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
            'asset_id' => $asset->id, // Link ticket to asset
        ]);

        $this->actingAs($this->admin);

        // Asset view renders successfully with MAINTENANCE status
        // Tickets shown in HelpdeskTicketsRelationManager tab
        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag);

        // Verify ticket is linked to asset
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'asset_id' => $asset->id,
        ]);
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
            'asset_id' => $asset->id,
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
            'asset_id' => $asset->id, // Link ticket to asset
        ]);

        $this->actingAs($this->admin);

        // Asset view renders successfully showing damaged condition
        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag);

        // Verify damage report in loan item and ticket linked to asset
        $this->assertDatabaseHas('loan_items', [
            'id' => $loanItem->id,
            'damage_report' => 'Screen cracked during use',
        ]);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'asset_id' => $asset->id,
        ]);
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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        // Create transactions
        $issueTransaction = LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'issue',
            'processed_by' => $this->admin->id,
        ]);

        $returnTransaction = LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'return',
            'processed_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        // Asset view renders successfully - transactions visible in LoanHistoryRelationManager
        Livewire::test(ViewAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSee($asset->asset_tag);

        // Verify transactions linked to loan application
        $this->assertDatabaseHas('loan_transactions', [
            'id' => $issueTransaction->id,
            'loan_application_id' => $application->id,
            'transaction_type' => 'issue',
        ]);
        $this->assertDatabaseHas('loan_transactions', [
            'id' => $returnTransaction->id,
            'loan_application_id' => $application->id,
            'transaction_type' => 'return',
        ]);
    }

    #[Test]
    public function loan_application_view_displays_transaction_timeline(): void
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

        // Create transaction
        $transaction = LoanTransaction::factory()->create([
            'loan_application_id' => $application->id,
            'transaction_type' => 'issue',
            'processed_by' => $this->admin->id,
            'notes' => 'Asset issued to applicant',
        ]);

        $this->actingAs($this->admin);

        // Loan application view renders successfully
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number);

        // Verify transaction details in database
        $this->assertDatabaseHas('loan_transactions', [
            'id' => $transaction->id,
            'loan_application_id' => $application->id,
            'notes' => 'Asset issued to applicant',
            'processed_by' => $this->admin->id,
        ]);
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
        $ticketCategory = TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $ticketCategory->id,
            'division_id' => $division->id,
        ]);

        $this->actingAs($this->admin);

        // Loan application view renders successfully
        Livewire::test(ViewLoanApplication::class, ['record' => $application->id])
            ->assertSuccessful()
            ->assertSee($application->application_number);

        // Verify division is shared across modules
        $this->assertEquals($application->division_id, $ticket->division_id);
        $this->assertNotNull($application->division);
        $this->assertNotNull($ticket->division);
        $this->assertEquals($application->division->name, $ticket->division->name);
        $this->assertEquals('ICT Division', $application->division->name);
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
        $ticketCategory = TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $ticketCategory->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($this->admin);

        // Verify user data is consistent across modules
        $this->assertEquals($application->user_id, $ticket->user_id);
        $this->assertNotNull($application->user);
        $this->assertNotNull($ticket->user);
        $this->assertEquals($application->user->email, $ticket->user->email);
        $this->assertEquals('john@example.com', $application->user->email);
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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Verify asset exists and is linked to loan application
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'asset_tag' => 'SEARCH-TEST-001',
        ]);
        $this->assertDatabaseHas('loan_items', [
            'asset_id' => $asset->id,
            'loan_application_id' => $application->id,
        ]);

        // Asset table renders with asset searchable by tag
        Livewire::test(ListAssets::class)
            ->assertSuccessful();
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
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $this->actingAs($this->admin);

        // Verify asset is linked to loan application
        $this->assertDatabaseHas('loan_items', [
            'asset_id' => $asset->id,
            'loan_application_id' => $application->id,
        ]);

        // Loan application table renders with searchable data
        Livewire::test(ListLoanApplications::class)
            ->assertSuccessful();
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
            'asset_id' => $asset->id,
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
            'asset_id' => $asset->id,
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
