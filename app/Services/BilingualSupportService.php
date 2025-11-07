<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class BilingualSupportService
{
    private const SUPPORTED_LOCALES = ['ms', 'en'];

    private const DEFAULT_LOCALE = 'ms';

    private const COOKIE_NAME = 'filament_locale';

    private const COOKIE_DURATION = 525600; // 1 year in minutes

    public function getSupportedLocales(): array
    {
        return [
            'ms' => [
                'code' => 'ms',
                'name' => 'Bahasa Melayu',
                'native_name' => 'Bahasa Melayu',
                'flag' => '🇲🇾',
                'direction' => 'ltr',
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇺🇸',
                'direction' => 'ltr',
            ],
        ];
    }

    public function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    public function setLocale(string $locale): void
    {
        if (! in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = self::DEFAULT_LOCALE;
        }

        // Set application locale
        App::setLocale($locale);

        // Store in session
        Session::put('locale', $locale);

        // Store in cookie
        Cookie::queue(
            self::COOKIE_NAME,
            $locale,
            self::COOKIE_DURATION
        );
    }

    public function detectLocale(): string
    {
        // Priority: Session > Cookie > Accept-Language > Default

        // 1. Check session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // 2. Check cookie
        if (Cookie::has(self::COOKIE_NAME)) {
            $locale = Cookie::get(self::COOKIE_NAME);
            if (in_array($locale, self::SUPPORTED_LOCALES)) {
                return $locale;
            }
        }

        // 3. Check Accept-Language header
        if (request()->hasHeader('Accept-Language')) {
            $acceptLanguage = request()->header('Accept-Language');
            $preferredLocale = $this->parseAcceptLanguage($acceptLanguage);
            if (in_array($preferredLocale, self::SUPPORTED_LOCALES)) {
                return $preferredLocale;
            }
        }

        // 4. Default fallback
        return self::DEFAULT_LOCALE;
    }

    public function getTranslationFiles(): array
    {
        $translations = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $langPath = resource_path("lang/{$locale}");

            if (File::exists($langPath)) {
                $files = File::allFiles($langPath);

                foreach ($files as $file) {
                    $key = $file->getFilenameWithoutExtension();
                    $translations[$locale][$key] = include $file->getPathname();
                }
            }
        }

        return $translations;
    }

    public function validateTranslations(): array
    {
        $translations = $this->getTranslationFiles();
        $issues = [];

        if (empty($translations)) {
            return ['error' => 'No translation files found'];
        }

        // Get all keys from all locales
        $allKeys = [];
        foreach ($translations as $locale => $files) {
            foreach ($files as $file => $content) {
                $keys = $this->flattenArray($content, $file);
                $allKeys = array_merge($allKeys, array_keys($keys));
            }
        }
        $allKeys = array_unique($allKeys);

        // Check for missing translations
        foreach (self::SUPPORTED_LOCALES as $locale) {
            foreach ($allKeys as $key) {
                $value = $this->getNestedValue($translations[$locale] ?? [], $key);
                if ($value === null) {
                    $issues['missing'][$locale][] = $key;
                }
            }
        }

        // Check for empty translations
        foreach ($translations as $locale => $files) {
            foreach ($files as $file => $content) {
                $flatContent = $this->flattenArray($content, $file);
                foreach ($flatContent as $key => $value) {
                    if (empty(trim($value))) {
                        $issues['empty'][$locale][] = $key;
                    }
                }
            }
        }

        return $issues;
    }

    public function getTranslationStats(): array
    {
        $translations = $this->getTranslationFiles();
        $stats = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $totalKeys = 0;
            $translatedKeys = 0;

            if (isset($translations[$locale])) {
                foreach ($translations[$locale] as $file => $content) {
                    $flatContent = $this->flattenArray($content);
                    $totalKeys += count($flatContent);
                    $translatedKeys += count(array_filter($flatContent, fn ($value) => ! empty(trim($value))));
                }
            }

            $stats[$locale] = [
                'total_keys' => $totalKeys,
                'translated_keys' => $translatedKeys,
                'completion_percentage' => $totalKeys > 0 ? round(($translatedKeys / $totalKeys) * 100, 2) : 0,
            ];
        }

        return $stats;
    }

    public function exportTranslations(string $format = 'json'): string
    {
        $translations = $this->getTranslationFiles();

        return match ($format) {
            'json' => json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'csv' => $this->exportToCsv($translations),
            'xlsx' => $this->exportToExcel($translations),
            default => json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        };
    }

    public function importTranslations(string $content, string $format = 'json'): bool
    {
        try {
            $translations = match ($format) {
                'json' => json_decode($content, true),
                'csv' => $this->importFromCsv($content),
                default => json_decode($content, true),
            };

            if (! $translations) {
                return false;
            }

            foreach ($translations as $locale => $files) {
                if (! in_array($locale, self::SUPPORTED_LOCALES)) {
                    continue;
                }

                foreach ($files as $file => $content) {
                    $filePath = resource_path("lang/{$locale}/{$file}.php");

                    // Ensure directory exists
                    $directory = dirname($filePath);
                    if (! File::exists($directory)) {
                        File::makeDirectory($directory, 0755, true);
                    }

                    // Write translation file
                    $phpContent = "<?php\n\nreturn ".var_export($content, true).";\n";
                    File::put($filePath, $phpContent);
                }
            }

            // Clear translation cache
            Cache::forget('translations');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLanguageSwitcherData(): array
    {
        $locales = $this->getSupportedLocales();
        $current = $this->getCurrentLocale();

        return [
            'current' => $locales[$current] ?? $locales[self::DEFAULT_LOCALE],
            'available' => $locales,
            'switch_url' => route('filament.admin.language.switch', ['locale' => '{locale}']),
        ];
    }

    private function parseAcceptLanguage(string $acceptLanguage): string
    {
        $languages = [];

        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', trim($lang));
            $code = trim($parts[0]);
            $quality = 1.0;

            if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
                $quality = (float) substr($parts[1], 2);
            }

            // Extract primary language code
            $primaryCode = explode('-', $code)[0];
            $languages[$primaryCode] = $quality;
        }

        // Sort by quality
        arsort($languages);

        // Return first supported language
        foreach (array_keys($languages) as $code) {
            if (in_array($code, self::SUPPORTED_LOCALES)) {
                return $code;
            }
        }

        return self::DEFAULT_LOCALE;
    }

    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    private function getNestedValue(array $array, string $key)
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (! isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    private function exportToCsv(array $translations): string
    {
        $csv = "Key,Bahasa Melayu,English\n";

        // Get all unique keys
        $allKeys = [];
        foreach ($translations as $locale => $files) {
            foreach ($files as $file => $content) {
                $keys = $this->flattenArray($content, $file);
                $allKeys = array_merge($allKeys, array_keys($keys));
            }
        }
        $allKeys = array_unique($allKeys);

        foreach ($allKeys as $key) {
            $msValue = $this->getNestedValue($translations['ms'] ?? [], $key) ?? '';
            $enValue = $this->getNestedValue($translations['en'] ?? [], $key) ?? '';

            $csv .= '"'.str_replace('"', '""', $key).'","'.
                   str_replace('"', '""', $msValue).'","'.
                   str_replace('"', '""', $enValue)."\"\n";
        }

        return $csv;
    }

    private function importFromCsv(string $content): array
    {
        $lines = explode("\n", $content);
        $header = str_getcsv(array_shift($lines));
        $translations = ['ms' => [], 'en' => []];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $data = str_getcsv($line);
            if (count($data) >= 3) {
                $key = $data[0];
                $msValue = $data[1] ?? '';
                $enValue = $data[2] ?? '';

                if (! empty($key)) {
                    $this->setNestedValue($translations['ms'], $key, $msValue);
                    $this->setNestedValue($translations['en'], $key, $enValue);
                }
            }
        }

        return $translations;
    }

    private function setNestedValue(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (! isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    private function exportToExcel(array $translations): string
    {
        // This would require a library like PhpSpreadsheet
        // For now, return JSON format
        return $this->exportTranslations('json');
    }
}
