<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Mail\Loan\OtpGeneratedMail;
use App\Mail\LoanStatusUpdated;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\Notifications\LoanNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Queue::fake();

        $this->user = User::factory()->create([
            'email' => 'test@motac.gov.my',
        ]);
    }

    public function test_otp_email_is_sent_when_status_changes_to_ready_issuance(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_email' => $this->user->email,
            'division_id' => $division->id,
            'status' => LoanStatus::APPROVED,
        ]);

        // Change status to READY_ISSUANCE
        $application->update(['status' => LoanStatus::READY_ISSUANCE]);

        // Trigger notification service
        $service = app(LoanNotificationService::class);
        $service->sendStatusUpdate($application, LoanStatus::APPROVED->value);

        // Assert OTP email was queued
        Mail::assertQueued(OtpGeneratedMail::class, function ($mail) use ($application) {
            return $mail->application->id === $application->id
                && strlen($mail->otp) === 6;
        });

        // Verify OTP was generated and stored
        $application->refresh();
        $this->assertNotNull($application->pickup_otp_hash);
        $this->assertNotNull($application->pickup_otp_generated_at);
        $this->assertNotNull($application->pickup_otp_expires_at);
        $this->assertEquals(0, $application->pickup_otp_attempts);
    }

    public function test_status_update_email_is_sent(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_email' => $this->user->email,
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        // Change status
        $application->update(['status' => LoanStatus::UNDER_REVIEW]);

        // Trigger notification
        $service = app(LoanNotificationService::class);
        $service->sendStatusUpdate($application, LoanStatus::SUBMITTED->value);

        // Assert status update email was queued
        Mail::assertQueued(LoanStatusUpdated::class, function ($mail) use ($application) {
            return $mail->application->id === $application->id
                && $mail->previousStatus === LoanStatus::SUBMITTED->value;
        });
    }

    public function test_otp_generation_creates_valid_6_digit_code(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        $otp = $application->generateOtp();

        // Verify OTP format
        $this->assertEquals(6, strlen($otp));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $otp);

        // Verify OTP validation works
        $this->assertTrue($application->isOtpValid($otp));
    }

    public function test_otp_validation_fails_with_wrong_code(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        $otp = $application->generateOtp();

        // Test with wrong OTP
        $this->assertFalse($application->isOtpValid('000000'));
        $this->assertFalse($application->isOtpValid('999999'));
        $this->assertFalse($application->isOtpValid($otp.'1'));
    }

    public function test_otp_validation_fails_when_expired(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        $otp = $application->generateOtp();

        // Manually expire the OTP
        $application->pickup_otp_expires_at = now()->subDays(1);
        $application->save();

        // Verify validation fails
        $this->assertFalse($application->isOtpValid($otp));
    }

    public function test_otp_attempts_are_tracked(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        $otp = $application->generateOtp();
        $this->assertEquals(0, $application->pickup_otp_attempts);

        // Simulate failed attempts
        $application->incrementOtpAttempts();
        $this->assertEquals(1, $application->fresh()->pickup_otp_attempts);

        $application->incrementOtpAttempts();
        $this->assertEquals(2, $application->fresh()->pickup_otp_attempts);

        $application->incrementOtpAttempts();
        $this->assertEquals(3, $application->fresh()->pickup_otp_attempts);

        // Verify locked after 3 attempts
        $this->assertTrue($application->isOtpLocked());
    }

    public function test_email_templates_support_bilingual_content(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_email' => $this->user->email,
            'division_id' => $division->id,
            'status' => LoanStatus::READY_ISSUANCE,
        ]);

        $otp = $application->generateOtp();

        // Create mailable
        $mail = new OtpGeneratedMail($application, $otp);

        // Verify envelope uses translation
        $envelope = $mail->envelope();
        $this->assertNotEmpty($envelope->subject);

        // Verify content uses translation keys
        $content = $mail->content();
        $this->assertEquals('emails.loan.otp-generated', $content->markdown);
        $this->assertArrayHasKey('application', $content->with);
        $this->assertArrayHasKey('otp', $content->with);
        $this->assertEquals($otp, $content->with['otp']);
    }

    public function test_emails_are_queued_not_sent_synchronously(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_email' => $this->user->email,
            'division_id' => $division->id,
            'status' => LoanStatus::APPROVED,
        ]);

        // Change status to trigger email
        $application->update(['status' => LoanStatus::READY_ISSUANCE]);

        $service = app(LoanNotificationService::class);
        $service->sendStatusUpdate($application, LoanStatus::APPROVED->value);

        // Verify emails were queued, not sent immediately
        Mail::assertQueued(OtpGeneratedMail::class);
        Mail::assertQueued(LoanStatusUpdated::class);
        Mail::assertNothingSent();
    }

    public function test_guest_applications_receive_emails_at_applicant_email(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => null, // Guest submission
            'applicant_email' => 'guest@example.com',
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $application->update(['status' => LoanStatus::UNDER_REVIEW]);

        $service = app(LoanNotificationService::class);
        $service->sendStatusUpdate($application, LoanStatus::SUBMITTED->value);

        // Verify email goes to guest email
        Mail::assertQueued(LoanStatusUpdated::class, function ($mail) {
            return $mail->hasTo('guest@example.com');
        });
    }

    public function test_authenticated_applications_receive_emails_at_user_email(): void
    {
        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_email' => 'old@example.com', // Different from user email
            'division_id' => $division->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $application->update(['status' => LoanStatus::UNDER_REVIEW]);

        $service = app(LoanNotificationService::class);
        $service->sendStatusUpdate($application, LoanStatus::SUBMITTED->value);

        // Verify email goes to user's current email
        Mail::assertQueued(LoanStatusUpdated::class, function ($mail) {
            return $mail->hasTo('test@motac.gov.my');
        });
    }
}
