<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\MobileOptimizationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mobile Optimization Middleware
 *
 * Applies mobile-specific optimizations to requests
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-015 (Mobile Optimization)
 * @trace D12 §6.8 (Performance Optimization)
 *
 * @version 1.0.0
 */
class MobileOptimizationMiddleware
{
    public function __construct(
        private readonly MobileOptimizationService $mobileService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect device type
        $deviceType = $this->mobileService->getDeviceType($request);
        $isMobile = $deviceType === 'mobile';
        $isTablet = $deviceType === 'tablet';

        // Share device info with all views
        View::share('deviceType', $deviceType);
        View::share('isMobile', $isMobile);
        View::share('isTablet', $isTablet);
        View::share('isDesktop', ! $isMobile && ! $isTablet);

        // Share touch target configuration
        View::share('touchTargetConfig', $this->mobileService->getTouchTargetConfig());

        // Share breakpoints
        View::share('breakpoints', $this->mobileService->getBreakpoints());

        // Share pagination limit
        View::share('paginationLimit', $this->mobileService->getPaginationLimit($deviceType));

        // Check reduced motion preference
        View::share('prefersReducedMotion', $this->mobileService->prefersReducedMotion($request));

        // Process the request
        $response = $next($request);

        // Add mobile-specific headers
        if ($response instanceof Response) {
            $this->addMobileHeaders($response, $deviceType);
        }

        return $response;
    }

    /**
     * Add mobile-specific response headers
     */
    private function addMobileHeaders(Response $response, string $deviceType): void
    {
        // Add Vary header for proper caching
        $response->headers->set('Vary', 'User-Agent, Accept-Encoding');

        // Add device type header for debugging
        if (app()->environment('local', 'development')) {
            $response->headers->set('X-Device-Type', $deviceType);
        }

        // Add Client Hints headers for better device detection
        $response->headers->set(
            'Accept-CH',
            'Sec-CH-UA, Sec-CH-UA-Mobile, Sec-CH-UA-Platform, Sec-CH-Prefers-Reduced-Motion, Sec-CH-Viewport-Width'
        );

        // Add Critical-CH for important hints
        $response->headers->set(
            'Critical-CH',
            'Sec-CH-UA-Mobile, Sec-CH-Viewport-Width'
        );
    }
}
