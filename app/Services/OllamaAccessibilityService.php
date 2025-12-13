<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * OllamaAccessibilityService
 *
 * Perkhidmatan kebolehcapaian untuk antara muka AI Ollama.
 * Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0.
 *
 * @see D12_UI_UX_DESIGN_GUIDE.md
 * @see D13_UI_UX_FRONTEND_FRAMEWORK.md
 * @see D14_UI_UX_STYLE_GUIDE.md
 * @see D15_LANGUAGE_MS_EN.md (Bahasa Melayu sahaja)
 */
class OllamaAccessibilityService
{
    /**
     * WCAG 2.2 AA Color Contrast Requirements
     * Text: 4.5:1 minimum
     * UI Components: 3:1 minimum
     */
    private const MIN_TEXT_CONTRAST = 4.5;

    private const MIN_UI_CONTRAST = 3.0;

    /**
     * Minimum touch target size (44x44px per WCAG 2.5.5)
     */
    private const MIN_TOUCH_TARGET = 44;

    /**
     * Focus indicator specifications (D14 v3.6.0)
     */
    private const FOCUS_OUTLINE_WIDTH = 3;

    private const FOCUS_OUTLINE_OFFSET = 2;

    /**
     * ICTServe compliant color palette (D14 v3.6.0)
     */
    private const COLOR_PALETTE = [
        'primary' => '#0056B3',
        'secondary' => '#0B4D8F',
        'success' => '#1B7C54',
        'warning' => '#CC7700',
        'danger' => '#B3002D',
        'text_primary' => '#1F2937',
        'text_secondary' => '#6B7280',
        'background' => '#FFFFFF',
    ];

    /**
     * Get ARIA attributes for AI chat interface
     *
     * @return array<string, string>
     */
    public function getChatAriaAttributes(): array
    {
        return [
            'role' => 'log',
            'aria-live' => 'polite',
            'aria-atomic' => 'false',
            'aria-relevant' => 'additions',
            'aria-label' => __('ollama.accessibility.chat_region'),
        ];
    }

    /**
     * Get ARIA attributes for AI response loading state
     *
     * @return array<string, string>
     */
    public function getLoadingAriaAttributes(bool $isLoading): array
    {
        return [
            'aria-busy' => $isLoading ? 'true' : 'false',
            'aria-live' => 'polite',
            'aria-label' => $isLoading
                ? __('ollama.accessibility.loading_response')
                : __('ollama.accessibility.response_ready'),
        ];
    }

    /**
     * Get ARIA attributes for error messages
     *
     * @return array<string, string>
     */
    public function getErrorAriaAttributes(): array
    {
        return [
            'role' => 'alert',
            'aria-live' => 'assertive',
            'aria-atomic' => 'true',
        ];
    }

    /**
     * Get ARIA attributes for success messages
     *
     * @return array<string, string>
     */
    public function getSuccessAriaAttributes(): array
    {
        return [
            'role' => 'status',
            'aria-live' => 'polite',
            'aria-atomic' => 'true',
        ];
    }

    /**
     * Get focus trap configuration for modal dialogs
     *
     * @return array<string, mixed>
     */
    public function getFocusTrapConfig(): array
    {
        return [
            'initialFocus' => '[data-autofocus]',
            'fallbackFocus' => '[role="dialog"]',
            'escapeDeactivates' => true,
            'clickOutsideDeactivates' => false,
            'returnFocusOnDeactivate' => true,
        ];
    }

    /**
     * Get skip navigation links configuration
     *
     * @return array<int, array{id: string, label: string}>
     */
    public function getSkipLinks(): array
    {
        return [
            [
                'id' => 'main-content',
                'label' => __('ollama.accessibility.skip_to_main'),
            ],
            [
                'id' => 'ai-chat',
                'label' => __('ollama.accessibility.skip_to_chat'),
            ],
            [
                'id' => 'ai-results',
                'label' => __('ollama.accessibility.skip_to_results'),
            ],
        ];
    }

    /**
     * Get keyboard navigation instructions
     *
     * @return array<string, string>
     */
    public function getKeyboardInstructions(): array
    {
        return [
            'enter' => __('ollama.accessibility.key_enter'),
            'escape' => __('ollama.accessibility.key_escape'),
            'tab' => __('ollama.accessibility.key_tab'),
            'arrow_up' => __('ollama.accessibility.key_arrow_up'),
            'arrow_down' => __('ollama.accessibility.key_arrow_down'),
        ];
    }

    /**
     * Validate color contrast ratio
     */
    public function validateContrast(string $foreground, string $background, string $type = 'text'): bool
    {
        $ratio = $this->calculateContrastRatio($foreground, $background);
        $minRatio = $type === 'text' ? self::MIN_TEXT_CONTRAST : self::MIN_UI_CONTRAST;

        return $ratio >= $minRatio;
    }

    /**
     * Calculate contrast ratio between two colors
     */
    public function calculateContrastRatio(string $color1, string $color2): float
    {
        $l1 = $this->getRelativeLuminance($color1);
        $l2 = $this->getRelativeLuminance($color2);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Get relative luminance of a color
     */
    private function getRelativeLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Get accessible color for given background
     */
    public function getAccessibleTextColor(string $background): string
    {
        $luminance = $this->getRelativeLuminance($background);

        // Use dark text on light backgrounds, light text on dark backgrounds
        return $luminance > 0.179 ? self::COLOR_PALETTE['text_primary'] : '#FFFFFF';
    }

    /**
     * Validate touch target size
     */
    public function validateTouchTarget(int $width, int $height): bool
    {
        return $width >= self::MIN_TOUCH_TARGET && $height >= self::MIN_TOUCH_TARGET;
    }

    /**
     * Get minimum touch target size
     */
    public function getMinTouchTargetSize(): int
    {
        return self::MIN_TOUCH_TARGET;
    }

    /**
     * Get focus indicator styles
     *
     * @return array<string, mixed>
     */
    public function getFocusIndicatorStyles(): array
    {
        return [
            'outline_width' => self::FOCUS_OUTLINE_WIDTH.'px',
            'outline_offset' => self::FOCUS_OUTLINE_OFFSET.'px',
            'outline_color' => self::COLOR_PALETTE['primary'],
            'outline_style' => 'solid',
        ];
    }

    /**
     * Get screen reader announcement for AI response
     */
    public function getResponseAnnouncement(string $status, ?string $content = null): string
    {
        return match ($status) {
            'loading' => __('ollama.accessibility.sr_loading'),
            'success' => __('ollama.accessibility.sr_response_received', ['preview' => mb_substr($content ?? '', 0, 100)]),
            'error' => __('ollama.accessibility.sr_error_occurred'),
            'empty' => __('ollama.accessibility.sr_no_results'),
            default => __('ollama.accessibility.sr_status_unknown'),
        };
    }

    /**
     * Get HTML lang attribute (D15 v3.6.0 - Bahasa Melayu sahaja)
     */
    public function getHtmlLangAttribute(): string
    {
        return 'ms';
    }

    /**
     * Check if reduced motion is preferred
     */
    public function prefersReducedMotion(): bool
    {
        // This would typically be detected via JavaScript and stored in session
        return (bool) Cache::get('prefers_reduced_motion_'.session()->getId(), false);
    }

    /**
     * Set reduced motion preference
     */
    public function setReducedMotionPreference(bool $prefer): void
    {
        Cache::put('prefers_reduced_motion_'.session()->getId(), $prefer, now()->addDay());
    }

    /**
     * Get animation duration based on reduced motion preference
     */
    public function getAnimationDuration(int $defaultMs = 300): int
    {
        return $this->prefersReducedMotion() ? 0 : $defaultMs;
    }

    /**
     * Get accessible loading indicator configuration
     *
     * @return array<string, mixed>
     */
    public function getLoadingIndicatorConfig(): array
    {
        return [
            'aria_busy' => true,
            'aria_live' => 'polite',
            'aria_label' => __('ollama.accessibility.loading_indicator'),
            'role' => 'status',
            'spinner_aria_hidden' => true,
            'text_visible' => true,
            'text' => __('ollama.accessibility.loading_text'),
        ];
    }

    /**
     * Get accessible notification configuration
     *
     * @return array<string, mixed>
     */
    public function getNotificationConfig(string $type): array
    {
        $configs = [
            'success' => [
                'role' => 'status',
                'aria_live' => 'polite',
                'icon_aria_label' => __('ollama.accessibility.icon_success'),
                'color_class' => 'text-success-600 bg-success-50',
            ],
            'error' => [
                'role' => 'alert',
                'aria_live' => 'assertive',
                'icon_aria_label' => __('ollama.accessibility.icon_error'),
                'color_class' => 'text-danger-600 bg-danger-50',
            ],
            'warning' => [
                'role' => 'alert',
                'aria_live' => 'polite',
                'icon_aria_label' => __('ollama.accessibility.icon_warning'),
                'color_class' => 'text-warning-600 bg-warning-50',
            ],
            'info' => [
                'role' => 'status',
                'aria_live' => 'polite',
                'icon_aria_label' => __('ollama.accessibility.icon_info'),
                'color_class' => 'text-primary-600 bg-primary-50',
            ],
        ];

        return $configs[$type] ?? $configs['info'];
    }

    /**
     * Get WCAG compliance status for AI interface
     *
     * @return array<string, mixed>
     */
    public function getComplianceStatus(): array
    {
        return [
            'wcag_level' => 'AA',
            'wcag_version' => '2.2',
            'color_contrast_text' => self::MIN_TEXT_CONTRAST,
            'color_contrast_ui' => self::MIN_UI_CONTRAST,
            'touch_target_min' => self::MIN_TOUCH_TARGET,
            'focus_indicator_width' => self::FOCUS_OUTLINE_WIDTH,
            'language' => 'ms',
            'language_name' => 'Bahasa Melayu',
            'keyboard_navigation' => true,
            'screen_reader_support' => true,
            'reduced_motion_support' => true,
        ];
    }
}
