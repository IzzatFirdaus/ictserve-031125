<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\User;

/**
 * Policy: HelpdeskTicketPolicy
 *
 * Authorization policy for HelpdeskTicket model operations.
 * Supports hybrid architecture: guest submissions (no user_id) and authenticated submissions.
 *
 * @see D03-FR-001.1 (Hybrid helpdesk ticket submission)
 * @see D03-FR-022.5 (Role-based access for authenticated users)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class HelpdeskTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     * Staff can view their own tickets, admin/superuser can view all tickets.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can access the ticket list (filtered by ownership)
    }

    /**
     * Determine whether the user can view the model.
     * Users can view their own tickets, admin/superuser can view any ticket.
     */
    public function view(User $user, HelpdeskTicket $ticket): bool
    {
        // Admin and superuser can view any ticket
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can view tickets they submitted (authenticated submissions)
        if ($ticket->user_id === $user->id) {
            return true;
        }

        // Users can view guest tickets if email matches
        if ($ticket->isGuestSubmission() && $ticket->guest_email === $user->email) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create tickets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only admin/superuser can update tickets (assignment, status, notes).
     */
    public function update(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     * Only superuser can delete tickets.
     */
    public function delete(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can add comments to the ticket.
     * Users can comment on their own tickets, admin/superuser can comment on any ticket.
     */
    public function addComment(User $user, HelpdeskTicket $ticket): bool
    {
        // Admin and superuser can comment on any ticket
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can comment on tickets they submitted
        if ($ticket->user_id === $user->id) {
            return true;
        }

        // Users can comment on guest tickets if email matches
        if ($ticket->isGuestSubmission() && $ticket->guest_email === $user->email) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can claim a guest ticket.
     * Users can claim guest tickets if email matches.
     */
    public function claim(User $user, HelpdeskTicket $ticket): bool
    {
        return $ticket->isGuestSubmission() && $ticket->guest_email === $user->email;
    }

    /**
     * Determine whether the user can claim a guest ticket.
     * Alias for claim() method for consistency with requirements.
     *
     * @see D03-FR-001.4 (Ticket claiming)
     */
    public function canClaim(User $user, HelpdeskTicket $ticket): bool
    {
        return $this->claim($user, $ticket);
    }

    /**
     * Determine whether the user can view internal comments and notes.
     * Only admin and superuser can view internal comments.
     *
     * @see D03-FR-001.3 (Internal comments for authenticated users)
     * @see D03-FR-010.1 (Role-based access control)
     */
    public function canViewInternal(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can restore the model.
     * Only superuser can restore soft-deleted tickets.
     */
    public function restore(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only superuser can force delete tickets.
     */
    public function forceDelete(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->isSuperuser();
    }
}
