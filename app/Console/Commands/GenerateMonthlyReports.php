<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutomatedReportService;
use Illuminate\Console\Command;

/**
 * Generate Monthly Reports Command
 *
 * Automated command to generate comprehensive monthly reports.
 * Includes detailed analytics, trends, and strategic recommendations.
 *
 * Requirements: 13.2, 13.5, 9.1, 4.5
 */
class GenerateMonthlyReports extends Command
{
    protected $signature = 'reports:generate-monthly
                           {--dry-run : Run without sending emails}
                           {--month= : Specific month to generate (YYYY-MM format)}
                           {--include-forecasts : Include forecasting data}';

    protected $description = 'Generate comprehensive monthly ICTServe reports with analytics and forecasts';

    public function handle(AutomatedReportService $reportService): int
    {
        $this->info('Starting monthly report generation...');

        try {
            if ($this->option('dry-run')) {
                $this->warn('Running in dry-run mode - no emails will be sent');

                $monthStart = $this->option('month')
                    ? \DateTime::createFromFormat('Y-m', $this->option('month'))->startOfMonth()
                    : now()->subMonth()->startOfMonth();

                $reportData = $reportService->generateCustomReport([
                    'frequency' => 'monthly',
                    'start_date' => $monthStart,
                    'end_date' => $monthStart->copy()->endOfMonth(),
                    'include_forecasts' => $this->option('include-forecasts'),
                ]);

                $this->displayMonthlyReportSummary($reportData);

                return self::SUCCESS;
            }

            $result = $reportService->generateMonthlyReport();

            $this->info('Monthly report generated successfully!');
            $this->info("Total recipients: {$result['total_recipients']}");
            $this->info("Successful deliveries: {$result['successful_deliveries']}");

            if ($result['successful_deliveries'] < $result['total_recipients']) {
                $this->warn('Some deliveries failed. Check logs for details.');
            }

            // Display key insights
            $this->displayMonthlyInsights($result['report_data']);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to generate monthly report: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function displayMonthlyReportSummary(array $reportData): void
    {
        $summary = $reportData['executive_summary'];
        $period = $reportData['report_info']['period'];

        $this->newLine();
        $this->info('=== MONTHLY REPORT SUMMARY ===');
        $this->info("Period: {$period['start']} to {$period['end']} ({$period['days']} days)");
        $this->info("System Health: {$summary['system_health']['score']}% ({$summary['system_health']['status']})");

        $this->newLine();
        $this->info('=== MONTHLY PERFORMANCE ===');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tickets', $summary['key_metrics']['total_tickets']],
                ['Resolution Rate', $summary['key_metrics']['ticket_resolution_rate'].'%'],
                ['Loan Applications', $summary['key_metrics']['total_loan_applications']],
                ['Approval Rate', $summary['key_metrics']['loan_approval_rate'].'%'],
                ['Asset Utilization', $summary['key_metrics']['asset_utilization_rate'].'%'],
            ]
        );

        if (isset($reportData['trend_analysis'])) {
            $this->newLine();
            $this->info('=== TREND ANALYSIS ===');
            $trends = $reportData['trend_analysis'];
            if (isset($trends['growth_rates'])) {
                $this->info('Growth rates and seasonal patterns analyzed');
            }
            if (isset($trends['forecasts'])) {
                $this->info('Forecasts for next month included');
            }
        }

        $this->displayRecommendations($reportData);
        $this->newLine();
    }

    private function displayMonthlyInsights(array $reportData): void
    {
        $this->newLine();
        $this->info('=== KEY MONTHLY INSIGHTS ===');

        $summary = $reportData['executive_summary'];

        // System health trend
        $healthScore = $summary['system_health']['score'];
        if ($healthScore >= 90) {
            $this->info('✅ Excellent system performance this month');
        } elseif ($healthScore >= 75) {
            $this->info('✅ Good system performance with room for improvement');
        } else {
            $this->warn('⚠️  System performance needs attention');
        }

        // Highlight achievements
        if (! empty($summary['highlights'])) {
            $this->newLine();
            $this->info('🌟 ACHIEVEMENTS:');
            foreach ($summary['highlights'] as $highlight) {
                $this->info("  • {$highlight}");
            }
        }

        // Critical issues
        $issues = $summary['critical_issues'];
        $totalIssues = $issues['overdue_tickets'] + $issues['overdue_loans'] + $issues['maintenance_assets'];

        if ($totalIssues > 0) {
            $this->newLine();
            $this->warn("⚠️  {$totalIssues} items require immediate attention");
        } else {
            $this->info('✅ No critical issues requiring immediate attention');
        }

        $this->newLine();
    }

    private function displayRecommendations(array $reportData): void
    {
        if (! empty($reportData['recommendations'])) {
            $this->newLine();
            $this->warn('=== STRATEGIC RECOMMENDATIONS ===');

            foreach ($reportData['recommendations'] as $rec) {
                $priority = strtoupper($rec['priority']);
                $icon = match ($rec['priority']) {
                    'high' => '🔴',
                    'medium' => '🟡',
                    'low' => '🟢',
                    default => '⚪',
                };

                $this->warn("{$icon} [{$priority}] {$rec['title']}");
                $this->line("   {$rec['description']}");

                if (! empty($rec['actions'])) {
                    foreach ($rec['actions'] as $action) {
                        $this->line("   → {$action}");
                    }
                }
                $this->newLine();
            }
        }
    }
}
