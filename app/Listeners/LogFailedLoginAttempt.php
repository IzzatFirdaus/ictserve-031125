<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LogFailedLoginAttempt
{
    public function handle(Failed $event): void
    {
        // Don't log passwords. Record email and guard for diagnostics only.
        $credentials = $event->credentials ?? [];
        $email = $credentials['email'] ?? null;

        Log::warning('Failed login attempt', [
            'guard' => $event->guard ?? null,
            'email' => $email,
            'user' => $event->user?->getAuthIdentifier() ?? null,
            // For debugging: whether attempted password matches the stored hash.
            'password_matches' => isset($event->credentials['password']) && $event->user ? Hash::check($event->credentials['password'], $event->user->getAuthPassword()) : null,
        ]);
    }
}
