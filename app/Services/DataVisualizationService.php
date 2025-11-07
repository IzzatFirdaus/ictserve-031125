<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Data Visualization Service
 *
 * Provides interactive charts, trend analysis visualizations,
 * drill-down capabilities, and chart export functionality for reports.
 *
 * @trace Requirements 8.5
 */
class DataVisualizationService
{
    /**
     * Generate chart data for ticket trends
     */
    public function getTicketTrendsChartData(Carbon $startDate, Carbon $endDate): array
    {
        $dates = collect();
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dates->push($current->copy());
            $current->addDay();
        }

        $chartData = $dates->map(function ($date) {
            $ticketsCreated = HelpdeskTicket::whereDate('created_at', $date)->count();
            $ticketsResolved = HelpdeskTicket::whereDate('resolved_at', $date)->count();

            return [
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('d/m'),
                'tickets_created' => $ticketsCreated,
                'tickets_resolved' => $ticketsResolved,
                'backlog' => $ticketsCreated - $ticketsResolved,
            ];
        });

        return [
            'type' => 'line',
            'title' => 'Trend Tiket Helpdesk',
            'subtitle' => $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y'),
            'data' => $chartData->toArray(),
            'series' => [
                [
                    'name' => 'Tiket Dicipta',
                    'data' => $chartData->pluck('tickets_created')->toArray(),
                    'color' => '#3b82f6',
                ],
                [
                    'name' => 'Tiket Selesai',
                    'data' => $chartData->pluck('tickets_resolved')->toArray(),
                    'color' => '#10b981',
                ],
            ],
            'categories' => $chartData->pluck('date_formatted')->toArray(),
            'drilldown' => $this->getTicketTrendsDrilldown($chartData),
        ];
    }

    /**
     * Generate chart data for asset utilization
     */
    public function getAssetUtilizationChartData(): array
    {
        $assets = Asset::with(['category', 'loanApplications'])
            ->get()
            ->groupBy('category.name_en');

        $chartData = $assets->map(function ($categoryAssets, $categoryName) {
            $totalAssets = $categoryAssets->count();
            $loanedAssets = $categoryAssets->filter(function ($asset) {
                return $asset->loanApplications->where('status', 'in_use')->isNotEmpty();
            })->count();

            $utilizationRate = $totalAssets > 0 ? round(($loanedAssets / $totalAssets) * 100, 1) : 0;

            return [
                'category' => $categoryName ?? 'Lain-lain',
                'total_assets' => $totalAssets,
                'loaned_assets' => $loanedAssets,
                'available_assets' => $totalAssets - $loanedAssets,
                'utilization_rate' => $utilizationRate,
            ];
        })->values();

        return [
            'type' => 'bar',
            'title' => 'Penggunaan Aset mengikut Kategori',
            'subtitle' => 'Perbandingan aset yang dipinjam vs tersedia',
            'data' => $chartData->toArray(),
            'series' => [
                [
                    'name' => 'Dipinjam',
                    'data' => $chartData->pluck('loaned_assets')->toArray(),
                    'color' => '#f59e0b',
                ],
                [
                    'name' => 'Tersedia',
                    'data' => $chartData->pluck('available_assets')->toArray(),
                    'color' => '#10b981',
                ],
            ],
            'categories' => $chartData->pluck('category')->toArray(),
            'drilldown' => $this->getAssetUtilizationDrilldown($chartData),
        ];
    }

    /**
     * Generate SLA compliance chart data
     */
    public function getSlaComplianceChartData(Carbon $startDate, Carbon $endDate): array
    {
        $helpdeskCompliance = $this->calculateHelpdeskSlaCompliance($startDate, $endDate);
        $loanCompliance = $this->calculateLoanSlaCompliance($startDate, $endDate);

        $chartData = [
            [
                'module' => 'Helpdesk',
                'compliant' => $helpdeskCompliance['compliant'],
                'non_compliant' => $helpdeskCompliance['total'] - $helpdeskCompliance['compliant'],
                'compliance_rate' => $helpdeskCompliance['rate'],
                'total' => $helpdeskCompliance['total'],
            ],
            [
                'module' => 'Pinjaman Aset',
                'compliant' => $loanCompliance['compliant'],
                'non_compliant' => $loanCompliance['total'] - $loanCompliance['compliant'],
                'compliance_rate' => $loanCompliance['rate'],
                'total' => $loanCompliance['total'],
            ],
        ];

        return [
            'type' => 'donut',
            'title' => 'Pematuhan SLA',
            'subtitle' => $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y'),
            'data' => $chartData,
            'series' => [
                [
                    'name' => 'Mematuhi SLA',
                    'data' => collect($chartData)->pluck('compliant')->toArray(),
                    'color' => '#10b981',
                ],
                [
                    'name' => 'Tidak Mematuhi SLA',
                    'data' => collect($chartData)->pluck('non_compliant')->toArray(),
                    'color' => '#ef4444',
                ],
            ],
            'categories' => collect($chartData)->pluck('module')->toArray(),
            'drilldown' => $this->getSlaComplianceDrilldown($chartData),
        ];
    }

    /**
     * Generate performance dashboard chart data
     */
    public function getPerformanceDashboardData(): array
    {
        $last30Days = now()->subDays(30);

        return [
            'ticket_trends' => $this->getTicketTrendsChartData($last30Days, now()),
            'asset_utilization' => $this->getAssetUtilizationChartData(),
            'sla_compliance' => $this->getSlaComplianceChartData($last30Days, now()),
            'priority_distribution' => $this->getPriorityDistributionData(),
            'resolution_time_trends' => $this->getResolutionTimeTrendsData($last30Days, now()),
        ];
    }

    /**
     * Generate priority distribution chart data
     */
    public function getPriorityDistributionData(): array
    {
        $tickets = HelpdeskTicket::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        $loans = LoanApplication::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        $priorities = ['low', 'normal', 'high', 'urgent'];
        $colors = ['#6b7280', '#3b82f6', '#f59e0b', '#ef4444'];

        $chartData = collect($priorities)->map(function ($priority, $index) use ($tickets, $loans, $colors) {
            $ticketCount = $tickets->where('priority', $priority)->first()?->count ?? 0;
            $loanCount = $loans->where('priority', $priority)->first()?->count ?? 0;

            return [
                'priority' => ucfirst($priority),
                'tickets' => $ticketCount,
                'loans' => $loanCount,
                'total' => $ticketCount + $loanCount,
                'color' => $colors[$index],
            ];
        });

        return [
            'type' => 'pie',
            'title' => 'Taburan Keutamaan',
            'subtitle' => 'Tiket dan pinjaman mengikut tahap keutamaan',
            'data' => $chartData->toArray(),
            'series' => $chartData->pluck('total')->toArray(),
            'categories' => $chartData->pluck('priority')->toArray(),
            'colors' => $chartData->pluck('color')->toArray(),
        ];
    }

    /**
     * Generate resolution time trends data
     */
    public function getResolutionTimeTrendsData(Carbon $startDate, Carbon $endDate): array
    {
        $tickets = HelpdeskTicket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->get();

        $weeklyData = $tickets->groupBy(function ($ticket) {
            return $ticket->resolved_at->format('Y-W');
        })->map(function ($weekTickets, $week) {
            $avgResolutionTime = $weekTickets->avg(function ($ticket) {
                return $ticket->created_at->diffInHours($ticket->resolved_at);
            });

            return [
                'week' => $week,
                'week_formatted' => 'Minggu '.substr($week, -2),
                'avg_resolution_time' => round($avgResolutionTime, 1),
                'ticket_count' => $weekTickets->count(),
            ];
        })->values();

        return [
            'type' => 'area',
            'title' => 'Trend Masa Penyelesaian',
            'subtitle' => 'Purata masa penyelesaian tiket (jam)',
            'data' => $weeklyData->toArray(),
            'series' => [
                [
                    'name' => 'Masa Penyelesaian (Jam)',
                    'data' => $weeklyData->pluck('avg_resolution_time')->toArray(),
                    'color' => '#8b5cf6',
                ],
            ],
            'categories' => $weeklyData->pluck('week_formatted')->toArray(),
        ];
    }

    /**
     * Export chart as image (placeholder)
     */
    public function exportChart(array $chartData, string $format = 'png'): array
    {
        // In production, this would use a chart library like Chart.js or ApexCharts
        // to generate actual image exports

        return [
            'success' => true,
            'format' => $format,
            'filename' => 'chart_'.now()->format('Y-m-d_H-i-s').'.'.$format,
            'size' => '1024x768',
            'message' => 'Chart export functionality would be implemented with a proper charting library',
        ];
    }

    /**
     * Get drill-down data for ticket trends
     */
    private function getTicketTrendsDrilldown(Collection $chartData): array
    {
        return $chartData->map(function ($dayData) {
            $date = Carbon::parse($dayData['date']);
            $tickets = HelpdeskTicket::whereDate('created_at', $date)->get();

            $priorityBreakdown = $tickets->groupBy('priority')->map(function ($priorityTickets, $priority) {
                return [
                    'priority' => ucfirst($priority),
                    'count' => $priorityTickets->count(),
                ];
            })->values();

            return [
                'date' => $dayData['date'],
                'priority_breakdown' => $priorityBreakdown->toArray(),
                'category_breakdown' => $this->getTicketCategoryBreakdown($tickets),
            ];
        })->toArray();
    }

    /**
     * Get drill-down data for asset utilization
     */
    private function getAssetUtilizationDrilldown(Collection $chartData): array
    {
        return $chartData->map(function ($categoryData) {
            $assets = Asset::whereHas('category', function ($query) use ($categoryData) {
                $query->where('name_en', $categoryData['category']);
            })->with('loanApplications')->get();

            $assetDetails = $assets->map(function ($asset) {
                $activeLoan = $asset->loanApplications->where('status', 'in_use')->first();

                return [
                    'asset_name' => $asset->name,
                    'asset_code' => $asset->asset_code,
                    'status' => $asset->status,
                    'is_loaned' => $activeLoan ? true : false,
                    'loan_count' => $asset->loanApplications->count(),
                ];
            });

            return [
                'category' => $categoryData['category'],
                'assets' => $assetDetails->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get drill-down data for SLA compliance
     */
    private function getSlaComplianceDrilldown(array $chartData): array
    {
        return collect($chartData)->map(function ($moduleData) {
            if ($moduleData['module'] === 'Helpdesk') {
                $breaches = HelpdeskTicket::where('sla_deadline', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->get()
                    ->map(function ($ticket) {
                        return [
                            'identifier' => $ticket->ticket_number,
                            'title' => $ticket->title,
                            'days_overdue' => $ticket->sla_deadline ? now()->diffInDays($ticket->sla_deadline) : 0,
                            'priority' => $ticket->priority,
                        ];
                    });
            } else {
                $breaches = LoanApplication::where('status', 'pending_approval')
                    ->where('created_at', '<', now()->subHours(48))
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'identifier' => $loan->application_number,
                            'title' => 'Pinjaman: '.$loan->loanItems->pluck('asset.name')->join(', '),
                            'days_overdue' => now()->diffInDays($loan->created_at->addHours(48)),
                            'priority' => $loan->priority,
                        ];
                    });
            }

            return [
                'module' => $moduleData['module'],
                'sla_breaches' => $breaches->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get ticket category breakdown
     */
    private function getTicketCategoryBreakdown(Collection $tickets): array
    {
        return $tickets->groupBy('category.name_en')->map(function ($categoryTickets, $category) {
            return [
                'category' => $category ?? 'Lain-lain',
                'count' => $categoryTickets->count(),
            ];
        })->values()->toArray();
    }

    /**
     * Calculate helpdesk SLA compliance
     */
    private function calculateHelpdeskSlaCompliance(Carbon $startDate, Carbon $endDate): array
    {
        $tickets = HelpdeskTicket::whereBetween('created_at', [$startDate, $endDate])->get();
        $total = $tickets->count();

        if ($total === 0) {
            return ['total' => 0, 'compliant' => 0, 'rate' => 100];
        }

        $compliant = $tickets->filter(function ($ticket) {
            return $ticket->resolved_at && $ticket->sla_deadline &&
                   $ticket->resolved_at <= $ticket->sla_deadline;
        })->count();

        return [
            'total' => $total,
            'compliant' => $compliant,
            'rate' => round(($compliant / $total) * 100, 1),
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
            return ['total' => 0, 'compliant' => 0, 'rate' => 100];
        }

        $compliant = $loans->filter(function ($loan) {
            return $loan->updated_at <= $loan->created_at->addHours(48);
        })->count();

        return [
            'total' => $total,
            'compliant' => $compliant,
            'rate' => round(($compliant / $total) * 100, 1),
        ];
    }
}
