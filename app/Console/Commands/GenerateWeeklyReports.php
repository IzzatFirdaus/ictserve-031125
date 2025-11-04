<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutomatedReportService;
use Illuminate\Console\Command;

/**
 * Generate Weekly Reports Command
 *
 * Automated command to generate and deliver weekly reports with trend analysis.
 * Provides comprehensive weekly performance overview.
 *
 * Requirements: 13.2, 13.5, 9.1, 4.5
 */
class GenerateWeeklyReports extends Command
{
    protected $signature = 'reports:generate-weekly
                           {--dry-run : Run without sending emails}
                           {--week= : Specific week to generate (YYYY-MM-DD format)}';

    protected $description = 'Generate and deliver weekly ICTServe reports with trend analysis';

    public function handle(AutomatedReportService $reportService): int
    {
        $this->info('Starting weekly report generation...');

        try {
            if ($this->option('dry-run')) {
                $this->warn('Running in dry-run mode - no emails will be sent');

                $weekStart = $this->option('week')
                    ? \DateTime::createFromFormat('Y-m-d', $this->option('week'))->startOfWeek()
                    : now()->subWeek()->startOfWeek();

                $reportData = $reportService->generateCustomReport([
                    'frequency' => 'weekly',
                    'start_date' => $weekStart,
                    'end_date' => $weekStart->copy()->endOfWeek(),
                ]);

                $this->displayWeeklyReportSummary($reportData);

                return self::SUCCESS;
            }

            $result = $reportService->generateWeeklyReport();

            $this->info('Weekly report generated successfully!');
            $this->info("Total recipients: {$result['total_recipients']}");
            $this->info("Successful deliveries: {$result['successful_deliveries']}");

            if ($result['successful_deliveries'] < $result['total_recipients']) {
                $this->warn('Some deliveries failed. Check logs for details.');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to generate weekly report: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function displayWeeklyReportSummary(array $reportData): void
    {
        $summary = $reportData['executive_summary'];
        $period = $reportData['report_info']['period'];

        $this->newLine();
        $this->info('=== WEEKLY REPORT SUMMARY ===');
        $this->info("Period: {$period['start']} to {$period['end']} ({$period['days']} days)");
        $this->info("System Health: {$summary['system_health']['score']}% ({$summary['system_health']['status']})");

        $this->newLine();
        $this->info('=== WEEKLY METRICS ===');
        $this->info("Total Tickets: {$summary['key_metrics']['total_tickets']}");
        $this->info("Resolution Rate: {$summary['key_metrics']['ticket_resolution_rate']}%");
        $this->info("Loan Applications: {$summary['key_metrics']['total_loan_applications']}");
        $this->info("Approval Rate: {$summary['key_metrics']['loan_approval_rate']}%");
        $this->info("Asset Utilization: {$summary['key_metrics']['asset_utilization_rate']}%");

        if (isset($reportData['trend_analysis'])) {
            $this->newLine();
            $this->info('=== TREND ANALYSIS ===');
            $this->info('Weekly trends and growth patterns included in full report');
        }

        if (! empty($reportData['recommendations'])) {
            $this->newLine();
            $this->warn('=== RECOMMENDATIONS ===');
            foreach ($reportData['recommendations'] as $rec) {
                $this->warn("• {$rec['title']} (Priority: {$rec['priority']})");
            }
        }

        $this->newLine();
    }
}
