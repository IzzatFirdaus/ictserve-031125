<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Notification Policy
 *
 * Implements role-based access controls for notification access.
 * Ensures users can only view and manage their own notifications
 * unless they have administrative privileges.
 *
 * @see Requirements 9.4 - Notification access authorization
 *
 * @trace D03 SRS-FR-043 (notification security)
 */
class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any notifications.
     *
     * Admins can view all notifications for monitoring purposes.
     * Regular users can only view their own notifications.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'bpm_admin']);
    }

    /**
     * Determine whether the user can view the notification.
     *
     * Users can only view notifications that belong to them.
     * Admins can view any notification for support purposes.
     */
    public function view(User $user, DatabaseNotification $notification): bool
    {
        // Admins can view any notification
        if ($user->hasRole(['super_admin', 'admin', 'bpm_admin'])) {
            return true;
        }

        // Users can only view their own notifications
        return $notification->notifiable_type === User::class
            && $notification->notifiable_id === $user->id;
    }

    /**
     * Determine whether the user can mark the notification as read.
     *
     * Only the notification owner can mark it as read.
     */
    public function markAsRead(User $user, DatabaseNotification $notification): bool
    {
        return $notification->notifiable_type === User::class
            && $notification->notifiable_id === $user->id;
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * Users can delete their own notifications.
     * Admins can delete any notification for cleanup purposes.
     */
    public function delete(User $user, DatabaseNotification $notification): bool
    {
        // Admins can delete any notification
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Users can only delete their own notifications
        return $notification->notifiable_type === User::class
            && $notification->notifiable_id === $user->id;
    }

    /**
     * Determine whether the user can bulk delete notifications.
     *
     * Only admins can perform bulk deletion operations.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can export notifications.
     *
     * Users can export their own notifications.
     * Admins can export all notifications for reporting.
     */
    public function export(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'bpm_admin']);
    }

    /**
     * Determine whether the user can view notification analytics.
     *
     * Only admins can view system-wide notification analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'bpm_admin']);
    }

    /**
     * Determine whether the user can manage notification preferences.
     *
     * Users can manage their own preferences.
     * Admins can manage preferences for any user.
     */
    public function managePreferences(User $user, ?User $targetUser = null): bool
    {
        // If no target user specified, user is managing their own preferences
        if ($targetUser === null) {
            return true;
        }

        // Admins can manage any user's preferences
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Users can only manage their own preferences
        return $user->id === $targetUser->id;
    }
}
