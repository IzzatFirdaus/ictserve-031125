<?php

declare(strict_types=1);

/**
 * Component name: Logout Action
 * Description: Handles user logout with proper session invalidation and security
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1 (Authentication)
 * @trace D04 §5.2 (Security)
 * @trace D10 §7 (Component Documentation)
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
