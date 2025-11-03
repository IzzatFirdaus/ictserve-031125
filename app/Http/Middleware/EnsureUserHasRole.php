<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureUserHasRole
 *
 * Ensures the authenticated user has one of the required roles to access a route.
 * Part of the four-role RBAC system: staff, approver, admin, superuser.
 *
 * @see D03-FR-001.6 (Four-role RBAC system)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  One or more roles required to access the route
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (! $request->user()) {
            abort(401, 'Unauthenticated');
        }

        // Check if user has one of the required roles
        $userRole = $request->user()->role;

        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Unauthorized - Insufficient permissions');
        }

        return $next($request);
    }
}
