<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\AccessibilityDarkModeManager;
use App\Services\DashboardColorManager;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced Theme Toggle Widget
 *
 * Comprehensive theme management widget with MyDS color system integration,
 * WCAG 2.2 AA compliance validation, and user preference persistence.
 *
 * @trace Requirements: R15 (Color System), R16 (WCAG Dark Mode)
 *
 * @see D12 §4 MyDS Design System
 * @see D14 §2 WCAG 2.2 AA Compliance
 *
 * @version 3.6.1
 */
class ThemeToggleWidget extends Widget
{
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 §4 MyDS Design System, D14 §2 WCAG 2.2 AA';
    }

    protected string $view = 'filament.widgets.theme-toggle-unified';

    public string $theme = 'light';

    public string $themePreference = 'system';

    public bool $highContrastMode = false;

    public bool $systemThemeDetected = false;

    /**
     * Widget metadata for registry
     */
    public static function getWidgetCategory(): string
    {
        return 'header';
    }

    public static function getWidgetSortOrder(): int
    {
        return 100; // Display at end of header
    }

    public static function getWidgetRoles(): array
    {
        return ['staff', 'admin', 'superuser']; // All authenticated users
    }

    public static function getWidgetRefreshRate(): int
    {
        return 0; // No auto-refresh needed
    }

    public static function getWidgetCacheTtl(): int
    {
        return 300; // 5 minutes cache for theme preferences
    }

    public function mount(): void
    {
        $userId = auth()->id();
        $colorManager = app(DashboardColorManager::class);
        $accessibilityManager = app(AccessibilityDarkModeManager::class);

        // Get user's theme preference
        $this->themePreference = $colorManager->getUserThemePreference($userId);

        // Resolve actual theme from preference
        $this->theme = $colorManager->resolveTheme($this->themePreference);

        // Get high contrast mode status
        $this->highContrastMode = $accessibilityManager->isHighContrastEnabled($userId);

        // Detect if system theme detection is available (will be handled by JavaScript)
        $this->systemThemeDetected = $this->themePreference === 'system';

        Log::info('Theme toggle widget mounted', [
            'user_id' => $userId,
            'theme_preference' => $this->themePreference,
            'resolved_theme' => $this->theme,
            'high_contrast' => $this->highContrastMode,
        ]);
    }

    /**
     * Set theme preference
     *
     * @param  string  $preference  Theme preference ('light', 'dark', 'system')
     */
    public function setThemePreference(string $preference): void
    {
        $userId = auth()->id();
        $colorManager = app(DashboardColorManager::class);

        // Validate and set preference
        if (! \in_array($preference, ['light', 'dark', 'system'], true)) {
            $this->addError('theme', 'Pilihan tema tidak sah.');

            return;
        }

        $success = $colorManager->setUserThemePreference($preference, $userId);

        if ($success) {
            $this->themePreference = $preference;
            $this->theme = $colorManager->resolveTheme($preference);
            $this->systemThemeDetected = $preference === 'system';

            // Dispatch theme change event
            $this->dispatch('theme-preference-changed', [
                'preference' => $preference,
                'theme' => $this->theme,
                'systemDetected' => $this->systemThemeDetected,
            ]);

            // Show success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Tema Dikemas Kini',
                'message' => 'Pilihan tema anda telah disimpan.',
            ]);

            Log::info('Theme preference updated', [
                'user_id' => $userId,
                'preference' => $preference,
                'resolved_theme' => $this->theme,
            ]);
        } else {
            $this->addError('theme', 'Gagal menyimpan pilihan tema. Sila cuba lagi.');

            Log::error('Failed to update theme preference', [
                'user_id' => $userId,
                'preference' => $preference,
            ]);
        }
    }

    /**
     * Toggle high contrast mode
     */
    public function toggleHighContrast(): void
    {
        $userId = auth()->id();
        $accessibilityManager = app(AccessibilityDarkModeManager::class);
        $newMode = ! $this->highContrastMode;

        $success = $accessibilityManager->setHighContrastMode($newMode, $userId);

        if ($success) {
            $this->highContrastMode = $newMode;

            // Dispatch high contrast change event
            $this->dispatch('high-contrast-changed', [
                'enabled' => $newMode,
            ]);

            // Show success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Mod Kontras Tinggi',
                'message' => $newMode
                    ? 'Mod kontras tinggi telah diaktifkan.'
                    : 'Mod kontras tinggi telah dimatikan.',
            ]);

            Log::info('High contrast mode toggled', [
                'user_id' => $userId,
                'enabled' => $newMode,
            ]);
        } else {
            $this->addError('contrast', 'Gagal mengubah mod kontras tinggi. Sila cuba lagi.');

            Log::error('Failed to toggle high contrast mode', [
                'user_id' => $userId,
                'enabled' => $newMode,
            ]);
        }
    }

    /**
     * Get theme statistics for display
     *
     * @return array<string, mixed> Theme statistics
     */
    public function getThemeStatistics(): array
    {
        $colorManager = app(DashboardColorManager::class);

        return $colorManager->getThemeStatistics();
    }

    /**
     * Get accessibility report for current theme
     *
     * @return array<string, mixed> Accessibility report
     */
    public function getAccessibilityReport(): array
    {
        $accessibilityManager = app(AccessibilityDarkModeManager::class);

        return $accessibilityManager->generateAccessibilityReport($this->theme);
    }

    /**
     * Get CSS custom properties for current theme
     *
     * @return array<string, string> CSS custom properties
     */
    public function getCssCustomProperties(): array
    {
        $colorManager = app(DashboardColorManager::class);
        $accessibilityManager = app(AccessibilityDarkModeManager::class);

        if ($this->highContrastMode) {
            return $accessibilityManager->getHighContrastColors($this->theme);
        }

        return $colorManager->getCssCustomProperties($this->theme);
    }

    /**
     * Legacy method for backward compatibility
     *
     * @param  string  $theme  Theme name ('light' or 'dark')
     */
    public function setTheme(string $theme): void
    {
        // Convert direct theme setting to preference
        $preference = $theme === 'dark' ? 'dark' : 'light';
        $this->setThemePreference($preference);
    }

    /**
     * Check if widget can be viewed by current user
     */
    public static function canView(): bool
    {
        return auth()->check(); // Only authenticated users can change themes
    }

    /**
     * Get widget configuration for customization panel
     */
    public function getWidgetConfiguration(): array
    {
        return [
            'theme_preference' => $this->themePreference,
            'high_contrast_mode' => $this->highContrastMode,
            'system_theme_detected' => $this->systemThemeDetected,
        ];
    }
}
