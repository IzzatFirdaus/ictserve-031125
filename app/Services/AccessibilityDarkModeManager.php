<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Accessibility Dark Mode Manager Service
 *
 * Manages WCAG 2.2 AA compliance validation for dark mode themes
 * following ICTServe v3.6.1 accessibility standards.
 *
 * @trace Requirements: R16 (WCAG Dark Mode), R6 (Accessibility)
 *
 * @see D14 §2 WCAG 2.2 AA Compliance
 * @see D12 §4 MyDS Design System
 *
 * @version 3.6.1
 */
class AccessibilityDarkModeManager
{
    /**
     * WCAG 2.2 AA contrast ratio requirements
     */
    private const WCAG_AA_TEXT_RATIO = 4.5;

    private const WCAG_AA_UI_RATIO = 3.0;

    private const WCAG_AAA_TEXT_RATIO = 7.0;

    /**
     * High contrast mode multiplier
     */
    private const HIGH_CONTRAST_MULTIPLIER = 1.2;

    /**
     * Cache TTL for contrast calculations (1 hour)
     */
    private const CACHE_TTL = 3600;

    public function __construct(
        private readonly DashboardColorManager $colorManager
    ) {}

    /**
     * Validate WCAG 2.2 AA compliance for theme
     *
     * @param  string  $theme  Theme name ('light' or 'dark')
     * @return array<string, mixed> Validation results
     */
    public function validateThemeCompliance(string $theme): array
    {
        $cacheKey = "wcag_validation.{$theme}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($theme) {
            $palette = $this->colorManager->getColorPalette($theme);
            $results = [
                'theme' => $theme,
                'compliant' => true,
                'issues' => [],
                'recommendations' => [],
                'contrast_ratios' => [],
            ];

            // Test critical color combinations
            $testCombinations = $this->getCriticalColorCombinations($theme);

            foreach ($testCombinations as $combination) {
                $ratio = $this->calculateContrastRatio(
                    $combination['foreground'],
                    $combination['background']
                );

                $results['contrast_ratios'][$combination['name']] = [
                    'ratio' => $ratio,
                    'required' => $combination['required_ratio'],
                    'compliant' => $ratio >= $combination['required_ratio'],
                    'foreground' => $combination['foreground'],
                    'background' => $combination['background'],
                ];

                if ($ratio < $combination['required_ratio']) {
                    $results['compliant'] = false;
                    $results['issues'][] = [
                        'type' => 'contrast_ratio',
                        'element' => $combination['name'],
                        'current_ratio' => $ratio,
                        'required_ratio' => $combination['required_ratio'],
                        'severity' => $ratio < self::WCAG_AA_UI_RATIO ? 'high' : 'medium',
                        'message_bm' => $this->getContrastIssueMessage($combination['name'], $ratio, $combination['required_ratio']),
                    ];
                }
            }

            // Generate recommendations
            if (! $results['compliant']) {
                $results['recommendations'] = $this->generateAccessibilityRecommendations($results['issues'], $theme);
            }

            return $results;
        });
    }

    /**
     * Get critical color combinations for testing
     *
     * @param  string  $theme  Theme name
     * @return array<int, array<string, mixed>> Color combinations to test
     */
    private function getCriticalColorCombinations(string $theme): array
    {
        return [
            [
                'name' => 'body_text',
                'foreground' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '900' : '900', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'card_text',
                'foreground' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '800' : '800', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '100' : '100', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'primary_button',
                'foreground' => $this->colorManager->getColor('neutral', '50', $theme),
                'background' => $this->colorManager->getColor('primary', $theme === 'dark' ? '600' : '600', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'secondary_button',
                'foreground' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '900', $theme),
                'background' => $this->colorManager->getColor('secondary', $theme === 'dark' ? '600' : '400', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'success_text',
                'foreground' => $this->colorManager->getColor('success', $theme === 'dark' ? '300' : '700', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'warning_text',
                'foreground' => $this->colorManager->getColor('warning', $theme === 'dark' ? '300' : '700', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'danger_text',
                'foreground' => $this->colorManager->getColor('danger', $theme === 'dark' ? '200' : '700', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_TEXT_RATIO,
            ],
            [
                'name' => 'border_elements',
                'foreground' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '500' : '500', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_UI_RATIO,
            ],
            [
                'name' => 'focus_indicator',
                'foreground' => $this->colorManager->getColor('primary', '500', $theme),
                'background' => $this->colorManager->getColor('neutral', $theme === 'dark' ? '50' : '50', $theme),
                'required_ratio' => self::WCAG_AA_UI_RATIO,
            ],
        ];
    }

    /**
     * Calculate contrast ratio between two colors
     *
     * @param  string  $foreground  Foreground color (hex)
     * @param  string  $background  Background color (hex)
     * @return float Contrast ratio
     */
    public function calculateContrastRatio(string $foreground, string $background): float
    {
        $foregroundLuminance = $this->calculateLuminance($foreground);
        $backgroundLuminance = $this->calculateLuminance($background);

        $lighter = max($foregroundLuminance, $backgroundLuminance);
        $darker = min($foregroundLuminance, $backgroundLuminance);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Calculate relative luminance of a color
     *
     * @param  string  $hex  Hex color code
     * @return float Relative luminance (0-1)
     */
    private function calculateLuminance(string $hex): float
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        // Apply gamma correction
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        // Calculate luminance
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Generate accessibility recommendations
     *
     * @param  array<int, array<string, mixed>>  $issues  Accessibility issues
     * @param  string  $theme  Theme name
     * @return array<int, array<string, string>> Recommendations
     */
    

/**
 * @param array<string, mixed> $issues
 */
private function generateAccessibilityRecommendations(array $issues, string $theme): array
    {
        $recommendations = [];

        foreach ($issues as $issue) {
            if ($issue['type'] === 'contrast_ratio') {
                $recommendations[] = [
                    'type' => 'color_adjustment',
                    'element' => $issue['element'],
                    'message_bm' => $this->getRecommendationMessage($issue['element'], $issue['current_ratio'], $issue['required_ratio'], $theme),
                    'priority' => $issue['severity'] === 'high' ? 'tinggi' : 'sederhana',
                ];
            }
        }

        // Add general recommendations
        if (count($issues) > 0) {
            $recommendations[] = [
                'type' => 'general',
                'element' => 'theme_optimization',
                'message_bm' => 'Pertimbangkan untuk menggunakan mod kontras tinggi untuk pengguna yang memerlukan aksesibiliti tambahan.',
                'priority' => 'sederhana',
            ];
        }

        return $recommendations;
    }

    /**
     * Get contrast issue message in Bahasa Melayu
     *
     * @param  string  $element  Element name
     * @param  float  $currentRatio  Current contrast ratio
     * @param  float  $requiredRatio  Required contrast ratio
     * @return string Issue message
     */
    private function getContrastIssueMessage(string $element, float $currentRatio, float $requiredRatio): string
    {
        $elementNames = [
            'body_text' => 'teks badan',
            'card_text' => 'teks kad',
            'primary_button' => 'butang utama',
            'secondary_button' => 'butang sekunder',
            'success_text' => 'teks kejayaan',
            'warning_text' => 'teks amaran',
            'danger_text' => 'teks bahaya',
            'border_elements' => 'elemen sempadan',
            'focus_indicator' => 'penunjuk fokus',
        ];

        $elementName = $elementNames[$element] ?? $element;

        return sprintf(
            'Nisbah kontras untuk %s adalah %.2f, tetapi memerlukan sekurang-kurangnya %.2f untuk mematuhi WCAG 2.2 AA.',
            $elementName,
            $currentRatio,
            $requiredRatio
        );
    }

    /**
     * Get recommendation message in Bahasa Melayu
     *
     * @param  string  $element  Element name
     * @param  float  $currentRatio  Current contrast ratio
     * @param  float  $requiredRatio  Required contrast ratio
     * @param  string  $theme  Theme name
     * @return string Recommendation message
     */
    private function getRecommendationMessage(string $element, float $currentRatio, float $requiredRatio, string $theme): string
    {
        $improvement = $requiredRatio - $currentRatio;
        $percentage = round($improvement / $currentRatio * 100);

        if ($theme === 'dark') {
            return sprintf(
                'Tingkatkan kecerahan warna latar depan atau kurangkan kecerahan latar belakang sebanyak kira-kira %d%% untuk mencapai nisbah kontras yang diperlukan.',
                $percentage
            );
        }

        return sprintf(
            'Gelapkan warna latar depan atau cerahkan latar belakang sebanyak kira-kira %d%% untuk mencapai nisbah kontras yang diperlukan.',
            $percentage
        );
    }

    /**
     * Check if high contrast mode is enabled
     *
     * @param  int|null  $userId  User ID (null for guest)
     * @return bool High contrast mode status
     */
    public function isHighContrastEnabled(?int $userId = null): bool
    {
        if ($userId === null) {
            return session('high_contrast_mode', false);
        }

        $user = \App\Models\User::find($userId);
        if (! $user) {
            return false;
        }

        return $user->dashboard_layout['high_contrast_mode'] ?? false;
    }

    /**
     * Enable/disable high contrast mode
     *
     * @param  bool  $enabled  High contrast mode status
     * @param  int|null  $userId  User ID (null for guest)
     * @return bool Success status
     */
    public function setHighContrastMode(bool $enabled, ?int $userId = null): bool
    {
        if ($userId === null) {
            session(['high_contrast_mode' => $enabled]);

            return true;
        }

        try {
            $user = \App\Models\User::find($userId);
            if (! $user) {
                return false;
            }

            $dashboardLayout = $user->dashboard_layout ?? [];
            $dashboardLayout['high_contrast_mode'] = $enabled;

            $user->update(['dashboard_layout' => $dashboardLayout]);

            Log::info('High contrast mode updated', [
                'user_id' => $userId,
                'enabled' => $enabled,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update high contrast mode', [
                'user_id' => $userId,
                'enabled' => $enabled,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get enhanced colors for high contrast mode
     *
     * @param  string  $theme  Theme name
     * @return array<string, string> Enhanced color mappings
     */
    public function getHighContrastColors(string $theme): array
    {
        $baseColors = $this->colorManager->getCssCustomProperties($theme);
        $enhancedColors = [];

        foreach ($baseColors as $property => $color) {
            if (str_contains($property, 'foreground') || str_contains($property, 'text')) {
                // Enhance text colors for better contrast
                $enhancedColors[$property] = $this->enhanceColorContrast($color, true, $theme);
            } elseif (str_contains($property, 'background') || str_contains($property, 'card')) {
                // Enhance background colors
                $enhancedColors[$property] = $this->enhanceColorContrast($color, false, $theme);
            } else {
                $enhancedColors[$property] = $color;
            }
        }

        return $enhancedColors;
    }

    /**
     * Enhance color contrast for high contrast mode
     *
     * @param  string  $color  Original color (hex)
     * @param  bool  $isForeground  Whether this is a foreground color
     * @param  string  $theme  Theme name
     * @return string Enhanced color (hex)
     */
    private function enhanceColorContrast(string $color, bool $isForeground, string $theme): string
    {
        $luminance = $this->calculateLuminance($color);

        if ($theme === 'dark') {
            if ($isForeground) {
                // Make foreground colors brighter in dark mode
                return $luminance < 0.8 ? '#ffffff' : $color;
            } else {
                // Make background colors darker in dark mode
                return $luminance > 0.2 ? '#000000' : $color;
            }
        } else {
            if ($isForeground) {
                // Make foreground colors darker in light mode
                return $luminance > 0.2 ? '#000000' : $color;
            } else {
                // Make background colors brighter in light mode
                return $luminance < 0.8 ? '#ffffff' : $color;
            }
        }
    }

    /**
     * Generate accessibility report
     *
     * @param  string  $theme  Theme name
     * @return array<string, mixed> Accessibility report
     */
    public function generateAccessibilityReport(string $theme): array
    {
        $validation = $this->validateThemeCompliance($theme);

        $report = [
            'theme' => $theme,
            'generated_at' => now()->toISOString(),
            'overall_compliance' => $validation['compliant'],
            'wcag_level' => $validation['compliant'] ? 'AA' : 'Tidak Mematuhi',
            'total_tests' => count($validation['contrast_ratios']),
            'passed_tests' => count(array_filter($validation['contrast_ratios'], fn ($test) => $test['compliant'])),
            'failed_tests' => count(array_filter($validation['contrast_ratios'], fn ($test) => ! $test['compliant'])),
            'issues' => $validation['issues'],
            'recommendations' => $validation['recommendations'],
            'detailed_results' => $validation['contrast_ratios'],
        ];

        // Add summary statistics
        $report['statistics'] = [
            'compliance_percentage' => round(($report['passed_tests'] / $report['total_tests']) * 100, 2),
            'high_severity_issues' => count(array_filter($validation['issues'], fn ($issue) => $issue['severity'] === 'high')),
            'medium_severity_issues' => count(array_filter($validation['issues'], fn ($issue) => $issue['severity'] === 'medium')),
        ];

        return $report;
    }

    /**
     * Clear accessibility cache
     *
     * @return bool Success status
     */
    public function clearAccessibilityCache(): bool
    {
        try {
            Cache::forget('wcag_validation.light');
            Cache::forget('wcag_validation.dark');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear accessibility cache', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
