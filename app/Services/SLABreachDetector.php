<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * SLA Breach Detector Service
 *
 * Detects and manages SLA breaches for helpdesk tickets.
 * Provides separate methods for dashboard metrics vs escalation processing.
 *
 * @see D03-FR-008 SLA management requirements
 * @see D04 §5.3 SLA escalation workflow
 * @see Requirements 18.1, 18.2
 */
class SLABreachDetector
{
    /**
     * Get all tickets with SLA breaches (for dashboard metrics).
     *
     * Returns tickets that have already been marked as breached and are still open.
     * Used for displaying current breach count on dashboard widgets.
     *
     * @return Collection<int, HelpdeskTicket>
     */
    public function getCurrentlyBreachedTickets(): Collection
    {
        return HelpdeskTicket::query()
            ->whereNotNull('sla_breached_at')
            ->whereNotIn('status', ['closed', 'resolved'])
            ->with(['category', 'assignedUser', 'user'])
            ->orderBy('sla_breached_at', 'asc')
            ->get();
    }

    /**
     * Get new breaches (for escalation job - only unprocessed).
     *
     * Returns tickets that have breached SLA but haven't been marked yet.
     * Used by SLAAutoEscalationJob to process new breaches.
     *
     * @return Collection<int, HelpdeskTicket>
     */
    public function getNewBreaches(): Collection
    {
        $now = now();

        return HelpdeskTicket::query()
            ->where(function ($query) use ($now): void {
                // Response SLA breached (no response yet and past due)
                $query->where(function ($q) use ($now): void {
                    $q->where('sla_response_due_at', '<', $now)
                        ->whereNull('responded_at')
                        ->whereNull('first_response_at');
                })
                    // OR Resolution SLA breached (not resolved and past due)
                    ->orWhere(function ($q) use ($now): void {
                        $q->where('sla_resolution_due_at', '<', $now)
                            ->whereNull('resolved_at');
                    });
            })
            ->whereNotIn('status', ['closed', 'resolved'])
            ->whereNull('sla_breached_at') // Only unprocessed breaches
            ->whereNull('sla_paused_at') // Not paused tickets
            ->with(['category', 'assignedUser', 'user'])
            ->get();
    }

    /**
     * Mark ticket as SLA breached.
     *
     * @param  string  $breachType  Type of breach: 'response', 'resolution', or 'both'
     */
    public function markAsBreached(HelpdeskTicket $ticket, string $breachType): void
    {
        // Skip if already marked as breached
        if ($ticket->sla_breached_at !== null) {
            Log::debug('Ticket already marked as breached', [
                'ticket_number' => $ticket->ticket_number,
                'existing_breach_at' => $ticket->sla_breached_at,
            ]);

            return;
        }

        $ticket->update([
            'sla_breached_at' => now(),
            'sla_breach_type' => $breachType,
        ]);

        Log::warning('SLA breach recorded', [
            'ticket_number' => $ticket->ticket_number,
            'breach_type' => $breachType,
            'sla_response_due_at' => $ticket->sla_response_due_at?->toDateTimeString(),
            'sla_resolution_due_at' => $ticket->sla_resolution_due_at?->toDateTimeString(),
            'responded_at' => $ticket->responded_at?->toDateTimeString(),
            'resolved_at' => $ticket->resolved_at?->toDateTimeString(),
        ]);
    }

    /**
     * Determine the type of SLA breach for a ticket.
     *
     * @return string 'response', 'resolution', 'both', or 'none'
     */
    public function determineBreachType(HelpdeskTicket $ticket): string
    {
        $now = now();

        $responseBreached = $ticket->sla_response_due_at !== null
            && $ticket->first_response_at === null
            && $ticket->responded_at === null
            && $now->isAfter($ticket->sla_response_due_at);

        $resolutionBreached = $ticket->sla_resolution_due_at !== null
            && $ticket->resolved_at === null
            && $now->isAfter($ticket->sla_resolution_due_at);

        if ($responseBreached && $resolutionBreached) {
            return 'both';
        }

        if ($responseBreached) {
            return 'response';
        }

        if ($resolutionBreached) {
            return 'resolution';
        }

        return 'none';
    }

    /**
     * Get count of currently breached tickets (for dashboard metrics).
     */
    public function getBreachedCount(): int
    {
        return HelpdeskTicket::query()
            ->whereNotNull('sla_breached_at')
            ->whereNotIn('status', ['closed', 'resolved'])
            ->count();
    }

    /**
     * Get SLA compliance percentage for a given period.
     *
     * @param  string  $period  'day', 'week', 'month', 'year'
     */
    public function getComplianceRate(string $period = 'month'): float
    {
        $startDate = $this->resolvePeriodStart($period);

        $totalTickets = HelpdeskTicket::query()
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($totalTickets === 0) {
            return 100.0;
        }

        $breachedTickets = HelpdeskTicket::query()
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('sla_breached_at')
            ->count();

        return round((($totalTickets - $breachedTickets) / $totalTickets) * 100, 2);
    }

    /**
     * Get tickets at risk of SLA breach (approaching deadline).
     *
     * @param  int  $hoursThreshold  Hours before deadline to consider "at risk"
     * @return Collection<int, HelpdeskTicket>
     */
    public function getAtRiskTickets(int $hoursThreshold = 2): Collection
    {
        $now = now();
        $threshold = $now->copy()->addHours($hoursThreshold);

        return HelpdeskTicket::query()
            ->whereNull('sla_breached_at')
            ->whereNotIn('status', ['closed', 'resolved'])
            ->whereNull('sla_paused_at')
            ->where(function ($query) use ($now, $threshold): void {
                // Response SLA at risk
                $query->where(function ($q) use ($now, $threshold): void {
                    $q->whereNull('first_response_at')
                        ->whereNull('responded_at')
                        ->where('sla_response_due_at', '>', $now)
                        ->where('sla_response_due_at', '<=', $threshold);
                })
                    // OR Resolution SLA at risk
                    ->orWhere(function ($q) use ($now, $threshold): void {
                        $q->whereNull('resolved_at')
                            ->where('sla_resolution_due_at', '>', $now)
                            ->where('sla_resolution_due_at', '<=', $threshold);
                    });
            })
            ->with(['category', 'assignedUser', 'user'])
            ->orderBy('sla_resolution_due_at', 'asc')
            ->get();
    }

    /**
     * Resolve reporting period start date.
     */
    private function resolvePeriodStart(string $period): \Carbon\Carbon
    {
        $now = now();

        return match ($period) {
            'day' => $now->copy()->subDay(),
            'week' => $now->copy()->subDays(7),
            'month' => $now->copy()->subDays(30),
            'year' => $now->copy()->subYear(),
            default => $now->copy()->subDays(30),
        };
    }
}
