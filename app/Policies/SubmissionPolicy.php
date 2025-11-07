<?php

declare(strict_types=1);

// name: SubmissionPolicy
// description: Authorization policy for helpdesk tickets and loan applications
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-009, D04 §6.1, D11 §8 (Requirements 4.1, 15.3)
// last-updated: 2025-11-06

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Determine if the user can view their own helpdesk ticket.
     */
    public function viewHelpdeskTicket(User $user, HelpdeskTicket $ticket): bool
    {
        // User can view if they created it OR if they have Admin/Superuser role
        return $ticket->user_id === $user->id || $user->hasAnyRole(['Admin', 'Superuser']);
    }

    /**
     * Determine if the user can view their own loan application.
     */
    public function viewLoanApplication(User $user, LoanApplication $loanApplication): bool
    {
        // User can view if they created it OR if they're an approver for this division OR Admin/Superuser
        if ($loanApplication->user_id === $user->id) {
            return true;
        }

        if ($user->hasAnyRole(['Admin', 'Superuser'])) {
            return true;
        }

        // Approvers can view applications from their division
        if ($user->hasRole('Approver') && $user->division_id === $loanApplication->user->division_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can claim a guest submission (ticket).
     */
    public function claimGuestHelpdeskTicket(User $user, HelpdeskTicket $ticket): bool
    {
        // Can claim if:
        // 1. Ticket was created as guest (user_id is null)
        // 2. Email matches user's email
        // 3. Not already claimed
        return $ticket->user_id === null
            && $ticket->email === $user->email
            && $ticket->claimed_at === null;
    }

    /**
     * Determine if the user can claim a guest submission (loan application).
     */
    public function claimGuestLoanApplication(User $user, LoanApplication $loanApplication): bool
    {
        // Can claim if:
        // 1. Application was created as guest (user_id is null)
        // 2. Email matches user's email
        // 3. Not already claimed
        return $loanApplication->user_id === null
            && $loanApplication->email === $user->email
            && $loanApplication->claimed_at === null;
    }

    /**
     * Determine if the user can add internal comments.
     */
    public function addInternalComment(User $user): bool
    {
        // Only authenticated staff can add internal comments
        return $user->hasAnyRole(['Staff', 'Approver', 'Admin', 'Superuser']);
    }

    /**
     * Determine if the user can view internal comments.
     */
    public function viewInternalComments(User $user): bool
    {
        // Only authenticated staff can view internal comments
        return $user->hasAnyRole(['Staff', 'Approver', 'Admin', 'Superuser']);
    }

    /**
     * Determine if the user can export their own submissions.
     */
    public function exportOwnSubmissions(User $user): bool
    {
        // All authenticated users can export their own submissions
        return $user->hasAnyRole(['Staff', 'Approver', 'Admin', 'Superuser']);
    }

    /**
     * Determine if the user can export all submissions (admin function).
     */
    public function exportAllSubmissions(User $user): bool
    {
        // Only Admin and Superuser can export all submissions
        return $user->hasAnyRole(['Admin', 'Superuser']);
    }

    /**
     * Determine if the user can approve loan applications.
     */
    public function approveLoanApplication(User $user, LoanApplication $loanApplication): bool
    {
        // Must be Approver, Admin, or Superuser
        if (! $user->hasAnyRole(['Approver', 'Admin', 'Superuser'])) {
            return false;
        }

        // Approvers can only approve applications from their division
        if ($user->hasRole('Approver')) {
            return $user->division_id === $loanApplication->user->division_id;
        }

        // Admin and Superuser can approve any application
        return true;
    }
}
