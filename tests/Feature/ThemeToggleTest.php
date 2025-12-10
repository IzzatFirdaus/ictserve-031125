<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Theme Toggle Component Tests
 *
 * @trace D14 §6.1.2, D14 §8.1
 *
 * @wcag SC 1.4.3, SC 2.1.1, SC 2.4.7
 */
class ThemeToggleTest extends TestCase
{
    /**
     * Test that theme toggle component renders correctly on landing page
     *
     * @test
     *
     * @trace Requirements 1.1, 2.1
     */
    public function theme_toggle_component_renders_on_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('theme-toggle-btn', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('theme-icon-sun', false);
        $response->assertSee('theme-icon-moon', false);
    }

    /**
     * Test that theme toggle has required accessibility attributes
     *
     * @test
     *
     * @trace Requirements 3.1, 3.2, 3.3, 3.4
     */
    public function theme_toggle_has_accessibility_attributes(): void
    {
        $response = $this->get('/');

        $response->assertSee('aria-label="Tukar tema"', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('min-h-11', false); // Minimum touch target size
        $response->assertSee('min-w-11', false);
    }

    /**
     * Test that theme init script is included in head section
     *
     * @test
     *
     * @trace Requirements 1.3, 2.1
     */
    public function theme_init_script_is_included_in_head(): void
    {
        $response = $this->get('/');

        // Verify theme init script exists
        $response->assertSeeInOrder([
            '<head>',
            'localStorage.getItem(\'theme\')',
            'document.documentElement',
            '</head>',
        ], false);
    }

    /**
     * Test that theme toggle JavaScript includes error handling
     *
     * @test
     *
     * @trace Requirements 4.1, 4.2, 4.3
     */
    public function theme_toggle_includes_error_handling(): void
    {
        $response = $this->get('/');

        $response->assertSee('try {', false);
        $response->assertSee('catch (error)', false);
        $response->assertSee('console.warn', false);
        $response->assertSee('console.log', false);
    }

    /**
     * Test that theme toggle uses event delegation
     *
     * @test
     *
     * @trace Requirements 2.3, 2.4
     */
    public function theme_toggle_uses_event_delegation(): void
    {
        $response = $this->get('/');

        $response->assertSee('document.addEventListener(\'click\'', false);
        $response->assertSee('e.target.closest(\'[data-theme-toggle]\')', false);
        $response->assertSee('e.preventDefault()', false);
        $response->assertSee('e.stopPropagation()', false);
    }

    /**
     * Test that theme toggle waits for DOM ready
     *
     * @test
     *
     * @trace Requirements 2.2, 4.1
     */
    public function theme_toggle_waits_for_dom_ready(): void
    {
        $response = $this->get('/');

        $response->assertSee('document.readyState', false);
        $response->assertSee('DOMContentLoaded', false);
        $response->assertSee('initThemeToggle', false);
    }

    /**
     * Test that theme toggle dispatches custom event
     *
     * @test
     *
     * @trace Requirements 2.4
     */
    public function theme_toggle_dispatches_custom_event(): void
    {
        $response = $this->get('/');

        $response->assertSee('window.dispatchEvent', false);
        $response->assertSee('new CustomEvent(\'themeChanged\'', false);
        $response->assertSee('detail:', false);
        $response->assertSee('theme', false);
    }

    /**
     * Test that theme toggle prevents duplicate initialization
     *
     * @test
     *
     * @trace Requirements 4.4
     */
    public function theme_toggle_prevents_duplicate_initialization(): void
    {
        $response = $this->get('/');

        $response->assertSee('window.themeToggleInitialized', false);
        $response->assertSee('if (window.themeToggleInitialized)', false);
    }

    /**
     * Test that theme toggle component renders in mobile menu
     *
     * @test
     *
     * @trace Requirements 1.4
     */
    public function theme_toggle_renders_in_mobile_menu(): void
    {
        $response = $this->get('/');

        // Check for mobile menu section
        $response->assertSee('id="mobile-menu"', false);

        // Verify theme toggle is present in mobile menu context
        $content = $response->getContent();
        $this->assertStringContainsString('mobile-menu', $content);
        $this->assertStringContainsString('theme-toggle', $content);
    }

    /**
     * Test that theme toggle uses capture phase for event handling
     *
     * @test
     *
     * @trace Requirements 2.3
     */
    public function theme_toggle_uses_capture_phase(): void
    {
        $response = $this->get('/');

        // Verify event listener uses capture phase (third parameter = true)
        $response->assertSee('}, true); // Use capture phase', false);
    }
}
