<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permission-based Access Control Middleware
 *
 * Implements granular permission checking for ICTServe RBAC system.
 * Supports multiple permissions with OR logic.
 *
 * @see D03-FR-010.1 Role-based access control
 * @see D04 §4.4 RBAC permission middleware
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        // Log unauthorized access attempt for security monitoring
        logger()->warning('Unauthorized permission access attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'required_permissions' => $permissions,
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'route' => $request->route()?->getName(),
            'url' => $request->url(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        abort(403, 'Access denied. Required permission: ' . implode(' or ', $permissions));
    }
}
