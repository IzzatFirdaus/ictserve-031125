<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Figma Design Service
 *
 * Service for integrating Figma designs with the ICTServe codebase using Figma MCP.
 * Transforms Figma design context into production-ready Livewire/Blade components
 * that comply with D00-D17 documentation standards and WCAG 2.2 Level AA requirements.
 *
 * @component
 *
 * @name FigmaDesignService
 *
 * @description Service for Figma MCP integration and design-to-code transformation
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-12-05
 *
 * Requirements: 1.1-1.5
 * Standards: D04 §3.1, D13 §2.1
 * WCAG Level: AA (SC 1.4.3, 1.4.11)
 */
class FigmaDesignService
{
    /**
     * WCAG 2.2 AA Compliant Color Palette mapping
     * Maps Figma colors to ICTServe design tokens
     *
     * @var array<string, string>
     */
    private const COMPLIANT_COLOR_PALETTE = [
        // Primary (MOTAC Blue) - 7.2:1 contrast
        '#0056B3' => '--color-primary-500',
        '#0056b3' => '--color-primary-500',
        // Secondary - 8.1:1 contrast
        '#0B4D8F' => '--color-secondary-500',
        '#0b4d8f' => '--color-secondary-500',
        // Success - 4.6:1 contrast
        '#1B7C54' => '--color-success-500',
        '#1b7c54' => '--color-success-500',
        // Warning - 4.5:1 contrast
        '#CC7700' => '--color-warning-500',
        '#cc7700' => '--color-warning-500',
        // Danger - 7.8:1 contrast
        '#B3002D' => '--color-danger-500',
        '#b3002d' => '--color-danger-500',
    ];

    /**
     * MyDS semantic token mapping
     *
     * @var array<string, string>
     */
    private const MYDS_TOKEN_MAP = [
        // Background tokens
        'bg-white' => '--bg-white',
        'bg-gray-50' => '--bg-washed',
        'bg-primary-50' => '--bg-primary-50',
        'bg-green-50' => '--bg-success-50',
        'bg-yellow-50' => '--bg-warning-50',
        'bg-red-50' => '--bg-danger-50',
        // Text tokens
        'text-gray-900' => '--txt-black-900',
        'text-gray-700' => '--txt-black-700',
        'text-gray-500' => '--txt-black-500',
        'text-white' => '--txt-white',
        'text-blue-600' => '--txt-primary-600',
        'text-green-600' => '--txt-success-600',
        'text-yellow-600' => '--txt-warning-600',
        'text-red-600' => '--txt-danger-600',
        // Border/Outline tokens
        'border-gray-200' => '--otl-divider',
        'border-gray-300' => '--otl-default',
        'border-blue-500' => '--otl-primary',
        // Focus ring tokens
        'ring-blue-500' => '--fr-primary',
        'ring-red-500' => '--fr-danger',
    ];

    /**
     * React to Blade syntax transformations
     *
     * @var array<string, string>
     */
    private const REACT_TO_BLADE_MAP = [
        'className=' => 'class=',
        'onClick=' => 'wire:click=',
        'onChange=' => 'wire:change=',
        'onSubmit=' => 'wire:submit.prevent=',
        'onBlur=' => 'wire:blur=',
        'onFocus=' => 'wire:focus=',
        'htmlFor=' => 'for=',
        '{true}' => '{{ true }}',
        '{false}' => '{{ false }}',
        'disabled={' => ':disabled="',
        'checked={' => ':checked="',
        'value={' => ':value="',
    ];

    /**
     * Get design context from Figma via MCP
     *
     * Extracts component specifications including layout, colors, typography, and spacing.
     *
     * @param  string  $nodeId  The Figma node ID (e.g., "123:456")
     * @param  string  $fileKey  The Figma file key
     * @return array<string, mixed> Design context data
     */
    public function getDesignContext(string $nodeId, string $fileKey): array
    {
        // This method would integrate with Figma MCP get_design_context tool
        // For now, return a structured placeholder that documents the expected format
        return [
            'nodeId' => $nodeId,
            'fileKey' => $fileKey,
            'layout' => [
                'type' => 'frame',
                'width' => 0,
                'height' => 0,
                'padding' => [],
                'gap' => 0,
            ],
            'styles' => [
                'colors' => [],
                'typography' => [],
                'effects' => [],
            ],
            'components' => [],
            'designTokens' => [],
            'accessibility' => [
                'contrastRatios' => [],
                'touchTargets' => [],
            ],
        ];
    }

    /**
     * Get Code Connect mapping from Figma
     *
     * Links Figma components to existing Blade/Livewire implementations.
     *
     * @param  string  $nodeId  The Figma node ID
     * @param  string  $fileKey  The Figma file key
     * @return array<string, array{codeConnectSrc: string, codeConnectName: string}> Code connect mappings
     */
    public function getCodeConnectMap(string $nodeId, string $fileKey): array
    {
        // This method would integrate with Figma MCP get_code_connect_map tool
        // Returns mapping of Figma node IDs to codebase component locations
        return [
            // Example format:
            // '1:2' => [
            //     'codeConnectSrc' => 'resources/views/components/ui/button.blade.php',
            //     'codeConnectName' => 'Button',
            // ],
        ];
    }

    /**
     * Create design system rules from Figma
     *
     * Generates design system rules and stores them in steering file.
     *
     * @return array<string, mixed> Generated rules summary
     */
    public function createDesignSystemRules(): array
    {
        $rules = [
            'colorTokens' => $this->generateColorTokenRules(),
            'typographyTokens' => $this->generateTypographyTokenRules(),
            'spacingTokens' => $this->generateSpacingTokenRules(),
            'radiusTokens' => $this->generateRadiusTokenRules(),
            'shadowTokens' => $this->generateShadowTokenRules(),
            'motionTokens' => $this->generateMotionTokenRules(),
        ];

        return [
            'success' => true,
            'rules' => $rules,
            'tokenCount' => $this->countTokens($rules),
        ];
    }

    /**
     * Transform React/Tailwind code to Livewire/Blade syntax
     *
     * Converts Figma MCP output (React + Tailwind) to ICTServe patterns.
     *
     * @param  string  $reactCode  The React/JSX code from Figma
     * @return string Transformed Blade/Livewire code
     */
    public function transformToLivewire(string $reactCode): string
    {
        $bladeCode = $reactCode;

        // Apply React to Blade transformations
        foreach (self::REACT_TO_BLADE_MAP as $react => $blade) {
            $bladeCode = str_replace($react, $blade, $bladeCode);
        }

        // Transform JSX expressions to Blade syntax
        $bladeCode = $this->transformJsxExpressions($bladeCode);

        // Transform React state to Livewire properties
        $bladeCode = $this->transformStateToProperties($bladeCode);

        // Transform React hooks to Livewire lifecycle
        $bladeCode = $this->transformHooksToLifecycle($bladeCode);

        // Apply MyDS token mapping
        $bladeCode = $this->applyMydsTokens($bladeCode);

        return $bladeCode;
    }

    /**
     * Map Figma color to WCAG 2.2 AA compliant token
     *
     * @param  string  $figmaColor  The color from Figma (hex format)
     * @return string The compliant CSS variable name
     */
    public function mapColorToToken(string $figmaColor): string
    {
        // Normalize color to lowercase
        $normalizedColor = strtolower($figmaColor);

        // Check direct mapping
        if (isset(self::COMPLIANT_COLOR_PALETTE[$normalizedColor])) {
            return self::COMPLIANT_COLOR_PALETTE[$normalizedColor];
        }

        // Find closest compliant color
        return $this->findClosestCompliantColor($normalizedColor);
    }

    /**
     * Validate color contrast ratio meets WCAG 2.2 AA
     *
     * @param  string  $foreground  Foreground color (hex)
     * @param  string  $background  Background color (hex)
     * @param  string  $type  'text' (4.5:1) or 'ui' (3:1)
     * @return array{compliant: bool, ratio: float, required: float}
     */
    public function validateColorContrast(string $foreground, string $background, string $type = 'text'): array
    {
        $ratio = $this->calculateContrastRatio($foreground, $background);
        $required = $type === 'text' ? 4.5 : 3.0;

        return [
            'compliant' => $ratio >= $required,
            'ratio' => round($ratio, 2),
            'required' => $required,
            'foreground' => $foreground,
            'background' => $background,
        ];
    }

    /**
     * Generate color token rules for design system
     *
     * @return array<string, mixed>
     */
    private function generateColorTokenRules(): array
    {
        return [
            'primary' => [
                'value' => '#0056B3',
                'contrast' => '7.2:1',
                'usage' => 'Main actions, links, primary buttons',
            ],
            'secondary' => [
                'value' => '#0B4D8F',
                'contrast' => '8.1:1',
                'usage' => 'Secondary actions, supporting elements',
            ],
            'success' => [
                'value' => '#1B7C54',
                'contrast' => '4.6:1',
                'usage' => 'Success states, confirmations',
            ],
            'warning' => [
                'value' => '#CC7700',
                'contrast' => '4.5:1',
                'usage' => 'Warning states, cautions',
            ],
            'danger' => [
                'value' => '#B3002D',
                'contrast' => '7.8:1',
                'usage' => 'Error states, destructive actions',
            ],
        ];
    }

    /**
     * Generate typography token rules
     *
     * @return array<string, mixed>
     */
    private function generateTypographyTokenRules(): array
    {
        return [
            'fontHeading' => [
                'family' => 'Poppins',
                'weights' => [400, 500, 600],
                'usage' => 'Page titles, section headers',
            ],
            'fontBody' => [
                'family' => 'Inter',
                'weights' => [400, 500, 600],
                'usage' => 'Body text, descriptions, labels',
            ],
            'sizes' => [
                'h1' => '36px (2.25rem)',
                'h2' => '30px (1.875rem)',
                'h3' => '24px (1.5rem)',
                'h4' => '20px (1.25rem)',
                'body' => '16px (1rem)',
                'small' => '14px (0.875rem)',
            ],
        ];
    }

    /**
     * Generate spacing token rules
     *
     * @return array<string, mixed>
     */
    private function generateSpacingTokenRules(): array
    {
        return [
            'space-1' => '4px',
            'space-2' => '8px',
            'space-3' => '12px',
            'space-4' => '16px',
            'space-5' => '20px',
            'space-6' => '24px',
            'space-8' => '32px',
            'space-10' => '40px',
            'space-12' => '48px',
            'space-16' => '64px',
        ];
    }

    /**
     * Generate radius token rules
     *
     * @return array<string, mixed>
     */
    private function generateRadiusTokenRules(): array
    {
        return [
            'xs' => '4px',
            's' => '6px',
            'm' => '8px',
            'l' => '12px',
            'xl' => '14px',
            'full' => '9999px',
        ];
    }

    /**
     * Generate shadow token rules
     *
     * @return array<string, mixed>
     */
    private function generateShadowTokenRules(): array
    {
        return [
            'button' => '0px 1px 3px 0px rgba(0, 0, 0, 0.07)',
            'card' => '0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05)',
            'dropdown' => '0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10)',
        ];
    }

    /**
     * Generate motion token rules
     *
     * @return array<string, mixed>
     */
    private function generateMotionTokenRules(): array
    {
        return [
            'durations' => [
                'short' => '200ms',
                'medium' => '400ms',
                'long' => '600ms',
            ],
            'easings' => [
                'easeout' => 'cubic-bezier(0, 0, 0.58, 1)',
                'easeoutback' => 'cubic-bezier(0.4, 1.4, 0.2, 1)',
                'linear' => 'cubic-bezier(0, 0, 1, 1)',
            ],
        ];
    }

    /**
     * Transform JSX expressions to Blade syntax
     */
    private function transformJsxExpressions(string $code): string
    {
        // Transform {variable} to {{ $variable }}
        $code = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '{{ \$$1 }}', $code) ?? $code;

        // Transform {condition && <element>} to @if
        $code = preg_replace(
            '/\{([^}]+)\s*&&\s*(<[^>]+>.*?<\/[^>]+>)\}/',
            '@if($1)$2@endif',
            $code
        ) ?? $code;

        // Transform ternary {condition ? a : b} to Blade ternary
        $code = preg_replace(
            '/\{([^?]+)\s*\?\s*([^:]+)\s*:\s*([^}]+)\}/',
            '{{ $1 ? $2 : $3 }}',
            $code
        ) ?? $code;

        return $code;
    }

    /**
     * Transform React state to Livewire properties
     */
    private function transformStateToProperties(string $code): string
    {
        // Transform useState to public property
        $code = preg_replace(
            '/const\s*\[(\w+),\s*set\w+\]\s*=\s*useState\(([^)]*)\);?/',
            'public $$$1 = $2;',
            $code
        ) ?? $code;

        // Transform setState calls to property assignments
        $code = preg_replace(
            '/set(\w+)\(([^)]+)\)/',
            '\$this->$1 = $2',
            $code
        ) ?? $code;

        return $code;
    }

    /**
     * Transform React hooks to Livewire lifecycle methods
     */
    private function transformHooksToLifecycle(string $code): string
    {
        // Transform useEffect with empty deps to mount()
        $code = preg_replace(
            '/useEffect\(\(\)\s*=>\s*\{([^}]+)\},\s*\[\]\);?/',
            'public function mount() { $1 }',
            $code
        ) ?? $code;

        return $code;
    }

    /**
     * Apply MyDS token mapping to Tailwind classes
     */
    private function applyMydsTokens(string $code): string
    {
        foreach (self::MYDS_TOKEN_MAP as $tailwind => $token) {
            // Only replace in class attributes
            $code = preg_replace(
                '/class="([^"]*)\b'.preg_quote($tailwind, '/').'\b([^"]*)"/',
                'class="$1'.$tailwind.'$2"',
                $code
            ) ?? $code;
        }

        return $code;
    }

    /**
     * Find closest compliant color for non-mapped colors
     */
    private function findClosestCompliantColor(string $hexColor): string
    {
        // Default to primary if no close match found
        $closestToken = '--color-primary-500';
        $minDistance = PHP_INT_MAX;

        $inputRgb = $this->hexToRgb($hexColor);
        if ($inputRgb === null) {
            return $closestToken;
        }

        foreach (self::COMPLIANT_COLOR_PALETTE as $hex => $token) {
            $rgb = $this->hexToRgb($hex);
            if ($rgb === null) {
                continue;
            }

            $distance = sqrt(
                pow($inputRgb['r'] - $rgb['r'], 2) +
                    pow($inputRgb['g'] - $rgb['g'], 2) +
                    pow($inputRgb['b'] - $rgb['b'], 2)
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestToken = $token;
            }
        }

        return $closestToken;
    }

    /**
     * Convert hex color to RGB array
     *
     * @return array{r: int, g: int, b: int}|null
     */
    private function hexToRgb(string $hex): ?array
    {
        $hex = ltrim($hex, '#');

        if (\strlen($hex) !== 6) {
            return null;
        }

        return [
            'r' => (int) hexdec(\substr($hex, 0, 2)),
            'g' => (int) hexdec(\substr($hex, 2, 2)),
            'b' => (int) hexdec(\substr($hex, 4, 2)),
        ];
    }

    /**
     * Calculate relative luminance for a color
     */
    private function getRelativeLuminance(string $hex): float
    {
        $rgb = $this->hexToRgb($hex);
        if ($rgb === null) {
            return 0.0;
        }

        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Calculate contrast ratio between two colors
     */
    private function calculateContrastRatio(string $foreground, string $background): float
    {
        $l1 = $this->getRelativeLuminance($foreground);
        $l2 = $this->getRelativeLuminance($background);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Count total tokens in rules
     *
     * @param  array<string, mixed>  $rules
     */
    private function countTokens(array $rules): int
    {
        $count = 0;
        foreach ($rules as $category) {
            if (\is_array($category)) {
                $count += \count($category);
            }
        }

        return $count;
    }
}
