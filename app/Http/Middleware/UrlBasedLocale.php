<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * URL-Based Locale Middleware
 *
 * Extracts locale from URL prefix (e.g., /ms/ticket/... or /en/ticket/...)
 * and sets the application locale accordingly.
 *
 * URL Pattern: /{locale}/path/to/resource
 * Supported Locales: en (English), ms (Bahasa Melayu)
 *
 * Priority:
 * 1. URL prefix locale (highest - explicit in URL)
 * 2. Falls back to SetLocaleMiddleware detection chain
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-27
 *
 * Requirements: R13 (Bilingual Support), 3.1.7 (URL-based locale)
 * WCAG Level: AA (SC 3.1.2 Language of Parts)
 * Standards: D03-FR-020, D04 §7.3
 *
 * @trace Task 3.1.7 - Implement URL-based locale
 */
class UrlBasedLocale
{
    /**
     * Supported locales for URL prefix.
     *
     * @var array<string>
     */
    protected array $supportedLocales = ['en', 'ms'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->extractLocaleFromUrl($request);

        if ($locale !== null) {
            // Set application locale
            App::setLocale($locale);

            // Store in session for consistency across requests
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        return $next($request);
    }

    /**
     * Extract locale from URL prefix.
     *
     * Checks if the first segment of the URL path is a valid locale.
     * Example: /ms/helpdesk/create → returns 'ms'
     *          /en/loan/apply → returns 'en'
     *          /helpdesk/create → returns null (no locale prefix)
     */
    protected function extractLocaleFromUrl(Request $request): ?string
    {
        $path = $request->path();
        $segments = explode('/', trim($path, '/'));

        if (empty($segments) || $segments[0] === '') {
            return null;
        }

        $firstSegment = strtolower($segments[0]);

        if (in_array($firstSegment, $this->supportedLocales, true)) {
            return $firstSegment;
        }

        return null;
    }

    /**
     * Generate URL with locale prefix.
     *
     * Helper method for generating localized URLs.
     *
     * @param  string  $path  The path without locale prefix
     * @param  string|null  $locale  The locale to use (defaults to current)
     * @return string The full URL with locale prefix
     */
    public static function localizedUrl(string $path, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $path = ltrim($path, '/');

        return url("/{$locale}/{$path}");
    }

    /**
     * Generate route URL with locale prefix.
     *
     * @param  string  $name  Route name
     * @param  array<string, mixed>  $parameters  Route parameters
     * @param  string|null  $locale  The locale to use (defaults to current)
     * @return string The full URL with locale prefix
     */
    public static function localizedRoute(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $url = route($name, $parameters);

        // Parse the URL and add locale prefix
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';

        // Check if path already has locale prefix
        $segments = explode('/', trim($path, '/'));
        if (! empty($segments) && in_array($segments[0], ['en', 'ms'], true)) {
            // Replace existing locale
            $segments[0] = $locale;
        } else {
            // Add locale prefix
            array_unshift($segments, $locale);
        }

        $newPath = '/'.implode('/', $segments);

        return ($parsed['scheme'] ?? 'https').'://'.
            ($parsed['host'] ?? config('app.url')).
            $newPath.
            (isset($parsed['query']) ? '?'.$parsed['query'] : '');
    }
}
