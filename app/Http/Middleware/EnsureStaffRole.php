<?php

declare(strict_types=1);

// name: EnsureStaffRole
// description: Middleware ensuring user has Staff role or higher for portal access
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-009, D04 §6.1, D11 §8 (Requirements 5.5, 15.3)
// last-updated: 2025-11-06

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (! $user) {
            return redirect()->route('login')->with('error', __('auth.must_login_portal'));
        }

        // Check if user has Staff role or higher (Staff, Approver, Admin, Superuser)
        // Using the role column attribute instead of Spatie permissions
        $allowedRoles = ['staff', 'approver', 'admin', 'superuser'];

        // Debug: Log role check
        Log::info('EnsureStaffRole check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_value' => $user->role,
            'role_lowercase' => strtolower($user->role ?? ''),
            'allowed_roles' => $allowedRoles,
            'test' => in_array(strtolower($user->role ?? ''), $allowedRoles) ? 'PASS' : 'FAIL',
        ]);

        if (! in_array(strtolower($user->role ?? ''), $allowedRoles)) {
            Log::warning('Staff middleware - Access denied', [
                'user_role' => $user->role,
                'required_roles' => $allowedRoles,
            ]);
            abort(403, __('auth.insufficient_permissions_portal'));
        }

        // Log portal access for audit trail
        // Temporarily disabled for testing
        // \App\Models\PortalActivity::create([
        //     'user_id' => $user->id,
        //     'activity_type' => 'portal_access',
        //     'subject_type' => null,
        //     'subject_id' => null,
        //     'metadata' => [
        //         'ip_address' => $request->ip(),
        //         'user_agent' => $request->userAgent(),
        //         'route' => $request->route()?->getName() ?? 'unknown',
        //         'timestamp' => now()->toIso8601String(),
        //     ],
        // ]);

        return $next($request);
    }
}
