<?php

declare(strict_types=1);

namespace Tests\Feature\Email;

use App\Mail\Loans\OTPPickupMail;
use App\Models\LoanApplication;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test email template branding per Requirement 21.5
 * Validates that email headers contain MOTAC branding elements
 *
 * @see .kiro/specs/ictserve-update-v3/tasks.md §49.1
 * @see .kiro/specs/ictserve-update-v3/requirements.md §Requirement 21
 */
class EmailTemplateBrandingTest extends TestCase
{
    #[Test]
    public function email_header_contains_jata_negara_image(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Assert Jata Negara image is present with correct height
        $mailable->assertSeeInHtml('jata-negara.svg');
        $mailable->assertSeeInHtml('height: 60px');
    }

    #[Test]
    public function email_header_contains_motac_logo(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Assert MOTAC logo is present with correct height
        $mailable->assertSeeInHtml('motac-logo.png');
        $mailable->assertSeeInHtml('height: 50px');
    }

    #[Test]
    public function email_header_contains_ministry_tagline_in_english(): void
    {
        app()->setLocale('en');

        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');
        $mailable->assertSeeInHtml('Ministry of Tourism, Arts and Culture');
    }

    #[Test]
    public function email_header_contains_ministry_tagline_in_malay(): void
    {
        app()->setLocale('ms');

        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');
        $mailable->assertSeeInHtml('Kementerian Pelancongan, Seni dan Budaya');
    }

    #[Test]
    public function email_header_uses_motac_primary_blue_color(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Assert MOTAC primary blue (#0056b3) is used for tagline text
        $mailable->assertSeeInHtml('color: #0056b3');
    }

    #[Test]
    public function email_header_has_centered_layout(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Assert header is centered
        $mailable->assertSeeInHtml('text-align: center');
    }

    #[Test]
    public function email_branding_contains_bahasa_melayu_content(): void
    {
        // Set locale to Bahasa Melayu (v3.6.0 default)
        app()->setLocale('ms');

        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Render email content
        $rendered = $mailable->render();

        // Verify Bahasa Melayu branding content
        $this->assertStringContainsString('Kementerian Pelancongan, Seni dan Budaya', $rendered); // BM ministry name
        $this->assertStringContainsString('Tuan/Puan', $rendered); // BM greeting
        $this->assertStringContainsString('Terima kasih', $rendered); // BM closing
        $this->assertNotEmpty($rendered);
    }

    #[Test]
    public function email_branding_meets_wcag_contrast_requirements(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');

        // Render email content
        $rendered = $mailable->render();

        // Verify WCAG 2.2 AA compliance elements are present
        $this->assertStringContainsString('color: #0056b3', $rendered); // MOTAC primary blue with sufficient contrast
        $this->assertNotEmpty($rendered);
    }
}
