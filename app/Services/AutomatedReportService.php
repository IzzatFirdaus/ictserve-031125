<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Automated Report Service
 *
 * Handles scheduled report generation and delivery for loan and helpdesk analytics.
 * Supports daily, weekly, and monthly report schedules with email delivery.
 *
 * Requirements: 13.2, 13.5, 9.1, 4.5
 */
class AutomatedReportService
{
    public function __construct(
        private UnifiedAnalyticsService $analyticsService,
        private HelpdeskReportService $helpdeskService,
        private ReportExportService $exportService
    ) {}

    /**
     * Generate and deliver daily reports
     */
    public function generateDailyReport(): array
    {
        $startDate = now()->subDay()->startOfDay();
        $endDate = now()->subDay()->endOfDay();

        $reportData = $this->generateReportData('daily', $startDate, $endDate);
        $recipients = $this->getReportRecipients('daily');

        return $this->deliverReport($reportData, $recipients, 'daily');
    }

    /**
     * Generate and deliver weekly reports
     */
    public function generateWeeklyReport(): array
    {
        $startDate = now()->subWeek()->startOfWeek();
        $endDate = now()->subWeek()->endOfWeek();

        $reportData = $this->generateReportData('weekly', $startDate, $endDate);
        $recipients = $this->getReportRecipients('weekly');

        return $this->deliverReport($reportData, $recipients, 'weekly');
    }

    /**
     * Generate and deliver monthly reports
     */
    public function generateMonthlyReport(): array
    {
        $startDate = now()->subMonth()->startOfMonth();
        $endDate = now()->subMonth()->endOfMonth();

        $reportData = $this->generateReportData('monthly', $startDate, $endDate);
        $recipients = $this->getReportRecipients('monthly');

        return $this->deliverReport($reportData, $recipients, 'monthly');
    }

    /**
     * Generate custom report with filters
     */
    public function generateCustomReport(array $options): array
    {
        $startDate = $options['start_date'] ?? now()->subMonth();
        $endDate = $options['end_date'] ?? now();
        $frequency = $options['frequency'] ?? 'custom';
        $recipients = $options['recipients'] ?? [];

        $reportData = $this->generateReportData($frequency, $startDate, $endDate, $options);

        if (! empty($recipients)) {
            return $this->deliverReport($reportData, $recipients, $frequency);
        }

        return $reportData;
    }

    /**
     * Generate comprehensive report data
     */
    private function generateReportData(string $frequency, \DateTime $startDate, \DateTime $endDate, array $options = []): array
    {
        $unifiedMetrics = $this->analyticsService->getDashboardMetrics($startDate, $endDate);
        $helpdeskDetails = $this->helpdeskService->getComprehensiveReportData($startDate, $endDate);

        $reportData = [
            'report_info' => [
                'title' => $this->getReportTitle($frequency),
                'frequency' => $frequency,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'days' => $startDate->diffInDays($endDate) + 1,
                ],
                'generated_at' => now()->toDateTimeString(),
                'generated_by' => 'Sistem Automatik ICTServe',
            ],
            'executive_summary' => $this->generateExecutiveSummary($unifiedMetrics),
            'unified_metrics' => $unifiedMetrics,
            'helpdesk_details' => $helpdeskDetails,
            'loan_statistics' => $this->generateLoanStatistics($startDate, $endDate),
            'asset_utilization' => $this->generateAssetUtilizationReport($startDate, $endDate),
            'cross_module_integration' => $this->generateIntegrationReport($startDate, $endDate),
            'recommendations' => $this->generateRecommendations($unifiedMetrics),
        ];

        // Add trend analysis for weekly/monthly reports
        if (in_array($frequency, ['weekly', 'monthly'])) {
            $reportData['trend_analysis'] = $this->generateTrendAnalysis($frequency, $startDate, $endDate);
        }

        return $reportData;
    }

    /**
     * Generate executive summary
     */
    private function generateExecutiveSummary(array $metrics): array
    {
        $helpdesk = $metrics['helpdesk'];
        $loans = $metrics['loans'];
        $assets = $metrics['assets'];
        $summary = $metrics['summary'];

        return [
            'system_health' => [
                'score' => $summary['overall_system_health'],
                'status' => $this->getHealthStatus($summary['overall_system_health']),
                'description' => $this->getHealthDescription($summary['overall_system_health']),
            ],
            'key_metrics' => [
                'total_tickets' => $helpdesk['total_tickets'],
                'ticket_resolution_rate' => $helpdesk['resolution_rate'],
                'total_loan_applications' => $loans['total_applications'],
                'loan_approval_rate' => $loans['approval_rate'],
                'asset_utilization_rate' => $assets['utilization_rate'],
            ],
            'critical_issues' => [
                'overdue_tickets' => $helpdesk['overdue_tickets'],
                'overdue_loans' => $loans['overdue_loans'],
                'maintenance_assets' => $assets['maintenance_assets'],
            ],
            'highlights' => $this->generateHighlights($metrics),
        ];
    }

    /**
     * Generate loan statistics
     */
    private function generateLoanStatistics(\DateTime $startDate, \DateTime $endDate): array
    {
        // This would use LoanApplication model to generate detailed statistics
        return [
            'application_trends' => [],
            'approval_analysis' => [],
            'asset_demand' => [],
            'user_activity' => [],
        ];
    }

    /**
     * Generate asset utilization report
     */
    private function generateAssetUtilizationReport(\DateTime $startDate, \DateTime $endDate): array
    {
        return [
            'utilization_by_category' => [],
            'high_demand_assets' => [],
            'underutilized_assets' => [],
            'maintenance_summary' => [],
        ];
    }

    /**
     * Generate integration report
     */
    private function generateIntegrationReport(\DateTime $startDate, \DateTime $endDate): array
    {
        return [
            'integration_volume' => [],
            'damage_reports' => [],
            'maintenance_requests' => [],
            'efficiency_metrics' => [],
        ];
    }

    /**
     * Generate trend analysis
     */
    private function generateTrendAnalysis(string $frequency, \DateTime $startDate, \DateTime $endDate): array
    {
        $periods = $frequency === 'weekly' ? 4 : 6; // 4 weeks or 6 months
        $trends = $this->analyticsService->getMonthlyTrends($periods);

        return [
            'chart_data' => $trends,
            'growth_rates' => $this->calculateGrowthRates($trends),
            'seasonal_patterns' => $this->identifySeasonalPatterns($trends),
            'forecasts' => $this->generateSimpleForecasts($trends),
        ];
    }

    /**
     * Generate recommendations based on metrics
     */
    private function generateRecommendations(array $metrics): array
    {
        $recommendations = [];

        // System health recommendations
        if ($metrics['summary']['overall_system_health'] < 75) {
            $recommendations[] = [
                'category' => 'system_health',
                'priority' => 'high',
                'title' => 'Tingkatkan Kesihatan Sistem',
                'description' => 'Skor kesihatan sistem di bawah 75%. Perlu tindakan segera untuk meningkatkan prestasi.',
                'actions' => [
                    'Semak tiket tertunggak dan proses dengan segera',
                    'Tingkatkan kadar kelulusan pinjaman',
                    'Pastikan aset dalam keadaan baik',
                ],
            ];
        }

        // Helpdesk recommendations
        if ($metrics['helpdesk']['overdue_tickets'] > 5) {
            $recommendations[] = [
                'category' => 'helpdesk',
                'priority' => 'medium',
                'title' => 'Kurangkan Tiket Tertunggak',
                'description' => "Terdapat {$metrics['helpdesk']['overdue_tickets']} tiket tertunggak yang perlu perhatian.",
                'actions' => [
                    'Tugaskan lebih ramai staf untuk mengendalikan tiket',
                    'Semak SLA dan proses kerja',
                    'Berikan latihan tambahan kepada staf',
                ],
            ];
        }

        // Asset recommendations
        if ($metrics['assets']['utilization_rate'] < 40) {
            $recommendations[] = [
                'category' => 'assets',
                'priority' => 'low',
                'title' => 'Tingkatkan Penggunaan Aset',
                'description' => 'Kadar penggunaan aset rendah. Pertimbangkan strategi untuk meningkatkan penggunaan.',
                'actions' => [
                    'Promosikan ketersediaan aset kepada staf',
                    'Semak keperluan aset sebenar',
                    'Pertimbangkan untuk mengurangkan inventori',
                ],
            ];
        }

        return $recommendations;
    }

    /**
     * Get report recipients based on frequency
     */
    private function getReportRecipients(string $frequency): Collection
    {
        // Get users with appropriate roles for report delivery
        return User::whereHas('roles', function ($query) use ($frequency) {
            $roles = match ($frequency) {
                'daily' => ['admin', 'superuser'],
                'weekly' => ['admin', 'superuser', 'approver'],
                'monthly' => ['admin', 'superuser', 'approver', 'staff'],
                default => ['admin', 'superuser'],
            };
            $query->whereIn('name', $roles);
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Deliver report to recipients
     */
    private function deliverReport(array $reportData, Collection $recipients, string $frequency): array
    {
        $deliveryResults = [];

        foreach ($recipients as $recipient) {
            try {
                // Generate report files
                $files = $this->exportService->generateReportFiles($reportData, [
                    'formats' => ['pdf', 'excel'],
                    'recipient' => $recipient,
                ]);

                // Send email with attachments
                Mail::to($recipient->email)->send(
                    new \App\Mail\AutomatedReportMail($reportData, $files, $frequency)
                );

                $deliveryResults[] = [
                    'recipient' => $recipient->email,
                    'status' => 'success',
                    'files' => array_keys($files),
                ];

                // Clean up temporary files
                foreach ($files as $file) {
                    if (Storage::exists($file)) {
                        Storage::delete($file);
                    }
                }

            } catch (\Exception $e) {
                $deliveryResults[] = [
                    'recipient' => $recipient->email,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'report_data' => $reportData,
            'delivery_results' => $deliveryResults,
            'total_recipients' => $recipients->count(),
            'successful_deliveries' => collect($deliveryResults)->where('status', 'success')->count(),
        ];
    }

    /**
     * Helper methods
     */
    private function getReportTitle(string $frequency): string
    {
        return match ($frequency) {
            'daily' => 'Laporan Harian ICTServe',
            'weekly' => 'Laporan Mingguan ICTServe',
            'monthly' => 'Laporan Bulanan ICTServe',
            default => 'Laporan Khas ICTServe',
        };
    }

    private function getHealthStatus(float $score): string
    {
        return match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 60 => 'fair',
            default => 'poor',
        };
    }

    private function getHealthDescription(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Sistem beroperasi dengan cemerlang. Semua metrik dalam keadaan optimum.',
            $score >= 75 => 'Sistem beroperasi dengan baik. Terdapat ruang untuk penambahbaikan kecil.',
            $score >= 60 => 'Sistem beroperasi secara sederhana. Perlu perhatian pada beberapa aspek.',
            default => 'Sistem memerlukan perhatian segera. Terdapat isu kritikal yang perlu diselesaikan.',
        };
    }

    private function generateHighlights(array $metrics): array
    {
        $highlights = [];

        // Add positive highlights
        if ($metrics['helpdesk']['resolution_rate'] >= 85) {
            $highlights[] = "Kadar penyelesaian tiket cemerlang: {$metrics['helpdesk']['resolution_rate']}%";
        }

        if ($metrics['loans']['approval_rate'] >= 80) {
            $highlights[] = "Kadar kelulusan pinjaman tinggi: {$metrics['loans']['approval_rate']}%";
        }

        if ($metrics['assets']['utilization_rate'] >= 70) {
            $highlights[] = "Penggunaan aset optimum: {$metrics['assets']['utilization_rate']}%";
        }

        return $highlights;
    }

    private function calculateGrowthRates(array $trends): array
    {
        // Simple growth rate calculation
        return [
            'helpdesk_growth' => 0, // Placeholder
            'loan_growth' => 0,     // Placeholder
            'integration_growth' => 0, // Placeholder
        ];
    }

    private function identifySeasonalPatterns(array $trends): array
    {
        // Placeholder for seasonal pattern analysis
        return [
            'peak_months' => [],
            'low_months' => [],
            'patterns' => [],
        ];
    }

    private function generateSimpleForecasts(array $trends): array
    {
        // Simple forecast based on recent trends
        return [
            'next_month_tickets' => 0,
            'next_month_loans' => 0,
            'confidence' => 'medium',
        ];
    }
}
