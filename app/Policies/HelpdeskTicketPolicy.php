<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\User;

/**
 * PKS 5.2.1 Compliant HelpdeskTicketPolicy
 *
 * Authorization policy for HelpdeskTicket model operations.
 * SSO-only architecture - all tickets require authenticated user_id.
 * Guest submission functionality has been removed per PKS 5.2.1.
 *
 * @see D03-FR-001.1 (Authenticated helpdesk ticket submission)
 * @see D03-FR-022.5 (Role-based access for authenticated users)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @trace Requirements 1.1, 3.1, 12.3, 25.1
 */
class HelpdeskTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     * For Filament admin panel: Only admin/superuser can view all tickets.
     * For staff portal: All authenticated users can view their own tickets (filtered by ownership).
     */
    public function viewAny(User $user): bool
    {
        // For Filament admin panel, only admin and superuser can access
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     * PKS 5.2.1: Users can view their own tickets, admin/superuser can view any ticket.
     */
    public function view(User $user, HelpdeskTicket $ticket): bool
    {
        // Admin and superuser can view any ticket
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can view tickets they submitted (authenticated submissions only)
        return $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * PKS 5.2.1: All authenticated users can create tickets.
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
     * PKS 5.2.1: Users can comment on their own tickets, admin/superuser can comment on any ticket.
     */
    public function addComment(User $user, HelpdeskTicket $ticket): bool
    {
        // Admin and superuser can comment on any ticket
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can comment on tickets they submitted (authenticated only)
        return $ticket->user_id === $user->id;
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
