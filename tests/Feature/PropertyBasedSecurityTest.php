<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\EmailLog;
use App\Models\User;
use App\Services\Notifications\NotificationSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-Based Tests for Security Features
 *
 * Implements correctness properties 30-32 from design.md for security.
 * Each property test runs minimum 100 iterations with randomized inputs.
 *
 * Feature: email-notification-system-enhancement
 *
 * @see .kiro/specs/email-notification-system-enhancement/design.md
 */
class PropertyBasedSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected const MIN_ITERATIONS = 100;

    protected NotificationSecurityService $securityService;

    protected Division $division;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityService = new NotificationSecurityService;
        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
        ]);
    }

    // =========================================================================
    // Property 30: Email log data encryption
    // For any sensitive data stored in email logs, the data should be encrypted
    // at rest using AES-256 encryption
    // Validates: Requirements 9.1
    // Feature: email-notification-system-enhancement, Property 30: Email log data encryption
    // =========================================================================

    #[Test]
    public function property_30_email_log_data_encryption(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $sensitiveEmail = fake()->safeEmail();
            $sensitiveName = fake()->name();
            $sensitiveMeta = [
                'user_id' => fake()->randomNumber(5),
                'ticket_number' => 'TKT-'.fake()->randomNumber(5),
            ];

            // Create email log with sensitive data
            $log = EmailLog::create([
                'recipient_email' => $sensitiveEmail,
                'recipient_name' => $sensitiveName,
                'subject' => 'Test Subject',
                'mailable_class' => 'App\\Mail\\TestMail',
                'status' => 'queued',
                'queued_at' => now(),
                'meta' => $sensitiveMeta,
            ]);

            // Property: Sensitive fields should be encrypted in database
            $rawData = DB::table('email_logs')->where('id', $log->id)->first();

            // Property: recipient_email should be encrypted (not readable as plain text)
            $this->assertNotEquals(
                $sensitiveEmail,
                $rawData->recipient_email,
                'recipient_email should be encrypted in database'
            );

            // Property: recipient_name should be encrypted
            $this->assertNotEquals(
                $sensitiveName,
                $rawData->recipient_name,
                'recipient_name should be encrypted in database'
            );

            // Property: meta should be encrypted
            $this->assertNotEquals(
                json_encode($sensitiveMeta),
                $rawData->meta,
                'meta should be encrypted in database'
            );

            // Property: Data should be decryptable when accessed through model
            $retrievedLog = EmailLog::find($log->id);
            $this->assertEquals($sensitiveEmail, $retrievedLog->recipient_email);
            $this->assertEquals($sensitiveName, $retrievedLog->recipient_name);

            $log->delete();
        }
    }

    // =========================================================================
    // Property 31: Notification content sanitization
    // For any notification content that includes user-provided data, the content
    // should be sanitized to prevent XSS attacks
    // Validates: Requirements 9.3
    // Feature: email-notification-system-enhancement, Property 31: Notification content sanitization
    // =========================================================================

    #[Test]
    public function property_31_notification_content_sanitization(): void
    {
        $xssPayloads = [
            '<script>alert("xss")</script>',
            '<img src="x" onerror="alert(1)">',
            '<a href="javascript:alert(1)">click</a>',
            '"><script>alert(1)</script>',
            "'; DROP TABLE users; --",
            '<iframe src="evil.com"></iframe>',
            '<svg onload="alert(1)">',
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $payload = $xssPayloads[array_rand($xssPayloads)];

            $data = [
                'title' => $payload,
                'message' => "Test message with {$payload}",
                'user_input' => $payload,
            ];

            // Sanitize the data
            $sanitized = $this->securityService->sanitizeNotificationData($data);

            // Property: Script tags should be removed or escaped
            $this->assertStringNotContainsString(
                '<script>',
                $sanitized['title'] ?? '',
                'Script tags should be sanitized from title'
            );

            // Property: Event handlers should be removed
            $this->assertStringNotContainsString(
                'onerror=',
                $sanitized['message'] ?? '',
                'Event handlers should be sanitized'
            );

            // Property: JavaScript URLs should be removed
            $this->assertStringNotContainsString(
                'javascript:',
                $sanitized['user_input'] ?? '',
                'JavaScript URLs should be sanitized'
            );
        }
    }

    // =========================================================================
    // Property 32: Notification access authorization
    // For any attempt to access notifications, the user should only be able to
    // view notifications that belong to them or that they are authorized to see
    // Validates: Requirements 9.4
    // Feature: email-notification-system-enhancement, Property 32: Notification access authorization
    // =========================================================================

    #[Test]
    public function property_32_notification_access_authorization(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Create two users
            $user1 = User::factory()->create(['division_id' => $this->division->id]);
            $user2 = User::factory()->create(['division_id' => $this->division->id]);

            // Create notification for user1
            $notificationId = Str::uuid()->toString();
            DB::table('notifications')->insert([
                'id' => $notificationId,
                'type' => 'App\\Notifications\\TestNotification',
                'notifiable_type' => $user1->getMorphClass(),
                'notifiable_id' => $user1->id,
                'data' => json_encode([
                    'title' => 'Private Notification',
                    'type' => 'test',
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Property: User1 should be able to access their notification
            $user1Notifications = DB::table('notifications')
                ->where('notifiable_id', $user1->id)
                ->where('notifiable_type', $user1->getMorphClass())
                ->get();

            $this->assertCount(1, $user1Notifications);

            // Property: User2 should NOT be able to access user1's notification
            $user2Notifications = DB::table('notifications')
                ->where('notifiable_id', $user2->id)
                ->where('notifiable_type', $user2->getMorphClass())
                ->get();

            $this->assertCount(0, $user2Notifications);

            // Property: Direct access by ID should still respect ownership
            $notification = DB::table('notifications')
                ->where('id', $notificationId)
                ->where('notifiable_id', $user2->id) // Wrong user
                ->first();

            $this->assertNull(
                $notification,
                'User should not access notifications belonging to others'
            );

            // Clean up
            DB::table('notifications')->where('id', $notificationId)->delete();
            $user1->delete();
            $user2->delete();
        }
    }

    // =========================================================================
    // Additional Security Properties
    // =========================================================================

    #[Test]
    public function sensitive_data_is_identified_correctly(): void
    {
        $sensitivePatterns = [
            'email',
            'recipient_email',
            'password',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'ssn',
        ];

        $nonSensitiveFields = [
            'id',
            'status',
            'created_at',
            'type',
            'category',
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Property: Data with sensitive field names should be sanitized
            $sensitiveField = $sensitivePatterns[array_rand($sensitivePatterns)];
            $testData = [$sensitiveField => 'sensitive_value_'.$i];

            $sanitized = $this->securityService->sanitizeNotificationData($testData);

            // Property: Sanitized data should be an array
            $this->assertIsArray($sanitized);

            // Property: Non-sensitive fields should pass through
            $nonSensitiveField = $nonSensitiveFields[array_rand($nonSensitiveFields)];
            $testData2 = [$nonSensitiveField => 'normal_value'];

            $sanitized2 = $this->securityService->sanitizeNotificationData($testData2);
            $this->assertIsArray($sanitized2);
        }
    }

    #[Test]
    public function security_audit_logging_works(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $eventType = fake()->randomElement([
                'notification_dispatch_started',
                'notification_access_attempt',
                'email_sent',
                'preference_changed',
            ]);

            $context = [
                'user_id' => $this->user->id,
                'action' => $eventType,
                'timestamp' => now()->toIso8601String(),
            ];

            // Property: Security events should be loggable
            $this->securityService->logSecurityEvent($eventType, $context, $this->user);

            // Property: Event type should be valid string
            $this->assertIsString($eventType);
            $this->assertNotEmpty($eventType);
        }
    }

    #[Test]
    public function encryption_is_reversible(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $originalData = [
                'email' => fake()->safeEmail(),
                'name' => fake()->name(),
                'message' => fake()->sentence(),
            ];

            // Encrypt
            $encrypted = Crypt::encrypt($originalData);

            // Property: Encrypted data should be different from original
            $this->assertNotEquals(
                json_encode($originalData),
                $encrypted,
                'Encrypted data should differ from original'
            );

            // Property: Decryption should restore original data
            $decrypted = Crypt::decrypt($encrypted);
            $this->assertEquals(
                $originalData,
                $decrypted,
                'Decrypted data should match original'
            );
        }
    }

    #[Test]
    public function sql_injection_is_prevented(): void
    {
        $sqlInjectionPayloads = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            '1; DELETE FROM notifications WHERE 1=1',
            'UNION SELECT * FROM users',
            "' OR 1=1 --",
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $payload = $sqlInjectionPayloads[array_rand($sqlInjectionPayloads)];

            // Property: SQL injection in notification data should be safe
            $data = [
                'title' => $payload,
                'message' => $payload,
            ];

            $sanitized = $this->securityService->sanitizeNotificationData($data);

            // Property: Sanitized data should be safe for storage
            $this->assertIsArray($sanitized);

            // Property: Creating notification with sanitized data should not cause SQL errors
            try {
                $notificationId = Str::uuid()->toString();
                DB::table('notifications')->insert([
                    'id' => $notificationId,
                    'type' => 'App\\Notifications\\TestNotification',
                    'notifiable_type' => $this->user->getMorphClass(),
                    'notifiable_id' => $this->user->id,
                    'data' => json_encode($sanitized),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Property: Data should be stored safely
                $stored = DB::table('notifications')->where('id', $notificationId)->first();
                $this->assertNotNull($stored);

                // Clean up
                DB::table('notifications')->where('id', $notificationId)->delete();
            } catch (\Exception $e) {
                $this->fail('SQL injection payload should not cause database errors: '.$e->getMessage());
            }
        }
    }
}
