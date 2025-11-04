<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Listeners\UpdateEmailLogOnFailure;
use App\Listeners\UpdateEmailLogOnSend;
use App\Mail\LoanStatusUpdated;
use App\Models\EmailLog;
use App\Models\LoanApplication;
use App\Services\Notifications\EmailDispatcher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_dispatcher_queues_email_and_logs_entry(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create();

        $dispatcher = app(EmailDispatcher::class);
        $log = $dispatcher->queue(
            new LoanStatusUpdated($application),
            'user@example.com',
            'User Name'
        );

        $this->assertDatabaseHas('email_logs', [
            'id' => $log->id,
            'recipient_email' => 'user@example.com',
            'status' => 'queued',
        ]);

        Mail::assertQueued(LoanStatusUpdated::class, function (LoanStatusUpdated $mail) use ($log) {
            $emailLog = (function () {
                return $this->emailLog ?? null;
            })->call($mail);

            return $emailLog instanceof EmailLog && $emailLog->id === $log->id;
        });
    }

    public function test_email_log_marked_sent_when_message_sent_event_fires(): void
    {
        $log = EmailLog::factory()->create([
            'status' => 'queued',
        ]);

        $email = (new SymfonyEmail)
            ->from(new Address('no-reply@example.com', 'ICTServe'))
            ->to('staff@example.com')
            ->subject('Status Update')
            ->text('Test body');

        $email->getHeaders()->addTextHeader('X-Email-Log-Id', (string) $log->id);
        $email->getHeaders()->addIdHeader('Message-ID', 'test-message-id@example.com');

        $envelope = new Envelope(
            new Address('no-reply@example.com', 'ICTServe'),
            [new Address('staff@example.com')]
        );

        $event = new MessageSent(
            new SentMessage(new SymfonySentMessage($email, $envelope))
        );

        app(UpdateEmailLogOnSend::class)->handle($event);

        $log->refresh();

        $this->assertSame('sent', $log->status);
        $this->assertSame('test-message-id@example.com', $log->message_id);
        $this->assertNotNull($log->sent_at);
    }

    public function test_email_log_marked_failed_when_mail_job_fails(): void
    {
        $application = LoanApplication::factory()->create();
        $log = EmailLog::factory()->create();

        $mailable = (new LoanStatusUpdated($application))->withEmailLog($log);

        $job = new SendQueuedMailable($mailable);

        $payload = [
            'displayName' => SendQueuedMailable::class,
            'data' => [
                'command' => serialize($job),
            ],
        ];

        $jobMock = $this->createMock(\Illuminate\Contracts\Queue\Job::class);
        $jobMock->method('payload')->willReturn($payload);

        $event = new JobFailed('redis', $jobMock, new \Exception('Simulated failure'));

        app(UpdateEmailLogOnFailure::class)->handle($event);

        $log->refresh();

        $this->assertSame('failed', $log->status);
        $this->assertSame('Simulated failure', $log->status_message);
    }
}
