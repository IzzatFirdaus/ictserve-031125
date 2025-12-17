<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performance Optimization Middleware v3.6.0
 *
 * Applies performance optimizations to HTTP responses:
 * - Adds cache headers for static assets
 * - Monitors TTFB and logs slow requests
 * - Adds performance timing headers
 *
 * @see D12 §9 Performance optimization patterns
 * @see Requirements 13.1 - TTFB <600ms target
 *
 * @version 3.6.0
 */
class PerformanceOptimizationMiddleware
{
    /**
     * TTFB threshold in milliseconds for slow request logging
     */
    private const SLOW_REQUEST_THRESHOLD = 1000;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;

        // Add Server-Timing header for performance monitoring
        $response->headers->set('Server-Timing', sprintf('total;dur=%.2f', $duration));

        // Log slow requests
        if ($duration > self::SLOW_REQUEST_THRESHOLD) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration_ms' => round($duration, 2),
                'threshold_ms' => self::SLOW_REQUEST_THRESHOLD,
            ]);
        }

        // Add cache headers for static assets
        if ($this->isStaticAsset($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        // Add security and performance headers
        $this->addPerformanceHeaders($response);

        return $response;
    }

    /**
     * Check if request is for a static asset
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'woff', 'woff2', 'ttf', 'eot'];

        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, '.'.$ext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add performance-related headers to response
     */
    private function addPerformanceHeaders(Response $response): void
    {
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable DNS prefetching
        $response->headers->set('X-DNS-Prefetch-Control', 'on');

        // Add Vary header for proper caching
        if (! $response->headers->has('Vary')) {
            $response->headers->set('Vary', 'Accept-Encoding');
        }
    }
}
