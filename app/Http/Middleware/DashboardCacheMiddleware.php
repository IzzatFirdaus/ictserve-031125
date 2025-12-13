<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard Cache Middleware
 *
 * Optimizes Filament dashboard load time by implementing
 * response caching and cache headers for static assets.
 *
 * Target: Dashboard load <3s with caching per Requirement 10.5.
 *
 * @trace Requirements: 10.5 (Filament dashboard <3s with caching)
 *
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
 */
class DashboardCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip caching for non-GET requests
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // Skip caching for authenticated users with specific roles
        // (they may see different data)
        if ($request->user() && $this->shouldSkipCache($request)) {
            return $next($request);
        }

        $response = $next($request);

        // Add cache headers for static assets
        if ($this->isStaticAsset($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        // Add ETag for dynamic content
        if ($response->isSuccessful() && ! $this->isStaticAsset($request)) {
            $etag = md5($response->getContent() ?: '');
            $response->headers->set('ETag', '"'.$etag.'"');

            // Check if client has cached version
            if ($request->headers->get('If-None-Match') === '"'.$etag.'"') {
                return response('', 304);
            }
        }

        return $response;
    }

    /**
     * Check if caching should be skipped for this request
     */
    protected function shouldSkipCache(Request $request): bool
    {
        // Skip for AJAX/Livewire requests
        if ($request->ajax() || $request->hasHeader('X-Livewire')) {
            return true;
        }

        // Skip for requests with query parameters (filters, pagination)
        if (! empty($request->query())) {
            return true;
        }

        return false;
    }

    /**
     * Check if request is for a static asset
     */
    protected function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2'];

        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, '.'.$ext)) {
                return true;
            }
        }

        return false;
    }
}
