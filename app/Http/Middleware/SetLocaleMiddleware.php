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

        if (! $this->isValidLocale($locale)) {
            $locale = config('app.locale', 'ms');
        }

        App::setLocale($locale);

        if ($request->hasSession() && ! $request->session()->has('locale')) {
            $request->session()->put('locale', $locale);
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
            $locale = $request->session()->get('locale');
            if (\is_string($locale)) {
                return $locale;
            }
        }

        // Priority 2: Cookie storage (persistent preference)
        // Check both cookie names for compatibility
        $cookieLocale = $request->cookie('locale') ?? $request->cookie('ictserve_locale');
        if ($cookieLocale && \is_string($cookieLocale)) {
            return $cookieLocale;
        }

        // Priority 3: Config fallback (system default) — D15 §2: default Bahasa Melayu
        $defaultLocale = config('app.locale', 'ms');

        return \is_string($defaultLocale) ? $defaultLocale : 'ms';
    }

    /**
     * Validate locale against supported locales.
     *
     * v3.6.0: Only Bahasa Melayu ('ms') is supported per government directive.
     */
    protected function isValidLocale(string $locale): bool
    {
        // v3.6.0: Bahasa Melayu sahaja - only 'ms' locale is supported
        $supportedLocalesConfig = config('app.supported_locales', ['ms']);
        $supportedLocales = array_values(array_filter(
            \is_array($supportedLocalesConfig) ? $supportedLocalesConfig : [],
            '\is_string'
        ));

        if ($supportedLocales === []) {
            $supportedLocales = ['ms'];
        }

        return \in_array($locale, $supportedLocales, true);
    }
}
