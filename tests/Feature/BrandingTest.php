<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class BrandingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function auth_header_uses_motac_branding(): void
    {
        $user = User::factory()->create();

        $this->blade('<x-layout.auth-header :user="$user" />', ['user' => $user])
            ->assertSee('images/jata-negara.svg', false)
            ->assertSee(__('common.jata_negara'), false)
            ->assertSee(__('common.motac_logo'), false);
    }

    #[Test]
    public function application_logo_component_renders_motac_asset(): void
    {
        $this->blade('<x-application-logo class="h-8" />')
            ->assertSee('images/motac-logo.jpeg', false)
            ->assertSee(__('common.motac_logo'), false);
    }

    #[Test]
    public function mail_header_component_uses_motac_logo(): void
    {
        $html = view('vendor.mail.html.header', [
            'url' => config('app.url'),
            'slot' => 'Laravel',
        ])->render();

        $this->assertStringContainsString('images/motac-logo.jpeg', $html);
        $this->assertStringContainsString(__('common.motac_logo'), $html);
    }

    #[Test]
    public function gov_header_displays_jata_negara_and_motac_branding(): void
    {
        $this->blade('<x-layout.gov-header />')
            ->assertSee('images/jata-negara.svg', false)
            ->assertSee(__('common.jata_negara'), false)
            ->assertSee('images/motac-logo.png', false)
            ->assertSee(__('common.motac_logo'), false)
            ->assertSee(__('common.motac_full_name'), false)
            ->assertSee(__('common.bpm_full_name'), false);
    }

    #[Test]
    public function gov_header_has_proper_image_dimensions(): void
    {
        // Jata Negara should be 48x48 minimum per MyGOV DSS v2.1.0
        $this->blade('<x-layout.gov-header />')
            ->assertSee('width="48"', false)
            ->assertSee('height="48"', false)
            ->assertSee('width="40"', false)
            ->assertSee('height="40"', false);
    }

    #[Test]
    public function gov_header_has_responsive_layout(): void
    {
        // Ministry name should be hidden on mobile (hidden sm:block)
        $this->blade('<x-layout.gov-header />')
            ->assertSee('hidden sm:block', false);
    }

    #[Test]
    public function gov_footer_displays_jata_negara_inverted(): void
    {
        $this->blade('<x-layout.gov-footer />')
            ->assertSee('images/jata-negara.svg', false)
            ->assertSee(__('common.jata_negara'), false)
            ->assertSee('brightness-0 invert', false); // Inverted filter for dark background
    }

    #[Test]
    public function gov_footer_displays_ministry_full_name(): void
    {
        $this->blade('<x-layout.gov-footer />')
            ->assertSee(__('common.motac_full_name'), false);
    }

    #[Test]
    public function gov_footer_displays_government_disclaimer(): void
    {
        $this->blade('<x-layout.gov-footer />')
            ->assertSee(__('common.gov_disclaimer'), false);
    }

    #[Test]
    public function gov_footer_displays_bpm_copyright(): void
    {
        $this->blade('<x-layout.gov-footer />')
            ->assertSee(__('common.bpm_full_name'), false)
            ->assertSee(__('common.all_rights_reserved'), false)
            ->assertSee(date('Y'), false); // Current year in copyright
    }

    #[Test]
    public function gov_footer_has_proper_accessibility_attributes(): void
    {
        $this->blade('<x-layout.gov-footer />')
            ->assertSee('role="contentinfo"', false)
            ->assertSee('aria-label', false);
    }

    #[Test]
    public function form_header_displays_bpm_logo(): void
    {
        $this->blade('<x-form.header title="Test Title" subtitle="Test Subtitle" />')
            ->assertSee('images/bpm-logo.png', false)
            ->assertSee(__('common.bpm_logo'), false);
    }

    #[Test]
    public function form_header_displays_title_and_subtitle(): void
    {
        $this->blade('<x-form.header title="Helpdesk Form" subtitle="Submit your ICT request" />')
            ->assertSee('Helpdesk Form', false)
            ->assertSee('Submit your ICT request', false);
    }

    #[Test]
    public function form_header_has_proper_image_dimensions(): void
    {
        // BPM logo should be 64x64 per Requirement 21.3
        $this->blade('<x-form.header title="Test" />')
            ->assertSee('width="64"', false)
            ->assertSee('height="64"', false);
    }

    #[Test]
    public function form_header_uses_motac_primary_blue_gradient(): void
    {
        // Per Requirement 22.1: Use official MOTAC color palette with Primary Blue (#0056b3)
        $this->blade('<x-form.header title="Test" />')
            ->assertSee('bg-linear-to-r', false)
            ->assertSee('#0056b3', false);
    }

    #[Test]
    public function form_header_has_proper_accessibility_attributes(): void
    {
        $this->blade('<x-form.header title="Test" />')
            ->assertSee('role="banner"', false)
            ->assertSee('aria-label', false);
    }

    #[Test]
    public function form_header_renders_without_subtitle(): void
    {
        $this->blade('<x-form.header title="Only Title" />')
            ->assertSee('Only Title', false)
            ->assertDontSee('text-blue-200', false); // Subtitle paragraph class
    }
}
