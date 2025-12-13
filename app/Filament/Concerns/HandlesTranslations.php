<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

trait HandlesTranslations
{
    /**
     * Safely translate a key, returning the fallback when translation is not a string.
     *
     * @return string
     */
    protected static function trans(string $key, string $fallback = ''): string
    {
        $translation = __($key);

        if (is_string($translation)) {
            return $translation;
        }

        return $fallback !== '' ? $fallback : $key;
    }

    /**
     * Safely translate a key, returning null when translation is not a string.
     *
     * @return string|null
     */
    protected static function transOrNull(string $key): ?string
    {
        $translation = __($key);

        if (is_string($translation)) {
            return $translation;
        }

        return null;
    }
}
