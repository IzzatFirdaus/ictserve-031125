<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Helpdesk Report Service
 *
 * Generates comprehensive reports and analytics for helpdesk operations.
 * Covers ticket volume, resolution times, agent performance, and SLA compliance.
 *
 * @see D03 Software Requirements Specification - Requirements 3.2, 3.5, 3.6, 8.1, 8.2, 8.5
 * @see D04 Software Design Document - Reporting Engine
 */
class HelpdeskReportService
{
    /**
     * Get ticket volume statistics for a date range
     */
    public function getTicketVolumeStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = HelpdeskTicket::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $byStatus = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byPriority = $query->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $byCategory = $query->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->category?->name ?? 'Uncategorized' => $item->count])
            ->toArray();

        $guestVsAuthenticated = [
            'guest' => $query->whereNull('user_id')->count(),
            'authenticated' => $query->whereNotNull('user_id')->count(),
        ];

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'by_category' => $byCategory,
            'guest_vs_authenticated' => $guestVsAuthenticated,
        ];
    }

    /**
     * Get resolution time statistics
     */
    public function getResolutionTimeStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = HelpdeskTicket::whereNotNull('resolved_at');

        if ($startDate) {
            $query->where('resolved_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('resolved_at', '<=', $endDate);
        }

        $tickets = $query->get();

        if ($tickets->isEmpty()) {
            return [
                'average_hours' => 0,
                'median_hours' => 0,
                'min_hours' => 0,
                'max_hours' => 0,
                'total_resolved' => 0,
            ];
        }

        $resolutionTimes = $tickets->map(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        })->sort()->values();

        return [
            'average_hours' => round($resolutionTimes->average(), 2),
            'median_hours' => $resolutionTimes->median(),
            'min_hours' => $resolutionTimes->min(),
            'max_hours' => $resolutionTimes->max(),
            'total_resolved' => $tickets->count(),
        ];
    }

    /**
     * Get agent performance statistics
     */
    public function getAgentPerformanceStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): Collection
    {
        $query = HelpdeskTicket::whereNotNull('assigned_to_user');

        if ($startDate) {
            $query->where('assigned_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('assigned_at', '<=', $endDate);
        }

        $agentStats = $query->with('assignedUser')
            ->get()
            ->groupBy('assigned_to_user')
            ->map(function ($tickets, $userId) {
                $user = User::find($userId);
                $resolved = $tickets->where('status', 'resolved')->count();
                $total = $tickets->count();

                $resolutionTimes = $tickets->filter(fn ($t) => $t->resolved_at)
                    ->map(fn ($t) => $t->created_at->diffInHours($t->resolved_at));

                return [
                    'agent_id' => $userId,
                    'agent_name' => $user?->name ?? 'Unknown',
                    'total_assigned' => $total,
                    'total_resolved' => $resolved,
                    'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
                    'average_resolution_hours' => $resolutionTimes->isNotEmpty() ? round($resolutionTimes->average(), 2) : 0,
                ];
            })
            ->sortByDesc('total_assigned')
            ->values();

        return $agentStats;
    }

    /**
     * Get SLA compliance statistics
     */
    public function getSLAComplianceStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = HelpdeskTicket::whereNotNull('sla_resolution_due_at');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $resolved = $query->whereNotNull('resolved_at')->count();
        $metSLA = $query->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
            ->count();
        $breachedSLA = $query->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '>', 'sla_resolution_due_at')
            ->count();
        $atRisk = $query->whereNull('resolved_at')
            ->where('sla_resolution_due_at', '<=', now()->addHours(24))
            ->count();

        return [
            'total_with_sla' => $total,
            'total_resolved' => $resolved,
            'met_sla' => $metSLA,
            'breached_sla' => $breachedSLA,
            'at_risk' => $atRisk,
            'compliance_rate' => $resolved > 0 ? round(($metSLA / $resolved) * 100, 1) : 100.0,
        ];
    }

    /**
     * Get daily ticket trends for charting
     */
    public function getDailyTicketTrends(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();

        $tickets = HelpdeskTicket::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M d');
            $data[] = $tickets->firstWhere('date', $date)?->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get comprehensive report data for export
     */
    public function getComprehensiveReportData(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        return [
            'period' => [
                'start' => $startDate?->format('Y-m-d') ?? 'All time',
                'end' => $endDate?->format('Y-m-d') ?? 'Present',
            ],
            'volume' => $this->getTicketVolumeStats($startDate, $endDate),
            'resolution_times' => $this->getResolutionTimeStats($startDate, $endDate),
            'agent_performance' => $this->getAgentPerformanceStats($startDate, $endDate)->toArray(),
            'sla_compliance' => $this->getSLAComplianceStats($startDate, $endDate),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
