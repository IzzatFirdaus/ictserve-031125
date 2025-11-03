<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

/**
 * LanguageController
 *
 * Handles language switching for the ICTServe application using session/cookie persistence.
 * NO user profile storage - designed for guest-first architecture.
 *
 * Storage Strategy:
 * - Session: Immediate application for current session
 * - Cookie: 1-year persistence for future visits
 * - Application: Applied to current request
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 20.3, 20.4, 15.2
 * WCAG Level: AA (SC 3.1.2 Language of Parts)
 * Standards: D03-FR-020, D04 §7.3, D10 §5.2, D11 §6.1
 */
class LanguageController extends Controller
{
    /**
     * Change the application locale.
     */
    public function change(Request $request, string $locale): RedirectResponse
    {
        // Validate locale parameter
        if (! $this->isValidLocale($locale)) {
            abort(400, 'Invalid locale specified.');
        }

        // Store locale in session (immediate application)
        $request->session()->put('locale', $locale);

        // Store locale in cookie (1 year persistence)
        Cookie::queue('locale', $locale, 60 * 24 * 365);

        // Apply locale to current request
        App::setLocale($locale);

        // Redirect back with success message
        return redirect()->back()->with('message', __('common.language_changed'));
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
