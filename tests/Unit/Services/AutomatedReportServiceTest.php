<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Services\AutomatedReportService;
use App\Services\HelpdeskReportService;
use App\Services\ReportExportService;
use App\Services\UnifiedAnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomatedReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_custom_report_includes_detailed_sections(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-11-06 09:00:00'));

        $start = now()->subDays(7)->startOfDay();
        $end = now()->endOfDay();

        $divisionA = Division::factory()->ict()->create();
        $divisionB = Division::factory()->finance()->create();

        $loanApproved = LoanApplication::factory()
            ->approved()
            ->create([
                'division_id' => $divisionA->id,
                'total_value' => 15000,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);

        $loanUnderReview = LoanApplication::factory()
            ->underReview()
            ->create([
                'division_id' => $divisionB->id,
                'total_value' => 9000,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

        LoanApplication::factory()
            ->submitted()
            ->create([
                'division_id' => $divisionA->id,
                'total_value' => 5000,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

        $assetLoaned = Asset::factory()->loaned()->create(['maintenance_tickets_count' => 1]);
        $assetAvailable = Asset::factory()->available()->create();
        $assetMaintenance = Asset::factory()->maintenance()->create();
        $assetDamaged = Asset::factory()->damaged()->create();

        collect([
            $assetLoaned,
            $assetAvailable,
            $assetMaintenance,
        ])->each(function (Asset $asset) use ($loanApproved): void {
            LoanItem::factory()->create([
                'loan_application_id' => $loanApproved->id,
                'asset_id' => $asset->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
        });

        LoanTransaction::factory()->count(2)->create([
            'loan_application_id' => $loanApproved->id,
            'asset_id' => $assetLoaned->id,
            'transaction_type' => TransactionType::ISSUE,
            'processed_at' => now()->subDay(),
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'resolved',
            'division_id' => $divisionA->id,
            'created_at' => now()->subDays(3),
            'resolved_at' => now()->subDay(),
            'sla_resolution_due_at' => now()->subDays(2),
        ]);

        CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApproved->id,
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
            'processed_at' => now()->subHours(3),
            'created_at' => now()->subHours(5),
        ]);

        CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApproved->id,
            'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
            'processed_at' => now()->subHour(),
            'created_at' => now()->subHours(4),
        ]);

        CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanUnderReview->id,
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'created_at' => now()->subDay(),
            'processed_at' => null,
        ]);

        $helpdeskService = new HelpdeskReportService();
        $service = new AutomatedReportService(
            new UnifiedAnalyticsService($helpdeskService),
            $helpdeskService,
            new ReportExportService()
        );

        $report = $service->generateCustomReport([
            'start_date' => $start,
            'end_date' => $end,
            'frequency' => 'weekly',
        ]);

        Carbon::setTestNow();

        $this->assertArrayHasKey('loan_statistics', $report);
        $this->assertTrue(
            collect($report['loan_statistics']['application_trends'])->contains(fn (array $point): bool => $point['count'] > 0)
        );
        $this->assertNotEmpty($report['loan_statistics']['approval_analysis']);
        $this->assertNotEmpty($report['loan_statistics']['asset_demand']);
        $this->assertNotEmpty($report['loan_statistics']['user_activity']);

        $this->assertArrayHasKey('asset_utilization', $report);
        $this->assertNotEmpty($report['asset_utilization']['high_demand_assets']);
        $this->assertNotEmpty($report['asset_utilization']['maintenance_summary']);

        $this->assertArrayHasKey('cross_module_integration', $report);
        $this->assertEquals(3, array_sum($report['cross_module_integration']['integration_volume']));
        $this->assertEquals(1, $report['cross_module_integration']['maintenance_requests']['total']);
        $this->assertGreaterThan(0, $report['cross_module_integration']['maintenance_requests']['avg_processing_hours']);

        $this->assertArrayHasKey('trend_analysis', $report);
        $this->assertNotEmpty($report['trend_analysis']['chart_data']['datasets']);
        $this->assertArrayHasKey('Tiket Helpdesk', $report['trend_analysis']['growth_rates']);
        $this->assertArrayHasKey('Tiket Helpdesk', $report['trend_analysis']['forecasts']);
    }
}
