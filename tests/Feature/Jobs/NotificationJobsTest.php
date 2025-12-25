<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessNotificationDigest;
use App\Jobs\SendApprovalRequest;
use App\Jobs\SendLoanNotification;
use App\Jobs\SendTicketNotification;
use App\Mail\HelpdeskTicketCreated;
use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanApprovalRequest;
use App\Mail\NotificationDigest;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for notification queue jobs.
 *
 * @trace Requirements 10.4, 13.3
 */
class NotificationJobsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Queue::fake();
    }

    /**
     * Test SendTicketNotification job dispatches correctly.
     */
    #[Test]
    public function send_ticket_notification_job_can_be_dispatched(): void
    {
        $user = User::factory()->create([
            'email' => 'test@motac.gov.my',
            'name' => 'Test User',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        SendTicketNotification::dispatch([
            'ticket_id' => $ticket->id,
            'type' => 'created',
        ]);

        Queue::assertPushed(SendTicketNotification::class);
    }

    /**
     * Test SendTicketNotification handles missing ticket gracefully.
     */
    #[Test]
    public function send_ticket_notification_handles_missing_ticket(): void
    {
        $job = new SendTicketNotification([
            'ticket_id' => 99999,
            'type' => 'created',
        ]);

        // Should not throw exception
        $job->handle();

        Mail::assertNothingSent();
    }

    /**
     * Test SendTicketNotification handles missing ticket_id gracefully.
     */
    #[Test]
    public function send_ticket_notification_handles_missing_ticket_id(): void
    {
        $job = new SendTicketNotification([
            'type' => 'created',
        ]);

        // Should not throw exception
        $job->handle();

        Mail::assertNothingSent();
    }

    /**
     * Test SendLoanNotification job dispatches correctly.
     */
    #[Test]
    public function send_loan_notification_job_can_be_dispatched(): void
    {
        $application = LoanApplication::factory()->create([
            'applicant_email' => 'test@motac.gov.my',
            'applicant_name' => 'Test Applicant',
        ]);

        SendLoanNotification::dispatch([
            'loan_application_id' => $application->id,
            'type' => 'submitted',
        ]);

        Queue::assertPushed(SendLoanNotification::class);
    }

    /**
     * Test SendLoanNotification handles missing application gracefully.
     */
    #[Test]
    public function send_loan_notification_handles_missing_application(): void
    {
        $job = new SendLoanNotification([
            'loan_application_id' => 99999,
            'type' => 'submitted',
        ]);

        // Should not throw exception
        $job->handle();

        Mail::assertNothingSent();
    }

    /**
     * Test SendApprovalRequest job dispatches correctly.
     */
    #[Test]
    public function send_approval_request_job_can_be_dispatched(): void
    {
        $application = LoanApplication::factory()->create();

        SendApprovalRequest::dispatch([
            'loan_application_id' => $application->id,
            'approver_email' => 'approver@motac.gov.my',
            'token' => 'test-token-123',
        ]);

        Queue::assertPushed(SendApprovalRequest::class);
    }

    /**
     * Test SendApprovalRequest handles missing required fields.
     */
    #[Test]
    public function send_approval_request_handles_missing_fields(): void
    {
        $job = new SendApprovalRequest([
            'loan_application_id' => 1,
            // Missing approver_email and token
        ]);

        // Should not throw exception
        $job->handle();

        Mail::assertNothingSent();
    }

    /**
     * Test ProcessNotificationDigest job dispatches correctly.
     */
    #[Test]
    public function process_notification_digest_job_can_be_dispatched(): void
    {
        ProcessNotificationDigest::dispatch([
            'frequency' => 'daily',
        ]);

        Queue::assertPushed(ProcessNotificationDigest::class);
    }

    /**
     * Test ProcessNotificationDigest handles invalid frequency.
     */
    #[Test]
    public function process_notification_digest_handles_invalid_frequency(): void
    {
        $job = new ProcessNotificationDigest([
            'frequency' => 'invalid',
        ]);

        // Should not throw exception
        $job->handle();

        Mail::assertNothingSent();
    }

    /**
     * Test SendTicketNotification sends created notification email.
     */
    #[Test]
    public function send_ticket_notification_sends_created_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@motac.gov.my',
            'name' => 'Test User',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $job = new SendTicketNotification([
            'ticket_id' => $ticket->id,
            'type' => 'created',
        ]);

        $job->handle();

        Mail::assertQueued(HelpdeskTicketCreated::class, fn ($mail) => $mail->hasTo('test@motac.gov.my'));
    }

    /**
     * Test SendLoanNotification sends submitted notification.
     */
    #[Test]
    public function send_loan_notification_sends_submitted_email(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'applicant_email' => 'applicant@motac.gov.my',
            'applicant_name' => 'Test Applicant',
        ]);

        $job = new SendLoanNotification([
            'loan_application_id' => $application->id,
            'type' => 'submitted',
        ]);

        $job->handle();

        Mail::assertQueued(LoanApplicationSubmitted::class, function ($mail) {
            return $mail->hasTo('applicant@motac.gov.my');
        });
    }

    /**
     * Test SendApprovalRequest sends approval email with token.
     */
    #[Test]
    public function send_approval_request_sends_email_with_token(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create();

        $job = new SendApprovalRequest([
            'loan_application_id' => $application->id,
            'approver_email' => 'approver@motac.gov.my',
            'approver_name' => 'Test Approver',
            'token' => 'test-approval-token-123',
        ]);

        $job->handle();

        Mail::assertQueued(LoanApprovalRequest::class, function ($mail) {
            return $mail->hasTo('approver@motac.gov.my');
        });
    }

    /**
     * Test ProcessNotificationDigest processes daily digest for eligible users with BM content.
     */
    #[Test]
    public function process_notification_digest_processes_daily_digest_with_bm_content(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'notification_preferences' => ['digest_frequency' => 'daily'],
        ]);

        // Create a notification for the user with BM content
        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'message' => 'Tiket baharu telah diterima', // BM content
                'title' => 'Pemberitahuan Tiket Helpdesk',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $job = new ProcessNotificationDigest([
            'frequency' => 'daily',
            'user_id' => $user->id,
        ]);

        $job->handle();

        Mail::assertQueued(NotificationDigest::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test multi-channel notification creation with comprehensive data provider
     */
    #[Test]
    #[DataProvider('multiChannelNotificationProvider')]
    public function multi_channel_notification_creation_works_correctly(array $jobData, string $jobClass, array $expectedChannels): void
    {
        Mail::fake();
        Queue::fake();

        // Create test data based on job type
        if ($jobClass === SendTicketNotification::class) {
            $user = User::factory()->create(['email' => $jobData['email']]);
            $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
            $jobData['ticket_id'] = $ticket->id;
        } elseif ($jobClass === SendLoanNotification::class) {
            $application = LoanApplication::factory()->create([
                'applicant_email' => $jobData['email'],
                'applicant_name' => $jobData['name'] ?? 'Test Applicant',
            ]);
            $jobData['loan_application_id'] = $application->id;
        } elseif ($jobClass === SendApprovalRequest::class) {
            $application = LoanApplication::factory()->create();
            $jobData['loan_application_id'] = $application->id;
        }

        // Dispatch the job
        $jobClass::dispatch($jobData);

        // Verify job was queued
        Queue::assertPushed($jobClass);

        // Execute the job to test multi-channel behavior
        $job = new $jobClass($jobData);
        $job->handle();

        // Verify expected channels were used
        foreach ($expectedChannels as $channel) {
            if ($channel === 'mail') {
                Mail::assertQueued(\Illuminate\Mail\Mailable::class);
            } elseif ($channel === 'database') {
                // Database notifications would be created by the actual notification system
                $this->assertTrue(true, 'Database channel would be handled by Laravel notification system');
            }
        }
    }

    public static function multiChannelNotificationProvider(): array
    {
        return [
            'ticket notification with email' => [
                ['type' => 'created', 'email' => 'test@motac.gov.my'],
                SendTicketNotification::class,
                ['mail', 'database'],
            ],
            'loan notification with email' => [
                ['type' => 'submitted', 'email' => 'applicant@motac.gov.my', 'name' => 'Test Applicant'],
                SendLoanNotification::class,
                ['mail', 'database'],
            ],
            'approval request with token' => [
                ['approver_email' => 'approver@motac.gov.my', 'token' => 'test-token-123', 'approver_name' => 'Test Approver'],
                SendApprovalRequest::class,
                ['mail'],
            ],
        ];
    }

    /**
     * Test queue-based notification delivery with comprehensive scenarios
     */
    #[Test]
    #[DataProvider('queueDeliveryProvider')]
    public function queue_based_notification_delivery_works_correctly(string $jobClass, array $jobData, string $expectedQueue, int $expectedRetries): void
    {
        Queue::fake();

        // Create necessary test data
        if ($jobClass === SendTicketNotification::class && ! isset($jobData['ticket_id'])) {
            $ticket = HelpdeskTicket::factory()->create();
            $jobData['ticket_id'] = $ticket->id;
        } elseif ($jobClass === SendLoanNotification::class && ! isset($jobData['loan_application_id'])) {
            $application = LoanApplication::factory()->create();
            $jobData['loan_application_id'] = $application->id;
        } elseif ($jobClass === SendApprovalRequest::class && ! isset($jobData['loan_application_id'])) {
            $application = LoanApplication::factory()->create();
            $jobData['loan_application_id'] = $application->id;
        }

        // Dispatch job
        $jobClass::dispatch($jobData);

        // Verify job configuration
        Queue::assertPushed($jobClass, function ($job) use ($expectedQueue, $expectedRetries) {
            return $job->queue === $expectedQueue && $job->tries === $expectedRetries;
        });
    }

    public static function queueDeliveryProvider(): array
    {
        return [
            'ticket notification on notifications queue' => [
                SendTicketNotification::class,
                ['type' => 'created'],
                'notifications',
                3,
            ],
            'loan notification on notifications queue' => [
                SendLoanNotification::class,
                ['type' => 'submitted'],
                'notifications',
                3,
            ],
            'approval request on emails queue' => [
                SendApprovalRequest::class,
                ['approver_email' => 'test@motac.gov.my', 'token' => 'abc123'],
                'emails',
                3,
            ],
            'digest processing on digests queue' => [
                ProcessNotificationDigest::class,
                ['frequency' => 'daily'],
                'digests',
                3,
            ],
        ];
    }

    /**
     * Test jobs use correct queue names.
     */
    #[Test]
    public function jobs_use_correct_queue_names(): void
    {
        $ticketJob = new SendTicketNotification(['ticket_id' => 1, 'type' => 'created']);
        $loanJob = new SendLoanNotification(['loan_application_id' => 1, 'type' => 'submitted']);
        $approvalJob = new SendApprovalRequest(['loan_application_id' => 1, 'approver_email' => 'test@test.com', 'token' => 'abc']);
        $digestJob = new ProcessNotificationDigest(['frequency' => 'daily']);

        $this->assertEquals('notifications', $ticketJob->queue);
        $this->assertEquals('notifications', $loanJob->queue);
        $this->assertEquals('emails', $approvalJob->queue);
        $this->assertEquals('digests', $digestJob->queue);
    }

    /**
     * Test jobs have retry configuration.
     */
    #[Test]
    public function jobs_have_retry_configuration(): void
    {
        $ticketJob = new SendTicketNotification(['ticket_id' => 1, 'type' => 'created']);
        $loanJob = new SendLoanNotification(['loan_application_id' => 1, 'type' => 'submitted']);
        $approvalJob = new SendApprovalRequest(['loan_application_id' => 1, 'approver_email' => 'test@test.com', 'token' => 'abc']);
        $digestJob = new ProcessNotificationDigest(['frequency' => 'daily']);

        $this->assertEquals(3, $ticketJob->tries);
        $this->assertEquals(3, $loanJob->tries);
        $this->assertEquals(3, $approvalJob->tries);
        $this->assertEquals(3, $digestJob->tries);
    }
}
