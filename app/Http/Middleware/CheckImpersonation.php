<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Impersonation Middleware
 *
 * Blocks critical security actions during impersonation sessions:
 * - Password changes
 * - Email updates
 * - Account deletion
 * - Two-factor authentication changes
 * - Profile security modifications
 *
 * All impersonation actions are logged for audit compliance.
 *
 * @trace D03-FR-002.5 (Impersonation Security)
 * @trace D04 §5.0.3 (Impersonation Security Middleware)
 * @trace D10 §7 (Component Documentation)
 * @trace D14 §10 (PDPA Compliance - Audit Logging)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-26
 */
class CheckImpersonation
{
    /**
     * Routes/patterns that are blocked during impersonation.
     *
     * @var array<string>
     */
    protected array $blockedPatterns = [
        'password*',
        'user-password*',
        'two-factor*',
        '2fa*',
        'profile/delete*',
        'account/delete*',
        'security*',
        '*email/update*',
        '*change-password*',
        '*update-password*',
    ];

    /**
     * Route names that are blocked during impersonation.
     *
     * @var array<string>
     */
    protected array $blockedRoutes = [
        'password.update',
        'password.confirm',
        'user-password.update',
        'profile.destroy',
        'two-factor.enable',
        'two-factor.disable',
        'two-factor-challenge',
        'verification.send',
        'profile.email.update',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isImpersonating()) {
            $this->logImpersonationActivity($request);

            if ($this->isBlockedAction($request)) {
                return $this->blockAction($request);
            }
        }

        return $next($request);
    }

    /**
     * Check if the current session is an impersonation session.
     */
    protected function isImpersonating(): bool
    {
        return session()->has('impersonator_id');
    }

    /**
     * Get the impersonator's user ID.
     */
    protected function getImpersonatorId(): ?int
    {
        return session()->get('impersonator_id');
    }

    /**
     * Check if the current request is a blocked action during impersonation.
     */
    protected function isBlockedAction(Request $request): bool
    {
        // Check route name
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->blockedRoutes, true)) {
            return true;
        }

        // Check URL patterns
        $path = $request->path();
        foreach ($this->blockedPatterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        // Check POST/PUT/PATCH/DELETE requests to sensitive endpoints
        if ($request->isMethod('POST') || $request->isMethod('PUT') ||
            $request->isMethod('PATCH') || $request->isMethod('DELETE')) {

            // Block password changes
            if ($request->has('password') || $request->has('current_password')) {
                return true;
            }

            // Block email changes
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($request->has('email') && $user && $request->input('email') !== $user->email) {
                return true;
            }
        }

        return false;
    }

    /**
     * Block the action and return an appropriate response.
     */
    protected function blockAction(Request $request): Response
    {
        $this->logBlockedAction($request);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('impersonation.action_blocked'),
                'error' => 'impersonation_security_block',
            ], Response::HTTP_FORBIDDEN);
        }

        return redirect()
            ->back()
            ->with('error', __('impersonation.action_blocked_message'));
    }

    /**
     * Log impersonation activity for audit compliance.
     */
    protected function logImpersonationActivity(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        Log::channel('audit')->info('Impersonation activity', [
            'impersonator_id' => $this->getImpersonatorId(),
            'impersonated_user_id' => Auth::id(),
            'impersonated_user_email' => $user?->email,
            'action' => $request->method().' '.$request->path(),
            'route_name' => $request->route()?->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log blocked actions for security audit.
     */
    protected function logBlockedAction(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        Log::channel('audit')->warning('Impersonation blocked action', [
            'impersonator_id' => $this->getImpersonatorId(),
            'impersonated_user_id' => Auth::id(),
            'impersonated_user_email' => $user?->email,
            'blocked_action' => $request->method().' '.$request->path(),
            'route_name' => $request->route()?->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'reason' => 'security_policy_violation',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
