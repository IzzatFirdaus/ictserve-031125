<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notification Rate Limiter Middleware
 *
 * Implements rate limiting for notification-related API endpoints
 * to prevent abuse and ensure system stability.
 *
 * @see Requirements 8.6 - Rate limiting for notification dispatch
 * @see Requirements 9.3 - Security enhancements
 *
 * @trace D03 SRS-FR-043 (notification security)
 */
class NotificationRateLimiter
{
    /**
     * Default rate limit: 60 requests per minute per user.
     */
    private const DEFAULT_MAX_ATTEMPTS = 60;

    /**
     * Decay time in seconds (1 minute).
     */
    private const DECAY_SECONDS = 60;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = self::DEFAULT_MAX_ATTEMPTS): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return $this->buildTooManyAttemptsResponse($key, $maxAttempts);
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response,
            $maxAttempts,
            RateLimiter::remaining($key, $maxAttempts)
        );
    }

    /**
     * Resolve the request signature for rate limiting.
     *
     * Uses user ID for authenticated requests, IP address for guests.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();

        if ($user !== null) {
            return 'notification_rate_limit:user:'.$user->id;
        }

        return 'notification_rate_limit:ip:'.$request->ip();
    }

    /**
     * Build the response for too many attempts.
     */
    protected function buildTooManyAttemptsResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'message' => 'Too many notification requests. Please try again later.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addRateLimitHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remainingAttempts),
        ]);

        return $response;
    }
}
