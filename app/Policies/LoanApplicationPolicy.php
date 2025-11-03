<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;

/**
 * Policy: LoanApplicationPolicy
 *
 * Authorization policy for LoanApplication model operations.
 * Supports hybrid architecture: guest applications (no user_id) and authenticated applications.
 * Implements dual approval workflow: email-based (no login) and portal-based (with login).
 *
 * @see D03-FR-001.4 (Hybrid loan application submission)
 * @see D03-FR-001.6 (Dual approval workflow)
 * @see D03-FR-022.5 (Role-based access for authenticated users)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class LoanApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     * Staff can view their own applications, approvers can view pending approvals,
     * admin/superuser can view all applications.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can access the application list (filtered by ownership/approval rights)
    }

    /**
     * Determine whether the user can view the model.
     * Users can view their own applications, approvers can view applications assigned to them,
     * admin/superuser can view any application.
     */
    public function view(User $user, LoanApplication $application): bool
    {
        // Admin and superuser can view any application
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Approvers can view applications assigned to them
        if ($user->canApprove() && $application->approver_id === $user->id) {
            return true;
        }

        // Users can view applications they submitted (authenticated submissions)
        if ($application->user_id === $user->id) {
            return true;
        }

        // Users can view guest applications if email matches
        if ($application->isGuestSubmission() && $application->applicant_email === $user->email) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create loan applications.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only admin/superuser can update applications (except approval decisions).
     */
    public function update(User $user, LoanApplication $application): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     * Only superuser can delete applications.
     */
    public function delete(User $user, LoanApplication $application): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can approve/decline the application (portal-based approval).
     * Only approvers, admin, and superuser can approve applications.
     * Approvers can only approve applications assigned to them.
     */
    public function approve(User $user, LoanApplication $application): bool
    {
        // Admin and superuser can approve any application
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Approvers can approve applications assigned to them
        if ($user->canApprove() && $application->approver_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can claim a guest application.
     * Users can claim guest applications if email matches.
     */
    public function claim(User $user, LoanApplication $application): bool
    {
        return $application->isGuestSubmission() && $application->applicant_email === $user->email;
    }

    /**
     * Determine whether the user can request an extension.
     * Users can request extensions for their own approved applications.
     */
    public function requestExtension(User $user, LoanApplication $application): bool
    {
        // Application must be approved
        if ($application->status !== 'approved') {
            return false;
        }

        // Admin and superuser can request extensions for any application
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can request extensions for their own applications
        if ($application->user_id === $user->id) {
            return true;
        }

        // Users can request extensions for guest applications if email matches
        if ($application->isGuestSubmission() && $application->applicant_email === $user->email) {
            return true;
        }

        return false;
    }
}
