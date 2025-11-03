<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocaleMiddleware
 *
 * Detects and applies user language preference using session/cookie persistence only.
 * NO user profile storage - designed for guest-first architecture.
 *
 * Detection Priority:
 * 1. Session storage: session('locale') - highest priority (explicit user choice)
 * 2. Cookie storage: $request->cookie('locale') - persistent preference (1 year)
 * 3. Accept-Language header: parseAcceptLanguageHeader() - browser preference
 * 4. Config fallback: config('app.locale') - system default
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 20.1, 20.2, 20.4, 7.2, 15.1
 * WCAG Level: AA (SC 3.1.2 Language of Parts)
 * Standards: D03-FR-020, D04 §7.3, D10 §5.2, D11 §6.1
 */
class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        // Validate locale against supported locales
        if ($this->isValidLocale($locale)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Detect user's preferred locale using priority chain.
     */
    protected function detectLocale(Request $request): string
    {
        // Priority 1: Session storage (explicit user choice)
        if ($request->hasSession() && $request->session()->has('locale')) {
            return $request->session()->get('locale');
        }

        // Priority 2: Cookie storage (persistent preference)
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }

        // Priority 3: Accept-Language header (browser preference)
        $browserLocale = $this->parseAcceptLanguageHeader($request);
        if ($browserLocale !== null) {
            return $browserLocale;
        }

        // Priority 4: Config fallback (system default)
        return config('app.locale', 'en');
    }

    /**
     * Parse Accept-Language header to extract preferred locale.
     */
    protected function parseAcceptLanguageHeader(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (! $acceptLanguage) {
            return null;
        }

        // Simple detection: check if Malay is preferred
        if (str_contains(strtolower($acceptLanguage), 'ms')) {
            return 'ms';
        }

        // Default to English for other languages
        return 'en';
    }

    /**
     * Validate locale against supported locales.
     */
    protected function isValidLocale(string $locale): bool
    {
        $supportedLocales = config('app.supported_locales', ['en', 'ms']);

        return in_array($locale, $supportedLocales, true);
    }
}
