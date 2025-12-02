<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\IpBlockingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guest Form Rate Limiting Middleware
 *
 * Applies rate limiting to guest form submissions to prevent abuse.
 * Limit: 60 requests per minute per IP address.
 * Integrates with IpBlockingService to auto-block repeat offenders.
 */
class GuestFormRateLimiter
{
    public function __construct(
        private readonly IpBlockingService $ipBlockingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();
        $key = 'guest-form:'.$ipAddress;

        if (RateLimiter::tooManyAttempts($key, 60)) {
            $seconds = RateLimiter::availableIn($key);

            // Record violation for potential auto-blocking
            $this->ipBlockingService->recordViolation(
                $ipAddress,
                'Rate limit exceeded on guest form'
            );

            $violationCount = $this->ipBlockingService->getViolationCount($ipAddress);
            $threshold = $this->ipBlockingService->getAutoBlockThreshold();

            $message = __('validation.rate_limit_exceeded', ['seconds' => $seconds]);

            // Warn user if approaching auto-block threshold
            if ($violationCount >= ($threshold - 2) && $violationCount < $threshold) {
                $message .= ' '.__('validation.rate_limit_warning', [
                    'remaining' => $threshold - $violationCount,
                ]);
            }

            return response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'violation_count' => $violationCount,
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
