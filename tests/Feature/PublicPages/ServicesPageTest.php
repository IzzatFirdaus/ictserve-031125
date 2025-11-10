<?php

declare(strict_types=1);

namespace Tests\Feature\PublicPages;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Services Page Test Suite
 *
 * Tests for the services overview page covering:
 * - Page rendering and content
 * - Service cards display
 * - Bilingual support
 * - WCAG 2.2 Level AA compliance
 * - Navigation links
 *
 * @trace D03-FR-004 (Public Information Pages)
 * @trace Requirements 4.1, 4.2, 4.3, 4.4, 4.5, 6.1, 8.2
 */
class ServicesPageTest extends TestCase
{
    /**
     * Test services page renders successfully
     */
    #[Test]
    public function services_page_renders_successfully(): void
    {
        $response = $this->get(route('services'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.services');
    }

    /**
     * Test services page contains required sections
     */
    #[Test]
    public function services_page_contains_required_sections(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.title'));
        $response->assertSee(__('pages.services.subtitle'));
    }

    /**
     * Test services page displays helpdesk service card
     */
    #[Test]
    public function services_page_displays_helpdesk_service_card(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.helpdesk_title'));
        $response->assertSee(__('pages.services.helpdesk_description'));
        $response->assertSee(__('pages.services.helpdesk_feature_1'));
        $response->assertSee(__('pages.services.helpdesk_feature_2'));
        $response->assertSee(__('pages.services.helpdesk_feature_3'));
        $response->assertSee(__('pages.services.helpdesk_feature_4'));
        $response->assertSee(__('pages.services.helpdesk_cta'));
    }

    /**
     * Test services page displays asset loan service card
     */
    #[Test]
    public function services_page_displays_asset_loan_service_card(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.loan_title'));
        $response->assertSee(__('pages.services.loan_description'));
        $response->assertSee(__('pages.services.loan_feature_1'));
        $response->assertSee(__('pages.services.loan_feature_2'));
        $response->assertSee(__('pages.services.loan_feature_3'));
        $response->assertSee(__('pages.services.loan_feature_4'));
        $response->assertSee(__('pages.services.loan_cta'));
    }

    /**
     * Test services page displays service request card
     */
    #[Test]
    public function services_page_displays_service_request_card(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.service_request_title'));
        $response->assertSee(__('pages.services.service_request_description'));
        $response->assertSee(__('pages.services.service_request_feature_1'));
        $response->assertSee(__('pages.services.service_request_feature_2'));
        $response->assertSee(__('pages.services.service_request_feature_3'));
        $response->assertSee(__('pages.services.service_request_feature_4'));
        $response->assertSee(__('pages.services.service_request_cta'));
    }

    /**
     * Test services page displays issue reporting card
     */
    #[Test]
    public function services_page_displays_issue_reporting_card(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.issue_reporting_title'));
        $response->assertSee(__('pages.services.issue_reporting_description'));
        $response->assertSee(__('pages.services.issue_reporting_feature_1'));
        $response->assertSee(__('pages.services.issue_reporting_feature_2'));
        $response->assertSee(__('pages.services.issue_reporting_feature_3'));
        $response->assertSee(__('pages.services.issue_reporting_feature_4'));
        $response->assertSee(__('pages.services.issue_reporting_cta'));
    }

    /**
     * Test services page displays general support card
     */
    #[Test]
    public function services_page_displays_general_support_card(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.support_title'));
        $response->assertSee(__('pages.services.support_description'));
        $response->assertSee(__('pages.services.support_feature_1'));
        $response->assertSee(__('pages.services.support_feature_2'));
        $response->assertSee(__('pages.services.support_feature_3'));
        $response->assertSee(__('pages.services.support_feature_4'));
        $response->assertSee(__('pages.services.support_cta'));
    }

    /**
     * Test services page displays CTA section
     */
    #[Test]
    public function services_page_displays_cta_section(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.cta_title'));
        $response->assertSee(__('pages.services.cta_description'));
        $response->assertSee(__('pages.services.cta_helpdesk'));
        $response->assertSee(__('pages.services.cta_loan'));
    }

    /**
     * Test services page displays footer note
     */
    #[Test]
    public function services_page_displays_footer_note(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('pages.services.footer_note'));
    }

    /**
     * Test services page has proper breadcrumbs
     */
    #[Test]
    public function services_page_has_proper_breadcrumbs(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(__('common.home'));
        $response->assertSee(__('pages.services.breadcrumb'));
    }

    /**
     * Test services page home link works
     */
    #[Test]
    public function services_page_home_link_works(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee(route('welcome'), false);
    }

    /**
     * Test services page in Bahasa Melayu
     */
    #[Test]
    public function services_page_displays_in_bahasa_melayu(): void
    {
        app()->setLocale('ms');

        $response = $this->get(route('services'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.services.title'));
    }

    /**
     * Test services page in English
     */
    #[Test]
    public function services_page_displays_in_english(): void
    {
        app()->setLocale('en');

        $response = $this->get(route('services'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.services.title'));
    }

    /**
     * Test services page has proper semantic HTML structure
     */
    #[Test]
    public function services_page_has_proper_semantic_structure(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('<section', false);
        $response->assertSee('<nav', false);
        $response->assertSee('<article', false);
        $response->assertSee('role="banner"', false);
    }

    /**
     * Test services page has proper ARIA attributes
     */
    #[Test]
    public function services_page_has_proper_aria_attributes(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('aria-label', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-hidden="true"', false);
    }

    /**
     * Test services page uses compliant color palette
     */
    #[Test]
    public function services_page_uses_compliant_color_palette(): void
    {
        $response = $this->get(route('services'));

        // Check for MOTAC blue
        $response->assertSee('motac-blue', false);

        // Check for success color (emerald)
        $response->assertSee('emerald', false);

        // Check for warning color
        $response->assertSee('text-warning', false);
    }

    /**
     * Test services page has responsive design classes
     */
    #[Test]
    public function services_page_has_responsive_design_classes(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('md:', false);
        $response->assertSee('lg:', false);
        $response->assertSee('sm:', false);
    }

    /**
     * Test services page has proper grid layout
     */
    #[Test]
    public function services_page_has_proper_grid_layout(): void
    {
        $response = $this->get(route('services'));

        // Check for grid layout classes
        $response->assertSee('grid', false);
        $response->assertSee('grid-cols-1', false);
        $response->assertSee('md:grid-cols-2', false);
        $response->assertSee('lg:grid-cols-3', false);
    }

    /**
     * Test service cards have proper styling
     */
    #[Test]
    public function service_cards_have_proper_styling(): void
    {
        $response = $this->get(route('services'));

        // Check for card styling classes
        $response->assertSee('rounded-2xl', false);
        $response->assertSee('shadow-lg', false);
        $response->assertSee('border', false);
        $response->assertSee('overflow-hidden', false);
    }

    /**
     * Test service cards have hover effects
     */
    #[Test]
    public function service_cards_have_hover_effects(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('hover:-translate-y-1', false);
        $response->assertSee('transition-transform', false);
    }

    /**
     * Test service cards have proper icons
     */
    #[Test]
    public function service_cards_have_proper_icons(): void
    {
        $response = $this->get(route('services'));

        // Check for SVG icons
        $response->assertSee('<svg', false);
        $response->assertSee('viewBox="0 0 24 24"', false);
        $response->assertSee('stroke="currentColor"', false);
    }

    /**
     * Test service cards have checkmark icons for features
     */
    #[Test]
    public function service_cards_have_checkmark_icons_for_features(): void
    {
        $response = $this->get(route('services'));

        // Check for checkmark SVG path
        $response->assertSee('M16.707 5.293a1 1 0 00-1.414 0L8 12.586', false);
    }

    /**
     * Test CTA section has gradient background
     */
    #[Test]
    public function cta_section_has_gradient_background(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('bg-gradient-to-r', false);
        $response->assertSee('from-motac-blue', false);
        $response->assertSee('to-motac-blue-dark', false);
    }

    /**
        * Test service buttons have proper link elements
     */
    #[Test]
        public function service_buttons_have_proper_link_elements(): void
    {
        $response = $this->get(route('services'));

           // Check for proper anchor tag structure with accessibility attributes
           $response->assertSee('<a href=', false);
           $response->assertSee('class="inline-flex', false);
           $response->assertSee('focus:ring-2', false);
           $response->assertSee('focus:outline-none', false);
    }

    /**
     * Test service buttons have minimum touch target sizes
     */
    #[Test]
    public function service_buttons_have_minimum_touch_target_sizes(): void
    {
        $response = $this->get(route('services'));

        // Check for full width and proper sizing
        $response->assertSee('w-full', false);
        $response->assertSee('justify-center', false);
    }

    /**
     * Test services page has proper focus indicators
     */
    #[Test]
    public function services_page_has_proper_focus_indicators(): void
    {
        $response = $this->get(route('services'));

        $response->assertSee('focus:ring', false);
        $response->assertSee('focus:outline-none', false);
    }

    /**
     * Test service cards have proper color coding
     */
    #[Test]
    public function service_cards_have_proper_color_coding(): void
    {
        $response = $this->get(route('services'));

        // Check for different color gradients for each service
        $response->assertSee('from-blue-500', false);
        $response->assertSee('from-emerald-500', false);
        $response->assertSee('from-purple-500', false);
        $response->assertSee('from-orange-500', false);
        $response->assertSee('from-indigo-500', false);
    }
}
