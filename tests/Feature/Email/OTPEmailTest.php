<?php

declare(strict_types=1);

namespace Tests\Feature\Email;

use App\Mail\Loans\OTPPickupMail;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OTPEmailTest extends TestCase
{
    #[Test]
    public function otp_email_can_be_sent(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'applicant_email' => 'test@example.com',
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $otp = '1234';

        Mail::to($application->applicant_email)->send(new OTPPickupMail($application, $otp));

        Mail::assertSent(OTPPickupMail::class, function ($mail) use ($application, $otp) {
            return $mail->application->id === $application->id &&
                $mail->otp === $otp;
        });
    }

    #[Test]
    public function otp_email_contains_correct_otp(): void
    {
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $otp = '5678';

        $mailable = new OTPPickupMail($application, $otp);
        $mailable->assertSeeInHtml($otp);
    }

    #[Test]
    public function otp_email_has_bilingual_subject(): void
    {
        $application = LoanApplication::factory()->create([
            'application_number' => 'TEST-001',
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        // Test English
        session(['locale' => 'en']);
        $mailableEn = new OTPPickupMail($application, '1234');
        $mailableEn->build();
        $this->assertStringContainsString('Asset Pickup OTP', $mailableEn->subject);

        // Test Malay
        session(['locale' => 'ms']);
        $mailableMs = new OTPPickupMail($application, '1234');
        $mailableMs->build();
        $this->assertStringContainsString('OTP Pengambilan Aset', $mailableMs->subject);
    }

    #[Test]
    public function otp_email_includes_expiry_date(): void
    {
        $expiryDate = now()->addHours(24);
        $application = LoanApplication::factory()->create([
            'pickup_otp_expires_at' => $expiryDate,
        ]);

        $mailable = new OTPPickupMail($application, '1234');
        $mailable->assertSeeInHtml($expiryDate->format('d/m/Y'));
    }

    #[Test]
    public function otp_email_includes_tracking_link(): void
    {
        $application = LoanApplication::factory()->create([
            'tracking_token' => 'test-token-123',
            'pickup_otp_expires_at' => now()->addHours(24),
        ]);

        $mailable = new OTPPickupMail($application, '1234');
        $mailable->assertSeeInHtml(route('loan.guest.track-token', ['token' => 'test-token-123']));
    }
}
