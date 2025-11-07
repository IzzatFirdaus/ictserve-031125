<?php

declare(strict_types=1);

namespace App\Observers;

use App\Mail\Users\UserWelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Component name: User Observer
 * Description: Observer for User model events, handles welcome email and password generation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-004.3 (User Management - Welcome Email)
 * @trace D04 §3.3 (User Creation Workflow)
 * @trace D10 §7 (Component Documentation)
 *
 * @version 1.0.0
 *
 * @created 2025-11-07
 */
class UserObserver
{
    /**
     * Handle the User "created" event.
     * Sends welcome email with temporary password when user is created via admin panel.
     */
    public function created(User $user): void
    {
        // Only send welcome email if created via admin panel (has no password yet or needs reset)
        if ($user->wasRecentlyCreated && ! empty($user->getOriginal('password'))) {
            // Check if temporary password was stored in a custom attribute
            $temporaryPassword = $user->temporary_password ?? null;

            if ($temporaryPassword) {
                // Send welcome email
                $loginUrl = route('filament.admin.auth.login');

                Mail::to($user->email)->queue(
                    new UserWelcomeMail($user, $temporaryPassword, $loginUrl)
                );

                // Clear temporary password from memory for security
                unset($user->temporary_password);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log role changes for audit trail (handled by Laravel Auditing)
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Cleanup handled by cascade deletes
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Log restoration (handled by Laravel Auditing)
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Permanent deletion logged (handled by Laravel Auditing)
    }
}
