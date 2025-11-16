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
            /** @var array{
             *     helpdesk_stats: array{total_tickets: int, open_tickets: int, resolved_this_month: int, avg_resolution_time: float},
             *     loan_stats: array{total_applications: int, active_loans: int, overdue_returns: int, utilization_rate: float},
             *     asset_stats: array{total_assets: int, available_assets: int, maintenance_assets: int, most_requested: array<int, array{name: string, asset_code: string, request_count: int}>},
             *     sla_compliance: array{helpdesk_sla: float, loan_approval_sla: float}
             * } $reportData */
            $reportData = $reportService->generateSystemUsageStats();
            $this->displayReportSummary($reportData);

            if ($this->option('dry-run')) {
                $this->warn('Dry-run complete - scheduled reports not processed.');

                return self::SUCCESS;
            }

            $result = $reportService->processDueReports();

            $processed = (int) ($result['processed'] ?? 0);
            $failed = (int) ($result['failed'] ?? 0);

            $this->info('Daily report generation completed.');
            $this->info("Processed schedules: {$processed}");
            $this->info("Failed schedules: {$failed}");

            if (! empty($result['errors'])) {
                $this->warn('Errors encountered while generating some reports:');
                foreach ($result['errors'] as $error) {
                    $this->line(" - {$error['schedule_name']}: {$error['error']}");
                }
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to generate daily report: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    /**
     * @param  array{
     *     helpdesk_stats: array{total_tickets: int, open_tickets: int, resolved_this_month: int, avg_resolution_time: float},
     *     loan_stats: array{total_applications: int, active_loans: int, overdue_returns: int, utilization_rate: float},
     *     asset_stats: array{total_assets: int, available_assets: int, maintenance_assets: int, most_requested: array<int, array{name: string, asset_code: string, request_count: int}>},
     *     sla_compliance: array{helpdesk_sla: float, loan_approval_sla: float}
     * }  $reportData
     */
    private function displayReportSummary(array $reportData): void
    {
        $helpdesk = $reportData['helpdesk_stats'];
        $loan = $reportData['loan_stats'];
        $asset = $reportData['asset_stats'];
        $sla = $reportData['sla_compliance'];

        $this->newLine();
        $this->info('=== DAILY REPORT SUMMARY ===');
        $this->info("Helpdesk Tickets: {$helpdesk['total_tickets']} (Open: {$helpdesk['open_tickets']})");
        $this->info("Resolved This Month: {$helpdesk['resolved_this_month']}, Avg Resolution Time: {$helpdesk['avg_resolution_time']} hrs");
        $this->info("Loan Applications: {$loan['total_applications']} (Active: {$loan['active_loans']}, Overdue returns: {$loan['overdue_returns']})");
        $this->info("Asset Utilization: {$loan['utilization_rate']}%");
        $this->info("Assets: {$asset['total_assets']} total, {$asset['maintenance_assets']} in maintenance");
        $this->info("SLA Compliance - Helpdesk: {$sla['helpdesk_sla']}%, Loan Approval: {$sla['loan_approval_sla']}%");

        if (! empty($asset['most_requested'])) {
            $this->newLine();
            $this->info('Top Requested Assets:');
            foreach ($asset['most_requested'] as $assetItem) {
                $this->line(" - {$assetItem['name']} ({$assetItem['asset_code']}): {$assetItem['request_count']} requests");
            }
        }

        $this->newLine();
    }
}
