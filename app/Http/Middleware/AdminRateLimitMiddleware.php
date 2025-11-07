<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Rate Limit Middleware
 *
 * Implements rate limiting for admin login attempts.
 * Requirement 17.2: Rate limiting (5 failed attempts = 15-minute lockout)
 *
 * @version 1.0.0
 *
 * @since 2025-11-07
 */
class AdminRateLimitMiddleware
{
    /**
     * Maximum login attempts before lockout
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    /**
     * Handle an incoming request.
     *
     * Checks if the user has exceeded the maximum number of login attempts.
     * If so, returns a 429 Too Many Requests response with lockout information.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply rate limiting to login attempts
        if ($request->routeIs('filament.admin.auth.login') && $request->isMethod('POST')) {
            $key = $this->throttleKey($request);

            // Check if too many attempts
            if ($this->limiter->tooManyAttempts($key, self::MAX_ATTEMPTS)) {
                $seconds = $this->limiter->availableIn($key);
                $minutes = ceil($seconds / 60);

                return response()->json([
                    'message' => __('Too many login attempts. Please try again in :minutes minutes.', [
                        'minutes' => $minutes,
                    ]),
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            // Increment attempts
            $this->limiter->hit($key, self::LOCKOUT_MINUTES * 60);
        }

        $response = $next($request);

        // Clear rate limit on successful authentication
        if ($request->routeIs('filament.admin.auth.login') && Auth::check()) {
            $this->limiter->clear($this->throttleKey($request));
        }

        return $response;
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return 'admin_login_'.strtolower((string) $request->input('email')).'|'.$request->ip();
    }
}
