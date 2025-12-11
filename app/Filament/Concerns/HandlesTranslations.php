<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

trait HandlesTranslations
{
    /**
     * Safely translate a key, returning the fallback when translation is not a string.
     */
    protected static function trans(string $key, string $fallback = ''): string
    {
        $translation = __($key);

        return is_string($translation) ? $translation : $fallback;
    }

    /**
     * Safely translate a key, returning null when translation is not a string.
     */
    protected static function transOrNull(string $key): ?string
    {
        $translation = __($key);

        return is_string($translation) ? $translation : null;
    }
}
