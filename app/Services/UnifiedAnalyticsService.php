<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\DB;

/**
 * Unified Analytics Service
 *
 * Provides comprehensive analytics combining helpdesk and asset loan data.
 * Implements cross-module integration metrics and unified dashboard analytics.
 *
 * Requirements: 13.1, 4.1, 4.2, 13.3
 */
class UnifiedAnalyticsService
{
    public function __construct(
        private HelpdeskReportService $helpdeskService
    ) {}

    /**
     * Get unified dashboard metrics combining helpdesk and loan data
     */
    public function getDashboardMetrics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $helpdeskMetrics = $this->getHelpdeskMetrics($startDate, $endDate);
        $loanMetrics = $this->getLoanMetrics($startDate, $endDate);
        $assetMetrics = $this->getAssetMetrics();
        $integrationMetrics = $this->getCrossModuleIntegrationMetrics($startDate, $endDate);

        return [
            'helpdesk' => $helpdeskMetrics,
            'loans' => $loanMetrics,
            'assets' => $assetMetrics,
            'integration' => $integrationMetrics,
            'summary' => $this->calculateSummaryMetrics($helpdeskMetrics, $loanMetrics, $assetMetrics),
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get helpdesk-specific metrics
     */
    private function getHelpdeskMetrics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = HelpdeskTicket::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $resolved = $query->where('status', 'resolved')->count();
        $pending = $query->whereIn('status', ['open', 'in_progress', 'pending_info'])->count();
        $overdue = $query->where('sla_resolution_due_at', '<', now())
            ->whereNull('resolved_at')
            ->count();

        $avgResolutionTime = $query->whereNotNull('resolved_at')
            ->selectRaw('AVG(JULIANDAY(resolved_at) - JULIANDAY(created_at)) * 24 as avg_hours')
            ->value('avg_hours') ?? 0;

        return [
            'total_tickets' => $total,
            'resolved_tickets' => $resolved,
            'pending_tickets' => $pending,
            'overdue_tickets' => $overdue,
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
            'avg_resolution_hours' => round($avgResolutionTime, 1),
        ];
    }

    /**
     * Get loan-specific metrics
     */
    private function getLoanMetrics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = LoanApplication::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $approved = $query->where('status', 'approved')->count();
        $active = $query->whereIn('status', ['issued', 'in_use'])->count();
        $overdue = $query->where('loan_end_date', '<', now()->toDateString())
            ->whereIn('status', ['issued', 'in_use'])
            ->count();
        $pendingApproval = $query->where('status', 'under_review')->count();

        $totalValue = $query->sum('total_value') ?? 0;

        return [
            'total_applications' => $total,
            'approved_applications' => $approved,
            'active_loans' => $active,
            'overdue_loans' => $overdue,
            'pending_approval' => $pendingApproval,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0,
            'total_loan_value' => $totalValue,
        ];
    }

    /**
     * Get asset utilization metrics
     */
    private function getAssetMetrics(): array
    {
        $total = Asset::count();
        $available = Asset::where('status', 'available')->count();
        $loaned = Asset::where('status', 'loaned')->count();
        $maintenance = Asset::whereIn('status', ['maintenance', 'damaged'])->count();
        $retired = Asset::where('status', 'retired')->count();

        $utilizationRate = $total > 0 ? round(($loaned / $total) * 100, 1) : 0;
        $availabilityRate = $total > 0 ? round(($available / $total) * 100, 1) : 0;

        return [
            'total_assets' => $total,
            'available_assets' => $available,
            'loaned_assets' => $loaned,
            'maintenance_assets' => $maintenance,
            'retired_assets' => $retired,
            'utilization_rate' => $utilizationRate,
            'availability_rate' => $availabilityRate,
        ];
    }

    /**
     * Get cross-module integration metrics
     */
    private function getCrossModuleIntegrationMetrics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = CrossModuleIntegration::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $assetDamageReports = $query->where('integration_type', 'asset_damage_report')->count();
        $maintenanceRequests = $query->where('integration_type', 'maintenance_request')->count();
        $assetTicketLinks = $query->where('integration_type', 'asset_ticket_link')->count();

        return [
            'total_integrations' => $total,
            'asset_damage_reports' => $assetDamageReports,
            'maintenance_requests' => $maintenanceRequests,
            'asset_ticket_links' => $assetTicketLinks,
        ];
    }

    /**
     * Calculate summary metrics across modules
     */
    private function calculateSummaryMetrics(array $helpdesk, array $loans, array $assets): array
    {
        $totalIssues = $helpdesk['pending_tickets'] + $loans['overdue_loans'] + $assets['maintenance_assets'];
        $totalActive = $helpdesk['pending_tickets'] + $loans['active_loans'];

        return [
            'total_active_items' => $totalActive,
            'total_issues_requiring_attention' => $totalIssues,
            'overall_system_health' => $this->calculateSystemHealth($helpdesk, $loans, $assets),
        ];
    }

    /**
     * Calculate overall system health score (0-100)
     */
    private function calculateSystemHealth(array $helpdesk, array $loans, array $assets): float
    {
        $helpdeskHealth = min(100, $helpdesk['resolution_rate']);
        $loanHealth = min(100, $loans['approval_rate']);
        $assetHealth = $assets['availability_rate'];

        // Weighted average: helpdesk 40%, loans 35%, assets 25%
        return round(($helpdeskHealth * 0.4) + ($loanHealth * 0.35) + ($assetHealth * 0.25), 1);
    }

    /**
     * Get monthly trends for unified chart
     */
    public function getMonthlyTrends(int $months = 6): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        $monthExpression = $this->monthSelectExpression('created_at');

        $helpdeskTrends = HelpdeskTicket::selectRaw("{$monthExpression} as month, COUNT(*) as count")
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw($monthExpression))
            ->pluck('count', 'month');

        $loanTrends = LoanApplication::selectRaw("{$monthExpression} as month, COUNT(*) as count")
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw($monthExpression))
            ->pluck('count', 'month');

        $integrationTrends = CrossModuleIntegration::selectRaw("{$monthExpression} as month, COUNT(*) as count")
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw($monthExpression))
            ->pluck('count', 'month');

        $labels = [];
        $helpdeskData = [];
        $loanData = [];
        $integrationData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
            $helpdeskData[] = (int) ($helpdeskTrends[$key] ?? 0);
            $loanData[] = (int) ($loanTrends[$key] ?? 0);
            $integrationData[] = (int) ($integrationTrends[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Tiket Helpdesk',
                    'data' => $helpdeskData,
                    'borderColor' => '#0056b3',
                    'backgroundColor' => 'rgba(0, 86, 179, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Permohonan Pinjaman',
                    'data' => $loanData,
                    'borderColor' => '#198754',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Integrasi Silang Modul',
                    'data' => $integrationData,
                    'borderColor' => '#ff8c00',
                    'backgroundColor' => 'rgba(255, 140, 0, 0.15)',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    private function monthSelectExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /**
     * Get asset utilization trends over time
     */
    public function getAssetUtilizationTrends(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();

        $transactions = AssetTransaction::where('transaction_date', '>=', $startDate)
            ->selectRaw('DATE(transaction_date) as date, type, COUNT(*) as count')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $labels = [];
        $issueData = [];
        $returnData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');

            $dayTransactions = $transactions->where('date', $dateStr);
            $issueData[] = $dayTransactions->where('type', 'issue')->sum('count');
            $returnData[] = $dayTransactions->where('type', 'return')->sum('count');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Aset Dikeluarkan',
                    'data' => $issueData,
                    'borderColor' => '#198754',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Aset Dikembalikan',
                    'data' => $returnData,
                    'borderColor' => '#0056b3',
                    'backgroundColor' => 'rgba(0, 86, 179, 0.15)',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Get drill-down data for specific metric
     */
    public function getDrillDownData(string $metric, array $filters = []): array
    {
        return match ($metric) {
            'overdue_tickets' => $this->getOverdueTicketsDetail($filters),
            'overdue_loans' => $this->getOverdueLoansDetail($filters),
            'maintenance_assets' => $this->getMaintenanceAssetsDetail($filters),
            'cross_module_integrations' => $this->getCrossModuleIntegrationsDetail($filters),
            default => [],
        };
    }

    /**
     * Get detailed overdue tickets data
     */
    private function getOverdueTicketsDetail(array $filters): array
    {
        $query = HelpdeskTicket::where('sla_resolution_due_at', '<', now())
            ->whereNull('resolved_at')
            ->with(['category', 'assignedUser']);

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->get()->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority,
                'category' => $ticket->category?->name_en ?? 'Uncategorized',
                'assigned_to' => $ticket->assignedUser?->name ?? 'Unassigned',
                'days_overdue' => now()->diffInDays($ticket->sla_resolution_due_at),
                'created_at' => $ticket->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();
    }

    /**
     * Get detailed overdue loans data
     */
    private function getOverdueLoansDetail(array $filters): array
    {
        $query = LoanApplication::where('loan_end_date', '<', now()->toDateString())
            ->whereIn('status', ['issued', 'in_use'])
            ->with(['loanItems.asset']);

        return $query->get()->map(function ($loan) {
            return [
                'id' => $loan->id,
                'application_number' => $loan->application_number,
                'applicant_name' => $loan->applicant_name,
                'loan_end_date' => $loan->loan_end_date,
                'days_overdue' => now()->diffInDays($loan->loan_end_date),
                'total_value' => $loan->total_value,
                'asset_count' => $loan->loanItems->count(),
                'assets' => $loan->loanItems->pluck('asset.name')->join(', '),
            ];
        })->toArray();
    }

    /**
     * Get detailed maintenance assets data
     */
    private function getMaintenanceAssetsDetail(array $filters): array
    {
        $query = Asset::whereIn('status', ['maintenance', 'damaged'])
            ->with(['category']);

        return $query->get()->map(function ($asset) {
            return [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'category' => $asset->category?->name ?? 'Uncategorized',
                'status' => $asset->status,
                'condition' => $asset->condition,
                'last_maintenance_date' => $asset->last_maintenance_date?->format('Y-m-d'),
                'maintenance_tickets_count' => $asset->maintenance_tickets_count,
            ];
        })->toArray();
    }

    /**
     * Get detailed cross-module integrations data
     */
    private function getCrossModuleIntegrationsDetail(array $filters): array
    {
        $query = CrossModuleIntegration::with(['helpdeskTicket', 'loanApplication']);

        if (isset($filters['integration_type'])) {
            $query->where('integration_type', $filters['integration_type']);
        }

        return $query->latest()->take(50)->get()->map(function ($integration) {
            return [
                'id' => $integration->id,
                'integration_type' => $integration->integration_type,
                'trigger_event' => $integration->trigger_event,
                'ticket_number' => $integration->helpdeskTicket?->ticket_number,
                'loan_number' => $integration->loanApplication?->application_number,
                'processed_at' => $integration->processed_at?->format('Y-m-d H:i'),
                'created_at' => $integration->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();
    }
}
