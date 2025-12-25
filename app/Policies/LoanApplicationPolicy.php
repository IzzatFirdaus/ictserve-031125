<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;

/**
 * PKS 5.2.1 Compliant LoanApplicationPolicy
 *
 * Authorization policy for LoanApplication model operations.
 * SSO-only architecture - all applications require authenticated user_id.
 * Guest submission functionality has been removed per PKS 5.2.1.
 * Implements dual approval workflow: email-based (no login) and portal-based (with login).
 *
 * @see D03-FR-001.4 (Authenticated loan application submission)
 * @see D03-FR-001.6 (Dual approval workflow)
 * @see D03-FR-022.5 (Role-based access for authenticated users)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @trace Requirements 1.4, 1.5, 3.1, 12.3, 25.1
 */
class LoanApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     * For Filament admin panel: Only admin/superuser can view all applications.
     * For staff portal: All authenticated users can view their own applications (filtered by ownership/approval rights).
     */
    public function viewAny(User $user): bool
    {
        // For Filament admin panel, only admin and superuser can access
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     * PKS 5.2.1: Users can view their own applications, approvers can view applications assigned to them,
     * admin/superuser can view any application.
     */
    public function view(User $user, LoanApplication $application): bool
    {
        // Admin and superuser can view any application
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Approvers can view applications assigned to them (by email)
        if (
            $user->canApprove() && $application->approver_email &&
            \strtolower($application->approver_email) === \strtolower($user->email)
        ) {
            return true;
        }

        // Users can view applications they submitted (authenticated submissions only)
        return $application->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * PKS 5.2.1: All authenticated users can create loan applications.
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

        // Approvers can approve applications assigned to them (by email)
        if (
            $user->canApprove() && $application->approver_email &&
            \strtolower($application->approver_email) === \strtolower($user->email)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can request an extension.
     * PKS 5.2.1: Users can request extensions for their own approved applications.
     */
    public function requestExtension(User $user, LoanApplication $application): bool
    {
        // Application must be approved
        if ($application->status !== LoanStatus::APPROVED) {
            return false;
        }

        // Admin and superuser can request extensions for any application
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can request extensions for their own applications (authenticated only)
        return $application->user_id === $user->id;
    }

    /**
     * Determine whether the user can issue/collect assets for approved loans.
     * Only admin and superuser can mark assets as collected.
     */
    public function issue(User $user, LoanApplication $application): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can process asset returns.
     * Only admin and superuser can process returns.
     */
    public function return(User $user, LoanApplication $application): bool
    {
        return $user->hasAdminAccess();
    }
}
