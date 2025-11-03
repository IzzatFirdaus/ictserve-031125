<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * Policy: UserPolicy
 *
 * Authorization policy for User model operations.
 * Implements four-role RBAC: staff, approver, admin, superuser.
 *
 * @see D03-FR-003.1 (Role-based access control)
 * @see D03-FR-003.2 (User management authorization)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     * Only admin and superuser can view user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     * Users can view their own profile, admin/superuser can view any user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can create models.
     * Only admin and superuser can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the model.
     * Users can update their own profile (except role), admin/superuser can update any user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the model's role.
     * Only superuser can change user roles.
     */
    public function updateRole(User $user, User $model): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can delete the model.
     * Only superuser can delete users, and cannot delete themselves.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isSuperuser() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     * Only superuser can restore soft-deleted users.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isSuperuser();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only superuser can force delete users.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperuser();
    }
}
