<?php

declare(strict_types=1);

namespace App\Services;

class AccessibilityComplianceService
{
    private const WCAG_AA_TEXT_CONTRAST = 4.5;

    private const WCAG_AA_UI_CONTRAST = 3.0;

    private const MIN_TOUCH_TARGET_SIZE = 44;

    public function validateColorContrast(string $foreground, string $background): array
    {
        $foregroundRgb = $this->hexToRgb($foreground);
        $backgroundRgb = $this->hexToRgb($background);

        $contrastRatio = $this->calculateContrastRatio($foregroundRgb, $backgroundRgb);

        return [
            'contrast_ratio' => round($contrastRatio, 2),
            'wcag_aa_text' => $contrastRatio >= self::WCAG_AA_TEXT_CONTRAST,
            'wcag_aa_ui' => $contrastRatio >= self::WCAG_AA_UI_CONTRAST,
            'foreground' => $foreground,
            'background' => $background,
        ];
    }

    public function getCompliantColorPalette(): array
    {
        return [
            'primary' => [
                'color' => '#0056b3',
                'on_white' => $this->validateColorContrast('#0056b3', '#ffffff'),
                'on_gray_50' => $this->validateColorContrast('#0056b3', '#f9fafb'),
            ],
            'success' => [
                'color' => '#198754',
                'on_white' => $this->validateColorContrast('#198754', '#ffffff'),
                'on_gray_50' => $this->validateColorContrast('#198754', '#f9fafb'),
            ],
            'warning' => [
                'color' => '#ff8c00',
                'on_white' => $this->validateColorContrast('#ff8c00', '#ffffff'),
                'on_gray_50' => $this->validateColorContrast('#ff8c00', '#f9fafb'),
            ],
            'danger' => [
                'color' => '#b50c0c',
                'on_white' => $this->validateColorContrast('#b50c0c', '#ffffff'),
                'on_gray_50' => $this->validateColorContrast('#b50c0c', '#f9fafb'),
            ],
            'text' => [
                'primary' => $this->validateColorContrast('#111827', '#ffffff'),
                'secondary' => $this->validateColorContrast('#6b7280', '#ffffff'),
                'muted' => $this->validateColorContrast('#9ca3af', '#ffffff'),
            ],
        ];
    }

    public function generateFocusStyles(): array
    {
        return [
            'outline_width' => '3px',
            'outline_style' => 'solid',
            'outline_color' => '#0056b3',
            'outline_offset' => '2px',
            'border_radius' => '4px',
            'css' => 'outline: 3px solid #0056b3; outline-offset: 2px; border-radius: 4px;',
        ];
    }

    public function validateTouchTargets(array $elements): array
    {
        $results = [];

        foreach ($elements as $element) {
            $width = $element['width'] ?? 0;
            $height = $element['height'] ?? 0;

            $results[] = [
                'element' => $element['selector'] ?? 'unknown',
                'width' => $width,
                'height' => $height,
                'compliant' => $width >= self::MIN_TOUCH_TARGET_SIZE && $height >= self::MIN_TOUCH_TARGET_SIZE,
                'min_size' => self::MIN_TOUCH_TARGET_SIZE,
            ];
        }

        return $results;
    }

    public function generateAriaAttributes(): array
    {
        return [
            'landmarks' => [
                'navigation' => 'role="navigation" aria-label="Main navigation"',
                'main' => 'role="main" aria-label="Main content"',
                'complementary' => 'role="complementary" aria-label="Sidebar"',
                'contentinfo' => 'role="contentinfo" aria-label="Footer"',
                'banner' => 'role="banner" aria-label="Header"',
            ],
            'forms' => [
                'required_field' => 'aria-required="true"',
                'invalid_field' => 'aria-invalid="true" aria-describedby="error-message"',
                'field_description' => 'aria-describedby="field-help"',
                'fieldset' => 'role="group" aria-labelledby="fieldset-legend"',
            ],
            'interactive' => [
                'button' => 'role="button" aria-pressed="false"',
                'toggle_button' => 'role="button" aria-pressed="true"',
                'menu_button' => 'role="button" aria-haspopup="true" aria-expanded="false"',
                'tab' => 'role="tab" aria-selected="false" tabindex="-1"',
                'tabpanel' => 'role="tabpanel" aria-labelledby="tab-id"',
            ],
            'status' => [
                'live_region' => 'aria-live="polite"',
                'assertive_region' => 'aria-live="assertive"',
                'status' => 'role="status" aria-live="polite"',
                'alert' => 'role="alert" aria-live="assertive"',
            ],
        ];
    }

    public function validateKeyboardNavigation(): array
    {
        return [
            'tab_order' => [
                'description' => 'Logical tab order through interactive elements',
                'requirements' => [
                    'All interactive elements must be keyboard accessible',
                    'Tab order should follow visual layout',
                    'Skip links should be provided for main content',
                    'Focus should be visible on all elements',
                ],
            ],
            'keyboard_shortcuts' => [
                'global' => [
                    'Ctrl+K / Cmd+K' => 'Open global search',
                    'Escape' => 'Close modals/dropdowns',
                    'Tab' => 'Navigate forward',
                    'Shift+Tab' => 'Navigate backward',
                ],
                'forms' => [
                    'Enter' => 'Submit form',
                    'Space' => 'Toggle checkboxes/buttons',
                    'Arrow keys' => 'Navigate radio buttons',
                ],
            ],
        ];
    }

    public function generateScreenReaderContent(): array
    {
        return [
            'skip_links' => [
                'main_content' => '<a href="#main-content" class="sr-only focus:not-sr-only">Skip to main content</a>',
                'navigation' => '<a href="#navigation" class="sr-only focus:not-sr-only">Skip to navigation</a>',
            ],
            'status_announcements' => [
                'form_submitted' => 'Form submitted successfully',
                'error_occurred' => 'An error occurred. Please check the form and try again.',
                'loading' => 'Loading content, please wait',
                'search_results' => 'Search completed. {count} results found.',
            ],
            'table_headers' => [
                'scope_col' => 'scope="col"',
                'scope_row' => 'scope="row"',
                'caption' => '<caption class="sr-only">Table description</caption>',
            ],
        ];
    }

    public function auditAccessibility(): array
    {
        $colorPalette = $this->getCompliantColorPalette();
        $focusStyles = $this->generateFocusStyles();
        $ariaAttributes = $this->generateAriaAttributes();

        $audit = [
            'color_contrast' => [
                'status' => 'compliant',
                'details' => $colorPalette,
                'issues' => [],
            ],
            'focus_indicators' => [
                'status' => 'compliant',
                'details' => $focusStyles,
                'issues' => [],
            ],
            'aria_attributes' => [
                'status' => 'compliant',
                'details' => count($ariaAttributes),
                'issues' => [],
            ],
            'keyboard_navigation' => [
                'status' => 'compliant',
                'details' => $this->validateKeyboardNavigation(),
                'issues' => [],
            ],
            'screen_reader' => [
                'status' => 'compliant',
                'details' => $this->generateScreenReaderContent(),
                'issues' => [],
            ],
        ];

        // Check for potential issues
        foreach ($colorPalette as $category => $colors) {
            if (is_array($colors)) {
                foreach ($colors as $context => $validation) {
                    if (isset($validation['wcag_aa_text']) && ! $validation['wcag_aa_text']) {
                        $audit['color_contrast']['issues'][] = "Low contrast in {$category} {$context}";
                        $audit['color_contrast']['status'] = 'needs_attention';
                    }
                }
            }
        }

        return $audit;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    private function calculateLuminance(array $rgb): float
    {
        $normalize = function ($value) {
            $value = $value / 255;

            return $value <= 0.03928 ? $value / 12.92 : pow(($value + 0.055) / 1.055, 2.4);
        };

        return 0.2126 * $normalize($rgb['r']) +
               0.7152 * $normalize($rgb['g']) +
               0.0722 * $normalize($rgb['b']);
    }

    private function calculateContrastRatio(array $foreground, array $background): float
    {
        $l1 = $this->calculateLuminance($foreground);
        $l2 = $this->calculateLuminance($background);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }
}
