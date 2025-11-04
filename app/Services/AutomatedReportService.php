<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $start = Carbon::instance($startDate)->startOfDay();
        $end = Carbon::instance($endDate)->endOfDay();

        $unifiedMetrics = $this->analyticsService->getDashboardMetrics($start, $end);
        $helpdeskDetails = $this->helpdeskService->getComprehensiveReportData($start, $end);

        $reportData = [
            'report_info' => [
                'title' => $this->getReportTitle($frequency),
                'frequency' => $frequency,
                'period' => [
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                    'days' => $start->diffInDays($end) + 1,
                ],
                'generated_at' => now()->toDateTimeString(),
                'generated_by' => 'Sistem Automatik ICTServe',
            ],
            'executive_summary' => $this->generateExecutiveSummary($unifiedMetrics),
            'unified_metrics' => $unifiedMetrics,
            'helpdesk_details' => $helpdeskDetails,
            'loan_statistics' => $this->generateLoanStatistics($start, $end),
            'asset_utilization' => $this->generateAssetUtilizationReport($start, $end),
            'cross_module_integration' => $this->generateIntegrationReport($start, $end),
            'recommendations' => $this->generateRecommendations($unifiedMetrics),
        ];

        // Add trend analysis for weekly/monthly reports
        if (in_array($frequency, ['weekly', 'monthly'])) {
            $reportData['trend_analysis'] = $this->generateTrendAnalysis($frequency, $start, $end);
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
    private function generateLoanStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $trendData = LoanApplication::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $trendSeries = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $trendSeries[] = [
                'date' => $key,
                'count' => (int) ($trendData[$key] ?? 0),
            ];
        }

        $approvalBreakdown = LoanApplication::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_value) as total_value'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->map(function ($row) {
                return [
                    'status' => $row->status,
                    'count' => (int) $row->count,
                    'total_value' => (float) $row->total_value,
                ];
            })
            ->toArray();

        $assetDemand = LoanItem::select('asset_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('asset_id')
            ->with('asset')
            ->orderByDesc('total')
            ->take(10)
            ->get()
            ->map(function (LoanItem $item) {
                return [
                    'asset_id' => $item->asset_id,
                    'asset_name' => $item->asset?->name ?? 'Tidak diketahui',
                    'asset_tag' => $item->asset?->asset_tag,
                    'total_requests' => (int) $item->total,
                ];
            })
            ->toArray();

        $userActivity = LoanApplication::query()
            ->selectRaw('loan_applications.division_id, COUNT(*) as total, SUM(total_value) as total_value, COALESCE(divisions.name_ms, "Tidak Ditentukan") as division_name')
            ->leftJoin('divisions', 'divisions.id', '=', 'loan_applications.division_id')
            ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
            ->groupBy('loan_applications.division_id', 'divisions.name_ms')
            ->orderByDesc('total')
            ->take(8)
            ->get()
            ->map(function ($row) {
                return [
                    'division' => $row->division_name,
                    'total_applications' => (int) $row->total,
                    'total_value' => (float) $row->total_value,
                ];
            })
            ->toArray();

        return [
            'application_trends' => $trendSeries,
            'approval_analysis' => $approvalBreakdown,
            'asset_demand' => $assetDemand,
            'user_activity' => $userActivity,
        ];
    }

    /**
     * Generate asset utilization report
     */
    private function generateAssetUtilizationReport(Carbon $startDate, Carbon $endDate): array
    {
        $byCategory = Asset::select(
            'category_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "loaned" THEN 1 ELSE 0 END) as loaned'),
            DB::raw('SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available')
        )
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(function (Asset $asset) {
                $loaned = (int) $asset->loaned;
                $total = (int) $asset->total;

                return [
                    'category' => $asset->category?->name ?? 'Tidak Dikategori',
                    'total_assets' => $total,
                    'loaned_assets' => $loaned,
                    'utilization_rate' => $total > 0 ? round(($loaned / $total) * 100, 1) : 0,
                    'available_assets' => (int) $asset->available,
                ];
            })
            ->toArray();

        $highDemandAssets = LoanTransaction::select('asset_id', DB::raw('COUNT(*) as transactions'))
            ->whereBetween('processed_at', [$startDate, $endDate])
            ->where('transaction_type', TransactionType::ISSUE->value)
            ->groupBy('asset_id')
            ->with('asset')
            ->orderByDesc('transactions')
            ->take(10)
            ->get()
            ->map(function (LoanTransaction $transaction) {
                return [
                    'asset_id' => $transaction->asset_id,
                    'asset_name' => $transaction->asset?->name ?? 'Tidak diketahui',
                    'asset_tag' => $transaction->asset?->asset_tag,
                    'issues' => (int) $transaction->transactions,
                ];
            })
            ->toArray();

        $loanedAssetIds = LoanItem::whereBetween('created_at', [$startDate, $endDate])
            ->pluck('asset_id')
            ->unique();

        $underUtilizedAssets = Asset::whereNotIn('id', $loanedAssetIds)
            ->where('status', 'available')
            ->take(10)
            ->get()
            ->map(function (Asset $asset) {
                return [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'asset_tag' => $asset->asset_tag,
                    'category' => $asset->category?->name ?? 'Tidak Dikategori',
                ];
            })
            ->toArray();

        $maintenanceSummary = Asset::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "maintenance" THEN 1 ELSE 0 END) as maintenance'),
            DB::raw('SUM(CASE WHEN status = "damaged" THEN 1 ELSE 0 END) as damaged'),
            DB::raw('AVG(maintenance_tickets_count) as avg_tickets')
        )
            ->first();

        return [
            'utilization_by_category' => $byCategory,
            'high_demand_assets' => $highDemandAssets,
            'underutilized_assets' => $underUtilizedAssets,
            'maintenance_summary' => [
                'total_assets' => (int) ($maintenanceSummary->total ?? 0),
                'maintenance_assets' => (int) ($maintenanceSummary->maintenance ?? 0),
                'damaged_assets' => (int) ($maintenanceSummary->damaged ?? 0),
                'average_maintenance_tickets' => round((float) ($maintenanceSummary->avg_tickets ?? 0), 2),
            ],
        ];
    }

    /**
     * Generate integration report
     */
    private function generateIntegrationReport(Carbon $startDate, Carbon $endDate): array
    {
        $integrationVolume = CrossModuleIntegration::select('integration_type', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('integration_type')
            ->pluck('total', 'integration_type')
            ->toArray();

        $damageReports = CrossModuleIntegration::where('integration_type', 'asset_damage_report')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['assetLoan', 'helpdeskTicket'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function (CrossModuleIntegration $integration) {
                return [
                    'ticket_number' => $integration->helpdeskTicket?->ticket_number,
                    'loan_number' => $integration->assetLoan?->application_number,
                    'trigger_event' => $integration->trigger_event,
                    'processed_at' => $integration->processed_at?->format('Y-m-d H:i'),
                ];
            })
            ->toArray();

        $maintenanceRequestsCollection = CrossModuleIntegration::where('integration_type', 'maintenance_request')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $processedRequests = $maintenanceRequestsCollection
            ->filter(fn (CrossModuleIntegration $record): bool => $record->processed_at !== null);

        $totalProcessingMinutes = $processedRequests->reduce(
            function (float $carry, CrossModuleIntegration $record): float {
                $minutes = $record->processed_at?->diffInMinutes($record->created_at, false) ?? 0;

                return $carry + abs($minutes);
            },
            0.0
        );

        $avgProcessingHours = $processedRequests->count() > 0
            ? ($totalProcessingMinutes / $processedRequests->count()) / 60
            : 0.0;

        $maintenanceRequests = [
            'total' => $maintenanceRequestsCollection->count(),
            'avg_processing_hours' => round($avgProcessingHours, 2),
        ];

        $efficiencyMetrics = [
            'total_integrations' => array_sum($integrationVolume),
            'avg_processing_hours' => $maintenanceRequests['avg_processing_hours'],
            'total_maintenance_requests' => $maintenanceRequests['total'],
        ];

        return [
            'integration_volume' => $integrationVolume,
            'damage_reports' => $damageReports,
            'maintenance_requests' => $maintenanceRequests,
            'efficiency_metrics' => $efficiencyMetrics,
        ];
    }

    /**
     * Generate trend analysis
     */
    private function generateTrendAnalysis(string $frequency, Carbon $startDate, Carbon $endDate): array
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
        $growth = [];

        foreach ($trends['datasets'] as $dataset) {
            $data = $dataset['data'];
            $count = count($data);

            if ($count < 2 || $data[$count - 2] == 0) {
                $growth[$dataset['label']] = 0.0;
                continue;
            }

            $previous = $data[$count - 2];
            $current = $data[$count - 1];
            $growth[$dataset['label']] = round((($current - $previous) / max($previous, 1)) * 100, 2);
        }

        return $growth;
    }

    private function identifySeasonalPatterns(array $trends): array
    {
        $patterns = [];

        foreach ($trends['datasets'] as $dataset) {
            $data = $dataset['data'];
            $labels = $trends['labels'];

            if (empty($data)) {
                $patterns[$dataset['label']] = [
                    'peak' => null,
                    'low' => null,
                ];
                continue;
            }

            $peakIndex = array_search(max($data), $data, true);
            $lowIndex = array_search(min($data), $data, true);

            $patterns[$dataset['label']] = [
                'peak' => $labels[$peakIndex] ?? null,
                'low' => $labels[$lowIndex] ?? null,
            ];
        }

        return $patterns;
    }

    private function generateSimpleForecasts(array $trends): array
    {
        $forecasts = [];

        foreach ($trends['datasets'] as $dataset) {
            $data = $dataset['data'];
            if (empty($data)) {
                $forecasts[$dataset['label']] = [
                    'forecast' => 0,
                    'confidence' => 'low',
                ];
                continue;
            }

            $recent = array_slice($data, -3);
            $average = array_sum($recent) / max(count($recent), 1);
            $variance = 0.0;

            if (count($recent) > 1) {
                foreach ($recent as $value) {
                    $variance += pow($value - $average, 2);
                }
                $variance /= count($recent);
            }

            $confidence = match (true) {
                $variance <= 5 => 'high',
                $variance <= 20 => 'medium',
                default => 'low',
            };

            $forecasts[$dataset['label']] = [
                'forecast' => round($average, 1),
                'confidence' => $confidence,
            ];
        }

        return $forecasts;
    }
}
