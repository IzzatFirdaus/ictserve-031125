<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Component: SessionTimeoutWarning
 *
 * Displays a warning modal 2 minutes before session expiration.
 * Allows users to extend their session or be logged out automatically.
 *
 * @see D03-FR-015.3 (Session management: 30-minute inactivity timeout)
 * @see D03-NFR-004.2 (Security: automatic logout on inactivity)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-06
 */
class SessionTimeoutWarning extends Component
{
    public bool $showWarning = false;

    public int $remainingSeconds = 120; // 2 minutes warning period

    /**
     * Show the timeout warning modal.
     * Called from JavaScript when 28 minutes have elapsed.
     */
    #[On('show-timeout-warning')]
    public function showWarning(): void
    {
        $this->showWarning = true;
        $this->remainingSeconds = 120;
    }

    /**
     * Extend the user's session by making a keepalive request.
     * Resets the session timeout counter.
     */
    public function extendSession(): void
    {
        // Touch the session to refresh last activity timestamp
        if (Auth::check()) {
            request()->session()->put('last_activity', now()->timestamp);
            $this->showWarning = false;

            // Dispatch event to reset JavaScript timeout counter
            $this->dispatch('session-extended');

            // Flash success message
            session()->flash('success', __('auth.session_extended'));

            $this->js('location.reload()');
        }
    }

    /**
     * Log the user out when they decline to extend session.
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->js('window.location.href = '.json_encode(route('welcome')));
    }

    /**
     * Render the component.
     */
    public function render(): mixed
    {
        return view('livewire.session-timeout-warning');
    }
}
