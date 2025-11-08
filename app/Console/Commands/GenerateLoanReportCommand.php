<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ReportGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Generate Loan Report Command
 *
 * Automated command for generating and emailing loan reports.
 *
 * @trace D03-FR-013.2 (Automated Report Generation)
 */
class GenerateLoanReportCommand extends Command
{
    protected $signature = 'loan:generate-report {period=monthly : Report period (daily, weekly, monthly)}';
    protected $description = 'Generate loan statistics report';

    public function handle(ReportGenerationService $service): int
    {
        $period = $this->argument('period');

        $this->info("Generating {$period} loan report...");

        $loanStats = $service->generateLoanStatisticsReport($period);
        $assetStats = $service->generateAssetUtilizationReport();
        $overdueReport = $service->generateOverdueReport();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Applications', $loanStats['total_applications']],
                ['Approved', $loanStats['approved_applications']],
                ['Rejected', $loanStats['rejected_applications']],
                ['Pending', $loanStats['pending_applications']],
                ['Avg Approval Time (hours)', $loanStats['average_approval_time'] ?? 'N/A'],
            ]
        );

        $this->newLine();
        $this->info('Asset Utilization:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Assets', $assetStats['total_assets']],
                ['Available', $assetStats['available_assets']],
                ['Loaned', $assetStats['loaned_assets']],
                ['Utilization Rate', $assetStats['utilization_rate'] . '%'],
            ]
        );

        if ($overdueReport->isNotEmpty()) {
            $this->newLine();
            $this->warn("Overdue Loans: {$overdueReport->count()}");
        }

        $this->info('Report generated successfully!');

        return self::SUCCESS;
    }
}
