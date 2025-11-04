<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Filament\Pages\UnifiedAnalyticsDashboard;
use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\LoanApprovalQueueWidget;
use App\Filament\Widgets\UnifiedDashboardOverview;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Dashboard Widget Tests
 *
 * Tests dashboard widgets and analytics functionality including
 * unified analytics, asset utilization, and loan approval queue.
 *
 * @see D03-FR-013.1 Analytics Dashboard
 * @see D03-FR-004.1 Unified Dashboard
 */
class DashboardWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superuser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create admin and superuser
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->superuser = User::factory()->create(['role' => 'superuser']);
        $this->superuser->assignRole('superuser');
    }

    // ========================================
    // Unified Dashboard Overview Widget Tests
    // ========================================

    public function test_unified_dashboard_overview_displays_loan_statistics(): void
    {
        $division = Division::factory()->create();

        // Create loan applications with different statuses
        LoanApplication::factory()->count(5)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);
        LoanApplication::factory()->count(3)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::APPROVED,
        ]);
        LoanApplication::factory()->count(2)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::ISSUED,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(UnifiedDashboardOverview::class);

        $widget->assertSuccessful()
            ->assertSee('5') // Submitted applications
            ->assertSee('3') // Approved applications
            ->assertSee('2'); // Issued applications
    }

    public function test_unified_dashboard_overview_displays_asset_statistics(): void
    {
        $category = AssetCategory::factory()->create();

        // Create assets with different statuses
        Asset::factory()->count(10)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);
        Asset::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
        ]);
        Asset::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(UnifiedDashboardOverview::class);

        $widget->assertSuccessful()
            ->assertSee('10') // Available assets
            ->assertSee('5') // Loaned assets
            ->assertSee('2'); // Assets in maintenance
    }

    public function test_unified_dashboard_calculates_utilization_rate(): void
    {
        $category = AssetCategory::factory()->create();

        // Create 20 total assets: 15 loaned, 5 available
        Asset::factory()->count(15)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
        ]);
        Asset::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(UnifiedDashboardOverview::class);

        // Utilization rate should be 75% (15/20)
        $widget->assertSuccessful()
            ->assertSee('75');
    }

    // ========================================
    // Asset Utilization Widget Tests
    // ========================================

    public function test_asset_utilization_widget_displays_category_breakdown(): void
    {
        $laptopCategory = AssetCategory::factory()->create(['name' => 'Laptops']);
        $projectorCategory = AssetCategory::factory()->create(['name' => 'Projectors']);

        // Create assets in different categories
        Asset::factory()->count(10)->create([
            'category_id' => $laptopCategory->id,
            'status' => AssetStatus::LOANED,
        ]);
        Asset::factory()->count(5)->create([
            'category_id' => $projectorCategory->id,
            'status' => AssetStatus::LOANED,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(AssetUtilizationWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Laptops')
            ->assertSee('Projectors')
            ->assertSee('10')
            ->assertSee('5');
    }

    public function test_asset_utilization_widget_shows_availability_status(): void
    {
        $category = AssetCategory::factory()->create();

        Asset::factory()->count(8)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);
        Asset::factory()->count(12)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(AssetUtilizationWidget::class);

        $widget->assertSuccessful()
            ->assertSee('8') // Available
            ->assertSee('12'); // Loaned
    }

    // ========================================
    // Loan Approval Queue Widget Tests
    // ========================================

    public function test_loan_approval_queue_widget_displays_pending_approvals(): void
    {
        $division = Division::factory()->create();

        // Create pending approval applications
        $pendingApps = LoanApplication::factory()->count(5)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Create approved applications (should not appear)
        LoanApplication::factory()->count(3)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::APPROVED,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(LoanApprovalQueueWidget::class);

        $widget->assertSuccessful()
            ->assertSee('5'); // Only pending approvals

        foreach ($pendingApps as $app) {
            $widget->assertSee($app->application_number);
        }
    }

    public function test_loan_approval_queue_widget_shows_applicant_details(): void
    {
        $division = Division::factory()->create();

        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'applicant_name' => 'John Doe',
            'applicant_email' => 'john@example.com',
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(LoanApprovalQueueWidget::class);

        $widget->assertSuccessful()
            ->assertSee('John Doe')
            ->assertSee($application->application_number);
    }

    public function test_loan_approval_queue_widget_displays_priority_indicators(): void
    {
        $division = Division::factory()->create();

        $highPriorityApp = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'priority' => \App\Enums\LoanPriority::HIGH,
        ]);

        $normalPriorityApp = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => LoanStatus::UNDER_REVIEW,
            'priority' => \App\Enums\LoanPriority::NORMAL,
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(LoanApprovalQueueWidget::class);

        $widget->assertSuccessful()
            ->assertSee($highPriorityApp->application_number)
            ->assertSee($normalPriorityApp->application_number);
    }

    // ========================================
    // Unified Analytics Dashboard Tests
    // ========================================

    public function test_unified_analytics_dashboard_is_accessible_to_admin(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(UnifiedAnalyticsDashboard::getUrl());

        $response->assertSuccessful();
    }

    public function test_unified_analytics_dashboard_is_accessible_to_superuser(): void
    {
        $this->actingAs($this->superuser);

        $response = $this->get(UnifiedAnalyticsDashboard::getUrl());

        $response->assertSuccessful();
    }

    public function test_unified_analytics_dashboard_is_not_accessible_to_staff(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        $this->actingAs($staff);

        $response = $this->get(UnifiedAnalyticsDashboard::getUrl());

        $response->assertForbidden();
    }

    public function test_unified_analytics_dashboard_displays_cross_module_metrics(): void
    {
        $category = AssetCategory::factory()->create();
        $division = Division::factory()->create();

        // Create test data
        Asset::factory()->count(5)->create(['category_id' => $category->id]);
        LoanApplication::factory()->count(10)->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        Livewire::test(UnifiedAnalyticsDashboard::class)
            ->assertSuccessful()
            ->assertSee('5') // Total assets
            ->assertSee('10'); // Total loan applications
    }

    // ========================================
    // Widget Refresh and Real-time Updates Tests
    // ========================================

    public function test_dashboard_widgets_can_be_refreshed(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(UnifiedDashboardOverview::class);

        $widget->assertSuccessful();

        // Simulate refresh
        $widget->call('$refresh');

        $widget->assertSuccessful();
    }

    public function test_widgets_display_empty_state_when_no_data(): void
    {
        $this->actingAs($this->admin);

        // Test with no data in database
        $widget = Livewire::test(UnifiedDashboardOverview::class);

        $widget->assertSuccessful()
            ->assertSee('0'); // Should show zero counts
    }

    // ========================================
    // Widget Performance Tests
    // ========================================

    public function test_dashboard_widgets_load_efficiently_with_large_dataset(): void
    {
        $category = AssetCategory::factory()->create();
        $division = Division::factory()->create();

        // Create large dataset
        Asset::factory()->count(100)->create(['category_id' => $category->id]);
        LoanApplication::factory()->count(100)->create(['division_id' => $division->id]);

        $this->actingAs($this->admin);

        $startTime = microtime(true);

        $widget = Livewire::test(UnifiedDashboardOverview::class);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $widget->assertSuccessful();

        // Widget should load in less than 2 seconds
        $this->assertLessThan(2.0, $executionTime);
    }

    // ========================================
    // Widget Authorization Tests
    // ========================================

    public function test_widgets_respect_role_based_access_control(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        $this->actingAs($staff);

        // Staff should not be able to access admin widgets
        $response = $this->get('/admin');

        $response->assertForbidden();
    }

    public function test_approver_can_view_approval_queue_widget(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);
        $approver->assignRole('approver');

        $division = Division::factory()->create();
        LoanApplication::factory()->count(3)->create([
            'division_id' => $division->id,
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        $this->actingAs($approver);

        // Approvers should be able to see pending approvals
        $widget = Livewire::test(LoanApprovalQueueWidget::class);

        $widget->assertSuccessful()
            ->assertSee('3');
    }
}
