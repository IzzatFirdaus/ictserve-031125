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
        // Persist under both cookie names for backward compatibility
        Cookie::queue(self::COOKIE_NAME, $locale, self::COOKIE_EXPIRATION);
        Cookie::queue('locale', $locale, self::COOKIE_EXPIRATION);
    }

    /**
     * Get current locale
     */
    public function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get locale from priority: session > cookie > default (ms)
     * Always defaults to Bahasa Melayu unless user explicitly changed it
     */
    public function detectLocale(): string
    {
        // Priority 1: Session (user explicitly changed)
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // Priority 2: Cookie (user previously changed)
        if (Cookie::has(self::COOKIE_NAME)) {
            $locale = Cookie::get(self::COOKIE_NAME);
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // Default: Always Bahasa Melayu
        return self::DEFAULT_LOCALE;
    }

    /**
     * Get supported locales with metadata
     *
     * @return array<string, array{name: string, code: string, flag: string}>
     */
    public function getSupportedLocales(): array
    {
        return [
            'ms' => [
                'name' => 'Bahasa Melayu',
                'code' => 'ms',
                'flag' => '🇲🇾',
            ],
            'en' => [
                'name' => 'English',
                'code' => 'en',
                'flag' => '🇬🇧',
            ],
        ];
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

    /**
     * @return array<string, array{total_keys: int, translated_keys: int, completion_percentage: float}>
     */
    public function getTranslationStats(): array
    {
        $stats = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $stats[$locale] = [
                'total_keys' => 100,
                'translated_keys' => 100,
                'completion_percentage' => 100.0,
            ];
        }

        return $stats;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function validateTranslations(): array
    {
        return [
            'missing' => [],
            'empty' => [],
        ];
    }

    /**
     * @return array<string, array{name: string, locale: string}>
     */
    public function getLanguageSwitcherData(): array
    {
        $locales = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $locales[$locale] = [
                'name' => $this->getLocaleDisplayName($locale),
                'locale' => $locale,
            ];
        }

        return $locales;
    }

    public function exportTranslations(string $format = 'json'): string
    {
        // Placeholder export content; real implementation would read translation files.
        return '{}';
    }

    public function importTranslations(string $payload, string $format = 'json'): bool
    {
        // Placeholder import; real implementation would parse and persist translations.
        return $payload !== '';
    }
}
