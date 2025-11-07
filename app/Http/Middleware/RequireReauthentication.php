<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Require Re-authentication Middleware
 *
 * Requires users to re-authenticate before performing sensitive operations
 * like user deletion, role changes, or configuration updates.
 *
 * Requirements: 17.5, D03-FR-017.5
 *
 * @see D04 §11.3 Re-authentication for sensitive operations
 */
class RequireReauthentication
{
    private const REAUTH_TIMEOUT = 900; // 15 minutes

    private const SENSITIVE_OPERATIONS = [
        'users.destroy',
        'users.update.role',
        'system.configuration.update',
        'security.settings.update',
        'two-factor.disable',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $operation = null): Response
    {
        // Skip if user is not authenticated
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $routeName = $request->route()?->getName();
        $operation = $operation ?? $routeName;

        // Check if this operation requires re-authentication
        if (! $this->requiresReauth($operation, $request)) {
            return $next($request);
        }

        // Check if user has recently authenticated
        if ($this->hasRecentAuth($user->id)) {
            return $next($request);
        }

        // Store the intended URL for redirect after re-auth
        Session::put('reauth.intended', $request->fullUrl());
        Session::put('reauth.operation', $operation);

        // Redirect to re-authentication page
        return redirect()->route('reauth.show')->with([
            'reauth_required' => true,
            'operation' => $this->getOperationDescription($operation),
        ]);
    }

    /**
     * Check if operation requires re-authentication
     */
    private function requiresReauth(?string $operation, Request $request): bool
    {
        // Check predefined sensitive operations
        if (in_array($operation, self::SENSITIVE_OPERATIONS)) {
            return true;
        }

        // Check HTTP method and route patterns
        if ($request->isMethod('DELETE')) {
            return true;
        }

        // Check for role/permission changes
        if ($request->has(['role', 'permissions']) && $request->isMethod('PUT')) {
            return true;
        }

        // Check for configuration updates
        if (str_contains($request->path(), 'admin/settings') && $request->isMethod('POST')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has recent authentication
     */
    private function hasRecentAuth(int $userId): bool
    {
        $lastReauth = Session::get("reauth.timestamp.{$userId}");

        if (! $lastReauth) {
            return false;
        }

        return (time() - $lastReauth) < self::REAUTH_TIMEOUT;
    }

    /**
     * Get human-readable operation description
     */
    private function getOperationDescription(?string $operation): string
    {
        return match ($operation) {
            'users.destroy' => 'delete user account',
            'users.update.role' => 'change user role',
            'system.configuration.update' => 'update system configuration',
            'security.settings.update' => 'update security settings',
            'two-factor.disable' => 'disable two-factor authentication',
            default => 'perform this sensitive operation',
        };
    }

    /**
     * Mark user as recently authenticated
     */
    public static function markRecentAuth(int $userId): void
    {
        Session::put("reauth.timestamp.{$userId}", time());

        // Log the re-authentication
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'reauthentication_completed'])
            ->log('User completed re-authentication for sensitive operation');
    }

    /**
     * Clear re-authentication status
     */
    public static function clearRecentAuth(int $userId): void
    {
        Session::forget("reauth.timestamp.{$userId}");
        Session::forget('reauth.intended');
        Session::forget('reauth.operation');
    }

    /**
     * Get remaining time before re-auth expires
     *
     * @return int Seconds remaining, 0 if expired
     */
    public static function getRemainingTime(int $userId): int
    {
        $lastReauth = Session::get("reauth.timestamp.{$userId}");

        if (! $lastReauth) {
            return 0;
        }

        $elapsed = time() - $lastReauth;
        $remaining = self::REAUTH_TIMEOUT - $elapsed;

        return max(0, $remaining);
    }
}
