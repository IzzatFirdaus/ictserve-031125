<?php

declare(strict_types=1);

namespace Tests\Feature\PublicPages;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Contact Page Test Suite
 *
 * @trace D03-FR-003 (Public Information Pages)
 */
class ContactPageTest extends TestCase
{
    #[Test]
    public function contact_page_renders_successfully(): void
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.contact');
    }

    #[Test]
    public function contact_page_contains_required_sections(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.title'));
        $response->assertSee(__('pages.contact.info_title'));
        $response->assertSee(__('pages.contact.form_title'));
    }

    #[Test]
    public function contact_page_displays_phone_information(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.phone_title'));
        $response->assertSee(__('pages.contact.phone_number'));
        $response->assertSee(__('pages.contact.phone_hours'));
        $response->assertSee('href="tel:+60312345678"', false);
    }

    #[Test]
    public function contact_page_displays_email_information(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.email_title'));
        $response->assertSee(__('pages.contact.email_address'));
        $response->assertSee(__('pages.contact.email_response'));
        $response->assertSee('href="mailto:ictserve@motac.gov.my"', false);
    }

    #[Test]
    public function contact_page_displays_address_information(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.address_title'));
        $response->assertSee(__('pages.contact.address_line1'));
        $response->assertSee(__('pages.contact.address_line2'));
        $response->assertSee(__('pages.contact.address_line3'));
        $response->assertSee(__('pages.contact.address_line4'));
    }

    #[Test]
    public function contact_page_displays_office_hours(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.hours_title'));
        $response->assertSee(__('pages.contact.hours_weekday'));
        $response->assertSee(__('pages.contact.hours_friday'));
        $response->assertSee(__('pages.contact.hours_weekend'));
    }

    #[Test]
    public function contact_page_displays_emergency_support(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(__('pages.contact.emergency_title'));
        $response->assertSee(__('pages.contact.emergency_text'));
        $response->assertSee(__('pages.contact.emergency_phone'));
        $response->assertSee(__('pages.contact.emergency_available'));
        $response->assertSee('href="tel:+60312349999"', false);
    }

    #[Test]
    public function contact_form_has_all_required_fields(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('id="name"', false);
        $response->assertSee('id="email"', false);
        $response->assertSee('id="subject"', false);
        $response->assertSee('id="message"', false);
    }

    #[Test]
    public function contact_form_message_counter_starts_at_zero(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('0/5000');
    }

    #[Test]
    public function contact_form_fields_have_proper_labels(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('Nama Penuh');
        $response->assertSee('Alamat E-mel');
        $response->assertSee('Subjek');
        $response->assertSee('Mesej');
    }

    #[Test]
    public function contact_form_fields_have_required_attribute(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('required', false);
        $response->assertSee('aria-required="true"', false);
    }

    #[Test]
    public function contact_form_has_submit_button(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('Hantar Mesej');
        $response->assertSee('type="submit"', false);
    }

    #[Test]
    public function contact_form_uses_livewire(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('wire:submit', false);
    }

    #[Test]
    public function contact_page_has_proper_breadcrumbs(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('Utama');
        $response->assertSee(__('pages.contact.breadcrumb'));
    }

    #[Test]
    public function contact_page_home_link_works(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee(route('welcome'), false);
    }

    #[Test]
    public function contact_page_displays_in_bahasa_melayu(): void
    {
        app()->setLocale('ms');
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertSee(__('pages.contact.title'));
    }

    #[Test]
    public function contact_page_displays_in_english(): void
    {
        app()->setLocale('en');
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertSee(__('pages.contact.title'));
    }

    #[Test]
    public function contact_page_has_proper_semantic_structure(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('<section', false);
        $response->assertSee('<nav', false);
        $response->assertSee('<form', false);
        $response->assertSee('role="banner"', false);
    }

    #[Test]
    public function contact_page_has_proper_aria_attributes(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('aria-label', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-hidden="true"', false);
        $response->assertSee('aria-required="true"', false);
    }

    #[Test]
    public function contact_page_uses_compliant_color_palette(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('primary-600', false);
        $response->assertSee('text-danger', false);
    }

    #[Test]
    public function contact_page_has_responsive_design_classes(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('md:', false);
        $response->assertSee('lg:', false);
        $response->assertSee('sm:', false);
    }

    #[Test]
    public function contact_form_fields_have_minimum_touch_target_sizes(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('min-h-11', false);
    }

    #[Test]
    public function contact_form_has_proper_focus_indicators(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('focus:border-primary-500', false);
        $response->assertSee('focus-visible:ring', false);
    }

    #[Test]
    public function contact_page_has_proper_grid_layout(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('grid', false);
        $response->assertSee('col-span-4', false);
        $response->assertSee('lg:col-span-4', false);
        $response->assertSee('lg:col-span-8', false);
    }

    #[Test]
    public function contact_form_placeholders_are_accessible(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('Masukkan nama penuh anda');
        $response->assertSee('Masukkan alamat e-mel anda');
        $response->assertSee('Apakah berkenaan mesej anda?');
        $response->assertSee('Sila huraikan pertanyaan anda dengan terperinci');
    }

    #[Test]
    public function emergency_support_section_has_proper_styling(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSee('border-danger', false);
        $response->assertSee('bg-danger-50', false);
        $response->assertSee('text-danger', false);
    }

    #[Test]
    public function contact_information_cards_display_correct_content(): void
    {
        $response = $this->get(route('contact'));
        $response->assertSeeInOrder([
            __('pages.contact.address_title'),
            __('pages.contact.info_title'),
            __('pages.contact.hours_title'),
        ]);
    }
}
