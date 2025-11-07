<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Report Template Service
 *
 * Provides pre-configured report templates for common reporting needs:
 * - Monthly ticket summary
 * - Asset utilization report
 * - SLA compliance report
 * - Overdue items report
 *
 * @trace Requirements 8.4
 */
class ReportTemplateService
{
    public function __construct(
        private ReportBuilderService $reportBuilderService,
        private DataExportService $dataExportService
    ) {}

    /**
     * Get all available report templates
     */
    public function getAvailableTemplates(): array
    {
        return [
            'monthly_ticket_summary' => [
                'name' => 'Ringkasan Tiket Bulanan',
                'description' => 'Laporan ringkasan tiket helpdesk untuk bulan semasa',
                'module' => 'helpdesk',
                'frequency' => 'monthly',
                'icon' => 'heroicon-o-ticket',
                'color' => 'primary',
            ],
            'asset_utilization' => [
                'name' => 'Laporan Penggunaan Aset',
                'description' => 'Analisis penggunaan dan permintaan aset ICT',
                'module' => 'assets',
                'frequency' => 'monthly',
                'icon' => 'heroicon-o-computer-desktop',
                'color' => 'success',
            ],
            'sla_compliance' => [
                'name' => 'Laporan Pematuhan SLA',
                'description' => 'Analisis pematuhan SLA untuk helpdesk dan pinjaman',
                'module' => 'unified',
                'frequency' => 'weekly',
                'icon' => 'heroicon-o-clock',
                'color' => 'warning',
            ],
            'overdue_items' => [
                'name' => 'Laporan Item Tertunggak',
                'description' => 'Senarai tiket dan pinjaman yang tertunggak',
                'module' => 'unified',
                'frequency' => 'daily',
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => 'danger',
            ],
            'weekly_performance' => [
                'name' => 'Prestasi Mingguan',
                'description' => 'Ringkasan prestasi sistem untuk minggu lepas',
                'module' => 'unified',
                'frequency' => 'weekly',
                'icon' => 'heroicon-o-chart-bar',
                'color' => 'info',
            ],
        ];
    }

    /**
     * Generate monthly ticket summary report
     */
    public function generateMonthlyTicketSummary(string $format = 'pdf', ?Carbon $month = null): array
    {
        $month = $month ?? now();
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $data = $this->getMonthlyTicketData($startDate, $endDate);

        $metadata = [
            'report_title' => 'Ringkasan Tiket Bulanan',
            'period' => $month->format('F Y'),
            'date_range' => $startDate->format('d/m/Y').' hingga '.$endDate->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters_applied' => ['Bulan: '.$month->format('F Y')],
        ];

        return $this->dataExportService->exportData($data, $format, $metadata);
    }

    /**
     * Generate asset utilization report
     */
    public function generateAssetUtilizationReport(string $format = 'pdf', ?Carbon $month = null): array
    {
        $month = $month ?? now();
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $data = $this->getAssetUtilizationData($startDate, $endDate);

        $metadata = [
            'report_title' => 'Laporan Penggunaan Aset',
            'period' => $month->format('F Y'),
            'date_range' => $startDate->format('d/m/Y').' hingga '.$endDate->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters_applied' => ['Bulan: '.$month->format('F Y')],
        ];

        return $this->dataExportService->exportData($data, $format, $metadata);
    }

    /**
     * Generate SLA compliance report
     */
    public function generateSlaComplianceReport(string $format = 'pdf', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subWeek();
        $endDate = $endDate ?? now();

        $data = $this->getSlaComplianceData($startDate, $endDate);

        $metadata = [
            'report_title' => 'Laporan Pematuhan SLA',
            'period' => 'Mingguan',
            'date_range' => $startDate->format('d/m/Y').' hingga '.$endDate->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters_applied' => ['Tempoh: 7 hari terakhir'],
        ];

        return $this->dataExportService->exportData($data, $format, $metadata);
    }

    /**
     * Generate overdue items report
     */
    public function generateOverdueItemsReport(string $format = 'pdf'): array
    {
        $data = $this->getOverdueItemsData();

        $metadata = [
            'report_title' => 'Laporan Item Tertunggak',
            'period' => 'Semasa',
            'date_range' => 'Sehingga '.now()->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters_applied' => ['Status: Tertunggak'],
        ];

        return $this->dataExportService->exportData($data, $format, $metadata);
    }

    /**
     * Generate weekly performance report
     */
    public function generateWeeklyPerformanceReport(string $format = 'pdf', ?Carbon $week = null): array
    {
        $week = $week ?? now();
        $startDate = $week->copy()->startOfWeek();
        $endDate = $week->copy()->endOfWeek();

        $data = $this->getWeeklyPerformanceData($startDate, $endDate);

        $metadata = [
            'report_title' => 'Prestasi Mingguan',
            'period' => 'Minggu '.$week->weekOfYear.', '.$week->year,
            'date_range' => $startDate->format('d/m/Y').' hingga '.$endDate->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters_applied' => ['Minggu: '.$week->weekOfYear.'/'.$week->year],
        ];

        return $this->dataExportService->exportData($data, $format, $metadata);
    }

    /**
     * Get monthly ticket data
     */
    private function getMonthlyTicketData(Carbon $startDate, Carbon $endDate): Collection
    {
        $tickets = HelpdeskTicket::with(['user', 'assignedTo', 'category'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $summary = collect([
            [
                'metric' => 'Jumlah Tiket Dicipta',
                'value' => $tickets->count(),
                'percentage' => '100%',
                'status' => 'Total',
            ],
            [
                'metric' => 'Tiket Selesai',
                'value' => $tickets->where('status', 'resolved')->count(),
                'percentage' => $tickets->count() > 0 ? round(($tickets->where('status', 'resolved')->count() / $tickets->count()) * 100, 1).'%' : '0%',
                'status' => 'Selesai',
            ],
            [
                'metric' => 'Tiket Terbuka',
                'value' => $tickets->whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
                'percentage' => $tickets->count() > 0 ? round(($tickets->whereIn('status', ['open', 'assigned', 'in_progress'])->count() / $tickets->count()) * 100, 1).'%' : '0%',
                'status' => 'Terbuka',
            ],
            [
                'metric' => 'Tiket Keutamaan Tinggi',
                'value' => $tickets->whereIn('priority', ['high', 'urgent'])->count(),
                'percentage' => $tickets->count() > 0 ? round(($tickets->whereIn('priority', ['high', 'urgent'])->count() / $tickets->count()) * 100, 1).'%' : '0%',
                'status' => 'Keutamaan Tinggi',
            ],
            [
                'metric' => 'Masa Penyelesaian Purata (Jam)',
                'value' => $this->calculateAverageResolutionTime($tickets),
                'percentage' => 'N/A',
                'status' => 'Prestasi',
            ],
        ]);

        return $summary;
    }

    /**
     * Get asset utilization data
     */
    private function getAssetUtilizationData(Carbon $startDate, Carbon $endDate): Collection
    {
        $assets = Asset::with(['category', 'loanApplications' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();

        return $assets->map(function ($asset) {
            $loanCount = $asset->loanApplications->count();
            $activeLoan = $asset->loanApplications->where('status', 'in_use')->first();

            return [
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'category' => $asset->category?->name_en ?? 'N/A',
                'current_status' => $asset->status,
                'loan_requests' => $loanCount,
                'currently_loaned' => $activeLoan ? 'Ya' : 'Tidak',
                'utilization_rate' => $loanCount > 0 ? 'Tinggi' : ($asset->status === 'available' ? 'Rendah' : 'N/A'),
                'last_loan_date' => $asset->loanApplications->max('created_at')?->format('d/m/Y') ?? 'Tiada',
            ];
        });
    }

    /**
     * Get SLA compliance data
     */
    private function getSlaComplianceData(Carbon $startDate, Carbon $endDate): Collection
    {
        $helpdeskSla = $this->calculateHelpdeskSlaCompliance($startDate, $endDate);
        $loanSla = $this->calculateLoanSlaCompliance($startDate, $endDate);

        return collect([
            [
                'module' => 'Helpdesk',
                'total_items' => $helpdeskSla['total'],
                'compliant_items' => $helpdeskSla['compliant'],
                'compliance_rate' => $helpdeskSla['rate'].'%',
                'average_response_time' => $helpdeskSla['avg_response'].' jam',
                'sla_target' => '24 jam',
                'status' => $helpdeskSla['rate'] >= 80 ? 'Baik' : 'Perlu Diperbaiki',
            ],
            [
                'module' => 'Pinjaman Aset',
                'total_items' => $loanSla['total'],
                'compliant_items' => $loanSla['compliant'],
                'compliance_rate' => $loanSla['rate'].'%',
                'average_response_time' => $loanSla['avg_response'].' jam',
                'sla_target' => '48 jam',
                'status' => $loanSla['rate'] >= 80 ? 'Baik' : 'Perlu Diperbaiki',
            ],
        ]);
    }

    /**
     * Get overdue items data
     */
    private function getOverdueItemsData(): Collection
    {
        $overdueTickets = HelpdeskTicket::with(['user', 'assignedTo'])
            ->where('sla_deadline', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->get();

        $overdueLoans = LoanApplication::with(['user'])
            ->where('status', 'in_use')
            ->where('expected_return_date', '<', now())
            ->get();

        $data = collect();

        // Add overdue tickets
        foreach ($overdueTickets as $ticket) {
            $data->push([
                'type' => 'Tiket Helpdesk',
                'identifier' => $ticket->ticket_number,
                'title' => $ticket->title,
                'requester' => $ticket->user?->name ?? $ticket->guest_name,
                'assigned_to' => $ticket->assignedTo?->name ?? 'Belum Ditugaskan',
                'due_date' => $ticket->sla_deadline?->format('d/m/Y H:i'),
                'days_overdue' => $ticket->sla_deadline ? now()->diffInDays($ticket->sla_deadline) : 0,
                'priority' => ucfirst($ticket->priority),
                'status' => ucfirst($ticket->status),
            ]);
        }

        // Add overdue loans
        foreach ($overdueLoans as $loan) {
            $data->push([
                'type' => 'Pinjaman Aset',
                'identifier' => $loan->application_number,
                'title' => 'Pinjaman: '.$loan->loanItems->pluck('asset.name')->join(', '),
                'requester' => $loan->applicant_name,
                'assigned_to' => 'N/A',
                'due_date' => $loan->expected_return_date?->format('d/m/Y'),
                'days_overdue' => $loan->expected_return_date ? now()->diffInDays($loan->expected_return_date) : 0,
                'priority' => ucfirst($loan->priority),
                'status' => 'Tertunggak',
            ]);
        }

        return $data->sortByDesc('days_overdue');
    }

    /**
     * Get weekly performance data
     */
    private function getWeeklyPerformanceData(Carbon $startDate, Carbon $endDate): Collection
    {
        $ticketsCreated = HelpdeskTicket::whereBetween('created_at', [$startDate, $endDate])->count();
        $ticketsResolved = HelpdeskTicket::whereBetween('resolved_at', [$startDate, $endDate])->count();
        $loansCreated = LoanApplication::whereBetween('created_at', [$startDate, $endDate])->count();
        $loansApproved = LoanApplication::whereBetween('updated_at', [$startDate, $endDate])
            ->where('status', 'approved')->count();

        return collect([
            [
                'metric' => 'Tiket Helpdesk Dicipta',
                'current_week' => $ticketsCreated,
                'previous_week' => $this->getPreviousWeekValue('tickets_created', $startDate),
                'change' => $this->calculatePercentageChange($ticketsCreated, $this->getPreviousWeekValue('tickets_created', $startDate)),
                'trend' => $this->getTrendIndicator($ticketsCreated, $this->getPreviousWeekValue('tickets_created', $startDate)),
            ],
            [
                'metric' => 'Tiket Helpdesk Selesai',
                'current_week' => $ticketsResolved,
                'previous_week' => $this->getPreviousWeekValue('tickets_resolved', $startDate),
                'change' => $this->calculatePercentageChange($ticketsResolved, $this->getPreviousWeekValue('tickets_resolved', $startDate)),
                'trend' => $this->getTrendIndicator($ticketsResolved, $this->getPreviousWeekValue('tickets_resolved', $startDate)),
            ],
            [
                'metric' => 'Permohonan Pinjaman Dicipta',
                'current_week' => $loansCreated,
                'previous_week' => $this->getPreviousWeekValue('loans_created', $startDate),
                'change' => $this->calculatePercentageChange($loansCreated, $this->getPreviousWeekValue('loans_created', $startDate)),
                'trend' => $this->getTrendIndicator($loansCreated, $this->getPreviousWeekValue('loans_created', $startDate)),
            ],
            [
                'metric' => 'Permohonan Pinjaman Diluluskan',
                'current_week' => $loansApproved,
                'previous_week' => $this->getPreviousWeekValue('loans_approved', $startDate),
                'change' => $this->calculatePercentageChange($loansApproved, $this->getPreviousWeekValue('loans_approved', $startDate)),
                'trend' => $this->getTrendIndicator($loansApproved, $this->getPreviousWeekValue('loans_approved', $startDate)),
            ],
        ]);
    }

    /**
     * Calculate average resolution time for tickets
     */
    private function calculateAverageResolutionTime(Collection $tickets): string
    {
        $resolvedTickets = $tickets->whereNotNull('resolved_at');

        if ($resolvedTickets->isEmpty()) {
            return '0';
        }

        $totalHours = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return number_format($totalHours / $resolvedTickets->count(), 1);
    }

    /**
     * Calculate helpdesk SLA compliance
     */
    private function calculateHelpdeskSlaCompliance(Carbon $startDate, Carbon $endDate): array
    {
        $tickets = HelpdeskTicket::whereBetween('created_at', [$startDate, $endDate])->get();
        $total = $tickets->count();

        if ($total === 0) {
            return ['total' => 0, 'compliant' => 0, 'rate' => 100, 'avg_response' => 0];
        }

        $compliant = $tickets->filter(function ($ticket) {
            return $ticket->resolved_at && $ticket->sla_deadline &&
                   $ticket->resolved_at <= $ticket->sla_deadline;
        })->count();

        $avgResponse = $tickets->whereNotNull('resolved_at')->avg(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return [
            'total' => $total,
            'compliant' => $compliant,
            'rate' => round(($compliant / $total) * 100, 1),
            'avg_response' => round($avgResponse ?? 0, 1),
        ];
    }

    /**
     * Calculate loan SLA compliance
     */
    private function calculateLoanSlaCompliance(Carbon $startDate, Carbon $endDate): array
    {
        $loans = LoanApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'rejected'])->get();
        $total = $loans->count();

        if ($total === 0) {
            return ['total' => 0, 'compliant' => 0, 'rate' => 100, 'avg_response' => 0];
        }

        $compliant = $loans->filter(function ($loan) {
            return $loan->updated_at <= $loan->created_at->addHours(48);
        })->count();

        $avgResponse = $loans->avg(function ($loan) {
            return $loan->created_at->diffInHours($loan->updated_at);
        });

        return [
            'total' => $total,
            'compliant' => $compliant,
            'rate' => round(($compliant / $total) * 100, 1),
            'avg_response' => round($avgResponse ?? 0, 1),
        ];
    }

    /**
     * Get previous week value for comparison
     */
    private function getPreviousWeekValue(string $metric, Carbon $currentWeekStart): int
    {
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $previousWeekStart->copy()->endOfWeek();

        return match ($metric) {
            'tickets_created' => HelpdeskTicket::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->count(),
            'tickets_resolved' => HelpdeskTicket::whereBetween('resolved_at', [$previousWeekStart, $previousWeekEnd])->count(),
            'loans_created' => LoanApplication::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->count(),
            'loans_approved' => LoanApplication::whereBetween('updated_at', [$previousWeekStart, $previousWeekEnd])
                ->where('status', 'approved')->count(),
            default => 0,
        };
    }

    /**
     * Calculate percentage change
     */
    private function calculatePercentageChange(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;

        return ($change >= 0 ? '+' : '').round($change, 1).'%';
    }

    /**
     * Get trend indicator
     */
    private function getTrendIndicator(int $current, int $previous): string
    {
        if ($current > $previous) {
            return 'Naik';
        } elseif ($current < $previous) {
            return 'Turun';
        } else {
            return 'Sama';
        }
    }
}
