<?php

declare(strict_types=1);

namespace Tests\Feature\Email;

use App\Mail\Loans\ResponsibleOfficerSponsorshipRequestMail;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SponsorshipEmailTest extends TestCase
{
    #[Test]
    public function sponsorship_email_can_be_sent(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'responsible_officer_name' => 'John Doe',
            'responsible_officer_email' => 'john@example.com',
            'sponsorship_token' => 'test-token',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        Mail::to($application->responsible_officer_email)
            ->queue(new ResponsibleOfficerSponsorshipRequestMail($application));

        Mail::assertQueued(ResponsibleOfficerSponsorshipRequestMail::class);
    }

    #[Test]
    public function sponsorship_email_contains_acknowledge_url(): void
    {
        $application = LoanApplication::factory()->create([
            'sponsorship_token' => 'secure-token-123',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        $mailable = new ResponsibleOfficerSponsorshipRequestMail($application);

        $expectedUrl = route('loan.sponsorship.acknowledge', ['token' => 'secure-token-123']);
        $this->assertEquals($expectedUrl, $mailable->acknowledgeUrl);
    }

    #[Test]
    public function sponsorship_email_has_bilingual_subject(): void
    {
        $application = LoanApplication::factory()->create([
            'application_number' => 'TEST-002',
            'sponsorship_token' => 'test-token',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        // Test English
        session(['locale' => 'en']);
        $mailableEn = new ResponsibleOfficerSponsorshipRequestMail($application);
        $mailableEn->build();
        $this->assertStringContainsString('Sponsorship Request', $mailableEn->subject);

        // Test Malay
        session(['locale' => 'ms']);
        $mailableMs = new ResponsibleOfficerSponsorshipRequestMail($application);
        $mailableMs->build();
        $this->assertStringContainsString('Permintaan Penajaan', $mailableMs->subject);
    }

    #[Test]
    public function sponsorship_email_includes_application_details(): void
    {
        $application = LoanApplication::factory()->create([
            'application_number' => 'APP-123',
            'applicant_name' => 'Jane Smith',
            'applicant_email' => 'jane@example.com',
            'responsible_officer_name' => 'Supervisor Name',
            'purpose' => 'Conference presentation',
            'sponsorship_token' => 'test-token',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        $mailable = new ResponsibleOfficerSponsorshipRequestMail($application);

        $mailable->assertSeeInHtml('APP-123');
        $mailable->assertSeeInHtml('Jane Smith');
        $mailable->assertSeeInHtml('jane@example.com');
        $mailable->assertSeeInHtml('Supervisor Name');
        $mailable->assertSeeInHtml('Conference presentation');
    }

    #[Test]
    public function sponsorship_email_shows_expiry_warning(): void
    {
        $expiryDate = now()->addHours(48);
        $application = LoanApplication::factory()->create([
            'sponsorship_token' => 'test-token',
            'sponsorship_token_expires_at' => $expiryDate,
        ]);

        $mailable = new ResponsibleOfficerSponsorshipRequestMail($application);
        $mailable->assertSeeInHtml($expiryDate->format('d/m/Y H:i'));
    }

    #[Test]
    public function sponsorship_email_includes_iso_document_id(): void
    {
        $application = LoanApplication::factory()->create([
            'sponsorship_token' => 'test-token',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        $mailable = new ResponsibleOfficerSponsorshipRequestMail($application);
        $mailable->assertSeeInHtml('PK.(S).MOTAC.07.(L3)');
    }
}
