<?php

declare(strict_types=1);

namespace App\Http\Controllers;

/**
 * LanguageController
 *
 * @deprecated v3.6.0 Language switching disabled per D15 documentation.
 *             ICTServe now uses Bahasa Melayu-only interface as per government directive.
 *             This controller is retained for backward compatibility but all methods
 *             redirect to home with appropriate messages.
 *
 * Previously handled language switching for the ICTServe application using session/cookie persistence.
 * NO user profile storage - was designed for guest-first architecture.
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0 (Deprecated)
 *
 * @since 2025-11-03
 *
 * Requirements: D15 §2.1 (Bahasa Melayu Primary Language)
 * WCAG Level: AA (SC 3.1.2 Language of Parts)
 * Standards: D15 v3.6.0 (Bahasa Melayu Only)
 */
class LanguageController extends Controller
{
    /**
     * Change the application locale.
     *
     * @deprecated v3.6.0 Language switching disabled per D15 documentation.
     *             ICTServe now uses Bahasa Melayu-only interface.
     */
    public function change(): \Illuminate\Http\RedirectResponse
    {
        // v3.6.0: Language switching disabled - redirect to home with message
        return redirect()->route('home')->with(
            'error',
            'Penukaran bahasa telah dilumpuhkan. Sistem ICTServe kini menggunakan Bahasa Melayu sahaja.'
        );
    }

    /**
     * Validate locale against supported locales.
     *
     * v3.6.0: Only Bahasa Melayu ('ms') is supported per government directive.
     */
    protected function isValidLocale(string $locale): bool
    {
        // v3.6.0: Bahasa Melayu sahaja - only 'ms' locale is supported
        $supportedLocales = config('app.supported_locales', ['ms']);
        if (! \is_array($supportedLocales)) {
            $supportedLocales = ['ms'];
        }

        return \in_array($locale, $supportedLocales, true);
    }
}
