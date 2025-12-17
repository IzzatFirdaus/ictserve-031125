<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

/**
 * Theme Preference Service
 *
 * Centralizes theme preference retrieval and persistence.
 *
 * trace: D12 §6.10, D14 §6.1.2, D14 §8.1
 */
class ThemePreferenceService
{
    /**
     * Get stored theme from session/cookie/user, defaulting to 'light'.
     * v3.6.0: Only 'light' and 'dark' allowed.
     */
    public function getStoredTheme(): string
    {
        $sessionTheme = Session::get('theme_preference');
        if (is_string($sessionTheme) && in_array($sessionTheme, ['light', 'dark'], true)) {
            return $sessionTheme;
        }

        $cookieTheme = Cookie::get('theme_preference');
        if (is_string($cookieTheme) && in_array($cookieTheme, ['light', 'dark'], true)) {
            return $cookieTheme;
        }

        $user = Auth::user();
        if ($user instanceof User) {
            $userTheme = $user->theme_preference;
            if (is_string($userTheme) && in_array($userTheme, ['light', 'dark'], true)) {
                return $userTheme;
            }
        }

        return 'light';
    }

    /**
     * Persist theme preference (session + cookie + user DB when authenticated).
     */
    public function setTheme(string $theme): void
    {
        if (! in_array($theme, ['light', 'dark'], true)) {
            return;
        }

        Session::put('theme_preference', $theme);
        Cookie::queue('theme_preference', $theme, 60 * 24 * 30);

        $user = Auth::user();
        if ($user instanceof User) {
            $user->update(['theme_preference' => $theme]);
        }
    }
}
