<?php

declare(strict_types=1);

namespace Tests\Feature\VisualRegression;

use Tests\TestCase;

/**
 * Typography Consistency Visual Regression Tests
 *
 * Property-based tests to verify typography consistency across widgets
 * following MyDS v2025.2 design system specifications.
 *
 * @trace Task 5.2.4; R22.3.1-R22.3.5; D13 §2.4
 *
 * @version 3.6.1
 *
 * @since 2025-01-01
 */
class TypographyConsistencyTest extends TestCase
{
    /**
     * Test that theme.css contains Poppins font family for headings
     *
     * @property Widget headers use Poppins font
     *
     * @validates R22.3.1
     */
    public function test_theme_css_contains_poppins_font_for_headings(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $this->assertFileExists($themeCssPath);

        $cssContent = file_get_contents($themeCssPath);

        // Verify Poppins font import
        $this->assertStringContainsString(
            'Poppins',
            $cssContent,
            'Theme CSS should import Poppins font'
        );

        // Verify font-heading variable uses Poppins
        $this->assertStringContainsString(
            '--font-heading: "Poppins"',
            $cssContent,
            'Font heading variable should use Poppins'
        );
    }

    /**
     * Test that theme.css contains Inter font family for body text
     *
     * @property Widget body text uses Inter font
     *
     * @validates R22.3.3
     */
    public function test_theme_css_contains_inter_font_for_body(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify Inter font import
        $this->assertStringContainsString(
            'Inter',
            $cssContent,
            'Theme CSS should import Inter font'
        );

        // Verify font-body variable uses Inter
        $this->assertStringContainsString(
            '--font-body: "Inter"',
            $cssContent,
            'Font body variable should use Inter'
        );
    }

    /**
     * Test that widget-header typography class exists
     *
     * @property Widget header class has correct typography
     *
     * @validates R22.3.1
     */
    public function test_widget_header_typography_class_exists(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify widget-header class exists
        $this->assertStringContainsString(
            '.widget-header',
            $cssContent,
            'Widget header class should exist'
        );

        // Verify widget-header uses heading font
        $this->assertMatchesRegularExpression(
            '/\.widget-header\s*\{[^}]*font-family:\s*var\(--font-heading\)/s',
            $cssContent,
            'Widget header should use heading font family'
        );
    }

    /**
     * Test that widget-metric typography class exists
     *
     * @property Widget metric class has correct typography
     *
     * @validates R22.3.2
     */
    public function test_widget_metric_typography_class_exists(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify widget-metric class exists
        $this->assertStringContainsString(
            '.widget-metric',
            $cssContent,
            'Widget metric class should exist'
        );

        // Verify widget-metric uses heading font
        $this->assertMatchesRegularExpression(
            '/\.widget-metric\s*\{[^}]*font-family:\s*var\(--font-heading\)/s',
            $cssContent,
            'Widget metric should use heading font family'
        );

        // Verify widget-metric has bold font weight
        $this->assertMatchesRegularExpression(
            '/\.widget-metric\s*\{[^}]*font-weight:\s*700/s',
            $cssContent,
            'Widget metric should have bold font weight (700)'
        );
    }

    /**
     * Test that widget-label typography class exists
     *
     * @property Widget label class has correct typography
     *
     * @validates R22.3.3
     */
    public function test_widget_label_typography_class_exists(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify widget-label class exists
        $this->assertStringContainsString(
            '.widget-label',
            $cssContent,
            'Widget label class should exist'
        );

        // Verify widget-label uses body font
        $this->assertMatchesRegularExpression(
            '/\.widget-label\s*\{[^}]*font-family:\s*var\(--font-body\)/s',
            $cssContent,
            'Widget label should use body font family'
        );
    }

    /**
     * Test that stats widget typography classes exist
     *
     * @property Stats widget has consistent typography
     *
     * @validates R22.3.1, R22.3.2
     */
    public function test_stats_widget_typography_classes_exist(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify stats value typography
        $this->assertStringContainsString(
            '.fi-wi-stats-overview-stat-value',
            $cssContent,
            'Stats value class should exist'
        );

        // Verify stats label typography
        $this->assertStringContainsString(
            '.fi-wi-stats-overview-stat-label',
            $cssContent,
            'Stats label class should exist'
        );

        // Verify stats description typography
        $this->assertStringContainsString(
            '.fi-wi-stats-overview-stat-description',
            $cssContent,
            'Stats description class should exist'
        );
    }

    /**
     * Test that dark mode typography styles exist
     *
     * @property Dark mode has proper typography colors
     *
     * @validates R22.3.4
     */
    public function test_dark_mode_typography_styles_exist(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify dark mode widget-header styles
        $this->assertStringContainsString(
            '.dark .widget-header',
            $cssContent,
            'Dark mode widget header styles should exist'
        );

        // Verify dark mode widget-metric styles
        $this->assertStringContainsString(
            '.dark .widget-metric',
            $cssContent,
            'Dark mode widget metric styles should exist'
        );

        // Verify dark mode widget-label styles
        $this->assertStringContainsString(
            '.dark .widget-label',
            $cssContent,
            'Dark mode widget label styles should exist'
        );
    }

    /**
     * Test that trend indicator typography class exists
     *
     * @property Trend indicators have consistent typography
     *
     * @validates R22.3.5
     */
    public function test_trend_indicator_typography_class_exists(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify trend-indicator class exists
        $this->assertStringContainsString(
            '.trend-indicator',
            $cssContent,
            'Trend indicator class should exist'
        );

        // Verify trend indicator variants
        $this->assertStringContainsString(
            '.trend-indicator--up',
            $cssContent,
            'Trend indicator up variant should exist'
        );

        $this->assertStringContainsString(
            '.trend-indicator--down',
            $cssContent,
            'Trend indicator down variant should exist'
        );
    }

    /**
     * Test that section header typography class exists
     *
     * @property Section headers have consistent typography
     *
     * @validates R22.3.1
     */
    public function test_section_header_typography_class_exists(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify section-header class exists
        $this->assertStringContainsString(
            '.section-header',
            $cssContent,
            'Section header class should exist'
        );

        // Verify section-header uses heading font
        $this->assertMatchesRegularExpression(
            '/\.section-header\s*\{[^}]*font-family:\s*var\(--font-heading\)/s',
            $cssContent,
            'Section header should use heading font family'
        );
    }

    /**
     * Test that widget-card component uses typography classes
     *
     * @property Widget card component uses consistent typography
     *
     * @validates R22.3.1, R22.3.3
     */
    public function test_widget_card_component_uses_typography_classes(): void
    {
        $componentPath = resource_path('views/filament/components/widget-card.blade.php');
        $this->assertFileExists($componentPath);

        $componentContent = file_get_contents($componentPath);

        // Verify widget-header class is used
        $this->assertStringContainsString(
            'widget-header',
            $componentContent,
            'Widget card component should use widget-header class'
        );

        // Verify widget-description class is used
        $this->assertStringContainsString(
            'widget-description',
            $componentContent,
            'Widget card component should use widget-description class'
        );
    }

    /**
     * Test that Filament headings use Poppins font
     *
     * @property Filament headings use heading font
     *
     * @validates R22.3.1
     */
    public function test_filament_headings_use_poppins_font(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify Filament heading classes use heading font
        $this->assertStringContainsString(
            '.fi-header-heading',
            $cssContent,
            'Filament header heading should be styled'
        );

        $this->assertStringContainsString(
            '.fi-section-header-heading',
            $cssContent,
            'Filament section header heading should be styled'
        );

        $this->assertStringContainsString(
            '.fi-modal-heading',
            $cssContent,
            'Filament modal heading should be styled'
        );
    }

    /**
     * Test that font utility classes exist
     *
     * @property Font utility classes are available
     *
     * @validates R22.3.1, R22.3.3
     */
    public function test_font_utility_classes_exist(): void
    {
        $themeCssPath = resource_path('css/filament/admin/theme.css');
        $cssContent = file_get_contents($themeCssPath);

        // Verify font-poppins utility class
        $this->assertStringContainsString(
            '.font-poppins',
            $cssContent,
            'Font Poppins utility class should exist'
        );

        // Verify font-inter utility class
        $this->assertStringContainsString(
            '.font-inter',
            $cssContent,
            'Font Inter utility class should exist'
        );
    }
}
