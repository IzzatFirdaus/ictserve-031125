<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session Timeout Middleware
 *
 * Implements automatic session timeout after 30 minutes of inactivity.
 * Requirement 17.2: Session timeout (30 minutes inactivity)
 * Requirement 17.5: Automatic logout on session expiry
 *
 * @version 1.0.0
 *
 * @since 2025-11-07
 */
class SessionTimeoutMiddleware
{
    /**
     * Session timeout duration in minutes
     */
    private const TIMEOUT_MINUTES = 30;

    /**
     * Handle an incoming request.
     *
     * Checks if the user's session has been inactive for more than 30 minutes.
     * If so, logs out the user and redirects to login with a timeout message.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity_time');
            $currentTime = now()->timestamp;

            // Check if last activity time exists
            if ($lastActivity) {
                $inactiveMinutes = ($currentTime - $lastActivity) / 60;

                // If inactive for more than timeout duration, logout
                if ($inactiveMinutes > self::TIMEOUT_MINUTES) {
                    Auth::logout();
                    Session::invalidate();
                    Session::regenerateToken();

                    return redirect()->route('login')
                        ->with('status', __('Your session has expired due to inactivity. Please login again.'));
                }
            }

            // Update last activity time
            Session::put('last_activity_time', $currentTime);
        }

        return $next($request);
    }
}
