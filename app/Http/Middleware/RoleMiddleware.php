<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based Access Control Middleware
 *
 * Implements route-level access control for ICTServe RBAC system.
 * Supports multiple roles and permission checking.
 *
 * @see D03-FR-010.1 Role-based access control
 * @see D04 §4.4 RBAC middleware implementation
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Log unauthorized access attempt for security monitoring
        logger()->warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'required_roles' => $roles,
            'user_roles' => $user->getRoleNames()->toArray(),
            'route' => $request->route()?->getName(),
            'url' => $request->url(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        abort(403, 'Access denied. Required role: ' . implode(' or ', $roles));
    }
}
