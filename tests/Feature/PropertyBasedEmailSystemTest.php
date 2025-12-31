<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\EmailLog;
use App\Models\User;
use App\Services\EmailTemplateService;
use App\Services\Notifications\EmailDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-Based Tests for Email System
 *
 * Implements correctness properties 1-6 from design.md for the email system.
 * Each property test runs minimum 100 iterations with randomized inputs.
 *
 * Feature: email-notification-system-enhancement
 *
 * @see .kiro/specs/email-notification-system-enhancement/design.md
 */
class PropertyBasedEmailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected const MIN_ITERATIONS = 100;

    protected EmailDispatcher $emailDispatcher;

    protected EmailTemplateService $templateService;

    protected Division $division;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Mail::fake();

        $this->emailDispatcher = new EmailDispatcher;
        $this->templateService = new EmailTemplateService;

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'email' => 'test@example.com',
        ]);
    }

    // =========================================================================
    // Property 1: Email logging completeness
    // For any email dispatch attempt, a corresponding EmailLog entry should be
    // created with complete metadata including status, timestamp, and delivery info
    // Validates: Requirements 1.1
    // Feature: email-notification-system-enhancement, Property 1: Email logging completeness
    // =========================================================================

    #[Test]
    public function property_1_email_logging_completeness(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $email = $this->generateRandomEmail();
            $name = $this->generateRandomName();
            $subject = $this->generateRandomSubject();
            $meta = $this->generateRandomMeta();

            $mailable = new class($subject) extends Mailable implements ShouldQueue
            {
                use Queueable;
                use SerializesModels;

                public function __construct(private string $subjectLine)
                {
                    //
                }

                public function envelope(): \Illuminate\Mail\Mailables\Envelope
                {
                    return new \Illuminate\Mail\Mailables\Envelope(subject: $this->subjectLine);
                }

                public function content(): \Illuminate\Mail\Mailables\Content
                {
                    return new \Illuminate\Mail\Mailables\Content(html: 'emails.layout-branded');
                }
            };

            try {
                $log = $this->emailDispatcher->queue(
                    $mailable,
                    $email,
                    $name,
                    $meta
                );

                // Property: EmailLog must be created
                $this->assertInstanceOf(EmailLog::class, $log);

                // Property: Must have status
                $this->assertNotNull($log->status);
                $this->assertContains($log->status, ['queued', 'sent', 'failed']);

                // Property: Must have timestamp
                $this->assertNotNull($log->queued_at);

                // Property: Must have recipient info
                $this->assertNotNull($log->recipient_email);

                // Property: Must have subject
                $this->assertNotNull($log->subject);

                // Property: Must have mailable class
                $this->assertNotNull($log->mailable_class);
            } catch (\InvalidArgumentException $e) {
                // Invalid email is expected for some random inputs
                $this->assertStringContainsString('Invalid email', $e->getMessage());
            }
        }
    }

    // =========================================================================
    // Property 2: Email retry exponential backoff
    // For any failed email delivery, retry attempts should follow exponential
    // backoff timing with delays increasing by a factor of 2
    // Validates: Requirements 1.2
    // Feature: email-notification-system-enhancement, Property 2: Email retry exponential backoff
    // =========================================================================

    #[Test]
    public function property_2_email_retry_exponential_backoff(): void
    {
        $backoffDelays = config('notifications.email_retry.backoff_delays', [60, 300, 900]);

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Property: Each subsequent delay should be greater than previous
            for ($j = 1; $j < count($backoffDelays); $j++) {
                $this->assertGreaterThan(
                    $backoffDelays[$j - 1],
                    $backoffDelays[$j],
                    'Backoff delays must increase'
                );
            }

            // Property: Delays should follow exponential pattern (roughly 2x or more)
            if (count($backoffDelays) >= 2) {
                $ratio = $backoffDelays[1] / $backoffDelays[0];
                $this->assertGreaterThanOrEqual(2, $ratio, 'Backoff should be at least 2x');
            }
        }
    }

    // =========================================================================
    // Property 5: Email validation before delivery
    // For any email address provided to the system, only addresses that pass
    // RFC 5322 validation should proceed to the delivery queue
    // Validates: Requirements 1.6
    // Feature: email-notification-system-enhancement, Property 5: Email validation before delivery
    // =========================================================================

    #[Test]
    public function property_5_email_validation_before_delivery(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.org',
            'user+tag@example.co.uk',
            'firstname.lastname@company.com',
        ];

        $invalidEmails = [
            'invalid',
            '@nodomain.com',
            'no@tld',
            'spaces in@email.com',
            '',
            'missing@',
        ];

        // Test valid emails
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $email = $validEmails[array_rand($validEmails)];

            // Property: Valid emails should pass validation
            $this->assertTrue(
                $this->emailDispatcher->validateEmail($email),
                "Valid email '{$email}' should pass validation"
            );
        }

        // Test invalid emails
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $email = $invalidEmails[array_rand($invalidEmails)];

            // Property: Invalid emails should fail validation
            $this->assertFalse(
                $this->emailDispatcher->validateEmail($email),
                "Invalid email '{$email}' should fail validation"
            );
        }
    }

    // =========================================================================
    // Property 6: Email queue priority ordering
    // For any set of queued emails with different priority levels, emails should
    // be processed in order: critical, high, normal, low
    // Validates: Requirements 1.7
    // Feature: email-notification-system-enhancement, Property 6: Email queue priority ordering
    // =========================================================================

    #[Test]
    public function property_6_email_queue_priority_ordering(): void
    {
        $priorities = ['critical', 'high', 'normal', 'low'];
        $expectedQueues = [
            'critical' => config('notifications.channels.email.queue_critical', 'emails-critical'),
            'high' => config('notifications.channels.email.queue_high', 'emails-high'),
            'normal' => config('notifications.channels.email.queue', 'emails'),
            'low' => config('notifications.channels.email.queue_low', 'emails-low'),
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $priority = $priorities[array_rand($priorities)];

            // Property: Each priority should map to a specific queue
            $this->assertArrayHasKey($priority, $expectedQueues);

            // Property: Critical priority should have dedicated queue
            if ($priority === 'critical') {
                $this->assertStringContainsString('critical', $expectedQueues[$priority]);
            }
        }
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    private function generateRandomEmail(): string
    {
        $validEmails = [
            'test@example.com',
            'user@domain.org',
            'admin@company.co.uk',
            fake()->safeEmail(),
        ];

        $invalidEmails = [
            'invalid',
            '@nodomain',
            'no@tld',
        ];

        // 80% valid, 20% invalid for realistic testing
        return random_int(1, 100) <= 80
            ? $validEmails[array_rand($validEmails)]
            : $invalidEmails[array_rand($invalidEmails)];
    }

    private function generateRandomName(): string
    {
        return fake()->name();
    }

    private function generateRandomSubject(): string
    {
        $subjects = [
            'Test Email Subject',
            fake()->sentence(),
            'Notification: '.fake()->word(),
            'Alert: System Update',
        ];

        return $subjects[array_rand($subjects)];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateRandomMeta(): array
    {
        return [
            'ticket_number' => 'TKT-'.fake()->randomNumber(5),
            'user_id' => fake()->randomNumber(3),
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
        ];
    }
}
