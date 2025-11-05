<?php

declare(strict_types=1);

namespace Tests\Feature\PublicPages;

use Tests\TestCase;

/**
 * Contact Page Test Suite
 *
 * Tests for the contact page covering:
 * - Page rendering and content
 * - Contact form functionality
 * - Bilingual support
 * - WCAG 2.2 Level AA compliance
 * - Contact information display
 *
 * @trace D03-FR-003 (Public Information Pages)
 * @trace Requirements 3.1, 3.2, 3.3, 3.4, 6.3, 6.5
 */
class ContactPageTest extends TestCase
{
    /**
     * Test contact page renders successfully
     *
     * @return void
     */
    public function test_contact_page_renders_successfully(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.contact');
    }

    /**
     * Test contact page contains required sections
     *
     * @return void
     */
    public function test_contact_page_contains_required_sections(): void
    {
        $response = $this->get(route('contact'));

        // Check for main sections
        $response->assertSee(__('pages.contact.title'));
        $response->assertSee(__('pages.contact.info_title'));
        $response->assertSee(__('pages.contact.form_title'));
    }

    /**
     * Test contact page displays phone information
     *
     * @return void
     */
    public function test_contact_page_displays_phone_information(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.phone_title'));
        $response->assertSee(__('pages.contact.phone_number'));
        $response->assertSee(__('pages.contact.phone_hours'));
        $response->assertSee('href="tel:+60312345678"', false);
    }

    /**
     * Test contact page displays email information
     *
     * @return void
     */
    public function test_contact_page_displays_email_information(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.email_title'));
        $response->assertSee(__('pages.contact.email_address'));
        $response->assertSee(__('pages.contact.email_response'));
        $response->assertSee('href="mailto:ictserve@motac.gov.my"', false);
    }

    /**
     * Test contact page displays address information
     *
     * @return void
     */
    public function test_contact_page_displays_address_information(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.address_title'));
        $response->assertSee(__('pages.contact.address_line1'));
        $response->assertSee(__('pages.contact.address_line2'));
        $response->assertSee(__('pages.contact.address_line3'));
        $response->assertSee(__('pages.contact.address_line4'));
    }

    /**
     * Test contact page displays office hours
     *
     * @return void
     */
    public function test_contact_page_displays_office_hours(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.hours_title'));
        $response->assertSee(__('pages.contact.hours_weekday'));
        $response->assertSee(__('pages.contact.hours_friday'));
        $response->assertSee(__('pages.contact.hours_weekend'));
    }

    /**
     * Test contact page displays emergency support information
     *
     * @return void
     */
    public function test_contact_page_displays_emergency_support(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.emergency_title'));
        $response->assertSee(__('pages.contact.emergency_text'));
        $response->assertSee(__('pages.contact.emergency_phone'));
        $response->assertSee(__('pages.contact.emergency_available'));
        $response->assertSee('href="tel:+60312349999"', false);
    }

    /**
     * Test contact form has all required fields
     *
     * @return void
     */
    public function test_contact_form_has_all_required_fields(): void
    {
        $response = $this->get(route('contact'));

        // Check for form fields
        $response->assertSee('id="contact-name"', false);
        $response->assertSee('id="contact-email"', false);
        $response->assertSee('id="contact-subject"', false);
        $response->assertSee('id="contact-message"', false);
    }

    /**
     * Test contact form fields have proper labels
     *
     * @return void
     */
    public function test_contact_form_fields_have_proper_labels(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.form_name'));
        $response->assertSee(__('pages.contact.form_email'));
        $response->assertSee(__('pages.contact.form_subject'));
        $response->assertSee(__('pages.contact.form_message'));
    }

    /**
     * Test contact form fields have required attribute
     *
     * @return void
     */
    public function test_contact_form_fields_have_required_attribute(): void
    {
        $response = $this->get(route('contact'));

        // Check for required attributes
        $response->assertSee('required', false);
        $response->assertSee('aria-required="true"', false);
    }

    /**
     * Test contact form has submit button
     *
     * @return void
     */
    public function test_contact_form_has_submit_button(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.form_submit'));
        $response->assertSee('type="submit"', false);
    }

    /**
     * Test contact form has CSRF protection
     *
     * @return void
     */
    public function test_contact_form_has_csrf_protection(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('@csrf', false);
    }

    /**
     * Test contact page has proper breadcrumbs
     *
     * @return void
     */
    public function test_contact_page_has_proper_breadcrumbs(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('common.home'));
        $response->assertSee(__('pages.contact.breadcrumb'));
    }

    /**
     * Test contact page home link works
     *
     * @return void
     */
    public function test_contact_page_home_link_works(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(route('welcome'), false);
    }

    /**
     * Test contact page in Bahasa Melayu
     *
     * @return void
     */
    public function test_contact_page_displays_in_bahasa_melayu(): void
    {
        app()->setLocale('ms');

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.contact.title'));
    }

    /**
     * Test contact page in English
     *
     * @return void
     */
    public function test_contact_page_displays_in_english(): void
    {
        app()->setLocale('en');

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee(__('pages.contact.title'));
    }

    /**
     * Test contact page has proper semantic HTML structure
     *
     * @return void
     */
    public function test_contact_page_has_proper_semantic_structure(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('<section', false);
        $response->assertSee('<nav', false);
        $response->assertSee('<form', false);
        $response->assertSee('role="banner"', false);
    }

    /**
     * Test contact page has proper ARIA attributes
     *
     * @return void
     */
    public function test_contact_page_has_proper_aria_attributes(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('aria-label', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-hidden="true"', false);
        $response->assertSee('aria-required="true"', false);
    }

    /**
     * Test contact page uses compliant color palette
     *
     * @return void
     */
    public function test_contact_page_uses_compliant_color_palette(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('motac-blue', false);
        $response->assertSee('text-danger', false);
    }

    /**
     * Test contact page has responsive design classes
     *
     * @return void
     */
    public function test_contact_page_has_responsive_design_classes(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('md:', false);
        $response->assertSee('lg:', false);
        $response->assertSee('sm:', false);
    }

    /**
     * Test contact form fields have minimum touch target sizes
     *
     * @return void
     */
    public function test_contact_form_fields_have_minimum_touch_target_sizes(): void
    {
        $response = $this->get(route('contact'));

        // Check for minimum 44px height
        $response->assertSee('min-h-[44px]', false);
    }

    /**
     * Test contact form has proper focus indicators
     *
     * @return void
     */
    public function test_contact_form_has_proper_focus_indicators(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee('focus:ring', false);
        $response->assertSee('focus:border-motac-blue', false);
    }

    /**
     * Test contact page has proper grid layout
     *
     * @return void
     */
    public function test_contact_page_has_proper_grid_layout(): void
    {
        $response = $this->get(route('contact'));

        // Check for grid layout classes
        $response->assertSee('grid', false);
        $response->assertSee('lg:col-span-1', false);
        $response->assertSee('lg:col-span-2', false);
    }

    /**
     * Test contact form placeholders are accessible
     *
     * @return void
     */
    public function test_contact_form_placeholders_are_accessible(): void
    {
        $response = $this->get(route('contact'));

        $response->assertSee(__('pages.contact.form_name_placeholder'));
        $response->assertSee(__('pages.contact.form_email_placeholder'));
        $response->assertSee(__('pages.contact.form_subject_placeholder'));
        $response->assertSee(__('pages.contact.form_message_placeholder'));
    }

    /**
     * Test emergency support section has proper styling
     *
     * @return void
     */
    public function test_emergency_support_section_has_proper_styling(): void
    {
        $response = $this->get(route('contact'));

        // Check for danger/warning styling
        $response->assertSee('border-danger', false);
        $response->assertSee('bg-red-50', false);
        $response->assertSee('text-danger', false);
    }

    /**
     * Test contact information cards use proper components
     *
     * @return void
     */
    public function test_contact_information_cards_use_proper_components(): void
    {
        $response = $this->get(route('contact'));

        // Check for x-ui.card component usage
        $response->assertSee('x-ui.card', false);
    }
}
