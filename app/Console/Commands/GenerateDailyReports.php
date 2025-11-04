<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutomatedReportService;
use Illuminate\Console\Command;

/**
 * Generate Daily Reports Command
 *
 * Automated command to generate and deliver daily reports.
 * Runs via scheduler to provide regular system analytics.
 *
 * Requirements: 13.2, 13.5, 9.1, 4.5
 */
class GenerateDailyReports extends Command
{
    protected $signature = 'reports:generate-daily
                           {--dry-run : Run without sending emails}
                           {--recipients=* : Override default recipients}';

    protected $description = 'Generate and deliver daily ICTServe reports';

    public function handle(AutomatedReportService $reportService): int
    {
        $this->info('Starting daily report generation...');

        try {
            if ($this->option('dry-run')) {
                $this->warn('Running in dry-run mode - no emails will be sent');

                // Generate report data only
                $reportData = $reportService->generateCustomReport([
                    'frequency' => 'daily',
                    'start_date' => now()->subDay()->startOfDay(),
                    'end_date' => now()->subDay()->endOfDay(),
                ]);

                $this->displayReportSummary($reportData);

                return self::SUCCESS;
            }

            $result = $reportService->generateDailyReport();

            $this->info('Daily report generated successfully!');
            $this->info("Total recipients: {$result['total_recipients']}");
            $this->info("Successful deliveries: {$result['successful_deliveries']}");

            if ($result['successful_deliveries'] < $result['total_recipients']) {
                $this->warn('Some deliveries failed. Check logs for details.');

                foreach ($result['delivery_results'] as $delivery) {
                    if ($delivery['status'] === 'failed') {
                        $this->error("Failed to deliver to {$delivery['recipient']}: {$delivery['error']}");
                    }
                }
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to generate daily report: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function displayReportSummary(array $reportData): void
    {
        $summary = $reportData['executive_summary'];

        $this->newLine();
        $this->info('=== DAILY REPORT SUMMARY ===');
        $this->info("System Health: {$summary['system_health']['score']}% ({$summary['system_health']['status']})");
        $this->info("Total Tickets: {$summary['key_metrics']['total_tickets']}");
        $this->info("Ticket Resolution Rate: {$summary['key_metrics']['ticket_resolution_rate']}%");
        $this->info("Total Loan Applications: {$summary['key_metrics']['total_loan_applications']}");
        $this->info("Loan Approval Rate: {$summary['key_metrics']['loan_approval_rate']}%");

        if (! empty($summary['critical_issues'])) {
            $this->newLine();
            $this->warn('=== CRITICAL ISSUES ===');
            if ($summary['critical_issues']['overdue_tickets'] > 0) {
                $this->warn("Overdue Tickets: {$summary['critical_issues']['overdue_tickets']}");
            }
            if ($summary['critical_issues']['overdue_loans'] > 0) {
                $this->warn("Overdue Loans: {$summary['critical_issues']['overdue_loans']}");
            }
            if ($summary['critical_issues']['maintenance_assets'] > 0) {
                $this->warn("Assets Needing Maintenance: {$summary['critical_issues']['maintenance_assets']}");
            }
        }

        $this->newLine();
    }
}
