<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 *
 * Implements comprehensive security headers for OWASP compliance:
 * - X-Frame-Options: Prevents clickjacking attacks
 * - X-Content-Type-Options: Prevents MIME type sniffing
 * - X-XSS-Protection: Legacy XSS protection (deprecated but still useful)
 * - Referrer-Policy: Controls referrer information
 * - Content-Security-Policy: Prevents XSS and data injection
 * - Strict-Transport-Security: Enforces HTTPS (HSTS)
 * - Permissions-Policy: Controls browser features
 *
 * @see D11 Technical Design Documentation - Security Requirements
 * @see OWASP Security Headers Guidelines
 *
 * @requirements R14 Security and Compliance
 *
 * @version 1.0.0
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking - only allow same origin framing
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Legacy XSS protection (still useful for older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy - balanced for Laravel/Livewire/Alpine.js
        $csp = $this->buildContentSecurityPolicy();
        $response->headers->set('Content-Security-Policy', $csp);

        // Strict Transport Security (HSTS) - only in production with HTTPS
        if ($request->secure() || config('app.env') === 'production') {
            // 1 year max-age, include subdomains, allow preload
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Permissions Policy - restrict browser features
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // Cross-Origin policies for enhanced security
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        return $response;
    }

    /**
     * Build Content Security Policy header value.
     *
     * Configured for Laravel 12 + Livewire 3 + Alpine.js + Tailwind CSS + Vite
     */
    private function buildContentSecurityPolicy(): string
    {
        $isLocal = config('app.env') === 'local';

        // Build script sources
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval'";
        if ($isLocal) {
            $scriptSrc .= ' http://localhost:5173 http://127.0.0.1:5173';
        }

        // Build style sources
        $styleSrc = "'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net";
        if ($isLocal) {
            $styleSrc .= ' http://localhost:5173 http://127.0.0.1:5173';
        }

        // Build connect sources
        $connectSrc = "'self' wss: https:";
        if ($isLocal) {
            $connectSrc .= ' http://localhost:5173 http://127.0.0.1:5173'
                .' ws://localhost:5173 ws://127.0.0.1:5173'
                .' ws://127.0.0.1:6001 wss://127.0.0.1:6001';
        }

        $directives = [
            // Default fallback - only same origin
            "default-src 'self'",

            // Scripts
            "script-src {$scriptSrc}",

            // Styles
            "style-src {$styleSrc}",

            // Images - allow data URIs for inline images and external sources
            "img-src 'self' data: https: blob:",

            // Fonts - allow Google Fonts and fonts.bunny.net
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",

            // Connect
            "connect-src {$connectSrc}",

            // Media - allow same origin
            "media-src 'self'",

            // Objects - disallow plugins
            "object-src 'none'",

            // Frames - allow same origin for Filament modals
            "frame-src 'self'",

            // Frame ancestors - prevent clickjacking
            "frame-ancestors 'self'",

            // Form actions - only same origin
            "form-action 'self'",

            // Base URI - only same origin
            "base-uri 'self'",

            // Upgrade insecure requests in production
            config('app.env') === 'production' ? 'upgrade-insecure-requests' : '',
        ];

        return implode('; ', array_filter($directives));
    }
}
