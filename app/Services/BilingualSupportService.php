<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\App;

/**
 * BilingualSupportService
 *
 * @deprecated v3.6.0 Bilingual support disabled. All methods now return 'ms' locale only.
 *             This service is retained for backward compatibility but language switching
 *             functionality is disabled. ICTServe now uses Bahasa Melayu-only interface.
 *             English translation files in lang/en/ are retained for technical reference.
 */
class BilingualSupportService
{
    /**
     * v3.6.0: Only Bahasa Melayu is supported
     */
    private const SUPPORTED_LOCALES = ['ms'];

    /**
     * v3.6.0: Default and only locale
     */
    private const DEFAULT_LOCALE = 'ms';

    /**
     * Set application locale
     *
     * @deprecated v3.6.0 Always sets to 'ms' regardless of parameter
     */
    public function setLocale(string $locale): void
    {
        // v3.6.0: Always use Bahasa Melayu - ignore parameter
        App::setLocale(self::DEFAULT_LOCALE);
    }

    /**
     * Get current locale
     *
     * @deprecated v3.6.0 Always returns 'ms'
     */
    public function getCurrentLocale(): string
    {
        return self::DEFAULT_LOCALE;
    }

    /**
     * Detect locale from request
     *
     * @deprecated v3.6.0 Always returns 'ms' - user preferences ignored
     */
    public function detectLocale(): string
    {
        // v3.6.0: Always return Bahasa Melayu - ignore session/cookie/browser
        return self::DEFAULT_LOCALE;
    }

    /**
     * Get supported locales with metadata
     *
     * @deprecated v3.6.0 Only returns 'ms' locale
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
        ];
    }

    /**
     * Get locale display name
     *
     * @deprecated v3.6.0 Only 'ms' is active
     */
    public function getLocaleDisplayName(string $locale): string
    {
        // v3.6.0: Always return Bahasa Melayu name
        return 'Bahasa Melayu';
    }

    /**
     * Switch locale
     *
     * @deprecated v3.6.0 No-op - language switching disabled
     */
    public function switchLocale(string $locale): void
    {
        // v3.6.0: No-op - language switching disabled
    }

    /**
     * Get date format for current locale
     */
    public function getDateFormat(): string
    {
        return 'd/m/Y'; // Malaysian date format
    }

    /**
     * Get time format for current locale
     */
    public function getTimeFormat(): string
    {
        return 'H:i'; // 24-hour format
    }

    /**
     * Get currency format for current locale
     */
    public function getCurrencyFormat(): string
    {
        return 'MYR'; // Malaysian Ringgit
    }

    /**
     * Get translation statistics
     *
     * @deprecated v3.6.0 Only returns 'ms' stats
     *
     * @return array<string, array{total_keys: int, translated_keys: int, completion_percentage: float}>
     */
    public function getTranslationStats(): array
    {
        return [
            'ms' => [
                'total_keys' => 100,
                'translated_keys' => 100,
                'completion_percentage' => 100.0,
            ],
        ];
    }

    /**
     * Validate translations
     *
     * @deprecated v3.6.0 Always returns empty arrays
     *
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
     * Get language switcher data
     *
     * @deprecated v3.6.0 Language switcher removed in v3.6.0
     *
     * @return array<string, array{name: string, locale: string}>
     */
    public function getLanguageSwitcherData(): array
    {
        // v3.6.0: Language switcher removed - return minimal data for backward compatibility
        return [
            'ms' => [
                'name' => 'Bahasa Melayu',
                'locale' => 'ms',
            ],
        ];
    }

    /**
     * Export translations
     *
     * @deprecated v3.6.0 Placeholder only
     */
    public function exportTranslations(string $format = 'json'): string
    {
        return '{}';
    }

    /**
     * Import translations
     *
     * @deprecated v3.6.0 Placeholder only
     */
    public function importTranslations(string $payload, string $format = 'json'): bool
    {
        return $payload !== '';
    }
}
