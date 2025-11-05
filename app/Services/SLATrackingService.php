<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\SLABreachWarningNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * SLA Tracking Service
 *
 * Monitors SLA compliance and triggers escalation at 25% breach threshold.
 *
 * @trace Requirements 8.4, 10.1, 10.3, 13.3
 */
class SLATrackingService
{
    /**
     * Calculate and set SLA due dates for ticket
     */
    public function calculateSLADueDates(HelpdeskTicket $ticket): void
    {
        if (! $ticket->category) {
            Log::warning('Cannot calculate SLA - no category', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        $responseHours = $ticket->category->sla_response_hours ?? 24;
        $resolutionHours = $ticket->category->sla_resolution_hours ?? 72;

        $ticket->update([
            'sla_response_due_at' => now()->addHours($responseHours),
            'sla_resolution_due_at' => now()->addHours($resolutionHours),
        ]);

        Log::info('SLA due dates calculated', [
            'ticket_id' => $ticket->id,
            'response_due' => $ticket->sla_response_due_at,
            'resolution_due' => $ticket->sla_resolution_due_at,
        ]);
    }

    /**
     * Check if ticket is approaching SLA breach (within 25% of deadline)
     */
    public function isApproachingBreach(HelpdeskTicket $ticket): bool
    {
        if (! $ticket->sla_resolution_due_at) {
            return false;
        }

        $now = now();
        $dueAt = $ticket->sla_resolution_due_at;
        $createdAt = $ticket->created_at;

        if (! $createdAt) {
            return false; // Cannot calculate SLA without creation timestamp
        }

        $totalDuration = $createdAt->diffInMinutes($dueAt);
        $elapsed = $createdAt->diffInMinutes($now);

        // Check if we're within 25% of breach time
        return $elapsed >= ($totalDuration * 0.75);
    }

    /**
     * Check if ticket has breached SLA
     */
    public function hasBreachedSLA(HelpdeskTicket $ticket): bool
    {
        if (! $ticket->sla_resolution_due_at) {
            return false;
        }

        return now()->greaterThan($ticket->sla_resolution_due_at) &&
            ! in_array($ticket->status, ['resolved', 'closed']);
    }

    /**
     * Get all tickets approaching SLA breach
     *
     * @return Collection<int, HelpdeskTicket>
     */
    public function getTicketsApproachingBreach(): Collection
    {
        return HelpdeskTicket::query()
            ->whereNotNull('sla_resolution_due_at')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with(['category', 'assignedUser', 'assignedDivision'])
            ->get()
            ->filter(fn ($ticket) => $this->isApproachingBreach($ticket));
    }

    /**
     * Get all tickets that have breached SLA
     *
     * @return Collection<int, HelpdeskTicket>
     */
    public function getBreachedTickets(): Collection
    {
        return HelpdeskTicket::query()
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with(['category', 'assignedUser', 'assignedDivision'])
            ->get();
    }

    /**
     * Send escalation notifications for tickets approaching breach
     */
    public function escalateApproachingBreaches(): int
    {
        $tickets = $this->getTicketsApproachingBreach();
        $escalated = 0;

        foreach ($tickets as $ticket) {
            try {
                // Ensure ticket is HelpdeskTicket instance
                if (! $ticket instanceof HelpdeskTicket) {
                    continue;
                }

                // Notify assigned user
                if ($ticket->assignedUser instanceof User) {
                    $ticket->assignedUser->notify(new SLABreachWarningNotification($ticket));
                }

                // Notify division head if assigned to division
                if ($ticket->assignedDivision) {
                    $divisionHead = $ticket->assignedDivision->head;
                    if ($divisionHead) {
                        $divisionHead->notify(new SLABreachWarningNotification($ticket));
                    }
                }

                // Add internal comment
                $ticket->comments()->create([
                    'user_id' => null,
                    'commenter_name' => 'System',
                    'commenter_email' => 'system@ictserve.local',
                    'comment' => 'SLA breach warning: Ticket is within 25% of resolution deadline.',
                    'is_internal' => true,
                ]);

                $escalated++;

                Log::info('SLA escalation sent', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                ]);
            } catch (\Exception $e) {
                Log::error('SLA escalation failed', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $escalated;
    }

    /**
     * Mark ticket as responded
     */
    public function markAsResponded(HelpdeskTicket $ticket): void
    {
        if (! $ticket->responded_at) {
            $ticket->update([
                'responded_at' => now(),
            ]);

            $createdAt = $ticket->created_at;
            $respondedAt = $ticket->responded_at;

            Log::info('Ticket marked as responded', [
                'ticket_id' => $ticket->id,
                'response_time' => ($createdAt && $respondedAt)
                    ? $createdAt->diffForHumans($respondedAt)
                    : 'N/A',
            ]);
        }
    }

    /**
     * Get SLA compliance statistics
     *
     * @return array<string, int|float>
     */
    public function getComplianceStats(): array
    {
        $total = HelpdeskTicket::query()
            ->whereNotNull('sla_resolution_due_at')
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $compliant = HelpdeskTicket::query()
            ->whereNotNull('sla_resolution_due_at')
            ->whereIn('status', ['resolved', 'closed'])
            ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
            ->count();

        $breached = $total - $compliant;
        $complianceRate = $total > 0 ? ($compliant / $total) * 100 : 0;

        return [
            'total' => $total,
            'compliant' => $compliant,
            'breached' => $breached,
            'compliance_rate' => round($complianceRate, 2),
        ];
    }
}
