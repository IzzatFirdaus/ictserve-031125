<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Dashboard Color Manager Service
 *
 * Manages MyDS color system integration with light and dark mode support
 * following ICTServe v3.6.1 patterns and WCAG 2.2 AA compliance.
 *
 * @trace Requirements: R15 (Color System), R16 (WCAG Dark Mode)
 *
 * @see D12 §4 MyDS Design System
 * @see D14 §2 WCAG 2.2 AA Compliance
 *
 * @version 3.6.1
 */
class DashboardColorManager
{
    /**
     * MyDS Color Palette - Light Theme
     * Following Malaysian Design System v2.1.0 specifications
     */
    private const MYDS_LIGHT_COLORS = [
        // Primary Colors (Government Blue)
        'primary' => [
            '50' => '#eff6ff',
            '100' => '#dbeafe',
            '200' => '#bfdbfe',
            '300' => '#93c5fd',
            '400' => '#60a5fa',
            '500' => '#3b82f6',  // Primary
            '600' => '#2563eb',
            '700' => '#1d4ed8',
            '800' => '#1e40af',
            '900' => '#1e3a8a',
        ],

        // Secondary Colors (Malaysian Gold)
        'secondary' => [
            '50' => '#fffbeb',
            '100' => '#fef3c7',
            '200' => '#fde68a',
            '300' => '#fcd34d',
            '400' => '#fbbf24',
            '500' => '#f59e0b',  // Secondary
            '600' => '#d97706',
            '700' => '#b45309',
            '800' => '#92400e',
            '900' => '#78350f',
        ],

        // Neutral Colors
        'neutral' => [
            '50' => '#f9fafb',
            '100' => '#f3f4f6',
            '200' => '#e5e7eb',
            '300' => '#d1d5db',
            '400' => '#9ca3af',
            '500' => '#6b7280',
            '600' => '#4b5563',
            '700' => '#374151',
            '800' => '#1f2937',
            '900' => '#111827',
        ],

        // Semantic Colors
        'success' => [
            '50' => '#ecfdf5',
            '100' => '#d1fae5',
            '200' => '#a7f3d0',
            '300' => '#6ee7b7',
            '400' => '#34d399',
            '500' => '#10b981',  // Success
            '600' => '#059669',
            '700' => '#047857',
            '800' => '#065f46',
            '900' => '#064e3b',
        ],

        'warning' => [
            '50' => '#fffbeb',
            '100' => '#fef3c7',
            '200' => '#fde68a',
            '300' => '#fcd34d',
            '400' => '#fbbf24',
            '500' => '#f59e0b',  // Warning
            '600' => '#d97706',
            '700' => '#b45309',
            '800' => '#92400e',
            '900' => '#78350f',
        ],

        'danger' => [
            '50' => '#fef2f2',
            '100' => '#fee2e2',
            '200' => '#fecaca',
            '300' => '#fca5a5',
            '400' => '#f87171',
            '500' => '#ef4444',  // Danger
            '600' => '#dc2626',
            '700' => '#b91c1c',
            '800' => '#991b1b',
            '900' => '#7f1d1d',
        ],
    ];

    /**
     * MyDS Color Palette - Dark Theme
     * Optimized for dark backgrounds with WCAG 2.2 AA compliance
     */
    private const MYDS_DARK_COLORS = [
        // Primary Colors (Lighter for dark backgrounds)
        'primary' => [
            '50' => '#1e3a8a',
            '100' => '#1e40af',
            '200' => '#1d4ed8',
            '300' => '#2563eb',
            '400' => '#3b82f6',
            '500' => '#60a5fa',  // Primary (lighter for dark)
            '600' => '#93c5fd',
            '700' => '#bfdbfe',
            '800' => '#dbeafe',
            '900' => '#eff6ff',
        ],

        // Secondary Colors (Adjusted for dark theme)
        'secondary' => [
            '50' => '#78350f',
            '100' => '#92400e',
            '200' => '#b45309',
            '300' => '#d97706',
            '400' => '#f59e0b',
            '500' => '#fbbf24',  // Secondary (lighter for dark)
            '600' => '#fcd34d',
            '700' => '#fde68a',
            '800' => '#fef3c7',
            '900' => '#fffbeb',
        ],

        // Neutral Colors (Inverted for dark theme)
        'neutral' => [
            '50' => '#111827',
            '100' => '#1f2937',
            '200' => '#374151',
            '300' => '#4b5563',
            '400' => '#6b7280',
            '500' => '#9ca3af',
            '600' => '#d1d5db',
            '700' => '#e5e7eb',
            '800' => '#f3f4f6',
            '900' => '#f9fafb',
        ],

        // Semantic Colors (Adjusted for dark backgrounds)
        'success' => [
            '50' => '#064e3b',
            '100' => '#065f46',
            '200' => '#047857',
            '300' => '#059669',
            '400' => '#10b981',
            '500' => '#34d399',  // Success (lighter for dark)
            '600' => '#6ee7b7',
            '700' => '#a7f3d0',
            '800' => '#d1fae5',
            '900' => '#ecfdf5',
        ],

        'warning' => [
            '50' => '#78350f',
            '100' => '#92400e',
            '200' => '#b45309',
            '300' => '#d97706',
            '400' => '#f59e0b',
            '500' => '#fbbf24',  // Warning (lighter for dark)
            '600' => '#fcd34d',
            '700' => '#fde68a',
            '800' => '#fef3c7',
            '900' => '#fffbeb',
        ],

        'danger' => [
            '50' => '#7f1d1d',
            '100' => '#991b1b',
            '200' => '#b91c1c',
            '300' => '#dc2626',
            '400' => '#ef4444',
            '500' => '#f87171',  // Danger (lighter for dark)
            '600' => '#fca5a5',
            '700' => '#fecaca',
            '800' => '#fee2e2',
            '900' => '#fef2f2',
        ],
    ];

    /**
     * Theme preference cache key
     */
    private const THEME_CACHE_KEY = 'dashboard_theme_preferences';

    /**
     * Cache TTL for theme preferences (24 hours)
     */
    private const CACHE_TTL = 86400;

    /**
     * Get color palette for specified theme
     *
     * @param  string  $theme  Theme name ('light' or 'dark')
     * @return array<string, array<string, string>> Color palette
     */
    public function getColorPalette(string $theme = 'light'): array
    {
        return match ($theme) {
            'dark' => self::MYDS_DARK_COLORS,
            default => self::MYDS_LIGHT_COLORS,
        };
    }

    /**
     * Get specific color from palette
     *
     * @param  string  $colorName  Color name (primary, secondary, neutral, etc.)
     * @param  string  $shade  Color shade (50-900)
     * @param  string  $theme  Theme name ('light' or 'dark')
     * @return string Hex color code
     */
    public function getColor(string $colorName, string $shade = '500', string $theme = 'light'): string
    {
        $palette = $this->getColorPalette($theme);

        return $palette[$colorName][$shade] ?? '#000000';
    }

    /**
     * Get CSS custom properties for theme
     *
     * @param  string  $theme  Theme name ('light' or 'dark')
     * @return array<string, string> CSS custom properties
     */
    public function getCssCustomProperties(string $theme = 'light'): array
    {
        $palette = $this->getColorPalette($theme);
        $properties = [];

        foreach ($palette as $colorName => $shades) {
            foreach ($shades as $shade => $hex) {
                $properties["--color-{$colorName}-{$shade}"] = $hex;
            }
        }

        // Add semantic color mappings
        $properties = [
            ...$properties,
            '--color-background' => $this->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
            '--color-foreground' => $this->getColor('neutral', $theme === 'dark' ? '900' : '900', $theme),
            '--color-card' => $this->getColor('neutral', $theme === 'dark' ? '100' : '100', $theme),
            '--color-card-foreground' => $this->getColor('neutral', $theme === 'dark' ? '800' : '800', $theme),
            '--color-border' => $this->getColor('neutral', $theme === 'dark' ? '300' : '200', $theme),
            '--color-input' => $this->getColor('neutral', $theme === 'dark' ? '100' : '100', $theme),
            '--color-ring' => $this->getColor('primary', '500', $theme),
        ];

        return $properties;
    }

    /**
     * Generate CSS custom properties string
     *
     * @param  string  $theme  Theme name ('light' or 'dark')
     * @return string CSS custom properties
     */
    public function generateCssProperties(string $theme = 'light'): string
    {
        $properties = $this->getCssCustomProperties($theme);
        $css = ":root {\n";

        foreach ($properties as $property => $value) {
            $css .= "  {$property}: {$value};\n";
        }

        $css .= "}\n";

        return $css;
    }

    /**
     * Get theme preference for user
     *
     * @param  int|null  $userId  User ID (null for guest)
     * @return string Theme preference ('light', 'dark', or 'system')
     */
    public function getUserThemePreference(?int $userId = null): string
    {
        if ($userId === null) {
            // Guest user - check session or return system default
            return session('theme_preference', 'system');
        }

        // Authenticated user - check cache first
        $cacheKey = self::THEME_CACHE_KEY.".user.{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $user = \App\Models\User::find($userId);

            if ($user && isset($user->dashboard_layout['theme_preference'])) {
                return $user->dashboard_layout['theme_preference'];
            }

            return 'system';
        });
    }

    /**
     * Set theme preference for user
     *
     * @param  string  $theme  Theme preference ('light', 'dark', or 'system')
     * @param  int|null  $userId  User ID (null for guest)
     * @return bool Success status
     */
    public function setUserThemePreference(string $theme, ?int $userId = null): bool
    {
        // Validate theme
        if (! \in_array($theme, ['light', 'dark', 'system'], true)) {
            Log::warning('Invalid theme preference', ['theme' => $theme, 'user_id' => $userId]);

            return false;
        }

        if ($userId === null) {
            // Guest user - store in session
            session(['theme_preference' => $theme]);

            return true;
        }

        try {
            // Authenticated user - update database and cache
            $user = \App\Models\User::find($userId);

            if (! $user) {
                return false;
            }

            $dashboardLayout = $user->dashboard_layout ?? [];
            $dashboardLayout['theme_preference'] = $theme;

            $user->update(['dashboard_layout' => $dashboardLayout]);

            // Update cache
            $cacheKey = self::THEME_CACHE_KEY.".user.{$userId}";
            Cache::put($cacheKey, $theme, self::CACHE_TTL);

            Log::info('Theme preference updated', [
                'user_id' => $userId,
                'theme' => $theme,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update theme preference', [
                'user_id' => $userId,
                'theme' => $theme,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Resolve actual theme from preference
     *
     * @param  string  $preference  Theme preference ('light', 'dark', or 'system')
     * @return string Actual theme ('light' or 'dark')
     */
    public function resolveTheme(string $preference): string
    {
        if ($preference === 'system') {
            // Detect system theme preference
            // This would typically be done via JavaScript, but we can provide a server-side fallback
            return $this->detectSystemTheme();
        }

        return \in_array($preference, ['light', 'dark'], true) ? $preference : 'light';
    }

    /**
     * Detect system theme preference (server-side fallback)
     *
     * @return string System theme ('light' or 'dark')
     */
    private function detectSystemTheme(): string
    {
        // Check for dark mode indicators in user agent or headers
        $acceptHeader = request()->header('Accept', '');

        // Some browsers send dark mode preferences in headers
        if (str_contains($acceptHeader, 'prefers-color-scheme: dark')) {
            return 'dark';
        }

        // Default to light theme for server-side detection
        return 'light';
    }

    /**
     * Get theme statistics
     *
     * @return array<string, mixed> Theme usage statistics
     */
    public function getThemeStatistics(): array
    {
        return Cache::remember('theme_statistics', 3600, function () {
            try {
                $users = \App\Models\User::whereNotNull('dashboard_layout')->get();
                $stats = [
                    'total_users' => $users->count(),
                    'light_users' => 0,
                    'dark_users' => 0,
                    'system_users' => 0,
                    'no_preference' => 0,
                ];

                foreach ($users as $user) {
                    $preference = $user->dashboard_layout['theme_preference'] ?? 'system';

                    match ($preference) {
                        'light' => $stats['light_users']++,
                        'dark' => $stats['dark_users']++,
                        'system' => $stats['system_users']++,
                        default => $stats['no_preference']++,
                    };
                }

                // Calculate percentages
                if ($stats['total_users'] > 0) {
                    $stats['light_percentage'] = round(($stats['light_users'] / $stats['total_users']) * 100, 2);
                    $stats['dark_percentage'] = round(($stats['dark_users'] / $stats['total_users']) * 100, 2);
                    $stats['system_percentage'] = round(($stats['system_users'] / $stats['total_users']) * 100, 2);
                }

                return $stats;
            } catch (\Exception $e) {
                Log::error('Failed to get theme statistics', ['error' => $e->getMessage()]);

                return [
                    'total_users' => 0,
                    'light_users' => 0,
                    'dark_users' => 0,
                    'system_users' => 0,
                    'no_preference' => 0,
                ];
            }
        });
    }

    /**
     * Clear theme cache for user
     *
     * @param  int|null  $userId  User ID (null for all users)
     * @return bool Success status
     */
    public function clearThemeCache(?int $userId = null): bool
    {
        try {
            if ($userId === null) {
                // Clear all theme caches
                Cache::forget('theme_statistics');

                // Clear user-specific caches (this is expensive, use sparingly)
                $users = \App\Models\User::pluck('id');
                foreach ($users as $id) {
                    Cache::forget(self::THEME_CACHE_KEY.".user.{$id}");
                }
            } else {
                // Clear specific user cache
                Cache::forget(self::THEME_CACHE_KEY.".user.{$userId}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear theme cache', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
