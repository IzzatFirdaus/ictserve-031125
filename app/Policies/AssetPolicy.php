<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

/**
 * Policy: AssetPolicy
 *
 * Authorization policy for Asset model operations.
 * Implements four-role RBAC: staff, approver, admin, superuser.
 *
 * @see D03-FR-003.1 (Role-based access control)
 * @see D03-FR-003.3 (Asset management authorization)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-07
 */
class AssetPolicy
{
    /**
     * Determine whether the user can view any models.
     * Only admin and superuser can view asset list in admin panel.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     * Admin and superuser can view any asset.
     * Staff and approvers can view assets for loan applications.
     */
    public function view(User $user, Asset $asset): bool
    {
        return $user->hasAdminAccess() || $user->isApprover() || $user->isStaff();
    }

    /**
     * Determine whether the user can create models.
     * Only admin and superuser can create assets.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the model.
     * Only admin and superuser can update assets.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     * Only superuser can delete assets.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can restore the model.
     * Only superuser can restore soft-deleted assets.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only superuser can force delete assets.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can mark asset for maintenance.
     * Admin and superuser can mark assets for maintenance.
     */
    public function markForMaintenance(User $user, Asset $asset): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can retire an asset.
     * Only superuser can retire assets.
     */
    public function retire(User $user, Asset $asset): bool
    {
        return $user->isSuperuser();
    }
}
