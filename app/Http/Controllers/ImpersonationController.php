<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * ImpersonationController - Handles user impersonation for superusers
 *
 * @trace D03-FR-002.1 (User Management)
 * @trace D04 §3.1 (Admin Panel)
 */
class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     * Only superusers can impersonate other users.
     */
    public function impersonate(User $user): RedirectResponse
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        // Only superusers can impersonate
        if (! $currentUser || ! $currentUser->hasRole('superuser')) {
            abort(403, __('users.impersonate_unauthorized'));
        }

        // Prevent impersonating yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', __('users.impersonate_self_error'));
        }

        // Prevent impersonating other superusers (security measure)
        if ($user->hasRole('superuser')) {
            return back()->with('error', __('users.impersonate_superuser_error'));
        }

        session()->put('impersonator_id', Auth::id());
        Auth::login($user);

        return redirect()->route('portal.dashboard');
    }

    /**
     * Stop impersonating and return to original user.
     */
    public function stop(): RedirectResponse
    {
        if (! session()->has('impersonator_id')) {
            return redirect()->route('portal.dashboard');
        }

        $impersonatorId = session('impersonator_id');
        if (is_int($impersonatorId) || is_string($impersonatorId)) {
            Auth::loginUsingId($impersonatorId);
        }
        session()->forget('impersonator_id');

        return redirect()->to('/admin');
    }
}
