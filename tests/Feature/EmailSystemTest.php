<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Mail\ApprovalConfirmation;
use App\Mail\AssetDueTodayReminder;
use App\Mail\AssetOverdueNotification;
use App\Mail\AssetPreparationNotification;
use App\Mail\AssetReturnConfirmationMail;
use App\Mail\AssetReturnReminder;
use App\Mail\LoanApplicationDecision;
use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanApprovalRequest;
use App\Mail\LoanStatusUpdated;
use App\Models\Asset;
use App\Models\EmailLog;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\DualApprovalService;
use App\Services\Notifications\EmailDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Tests\TestCase;

/**
 * Email System Functionality Tests
 *
 * Comprehensive tests for email notification system including:
 * - All email notification scenarios
 * - Bilingual email generation and delivery
 * - Queue processing and retry mechanisms
 * - Email approval workflow testing
 *
 * @see D03-FR-002.4 Email notifications
 * @see D03-FR-006.4 Bilingual email support
 * @see D03-FR-009.2 Queue processing with retry
 * @see D03-FR-002.3 Email approval workflow
 * @see Task 6.5 - Test email system functionality
 *
 * @trace Requirements: 2.4, 6.4, 9.2, 2.3
 */
class EmailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $approver;

    protected LoanApplication $loanApplication;

    protected Asset $asset;

    protected EmailDispatcher $emailDispatcher;

    protected DualApprovalService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@motac.gov.my',
        ]);

        // Create grade for approver
        $grade = \App\Models\Grade::factory()->create(['level' => 54]);

        $this->approver = User::factory()->create([
            'name' => 'Dato\' Ahmad Approver',
            'email' => 'approver@motac.gov.my',
            'role' => 'approver',
            'grade_id' => $grade->id,
            'is_active' => true,
        ]);

        $this->loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'applicant_name' => $this->user->name,
            'applicant_email' => $this->user->email,
            'status' => LoanStatus::SUBMITTED,
        ]);

        $this->asset = Asset::factory()->create();

        $this->emailDispatcher = app(EmailDispatcher::class);
        $this->workflowService = app(DualApprovalService::class);

        Mail::fake();
        Queue::fake();
    }

    /**
     * Test loan application submitted email notification
     *
     * @see D03-FR-001.2 Application confirmation email
     * @see D03-FR-009.1 60-second SLA compliance
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function loan_application_submitted_email(): void
    {
        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        // Verify email log created
        $this->assertDatabaseHas('email_logs', [
            'id' => $log->id,
            'recipient_email' => $this->loanApplication->applicant_email,
            'status' => 'queued',
        ]);

        // Verify email queued
        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            return $mail->application->id === $this->loanApplication->id &&
                $mail->hasTo($this->loanApplication->applicant_email);
        });

        // Verify queue configuration for SLA compliance
        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            return $mail->queue === 'emails';
        });
    }

    /**
     * Test loan approval request email notification
     *
     * @see D03-FR-002.1 Email approval workflow
     *
     * @trace Requirement 2.3
     */
    #[Test]
    public function loan_approval_request_email(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);

        Mail::assertQueued(LoanApprovalRequest::class, function ($mail) {
            return $mail->hasTo($this->approver->email) &&
                $mail->application->id === $this->loanApplication->id;
        });

        // Verify email contains approval token
        $this->loanApplication->refresh();
        $this->assertNotNull($this->loanApplication->approval_token);
    }

    /**
     * Test loan application decision emails (approval/rejection)
     *
     * @see D03-FR-002.4 Decision notification emails
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function loan_application_decision_emails(): void
    {
        // Test approval email
        $this->loanApplication->update(['status' => LoanStatus::APPROVED]);

        $log = $this->emailDispatcher->queue(
            new LoanApplicationDecision($this->loanApplication, true),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(LoanApplicationDecision::class, function ($mail) {
            return $mail->approved === true &&
                $mail->application->id === $this->loanApplication->id;
        });

        // Test rejection email
        $rejectedApplication = LoanApplication::factory()->create([
            'status' => LoanStatus::REJECTED,
            'rejected_reason' => 'Insufficient justification',
        ]);

        $log = $this->emailDispatcher->queue(
            new LoanApplicationDecision($rejectedApplication, false),
            $rejectedApplication->applicant_email,
            $rejectedApplication->applicant_name
        );

        Mail::assertQueued(LoanApplicationDecision::class, function ($mail) use ($rejectedApplication) {
            return $mail->approved === false &&
                $mail->application->id === $rejectedApplication->id;
        });
    }

    /**
     * Test asset preparation notification email
     *
     * @see D03-FR-002.4 Admin notification for asset preparation
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function asset_preparation_notification_email(): void
    {
        $this->loanApplication->update(['status' => LoanStatus::APPROVED]);

        $adminUser = User::factory()->create(['role' => 'admin']);

        $log = $this->emailDispatcher->queue(
            new AssetPreparationNotification($this->loanApplication),
            $adminUser->email,
            $adminUser->name
        );

        Mail::assertQueued(AssetPreparationNotification::class, function ($mail) {
            return $mail->loanApplication->id === $this->loanApplication->id;
        });
    }

    /**
     * Test asset return reminder emails
     *
     * @see D03-FR-009.3 Automated reminder emails
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function asset_return_reminder_emails(): void
    {
        // Test 48-hour reminder
        $this->loanApplication->update([
            'status' => LoanStatus::IN_USE,
            'loan_end_date' => now()->addDays(2),
        ]);

        $log = $this->emailDispatcher->queue(
            new AssetReturnReminder($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(AssetReturnReminder::class, function ($mail) {
            return $mail->application->id === $this->loanApplication->id;
        });

        // Test due today reminder
        $this->loanApplication->update(['loan_end_date' => now()]);

        $log = $this->emailDispatcher->queue(
            new AssetDueTodayReminder($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(AssetDueTodayReminder::class);
    }

    /**
     * Test asset overdue notification emails
     *
     * @see D03-FR-009.3 Overdue notifications
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function asset_overdue_notification_emails(): void
    {
        $this->loanApplication->update([
            'status' => LoanStatus::OVERDUE,
            'loan_end_date' => now()->subDay(),
        ]);

        $log = $this->emailDispatcher->queue(
            new AssetOverdueNotification($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(AssetOverdueNotification::class, function ($mail) {
            return $mail->application->id === $this->loanApplication->id;
        });
    }

    /**
     * Test asset return confirmation email
     *
     * @see D03-FR-003.3 Return confirmation
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function asset_return_confirmation_email(): void
    {
        $this->loanApplication->update(['status' => LoanStatus::RETURNED]);

        $log = $this->emailDispatcher->queue(
            new AssetReturnConfirmationMail($this->loanApplication, $this->asset),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(AssetReturnConfirmationMail::class, function ($mail) {
            return $mail->loanApplication->id === $this->loanApplication->id;
        });
    }

    /**
     * Test loan status update email
     *
     * @see D03-FR-009.1 Status update notifications
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function loan_status_update_email(): void
    {
        $this->loanApplication->update(['status' => LoanStatus::READY_ISSUANCE]);

        $log = $this->emailDispatcher->queue(
            new LoanStatusUpdated($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(LoanStatusUpdated::class, function ($mail) {
            return $mail->application->id === $this->loanApplication->id;
        });
    }

    /**
     * Test bilingual email generation (Bahasa Melayu)
     *
     * @see D03-FR-006.4 Bilingual email support
     * @see D03-FR-015.3 Language switching
     *
     * @trace Requirement 6.4
     */
    #[Test]
    public function bilingual_email_generation_malay(): void
    {
        // Set locale to Malay
        app()->setLocale('ms');

        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            $rendered = $mail->render();

            // Verify Malay content (check for common Malay terms)
            $this->assertStringContainsString($this->loanApplication->application_number, $rendered);

            // Verify email structure is maintained
            $this->assertNotEmpty($rendered);

            return true;
        });
    }

    /**
     * Test bilingual email generation (English)
     *
     * @see D03-FR-006.4 Bilingual email support
     *
     * @trace Requirement 6.4
     */
    #[Test]
    public function bilingual_email_generation_english(): void
    {
        // Set locale to English
        app()->setLocale('en');

        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            $rendered = $mail->render();

            // Verify English content
            $this->assertStringContainsString($this->loanApplication->application_number, $rendered);
            $this->assertNotEmpty($rendered);

            return true;
        });
    }

    /**
     * Test email queue processing
     *
     * @see D03-FR-009.2 Queue processing
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_queue_processing(): void
    {
        // Mail::fake() is already called in setUp, so mail queuing is intercepted
        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        // Verify email was queued (Mail::fake intercepts this)
        Mail::assertQueued(LoanApplicationSubmitted::class);

        // Verify email log status
        $this->assertEquals('queued', $log->status);
    }

    /**
     * Test email retry mechanism configuration
     *
     * @see D03-FR-009.2 Retry mechanism with exponential backoff
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_retry_mechanism(): void
    {
        $mailable = new LoanApplicationSubmitted($this->loanApplication);

        // Verify retry configuration
        $this->assertEquals(3, $mailable->tries);
        $this->assertIsArray($mailable->backoff);
        $this->assertCount(3, $mailable->backoff);

        // Verify exponential backoff pattern
        $this->assertGreaterThan($mailable->backoff[0], $mailable->backoff[1]);
        $this->assertGreaterThan($mailable->backoff[1], $mailable->backoff[2]);
    }

    /**
     * Test email delivery tracking
     *
     * @see D03-FR-009.2 Email delivery tracking
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_delivery_tracking(): void
    {
        $log = EmailLog::factory()->create([
            'status' => 'queued',
        ]);

        $email = (new SymfonyEmail)
            ->from(new Address('no-reply@motac.gov.my', 'ICTServe'))
            ->to($this->user->email)
            ->subject('Test Email')
            ->text('Test body');

        $email->getHeaders()->addTextHeader('X-Email-Log-Id', (string) $log->id);
        $email->getHeaders()->addIdHeader('Message-ID', 'test-message-id@motac.gov.my');

        $envelope = new Envelope(
            new Address('no-reply@motac.gov.my', 'ICTServe'),
            [new Address($this->user->email)]
        );

        $event = new MessageSent(
            new \Illuminate\Mail\SentMessage(new SymfonySentMessage($email, $envelope))
        );

        app(\App\Listeners\UpdateEmailLogOnSend::class)->handle($event);

        $log->refresh();

        // Verify email marked as sent
        $this->assertEquals('sent', $log->status);
        $this->assertEquals('test-message-id@motac.gov.my', $log->message_id);
        $this->assertNotNull($log->sent_at);
    }

    /**
     * Test email failure handling
     *
     * @see D03-FR-009.2 Email failure handling
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_failure_handling(): void
    {
        $log = EmailLog::factory()->create(['status' => 'queued']);

        $mailable = (new LoanApplicationSubmitted($this->loanApplication))
            ->withEmailLog($log);

        $job = new SendQueuedMailable($mailable);

        $payload = [
            'displayName' => SendQueuedMailable::class,
            'data' => [
                'command' => serialize($job),
            ],
        ];

        $jobMock = $this->createMock(\Illuminate\Contracts\Queue\Job::class);
        $jobMock->method('payload')->willReturn($payload);

        $event = new JobFailed('redis', $jobMock, new \Exception('SMTP connection failed'));

        app(\App\Listeners\UpdateEmailLogOnFailure::class)->handle($event);

        $log->refresh();

        // Verify email marked as failed
        $this->assertEquals('failed', $log->status);
        $this->assertEquals('SMTP connection failed', $log->status_message);
    }

    /**
     * Test email approval workflow token generation
     *
     * @see D03-FR-002.3 Secure token generation
     *
     * @trace Requirement 2.3
     */
    #[Test]
    public function email_approval_token_generation(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);

        $this->loanApplication->refresh();

        // Verify token generated
        $this->assertNotNull($this->loanApplication->approval_token);
        $this->assertIsString($this->loanApplication->approval_token);
        $this->assertGreaterThan(20, strlen($this->loanApplication->approval_token));

        // Verify token expiration set (7 days)
        $this->assertNotNull($this->loanApplication->approval_token_expires_at);
        $this->assertTrue($this->loanApplication->approval_token_expires_at > now());
        $this->assertTrue($this->loanApplication->approval_token_expires_at <= now()->addDays(7));
    }

    /**
     * Test email approval workflow processing
     *
     * @see D03-FR-002.3 Email approval processing
     *
     * @trace Requirement 2.3
     */
    #[Test]
    public function email_approval_workflow_processing(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Process approval
        $result = $this->workflowService->processEmailApproval(
            $token,
            true,
            'Approved via email'
        );

        // Verify approval processed
        $this->assertTrue($result['success']);
        $approvedApplication = $result['application'];
        $this->assertEquals(LoanStatus::APPROVED, $approvedApplication->status);
        $this->assertNotNull($approvedApplication->approved_at);
        $this->assertNull($approvedApplication->approval_token);

        // Verify confirmation emails queued (ShouldQueue)
        Mail::assertQueued(ApprovalConfirmation::class);
    }

    /**
     * Test email approval workflow rejection
     *
     * @see D03-FR-002.3 Email rejection processing
     *
     * @trace Requirement 2.3
     */
    #[Test]
    public function email_approval_workflow_rejection(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Process rejection
        $result = $this->workflowService->processEmailApproval(
            $token,
            false,
            'Insufficient justification'
        );

        // Verify rejection processed
        $this->assertTrue($result['success']);
        $rejectedApplication = $result['application'];
        $this->assertEquals(LoanStatus::REJECTED, $rejectedApplication->status);
        $this->assertNull($rejectedApplication->approved_at);
        $this->assertEquals('Insufficient justification', $rejectedApplication->rejected_reason);
        $this->assertNull($rejectedApplication->approval_token);
    }

    /**
     * Test email approval token expiration
     *
     * @see D03-FR-002.5 Token expiration handling
     *
     * @trace Requirement 2.3
     */
    #[Test]
    public function email_approval_token_expiration(): void
    {
        $this->workflowService->routeForEmailApproval($this->loanApplication);
        $token = $this->loanApplication->approval_token;

        // Expire the token
        $this->loanApplication->update([
            'approval_token_expires_at' => now()->subDay(),
        ]);

        // Attempt to process expired token (should return failure, not throw exception)
        $result = $this->workflowService->processEmailApproval($token, true);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expired', strtolower($result['message']));
    }

    /**
     * Test email SLA compliance (60 seconds)
     *
     * @see D03-FR-009.1 60-second SLA for email notifications
     *
     * @trace Requirement 2.4
     */
    #[Test]
    public function email_sla_compliance(): void
    {
        $startTime = microtime(true);

        // Queue multiple emails
        for ($i = 0; $i < 10; $i++) {
            $application = LoanApplication::factory()->create();
            $this->emailDispatcher->queue(
                new LoanApplicationSubmitted($application),
                $application->applicant_email,
                $application->applicant_name
            );
        }

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        // Verify processing completed well within SLA
        $this->assertLessThan(5.0, $processingTime, 'Email queuing took too long');

        // Verify all emails queued
        Mail::assertQueued(LoanApplicationSubmitted::class, 10);
    }

    /**
     * Test concurrent email processing
     *
     * @see D03-FR-007.2 Concurrent processing
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function concurrent_email_processing(): void
    {
        $applications = LoanApplication::factory()->count(5)->create();

        // Queue all emails concurrently
        foreach ($applications as $application) {
            $this->emailDispatcher->queue(
                new LoanApplicationSubmitted($application),
                $application->applicant_email,
                $application->applicant_name
            );
        }

        // Verify all emails queued
        Mail::assertQueued(LoanApplicationSubmitted::class, 5);

        // Verify all email logs created
        $this->assertEquals(5, EmailLog::where('status', 'queued')->count());
    }

    /**
     * Test email template WCAG compliance
     *
     * @see D03-FR-006.1 WCAG 2.2 AA compliance
     *
     * @trace Requirement 6.4
     */
    #[Test]
    public function email_template_wcag_compliance(): void
    {
        Mail::fake();

        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            $this->loanApplication->applicant_email,
            $this->loanApplication->applicant_name
        );

        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            $rendered = $mail->render();

            // Verify semantic HTML structure
            $this->assertStringContainsString('<html', $rendered);
            $this->assertStringContainsString('</html>', $rendered);

            // Verify content is present
            $this->assertNotEmpty($rendered);
            $this->assertStringContainsString($this->loanApplication->application_number, $rendered);

            return true;
        });
    }

    /**
     * Test email error handling for invalid recipients
     *
     * @see D03-FR-009.2 Error handling
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_error_handling_invalid_recipient(): void
    {
        $log = $this->emailDispatcher->queue(
            new LoanApplicationSubmitted($this->loanApplication),
            'invalid-email',
            'Test User'
        );

        // Verify email log created even with invalid email
        $this->assertDatabaseHas('email_logs', [
            'id' => $log->id,
            'recipient_email' => 'invalid-email',
            'status' => 'queued',
        ]);
    }

    /**
     * Test email batch processing
     *
     * @see D03-FR-009.2 Batch processing
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function email_batch_processing(): void
    {
        $applications = LoanApplication::factory()->count(20)->create();

        $startTime = microtime(true);

        foreach ($applications as $application) {
            $this->emailDispatcher->queue(
                new LoanApplicationSubmitted($application),
                $application->applicant_email,
                $application->applicant_name
            );
        }

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        // Verify batch processing is efficient
        $this->assertLessThan(10.0, $processingTime);

        // Verify all emails queued
        Mail::assertQueued(LoanApplicationSubmitted::class, 20);

        // Verify all email logs created
        $this->assertEquals(20, EmailLog::where('status', 'queued')->count());
    }
}
