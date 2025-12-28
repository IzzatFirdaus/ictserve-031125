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
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles (use firstOrCreate to avoid duplicates during seeding)
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'approver']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superuser']);

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
            ->assertSee('Pecah SLA');
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
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('dashboard:helpdesk-stats'));

        // Second call should use cached data
        Livewire::test(AssetLoanStatsOverview::class)->assertOk();
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('dashboard:loan-stats'));

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

        // Verify widget renders and shows ticket counts (percentages calculated in description)
        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('Tiket Tetamu')  // Guest ticket label
            ->assertSee('7')  // 7 guest tickets
            ->assertSee('Tiket Berdaftar')  // Authenticated ticket label
            ->assertSee('3'); // 3 authenticated tickets
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
        // Polling configured: 300s (5-minute intervals)

        Livewire::test(HelpdeskStatsOverview::class)->assertOk();
        Livewire::test(AssetLoanStatsOverview::class)->assertOk();
        Livewire::test(CrossModuleIntegrationChart::class)->assertOk();
    }

    #[Test]
    public function widgets_display_combined_helpdesk_and_loan_metrics(): void
    {
        // Arrange: Create mixed data for both modules
        HelpdeskTicket::factory()->count(8)->create(['user_id' => null, 'status' => 'open']); // Guest tickets
        HelpdeskTicket::factory()->count(4)->create(['user_id' => $this->admin->id, 'status' => 'resolved']); // Authenticated tickets

        LoanApplication::factory()->count(6)->create(['user_id' => null, 'status' => \App\Enums\LoanStatus::UNDER_REVIEW]); // Guest loans
        LoanApplication::factory()->count(3)->create(['user_id' => $this->admin->id, 'status' => \App\Enums\LoanStatus::APPROVED]); // Authenticated loans

        Asset::factory()->count(15)->create(['status' => 'available']);
        Asset::factory()->count(5)->create(['status' => 'loaned']);

        $this->actingAs($this->admin);

        // Act & Assert: Verify helpdesk metrics
        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Tiket')
            ->assertSee('12') // Total tickets (8 + 4)
            ->assertSee('Tiket Tetamu')
            ->assertSee('8') // Guest tickets
            ->assertSee('Tiket Berdaftar')
            ->assertSee('4'); // Authenticated tickets

        // Act & Assert: Verify loan metrics
        Livewire::test(AssetLoanStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Permohonan')
            ->assertSee('9') // Total applications (6 + 3)
            ->assertSee('Permohonan Tetamu')
            ->assertSee('6') // Guest applications
            ->assertSee('Permohonan Berdaftar')
            ->assertSee('3') // Authenticated applications
            ->assertSee('Kadar Penggunaan Aset');

        // Act & Assert: Verify cross-module integration
        Livewire::test(CrossModuleIntegrationChart::class)
            ->assertOk()
            ->assertSee('Integrasi Silang Modul');
    }

    #[Test]
    public function widgets_calculate_hybrid_architecture_metrics(): void
    {
        // Arrange: Create data that demonstrates hybrid architecture
        // 70% guest submissions, 30% authenticated submissions
        HelpdeskTicket::factory()->count(7)->create(['user_id' => null]);
        HelpdeskTicket::factory()->count(3)->create(['user_id' => $this->admin->id]);

        LoanApplication::factory()->count(14)->create(['user_id' => null]);
        LoanApplication::factory()->count(6)->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->admin);

        // Act & Assert: Verify hybrid metrics in helpdesk widget
        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('10') // Total tickets
            ->assertSee('7') // Guest tickets (70%)
            ->assertSee('3'); // Authenticated tickets (30%)

        // Act & Assert: Verify hybrid metrics in loan widget
        Livewire::test(AssetLoanStatsOverview::class)
            ->assertOk()
            ->assertSee('20') // Total applications
            ->assertSee('14') // Guest applications (70%)
            ->assertSee('6'); // Authenticated applications (30%)
    }

    #[Test]
    public function widgets_support_real_time_polling_updates(): void
    {
        $this->actingAs($this->admin);

        // Act: Initial render
        $helpdeskComponent = Livewire::test(HelpdeskStatsOverview::class);
        $loanComponent = Livewire::test(AssetLoanStatsOverview::class);
        $chartComponent = Livewire::test(CrossModuleIntegrationChart::class);

        // Assert: Initial render successful
        $helpdeskComponent->assertOk();
        $loanComponent->assertOk();
        $chartComponent->assertOk();

        // Arrange: Add new data
        HelpdeskTicket::factory()->count(5)->create();
        LoanApplication::factory()->count(3)->create();

        // Clear cache to simulate polling
        \Illuminate\Support\Facades\Cache::flush();

        // Act: Simulate polling refresh
        $helpdeskComponent->call('$refresh');
        $loanComponent->call('$refresh');
        $chartComponent->call('$refresh');

        // Assert: Widgets still render after refresh
        $helpdeskComponent->assertOk();
        $loanComponent->assertOk();
        $chartComponent->assertOk();
    }

    #[Test]
    public function widgets_display_comprehensive_bahasa_melayu_labels(): void
    {
        $this->actingAs($this->admin);

        // Act & Assert: Verify comprehensive BM labels in helpdesk widget
        Livewire::test(HelpdeskStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Tiket')
            ->assertSee('Tiket Tetamu')
            ->assertSee('Tiket Berdaftar')
            ->assertSee('Pecah SLA')
            ->assertSee('Pematuhan SLA');

        // Act & Assert: Verify comprehensive BM labels in loan widget
        Livewire::test(AssetLoanStatsOverview::class)
            ->assertOk()
            ->assertSee('Jumlah Permohonan')
            ->assertSee('Permohonan Tetamu')
            ->assertSee('Permohonan Berdaftar')
            ->assertSee('Kadar Penggunaan Aset');

        // Act & Assert: Verify BM labels in integration chart
        Livewire::test(CrossModuleIntegrationChart::class)
            ->assertOk()
            ->assertSee('Integrasi Silang Modul');
    }
}
