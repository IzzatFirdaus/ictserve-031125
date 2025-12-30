<?php

declare(strict_types=1);

namespace App\Filament\Traits;

/**
 * Theme-Aware Chart Colors Trait
 *
 * Provides WCAG 2.2 AA compliant chart colors that adapt to light/dark theme.
 * Colors maintain 3:1 contrast ratio for chart elements in both themes.
 *
 * @trace Requirements: 6.7 (Chart Theme Adaptation)
 *
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA compliance
 */
trait ThemeAwareChartColors
{
    /**
     * Get theme-aware primary color
     */
    protected function getChartPrimaryColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(96, 165, 250, {$opacity})"  // blue-400 for dark mode
            : "rgba(0, 86, 179, {$opacity})";   // MOTAC primary for light mode
    }

    /**
     * Get theme-aware success color
     */
    protected function getChartSuccessColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(74, 222, 128, {$opacity})"  // green-400 for dark mode
            : "rgba(27, 124, 84, {$opacity})";  // MOTAC success for light mode
    }

    /**
     * Get theme-aware warning color
     */
    protected function getChartWarningColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(251, 191, 36, {$opacity})"  // amber-400 for dark mode
            : "rgba(204, 119, 0, {$opacity})";  // MOTAC warning for light mode
    }

    /**
     * Get theme-aware danger color
     */
    protected function getChartDangerColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(248, 113, 113, {$opacity})" // red-400 for dark mode
            : "rgba(179, 0, 45, {$opacity})";   // MOTAC danger for light mode
    }

    /**
     * Get theme-aware info color
     */
    protected function getChartInfoColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(56, 189, 248, {$opacity})"  // sky-400 for dark mode
            : "rgba(13, 202, 240, {$opacity})"; // info for light mode
    }

    /**
     * Get theme-aware gray color
     */
    protected function getChartGrayColor(float $opacity = 1.0): string
    {
        return $this->isDarkMode()
            ? "rgba(148, 163, 184, {$opacity})" // slate-400 for dark mode
            : "rgba(107, 114, 128, {$opacity})"; // gray-500 for light mode
    }

    /**
     * Get theme-aware text color for chart labels
     */
    protected function getChartTextColor(): string
    {
        return $this->isDarkMode() ? '#e2e8f0' : '#374151'; // slate-200 / gray-700
    }

    /**
     * Get theme-aware grid color
     */
    protected function getChartGridColor(): string
    {
        return $this->isDarkMode()
            ? 'rgba(71, 85, 105, 0.5)'   // slate-600 with transparency
            : 'rgba(229, 231, 235, 0.5)'; // gray-200 with transparency
    }

    /**
     * Get theme-aware tooltip configuration
     *
     * @return array<string, mixed>
     */
    protected function getChartTooltipConfig(): array
    {
        return [
            'enabled' => true,
            'backgroundColor' => $this->isDarkMode() ? 'rgba(30, 41, 59, 0.95)' : 'rgba(31, 41, 55, 0.95)',
            'titleColor' => '#ffffff',
            'bodyColor' => '#ffffff',
            'borderColor' => $this->isDarkMode() ? 'rgba(71, 85, 105, 0.5)' : 'rgba(229, 231, 235, 0.5)',
            'borderWidth' => 1,
            'cornerRadius' => 8,
            'padding' => 12,
        ];
    }

    /**
     * Get standard chart color palette (theme-aware)
     *
     * @return array<int, string>
     */
    protected function getChartColorPalette(float $opacity = 0.7): array
    {
        return [
            $this->getChartPrimaryColor($opacity),
            $this->getChartSuccessColor($opacity),
            $this->getChartWarningColor($opacity),
            $this->getChartDangerColor($opacity),
            $this->getChartInfoColor($opacity),
            $this->getChartGrayColor($opacity),
        ];
    }

    /**
     * Get standard chart border color palette (theme-aware)
     *
     * @return array<int, string>
     */
    protected function getChartBorderPalette(): array
    {
        return [
            $this->getChartPrimaryColor(1.0),
            $this->getChartSuccessColor(1.0),
            $this->getChartWarningColor(1.0),
            $this->getChartDangerColor(1.0),
            $this->getChartInfoColor(1.0),
            $this->getChartGrayColor(1.0),
        ];
    }

    /**
     * Get theme-aware scale options for Y axis
     *
     * @return array<string, mixed>
     */
    protected function getChartYScaleOptions(): array
    {
        return [
            'beginAtZero' => true,
            'ticks' => [
                'precision' => 0,
                'color' => $this->getChartTextColor(),
            ],
            'grid' => [
                'color' => $this->getChartGridColor(),
            ],
        ];
    }

    /**
     * Get theme-aware scale options for X axis
     *
     * @return array<string, mixed>
     */
    protected function getChartXScaleOptions(bool $showGrid = false): array
    {
        return [
            'ticks' => [
                'color' => $this->getChartTextColor(),
            ],
            'grid' => [
                'display' => $showGrid,
                'color' => $this->getChartGridColor(),
            ],
        ];
    }

    /**
     * Check if dark mode is active
     */
    protected function isDarkMode(): bool
    {
        // Check user preference first
        if (auth()->check() && auth()->user()->theme === 'dark') {
            return true;
        }

        // Check session/cookie for theme preference
        if (session()->has('theme')) {
            return session('theme') === 'dark';
        }

        // Default to light mode
        return false;
    }
}
