<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
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
     * @trace Requirements 1.1, 2.1
     */
    #[Test]
    public function theme_toggle_component_renders_on_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('theme-icon-sun', false);
        $response->assertSee('theme-icon-moon', false);
    }

    /**
     * Test that theme toggle has required accessibility attributes
     *
     * @trace Requirements 3.1, 3.2, 3.3, 3.4
     */
    #[Test]
    public function theme_toggle_has_accessibility_attributes(): void
    {
        $response = $this->get('/');

        $response->assertSee('aria-label="Tukar tema"', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('min-h-11', false);
        $response->assertSee('min-w-11', false);
    }

    /**
     * Test that theme init script is included in head section
     *
     * @trace Requirements 1.3, 2.1
     */
    #[Test]
    public function theme_init_script_is_included_in_head(): void
    {
        $response = $this->get('/');

        $response->assertSee('localStorage.getItem(\'theme\')', false);
        $response->assertSee('document.documentElement', false);
    }

    /**
     * Test that theme toggle JavaScript includes error handling
     *
     * @trace Requirements 4.1, 4.2, 4.3
     */
    #[Test]
    public function theme_toggle_includes_error_handling(): void
    {
        $response = $this->get('/');

        $response->assertSee('catch (error)', false);
        $response->assertSee('console.warn', false);
        $response->assertSee('console.log', false);
    }

    /**
     * Test that theme toggle uses event delegation
     *
     * @trace Requirements 2.3, 2.4
     */
    #[Test]
    public function theme_toggle_uses_event_delegation(): void
    {
        $response = $this->get('/');

        $response->assertSee('document.addEventListener', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('preventDefault', false);
        $response->assertSee('stopPropagation', false);
    }

    /**
     * Test that theme toggle waits for DOM ready
     *
     * @trace Requirements 2.2, 4.1
     */
    #[Test]
    public function theme_toggle_waits_for_dom_ready(): void
    {
        $response = $this->get('/');

        $response->assertSee('document.readyState', false);
        $response->assertSee('DOMContentLoaded', false);
    }

    /**
     * Test that theme toggle dispatches custom event
     *
     * @trace Requirements 2.4
     */
    #[Test]
    public function theme_toggle_dispatches_custom_event(): void
    {
        $response = $this->get('/');

        $response->assertSee('window.dispatchEvent', false);
        $response->assertSee('CustomEvent', false);
        $response->assertSee('themeChanged', false);
    }

    /**
     * Test that theme toggle prevents duplicate initialization
     *
     * @trace Requirements 4.4
     */
    #[Test]
    public function theme_toggle_prevents_duplicate_initialization(): void
    {
        $response = $this->get('/');

        $response->assertSee('themeToggleInitialized', false);
    }

    /**
     * Test that theme toggle component renders in mobile menu
     *
     * @trace Requirements 1.4
     */
    #[Test]
    public function theme_toggle_renders_in_mobile_menu(): void
    {
        $response = $this->get('/');

        $response->assertSee('id="mobile-menu"', false);
        $response->assertSee('data-theme-toggle', false);
    }

    /**
     * Test that theme toggle uses capture phase for event handling
     *
     * @trace Requirements 2.3
     */
    #[Test]
    public function theme_toggle_uses_capture_phase(): void
    {
        $response = $this->get('/');

        $response->assertSee('true); // Use capture phase', false);
    }
}
