<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Services\Notifications\SLANotificationService;
use Illuminate\Support\Facades\Log;

/**
 * SLA Management Service
 *
 * Tracks SLA compliance, triggers escalations, and manages auto-close workflows.
 *
 * @see D03-FR-008 SLA management requirements
 * @see D04 §5.3 SLA escalation workflow
 */
class SLAManagementService
{
    /**
     * Default SLA hours if category not specified.
     */
    private const DEFAULT_RESPONSE_HOURS = 4;

    private const DEFAULT_RESOLUTION_HOURS = 24;

    /**
     * Escalation threshold (percentage of SLA time).
     */
    private const ESCALATION_THRESHOLD = 0.75;

    /**
     * Auto-close days after resolution.
     */
    private const AUTO_CLOSE_DAYS = 7;

    public function __construct(
        private SLANotificationService $slaNotifications
    ) {}

    /**
     * Calculate SLA due dates for a ticket.
     *
     * @return array{sla_response_due_at: \Carbon\Carbon, sla_resolution_due_at: \Carbon\Carbon}
     */
    public function calculateDueDates(HelpdeskTicket $ticket, ?TicketCategory $category = null): array
    {
        $category ??= $ticket->category;

        $responseHours = $category->sla_response_hours ?? self::DEFAULT_RESPONSE_HOURS;
        $resolutionHours = $category->sla_resolution_hours ?? self::DEFAULT_RESOLUTION_HOURS;

        // Apply priority multipliers
        $priorityMultiplier = $this->getPriorityMultiplier($ticket->priority);
        $responseHours = (int) ceil($responseHours * $priorityMultiplier);
        $resolutionHours = (int) ceil($resolutionHours * $priorityMultiplier);

        return [
            'sla_response_due_at' => now()->addHours($responseHours),
            'sla_resolution_due_at' => now()->addHours($resolutionHours),
        ];
    }

    /**
     * Get priority multiplier for SLA calculation.
     */
    private function getPriorityMultiplier(string $priority): float
    {
        return match ($priority) {
            'urgent' => 0.5,
            'high' => 0.75,
            'medium' => 1.0,
            'low' => 1.5,
            default => 1.0,
        };
    }

    /**
     * Check SLA status for a ticket.
     *
     * @return array{status: string, response_remaining: int|null, resolution_remaining: int|null, at_risk: bool}
     */
    public function checkSLAStatus(HelpdeskTicket $ticket): array
    {
        $now = now();

        $responseRemaining = $ticket->sla_response_due_at
            ? (int) $now->diffInHours($ticket->sla_response_due_at, false)
            : null;

        $resolutionRemaining = $ticket->sla_resolution_due_at
            ? (int) $now->diffInHours($ticket->sla_resolution_due_at, false)
            : null;

        // Determine SLA status
        $status = 'on_track';
        $atRisk = false;

        // Check if breached
        if ($responseRemaining !== null && $responseRemaining < 0 && ! $ticket->first_response_at) {
            $status = 'breached';
        } elseif ($resolutionRemaining !== null && $resolutionRemaining < 0 && ! $ticket->resolved_at) {
            $status = 'breached';
        }

        // Check if at risk (>75% of SLA time used)
        if ($status !== 'breached') {
            $atRisk = $this->isAtRisk($ticket);
            if ($atRisk) {
                $status = 'at_risk';
            }
        }

        return [
            'status' => $status,
            'response_remaining' => $responseRemaining,
            'resolution_remaining' => $resolutionRemaining,
            'at_risk' => $atRisk,
        ];
    }

    /**
     * Check if ticket is at risk of SLA breach.
     */
    private function isAtRisk(HelpdeskTicket $ticket): bool
    {
        $now = now();

        // Check response SLA
        if ($ticket->sla_response_due_at && ! $ticket->first_response_at) {
            $totalHours = $ticket->created_at->diffInHours($ticket->sla_response_due_at);
            $elapsedHours = $ticket->created_at->diffInHours($now);

            if ($totalHours > 0 && ($elapsedHours / $totalHours) >= self::ESCALATION_THRESHOLD) {
                return true;
            }
        }

        // Check resolution SLA
        if ($ticket->sla_resolution_due_at && ! $ticket->resolved_at) {
            $totalHours = $ticket->created_at->diffInHours($ticket->sla_resolution_due_at);
            $elapsedHours = $ticket->created_at->diffInHours($now);

            if ($totalHours > 0 && ($elapsedHours / $totalHours) >= self::ESCALATION_THRESHOLD) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check and process escalations for all open tickets.
     * This should be called by a scheduled job.
     *
     * @return array{escalated: int, breached: int}
     */
    public function checkEscalations(): array
    {
        $escalated = 0;
        $breached = 0;

        $openTickets = HelpdeskTicket::query()
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
            ->whereNull('resolved_at')
            ->with(['category', 'user', 'division'])
            ->get();

        foreach ($openTickets as $ticket) {
            $slaStatus = $this->checkSLAStatus($ticket);

            if ($slaStatus['status'] === 'breached') {
                $this->recordSLABreach($ticket);
                $breached++;
            } elseif ($slaStatus['at_risk'] && ! $ticket->escalation_notified_at) {
                $this->escalateTicket($ticket);
                $escalated++;
            }
        }

        Log::info('SLA escalation check completed', [
            'escalated' => $escalated,
            'breached' => $breached,
            'total_checked' => $openTickets->count(),
        ]);

        return [
            'escalated' => $escalated,
            'breached' => $breached,
        ];
    }

    /**
     * Escalate a ticket at 75% SLA threshold.
     */
    public function escalateTicket(HelpdeskTicket $ticket): void
    {
        $slaStatus = $this->checkSLAStatus($ticket);

        // Update ticket with escalation info
        $ticket->update([
            'escalation_level' => ($ticket->escalation_level ?? 0) + 1,
            'escalation_notified_at' => now(),
        ]);

        // Send SLA warning notification to assigned agent and admins
        $this->slaNotifications->sendSlaBreachWarning($ticket);

        Log::warning('Ticket escalated due to SLA risk', [
            'ticket_number' => $ticket->ticket_number,
            'escalation_level' => $ticket->escalation_level,
            'sla_status' => $slaStatus,
        ]);
    }

    /**
     * Get SLA breach risk percentage for a ticket.
     */
    public function getSLABreachRisk(HelpdeskTicket $ticket): float
    {
        if ($ticket->resolved_at) {
            return 0.0;
        }

        $now = now();
        $riskPercentage = 0.0;

        // Check resolution SLA (primary indicator)
        if ($ticket->sla_resolution_due_at) {
            $totalHours = $ticket->created_at->diffInHours($ticket->sla_resolution_due_at);
            $elapsedHours = $ticket->created_at->diffInHours($now);

            if ($totalHours > 0) {
                $riskPercentage = min(($elapsedHours / $totalHours) * 100, 100);
            }
        }

        return round($riskPercentage, 2);
    }

    /**
     * Record SLA breach for a ticket.
     */
    public function recordSLABreach(HelpdeskTicket $ticket): void
    {
        // Already breached, skip
        if ($ticket->sla_breached_at) {
            return;
        }

        $ticket->update([
            'sla_breached_at' => now(),
            'sla_breach_type' => $this->determinBreachType($ticket),
        ]);

        // Send breach notification
        $this->slaNotifications->sendSlaBreachWarning($ticket);

        Log::error('SLA breach recorded', [
            'ticket_number' => $ticket->ticket_number,
            'breach_type' => $ticket->sla_breach_type,
            'sla_response_due_at' => $ticket->sla_response_due_at,
            'sla_resolution_due_at' => $ticket->sla_resolution_due_at,
        ]);
    }

    /**
     * Determine type of SLA breach.
     */
    private function determinBreachType(HelpdeskTicket $ticket): string
    {
        $now = now();

        $responseBreached = $ticket->sla_response_due_at
            && ! $ticket->first_response_at
            && $now->isAfter($ticket->sla_response_due_at);

        $resolutionBreached = $ticket->sla_resolution_due_at
            && ! $ticket->resolved_at
            && $now->isAfter($ticket->sla_resolution_due_at);

        if ($responseBreached && $resolutionBreached) {
            return 'both';
        } elseif ($responseBreached) {
            return 'response';
        } elseif ($resolutionBreached) {
            return 'resolution';
        }

        return 'unknown';
    }

    /**
     * Auto-close resolved tickets after specified days.
     * This should be called by a scheduled job.
     *
     * @return int Number of tickets auto-closed
     */
    public function autoClose(): int
    {
        $cutoffDate = now()->subDays(self::AUTO_CLOSE_DAYS);

        $ticketsToClose = HelpdeskTicket::query()
            ->where('status', 'resolved')
            ->where('resolved_at', '<=', $cutoffDate)
            ->get();

        $closedCount = 0;

        foreach ($ticketsToClose as $ticket) {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closure_reason' => 'Auto-closed after '.self::AUTO_CLOSE_DAYS.' days without reopening',
            ]);

            $closedCount++;

            Log::info('Ticket auto-closed', [
                'ticket_number' => $ticket->ticket_number,
                'resolved_at' => $ticket->resolved_at instanceof \Carbon\Carbon ? $ticket->resolved_at->toDateTimeString() : (string) $ticket->resolved_at,
            ]);
        }

        Log::info('Auto-close completed', [
            'closed_count' => $closedCount,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
        ]);

        return $closedCount;
    }

    /**
     * Get SLA metrics for reporting.
     *
     * @return array{total: int, on_track: int, at_risk: int, breached: int, compliance_rate: float}
     */
    public function getSLAMetrics(?string $period = 'month'): array
    {
        $startDate = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $tickets = HelpdeskTicket::query()
            ->where('created_at', '>=', $startDate)
            ->get();

        $total = $tickets->count();
        $onTrack = 0;
        $atRisk = 0;
        $breached = 0;

        foreach ($tickets as $ticket) {
            if ($ticket->sla_breached_at) {
                $breached++;
            } elseif ($this->isAtRisk($ticket)) {
                $atRisk++;
            } else {
                $onTrack++;
            }
        }

        $complianceRate = $total > 0
            ? round((($total - $breached) / $total) * 100, 2)
            : 100.0;

        return [
            'total' => $total,
            'on_track' => $onTrack,
            'at_risk' => $atRisk,
            'breached' => $breached,
            'compliance_rate' => $complianceRate,
        ];
    }

    /**
     * Update SLA due dates when ticket is assigned/responded.
     */
    public function updateSLAOnResponse(HelpdeskTicket $ticket): void
    {
        if (! $ticket->first_response_at) {
            $ticket->update([
                'first_response_at' => now(),
            ]);

            Log::info('First response recorded for SLA', [
                'ticket_number' => $ticket->ticket_number,
                'response_time_hours' => $ticket->created_at->diffInHours(now()),
            ]);
        }
    }

    /**
     * Pause SLA timer (e.g., waiting for customer).
     */
    public function pauseSLA(HelpdeskTicket $ticket, string $reason): void
    {
        $ticket->update([
            'sla_paused_at' => now(),
            'sla_pause_reason' => $reason,
        ]);

        Log::info('SLA paused', [
            'ticket_number' => $ticket->ticket_number,
            'reason' => $reason,
        ]);
    }

    /**
     * Resume SLA timer.
     */
    public function resumeSLA(HelpdeskTicket $ticket): void
    {
        $slaPausedAt = $ticket->sla_paused_at;

        if (! $slaPausedAt instanceof \Carbon\Carbon) {
            return;
        }

        $pausedDuration = $slaPausedAt->diffInHours(now());

        $newResponseDue = $ticket->sla_response_due_at instanceof \Carbon\Carbon
            ? $ticket->sla_response_due_at->copy()->addHours($pausedDuration)
            : null;

        $newResolutionDue = $ticket->sla_resolution_due_at instanceof \Carbon\Carbon
            ? $ticket->sla_resolution_due_at->copy()->addHours($pausedDuration)
            : null;

        // Extend due dates by paused duration
        $ticket->update([
            'sla_response_due_at' => $newResponseDue,
            'sla_resolution_due_at' => $newResolutionDue,
            'sla_paused_at' => null,
            'sla_pause_reason' => null,
            'sla_total_paused_hours' => ($ticket->sla_total_paused_hours ?? 0) + $pausedDuration,
        ]);

        Log::info('SLA resumed', [
            'ticket_number' => $ticket->ticket_number,
            'paused_duration_hours' => $pausedDuration,
        ]);
    }
}
