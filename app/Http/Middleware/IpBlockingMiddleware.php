<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\IpBlockingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IP Blocking Middleware
 *
 * Blocks requests from IP addresses that have been flagged for abuse.
 * Works in conjunction with GuestFormRateLimiter to auto-block repeat offenders.
 */
class IpBlockingMiddleware
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

        if ($this->ipBlockingService->isBlocked($ipAddress)) {
            $block = $this->ipBlockingService->getActiveBlock($ipAddress);

            $message = __('validation.ip_blocked');
            $expiresAt = $block?->expires_at;

            if ($expiresAt) {
                $message = __('validation.ip_blocked_until', [
                    'time' => $expiresAt->diffForHumans(),
                ]);
            }

            // Return appropriate response based on request type
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'blocked' => true,
                    'expires_at' => $expiresAt?->toIso8601String(),
                ], 403);
            }

            // For regular requests, show a blocked page
            return response()->view('errors.blocked', [
                'message' => $message,
                'expiresAt' => $expiresAt,
                'reason' => $block?->reason,
            ], 403);
        }

        return $next($request);
    }
}
