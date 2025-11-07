<?php

declare(strict_types=1);

// name: EnsureApproverRole
// description: Middleware ensuring user is Grade 41+ Approver for approval features
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-009, D04 §6.1, D11 §8 (Requirements 4.1, 15.3)
// last-updated: 2025-11-06

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApproverRole
{
    /**
     * Handle an incoming request.
     *
     * Ensures user has Approver role (Grade 41+) or higher for approval operations.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (! $user) {
            return redirect()->route('login')->with('error', __('auth.must_login_approvals'));
        }

        // Check if user is approver (Grade 41+) or has higher roles
        // Using the role column attribute instead of Spatie permissions
        $allowedRoles = ['approver', 'admin', 'superuser'];

        if (! in_array(strtolower($user->role ?? ''), $allowedRoles)) {
            abort(403, __('approvals.unauthorized'));
        }

        // Log approval interface access for audit trail
        \App\Models\PortalActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'approval_interface_access',
            'subject_type' => null,
            'subject_id' => null,
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()?->getName() ?? 'unknown',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        return $next($request);
    }
}
