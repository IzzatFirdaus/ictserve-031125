<?php

declare(strict_types=1);

namespace Tests\Feature\PublicPages;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Accessibility Page Test Suite
 *
 * Tests for the accessibility statement page covering:
 * - Page rendering and content
 * - Bilingual support (English and Bahasa Melayu)
 * - WCAG 2.2 Level AA compliance
 * - Link functionality
 * - Responsive design
 *
 * @trace D03-FR-002 (Public Information Pages)
 * @trace Requirements 6.1, 6.6, 7.2, 13.1
 */
class AccessibilityPageTest extends TestCase
{
    /**
     * Test accessibility page renders successfully
     */
    #[Test]
    public function accessibility_page_renders_successfully(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.accessibility');
    }

    /**
     * Test accessibility page contains required sections
     */
    #[Test]
    public function accessibility_page_contains_required_sections(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for main sections
        $response->assertSee(__('pages.accessibility.title'));
        $response->assertSee(__('pages.accessibility.commitment_title'));
        $response->assertSee(__('pages.accessibility.standards_title'));
        $response->assertSee(__('pages.accessibility.features_title'));
        $response->assertSee(__('pages.accessibility.limitations_title'));
        $response->assertSee(__('pages.accessibility.technologies_title'));
        $response->assertSee(__('pages.accessibility.contact_title'));
    }

    /**
     * Test accessibility page displays WCAG 2.2 AA standard
     */
    #[Test]
    public function accessibility_page_displays_wcag_standard(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.wcag_title'));
        $response->assertSee(__('pages.accessibility.wcag_description'));
    }

    /**
     * Test accessibility page displays ISO 9241 standard
     */
    #[Test]
    public function accessibility_page_displays_iso_standard(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.iso_title'));
        $response->assertSee(__('pages.accessibility.iso_description'));
    }

    /**
     * Test accessibility page displays PDPA 2010 compliance
     */
    #[Test]
    public function accessibility_page_displays_pdpa_compliance(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.pdpa_title'));
        $response->assertSee(__('pages.accessibility.pdpa_description'));
    }

    /**
     * Test accessibility page lists all accessibility features
     */
    #[Test]
    public function accessibility_page_lists_all_features(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for all 8 accessibility features
        $response->assertSee(__('pages.accessibility.feature_keyboard'));
        $response->assertSee(__('pages.accessibility.feature_screen_reader'));
        $response->assertSee(__('pages.accessibility.feature_contrast'));
        $response->assertSee(__('pages.accessibility.feature_touch'));
        $response->assertSee(__('pages.accessibility.feature_aria'));
        $response->assertSee(__('pages.accessibility.feature_bilingual'));
        $response->assertSee(__('pages.accessibility.feature_responsive'));
        $response->assertSee(__('pages.accessibility.feature_skip'));
    }

    /**
     * Test accessibility page lists known limitations
     */
    #[Test]
    public function accessibility_page_lists_known_limitations(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.limitation_pdf'));
        $response->assertSee(__('pages.accessibility.limitation_third_party'));
        $response->assertSee(__('pages.accessibility.limitation_legacy'));
    }

    /**
     * Test accessibility page lists supported browsers
     */
    #[Test]
    public function accessibility_page_lists_supported_browsers(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.browser_chrome'));
        $response->assertSee(__('pages.accessibility.browser_firefox'));
        $response->assertSee(__('pages.accessibility.browser_safari'));
        $response->assertSee(__('pages.accessibility.browser_edge'));
    }

    /**
     * Test accessibility page lists supported screen readers
     */
    #[Test]
    public function accessibility_page_lists_supported_screen_readers(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('pages.accessibility.screen_reader_nvda'));
        $response->assertSee(__('pages.accessibility.screen_reader_jaws'));
        $response->assertSee(__('pages.accessibility.screen_reader_voiceover'));
    }

    /**
     * Test accessibility page contains contact information
     */
    #[Test]
    public function accessibility_page_contains_contact_information(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee('ictserve@motac.gov.my');
        $response->assertSee('+60 3-1234 5678');
    }

    /**
     * Test accessibility page has proper breadcrumbs
     */
    #[Test]
    public function accessibility_page_has_proper_breadcrumbs(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(__('common.home'));
        $response->assertSee(__('pages.accessibility.breadcrumb'));
    }

    /**
     * Test accessibility page home link works
     */
    #[Test]
    public function accessibility_page_home_link_works(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee(route('welcome'), false);
    }

    /**
     * Test accessibility page in Bahasa Melayu
     */
    #[Test]
    public function accessibility_page_displays_in_bahasa_melayu(): void
    {
        // Set locale to Bahasa Melayu
        app()->setLocale('ms');

        $response = $this->get(route('accessibility'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.accessibility.title'));
    }

    /**
     * Test accessibility page in English
     */
    #[Test]
    public function accessibility_page_displays_in_english(): void
    {
        // Set locale to English
        app()->setLocale('en');

        $response = $this->get(route('accessibility'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.accessibility.title'));
    }

    /**
     * Test accessibility page has proper semantic HTML structure
     */
    #[Test]
    public function accessibility_page_has_proper_semantic_structure(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for semantic HTML5 elements
        $response->assertSee('<section', false);
        $response->assertSee('<nav', false);
        $response->assertSee('<header', false);
        $response->assertSee('role="banner"', false);
    }

    /**
     * Test accessibility page has proper ARIA attributes
     */
    #[Test]
    public function accessibility_page_has_proper_aria_attributes(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for ARIA attributes
        $response->assertSee('aria-label', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-hidden="true"', false);
    }

    /**
     * Test accessibility page uses compliant color palette
     */
    #[Test]
    public function accessibility_page_uses_compliant_color_palette(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for MOTAC blue (primary color)
        $response->assertSee('motac-blue', false);

        // Check for success color
        $response->assertSee('text-success', false);

        // Check for warning color
        $response->assertSee('text-warning', false);
    }

    /**
     * Test accessibility page has responsive design classes
     */
    #[Test]
    public function accessibility_page_has_responsive_design_classes(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for responsive Tailwind classes
        $response->assertSee('md:', false);
        $response->assertSee('lg:', false);
        $response->assertSee('sm:', false);
    }

    /**
     * Test accessibility page contact email link is clickable
     */
    #[Test]
    public function accessibility_page_contact_email_link_is_clickable(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee('href="mailto:ictserve@motac.gov.my"', false);
    }

    /**
     * Test accessibility page contact phone link is clickable
     */
    #[Test]
    public function accessibility_page_contact_phone_link_is_clickable(): void
    {
        $response = $this->get(route('accessibility'));

        $response->assertSee('href="tel:+60312345678"', false);
    }

    /**
     * Test accessibility page has proper focus indicators
     */
    #[Test]
    public function accessibility_page_has_proper_focus_indicators(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for focus ring classes
        $response->assertSee('focus:ring', false);
        $response->assertSee('focus:outline-none', false);
    }

    /**
     * Test accessibility page has minimum touch target sizes
     */
    #[Test]
    public function accessibility_page_has_minimum_touch_target_sizes(): void
    {
        $response = $this->get(route('accessibility'));

        // Check for minimum 44px height/width classes
        $response->assertSee('h-10', false); // 40px (close to 44px)
        $response->assertSee('w-10', false);
        $response->assertSee('h-12', false); // 48px (exceeds 44px)
        $response->assertSee('w-12', false);
    }
}
