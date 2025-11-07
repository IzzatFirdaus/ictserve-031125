<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\Helpdesk\TicketStatusChangedMail;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Ticket Status Transition Service
 *
 * Implements state machine for ticket status transitions with validation
 * and email notifications.
 *
 * @trace Requirements D03-FR-001.4, D04 §4.1, Requirement 1.4
 */
class TicketStatusTransitionService
{
    /**
     * Valid status transitions
     * Format: 'current_status' => ['allowed_next_statuses']
     */
    private const VALID_TRANSITIONS = [
        'open' => ['assigned', 'in_progress', 'closed'],
        'assigned' => ['in_progress', 'pending_user', 'resolved', 'closed'],
        'in_progress' => ['pending_user', 'resolved', 'closed'],
        'pending_user' => ['in_progress', 'resolved', 'closed'],
        'resolved' => ['closed', 'in_progress'], // Can reopen if needed
        'closed' => [], // Terminal state - cannot transition
    ];

    /**
     * Validate if status transition is allowed
     */
    public function canTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true; // Same status is always allowed
        }

        $allowedTransitions = self::VALID_TRANSITIONS[$currentStatus] ?? [];

        return in_array($newStatus, $allowedTransitions, true);
    }

    /**
     * Get allowed next statuses for current status
     */
    public function getAllowedTransitions(string $currentStatus): array
    {
        return self::VALID_TRANSITIONS[$currentStatus] ?? [];
    }

    /**
     * Transition ticket to new status with validation
     *
     * @throws ValidationException
     */
    public function transition(HelpdeskTicket $ticket, string $newStatus, ?string $notes = null): void
    {
        if (! $this->canTransition($ticket->status, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$ticket->status} to {$newStatus}",
            ]);
        }

        $oldStatus = $ticket->status;

        // Update ticket status
        $ticket->update([
            'status' => $newStatus,
            'resolved_at' => $newStatus === 'resolved' ? now() : $ticket->resolved_at,
            'closed_at' => $newStatus === 'closed' ? now() : $ticket->closed_at,
        ]);

        // Audit trail is automatically logged by OwenIt\Auditing package

        // Send email notification to ticket owner
        $this->sendStatusChangeNotification($ticket, $oldStatus, $newStatus);
    }

    /**
     * Send email notification for status change
     */
    private function sendStatusChangeNotification(
        HelpdeskTicket $ticket,
        string $oldStatus,
        string $newStatus
    ): void {
        // Notify ticket owner (guest or authenticated user)
        if ($ticket->user_id) {
            $user = User::find($ticket->user_id);
            if ($user) {
                Mail::to($user->email)
                    ->queue(new TicketStatusChangedMail($ticket, $oldStatus, $newStatus));
            }
        } elseif ($ticket->guest_email) {
            Mail::to($ticket->guest_email)
                ->queue(new TicketStatusChangedMail($ticket, $oldStatus, $newStatus));
        }

        // Notify assigned user if different from ticket owner
        if ($ticket->assigned_to_user && $ticket->assigned_to_user !== $ticket->user_id) {
            $assignedUser = User::find($ticket->assigned_to_user);
            if ($assignedUser) {
                Mail::to($assignedUser->email)
                    ->queue(new TicketStatusChangedMail($ticket, $oldStatus, $newStatus));
            }
        }
    }

    /**
     * Get status transition description
     */
    public function getTransitionDescription(string $currentStatus, string $newStatus): string
    {
        return match ([$currentStatus, $newStatus]) {
            ['open', 'assigned'] => 'Tiket telah ditugaskan kepada pegawai',
            ['open', 'in_progress'] => 'Tiket sedang diproses',
            ['assigned', 'in_progress'] => 'Pegawai mula memproses tiket',
            ['in_progress', 'pending_user'] => 'Menunggu maklum balas daripada pengguna',
            ['pending_user', 'in_progress'] => 'Pemprosesan tiket disambung semula',
            ['in_progress', 'resolved'] => 'Tiket telah diselesaikan',
            ['resolved', 'closed'] => 'Tiket ditutup',
            ['resolved', 'in_progress'] => 'Tiket dibuka semula untuk pemprosesan lanjut',
            default => "Status berubah dari {$currentStatus} ke {$newStatus}",
        };
    }
}
