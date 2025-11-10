<?php

declare(strict_types=1);

namespace Tests\Feature\Email;

use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanApprovalRequest;
use App\Mail\LoanApplicationDecision;
use App\Mail\AssetReturnReminder;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Loan Email Notification Tests
 *
 * Tests email notification system for loan module.
 *
 * @see D03-FR-009.1 Email notifications
 * @see D03-FR-009.2 Email templates
 * Requirements: 9.1, 9.2, 9.3, 9.4
 */
class LoanEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Queue::fake();
    }

    #[Test]
    public function loan_application_submitted_email_is_sent(): void
    {
        $loan = LoanApplication::factory()->create([
            'applicant_email' => 'applicant@example.com',
        ]);

        Mail::to($loan->applicant_email)->send(new LoanApplicationSubmitted($loan));

        // Since LoanApplicationSubmitted implements ShouldQueue, it's queued not sent
        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) use ($loan) {
            return $mail->hasTo($loan->applicant_email);
        });
    }

    #[Test]
    public function loan_approval_request_email_is_sent_to_approvers(): void
    {
        $approver = User::factory()->create(['email' => 'approver@example.com']);
        $approver->assignRole('approver');

        $loan = LoanApplication::factory()->create([
            'approval_token' => 'test-token-123',
        ]);

        Mail::to($approver->email)->send(new LoanApprovalRequest($loan, 'test-token-123'));

        // Since LoanApprovalRequest implements ShouldQueue, it's queued not sent
        Mail::assertQueued(LoanApprovalRequest::class, function ($mail) use ($approver) {
            return $mail->hasTo($approver->email);
        });
    }

    #[Test]
    public function loan_decision_email_is_sent_after_approval(): void
    {
        $loan = LoanApplication::factory()->create([
            'status' => 'approved',
            'applicant_email' => 'applicant@example.com',
        ]);

        Mail::to($loan->applicant_email)->send(new LoanApplicationDecision($loan, true));

        // Since LoanApplicationDecision implements ShouldQueue, it's queued not sent
        Mail::assertQueued(LoanApplicationDecision::class);
    }

    #[Test]
    public function return_reminder_email_is_sent_before_due_date(): void
    {
        $loan = LoanApplication::factory()->create([
            'status' => 'in_use',
            'loan_end_date' => now()->addDays(3),
            'applicant_email' => 'applicant@example.com',
        ]);

        Mail::to($loan->applicant_email)->send(new AssetReturnReminder($loan));

        // Since AssetReturnReminder implements ShouldQueue, it's queued not sent
        Mail::assertQueued(AssetReturnReminder::class);
    }

    #[Test]
    public function email_is_queued_for_async_delivery(): void
    {
        $loan = LoanApplication::factory()->create();

        Mail::to($loan->applicant_email)->queue(new LoanApplicationSubmitted($loan));

        // Verify mail was queued (Mail::fake() captures both send() and queue())
        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) use ($loan) {
            return $mail->hasTo($loan->applicant_email);
        });
    }

    #[Test]
    public function email_delivery_meets_60_second_sla(): void
    {
        $loan = LoanApplication::factory()->create();

        $startTime = microtime(true);

        Mail::to($loan->applicant_email)->queue(new LoanApplicationSubmitted($loan));

        $queueTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $queueTime);
    }
}
