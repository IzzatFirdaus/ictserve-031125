<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ReportGenerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Report Generation Service Tests
 *
 * @trace D03-FR-013.1 (Reporting and Analytics)
 * @trace D03-FR-013.2 (Automated Report Generation)
 */
class ReportGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportGenerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReportGenerationService::class);
    }

    public function test_generates_daily_loan_statistics(): void
    {
        LoanApplication::factory()->count(5)->create([
            'created_at' => now(),
            'status' => 'approved',
        ]);

        $report = $this->service->generateLoanStatistics('daily');

        $this->assertArrayHasKey('period', $report);
        $this->assertArrayHasKey('total_applications', $report);
        $this->assertArrayHasKey('approved_count', $report);
        $this->assertEquals(5, $report['total_applications']);
    }

    public function test_generates_weekly_loan_statistics(): void
    {
        LoanApplication::factory()->count(10)->create([
            'created_at' => now()->subDays(3),
        ]);

        $report = $this->service->generateLoanStatistics('weekly');

        $this->assertEquals('weekly', $report['period']);
        $this->assertGreaterThanOrEqual(10, $report['total_applications']);
    }

    public function test_generates_monthly_loan_statistics(): void
    {
        LoanApplication::factory()->count(15)->create([
            'created_at' => now()->subDays(10),
        ]);

        $report = $this->service->generateLoanStatistics('monthly');

        $this->assertEquals('monthly', $report['period']);
        $this->assertArrayHasKey('approval_rate', $report);
    }

    public function test_calculates_approval_rate_correctly(): void
    {
        LoanApplication::factory()->count(8)->create(['status' => 'approved']);
        LoanApplication::factory()->count(2)->create(['status' => 'rejected']);

        $report = $this->service->generateLoanStatistics('daily');

        $this->assertEquals(80.0, $report['approval_rate']);
    }

    public function test_generates_asset_utilization_report(): void
    {
        Asset::factory()->count(5)->create(['status' => 'available']);
        Asset::factory()->count(3)->create(['status' => 'loaned']);
        Asset::factory()->count(2)->create(['status' => 'maintenance']);

        $report = $this->service->generateAssetUtilizationReport();

        $this->assertArrayHasKey('total_assets', $report);
        $this->assertArrayHasKey('available_count', $report);
        $this->assertArrayHasKey('loaned_count', $report);
        $this->assertEquals(10, $report['total_assets']);
        $this->assertEquals(30.0, $report['utilization_rate']);
    }

    public function test_generates_overdue_report(): void
    {
        LoanApplication::factory()->count(3)->create([
            'status' => 'approved',
            'return_by' => now()->subDays(5),
        ]);

        $report = $this->service->generateOverdueReport();

        $this->assertArrayHasKey('overdue_count', $report);
        $this->assertGreaterThanOrEqual(3, $report['overdue_count']);
    }

    public function test_handles_empty_data_gracefully(): void
    {
        $report = $this->service->generateLoanStatistics('daily');

        $this->assertEquals(0, $report['total_applications']);
        $this->assertEquals(0.0, $report['approval_rate']);
    }
}
