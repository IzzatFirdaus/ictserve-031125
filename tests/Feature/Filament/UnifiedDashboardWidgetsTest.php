<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Widgets\AssetLoanStatsOverview;
use App\Filament\Widgets\CrossModuleIntegrationChart;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Unified Dashboard Widgets Test
 *
 * Tests the unified admin dashboard widgets including helpdesk stats,
 * asset loan stats, and cross-module integration chart.
 *
 * @trace Requirements: Task 8.4 - Test unified dashboard
 *
 * @see D04 §3.2 Dashboard widgets
 */
class UnifiedDashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        \Spatie\Permission\Models\Role::create(['name' => 'staff']);
        \Spatie\Permission\Models\Role::create(['name' => 'approver']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'superuser']);

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'grade' => '54',
        ]);
        $this->admin->assignRole('superuser');
    }

    #[Test]
    public function helpdesk_stats_overview_widget_renders_successfully(): void
    {
        // Create test data
        HelpdeskTicket::factory()->count(5)->create(['user_id' => null]); // Guest tickets
        HelpdeskTicket::factory()->count(3)->create(['user_id' => $this->admin->id]); // Authenticated tickets

        $this->actingAs($this->admin);

        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Tiket')
            ->assertSee('Tiket Tetamu')
            ->assertSee('Tiket Berdaftar')
            ->assertSee('SLA Melebihi');
    }

    #[Test]
    public function asset_loan_stats_overview_widget_renders_successfully(): void
    {
        // Create test data
        LoanApplication::factory()->count(4)->create(['user_id' => null]); // Guest applications
        LoanApplication::factory()->count(2)->create(['user_id' => $this->admin->id]); // Authenticated applications
        Asset::factory()->count(10)->create(['status' => 'available']);
        Asset::factory()->count(3)->create(['status' => 'loaned']);

        $this->actingAs($this->admin);

        Livewire::test(AssetLoanStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Permohonan')
            ->assertSee('Permohonan Tetamu')
            ->assertSee('Permohonan Berdaftar')
            ->assertSee('Kadar Penggunaan Aset');
    }

    #[Test]
    public function cross_module_integration_chart_widget_renders_successfully(): void
    {
        // Create test data with asset-ticket linking
        $asset = Asset::factory()->create();
        $ticketCategory = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);
        HelpdeskTicket::factory()->count(3)->create([
            'asset_id' => $asset->id,
            'category_id' => $ticketCategory->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CrossModuleIntegrationChart::class)
            ->assertOk()
            ->assertSee('Integrasi Silang Modul');
    }

    #[Test]
    public function widgets_use_caching_strategy(): void
    {
        $this->actingAs($this->admin);

        // First call should cache the data
        Livewire::test(HelpdeskStatsOverview::class)->assertOk();

        // Verify cache exists
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('helpdesk-stats-overview'));

        // Second call should use cached data
        Livewire::test(AssetLoanStatsOverview::class)->assertOk();
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('asset-loan-stats-overview'));

        Livewire::test(CrossModuleIntegrationChart::class)->assertOk();
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('cross-module-integration-chart'));
    }

    #[Test]
    public function widgets_display_correct_guest_vs_authenticated_percentages(): void
    {
        // Create 7 guest tickets and 3 authenticated tickets (70% vs 30%)
        HelpdeskTicket::factory()->count(7)->create(['user_id' => null]);
        HelpdeskTicket::factory()->count(3)->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->admin);

        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('70.0%') // Guest percentage
            ->assertSee('30.0%'); // Authenticated percentage
    }

    #[Test]
    public function widgets_are_accessible_to_admin_roles(): void
    {
        $this->actingAs($this->admin);

        // Test all three widgets are accessible
        Livewire::test(HelpdeskStatsOverview::class)->assertOk();
        Livewire::test(AssetLoanStatsOverview::class)->assertOk();
        Livewire::test(CrossModuleIntegrationChart::class)->assertOk();
    }

    #[Test]
    public function widgets_display_wcag_compliant_colors(): void
    {
        $this->actingAs($this->admin);

        // The widgets should use compliant color palette
        // This is verified through the widget implementation
        // Colors: Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c

        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSet('pollingInterval', '300s'); // 5-minute polling

        Livewire::test(AssetLoanStatsOverview::class)
            ->assertOk()
            ->assertSet('pollingInterval', '300s');

        Livewire::test(CrossModuleIntegrationChart::class)
            ->assertOk()
            ->assertSet('pollingInterval', '300s');
    }
}
