<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Access Middleware
 *
 * Ensures that only users with admin or superuser roles can access the Filament admin panel.
 * Implements Requirements 17.1 (RBAC) and 4.1 (User Management Authorization).
 */
class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the authenticated user has either 'admin' or 'superuser' role.
     * If not, aborts with 403 Forbidden response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has admin or superuser role using Spatie Permission or role attribute
        if (! $user->hasRole(['admin', 'superuser']) && ! in_array($user->role, ['admin', 'superuser'])) {
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
