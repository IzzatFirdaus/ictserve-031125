<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class BilingualSupportService
{
    /**
     * Supported locales
     */
    private const SUPPORTED_LOCALES = ['ms', 'en'];

    /**
     * Default locale
     */
    private const DEFAULT_LOCALE = 'ms';

    /**
     * Cookie name for language preference
     */
    private const COOKIE_NAME = 'ictserve_locale';

    /**
     * Cookie expiration (1 year in minutes)
     */
    private const COOKIE_EXPIRATION = 525600;

    /**
     * Set application locale
     */
    public function setLocale(string $locale): void
    {
        if (! in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = self::DEFAULT_LOCALE;
        }

        App::setLocale($locale);
        Session::put('locale', $locale);
        Cookie::queue(self::COOKIE_NAME, $locale, self::COOKIE_EXPIRATION);
    }

    /**
     * Get current locale
     */
    public function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get locale from priority: session > cookie > Accept-Language > config
     */
    public function detectLocale(): string
    {
        // Priority 1: Session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // Priority 2: Cookie
        if (Cookie::has(self::COOKIE_NAME)) {
            $locale = Cookie::get(self::COOKIE_NAME);
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // Priority 3: Accept-Language header
        $acceptLanguage = request()->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = substr($acceptLanguage, 0, 2);
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // Priority 4: Config fallback
        return config('app.locale', self::DEFAULT_LOCALE);
    }

    /**
     * Get supported locales
     */
    public function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Get locale display name
     */
    public function getLocaleDisplayName(string $locale): string
    {
        return match ($locale) {
            'ms' => 'Bahasa Melayu',
            'en' => 'English',
            default => $locale,
        };
    }

    /**
     * Switch locale (for Livewire components)
     */
    public function switchLocale(string $locale): void
    {
        $this->setLocale($locale);
    }

    /**
     * Get date format for current locale
     */
    public function getDateFormat(): string
    {
        return match ($this->getCurrentLocale()) {
            'ms' => 'd/m/Y',
            'en' => 'm/d/Y',
            default => 'd/m/Y',
        };
    }

    /**
     * Get time format for current locale
     */
    public function getTimeFormat(): string
    {
        return 'H:i'; // 24-hour format for both locales
    }

    /**
     * Get currency format for current locale
     */
    public function getCurrencyFormat(): string
    {
        return 'MYR'; // Malaysian Ringgit for both locales
    }
}
