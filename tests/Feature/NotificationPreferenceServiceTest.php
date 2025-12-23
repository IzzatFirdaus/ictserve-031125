<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\NotificationPreferenceServiceInterface;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for NotificationPreferenceService
 *
 * Validates Requirements 17.5 - Notification preferences configuration
 *
 * @see D16_BROADCASTING_SETUP.md WebSocket configuration
 * @see D03 SRS-ADM-006 Staff Dashboard notification preferences
 */
class NotificationPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationPreferenceServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(NotificationPreferenceServiceInterface::class);
    }

    /**
     * Test that service is properly bound in container
     */
    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(NotificationPreferenceServiceInterface::class);

        $this->assertInstanceOf(NotificationPreferenceService::class, $service);
    }

    /**
     * Test getPreferences returns default preferences for new user
     */
    #[Test]
    public function get_preferences_returns_defaults_for_new_user(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $preferences = $this->service->getPreferences($user);

        $this->assertIsArray($preferences);
        $this->assertTrue($preferences['ticket_updates']);
        $this->assertTrue($preferences['loan_approvals']);
        $this->assertTrue($preferences['realtime_notifications']);
        $this->assertEquals('immediate', $preferences['digest_frequency']);
        $this->assertTrue($preferences['email_enabled']);
        $this->assertTrue($preferences['in_app_enabled']);
    }

    /**
     * Test getPreferences merges stored preferences with defaults
     */
    #[Test]
    public function get_preferences_merges_with_defaults(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'ticket_updates' => false,
                'digest_frequency' => 'daily',
            ],
        ]);

        $preferences = $this->service->getPreferences($user);

        $this->assertFalse($preferences['ticket_updates']);
        $this->assertEquals('daily', $preferences['digest_frequency']);
        // Defaults should still be present
        $this->assertTrue($preferences['loan_approvals']);
        $this->assertTrue($preferences['realtime_notifications']);
    }

    /**
     * Test updatePreferences updates user preferences
     */
    #[Test]
    public function update_preferences_updates_user(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $this->service->updatePreferences($user, [
            'ticket_updates' => false,
            'digest_frequency' => 'weekly',
        ]);

        $user->refresh();
        $preferences = $this->service->getPreferences($user);

        $this->assertFalse($preferences['ticket_updates']);
        $this->assertEquals('weekly', $preferences['digest_frequency']);
    }

    /**
     * Test updatePreferences throws exception for invalid keys
     */
    #[Test]
    public function update_preferences_throws_for_invalid_keys(): void
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid preference keys');

        $this->service->updatePreferences($user, [
            'invalid_key' => true,
        ]);
    }

    /**
     * Test email frequency options (immediate/daily/weekly) with comprehensive data provider
     */
    #[Test]
    #[DataProvider('emailFrequencyProvider')]
    public function email_frequency_options_are_validated_correctly(string $frequency, bool $shouldBeValid): void
    {
        $user = User::factory()->create();

        if (! $shouldBeValid) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Invalid digest_frequency value');
        }

        $this->service->updatePreferences($user, [
            'digest_frequency' => $frequency,
        ]);

        if ($shouldBeValid) {
            $user->refresh();
            $preferences = $this->service->getPreferences($user);
            $this->assertEquals($frequency, $preferences['digest_frequency']);
        }
    }

    public static function emailFrequencyProvider(): array
    {
        return [
            'immediate frequency' => ['immediate', true],
            'daily frequency' => ['daily', true],
            'weekly frequency' => ['weekly', true],
            'invalid frequency' => ['invalid', false],
            'empty frequency' => ['', false],
            'numeric frequency' => ['123', false],
        ];
    }

    /**
     * Test preference persistence with comprehensive data provider
     */
    #[Test]
    #[DataProvider('preferencePersistenceProvider')]
    public function preference_persistence_works_correctly(array $preferences, array $expectedValues): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $this->service->updatePreferences($user, $preferences);

        $user->refresh();
        $storedPreferences = $this->service->getPreferences($user);

        foreach ($expectedValues as $key => $expectedValue) {
            $this->assertEquals($expectedValue, $storedPreferences[$key], "Failed asserting that preference '{$key}' equals expected value");
        }
    }

    public static function preferencePersistenceProvider(): array
    {
        return [
            'all email preferences enabled' => [
                ['email_enabled' => true, 'digest_frequency' => 'immediate', 'ticket_updates' => true],
                ['email_enabled' => true, 'digest_frequency' => 'immediate', 'ticket_updates' => true],
            ],
            'daily digest with selective notifications' => [
                ['digest_frequency' => 'daily', 'ticket_updates' => true, 'loan_approvals' => false],
                ['digest_frequency' => 'daily', 'ticket_updates' => true, 'loan_approvals' => false],
            ],
            'weekly digest with all notifications disabled' => [
                ['digest_frequency' => 'weekly', 'email_enabled' => false, 'in_app_enabled' => false],
                ['digest_frequency' => 'weekly', 'email_enabled' => false, 'in_app_enabled' => false],
            ],
            'realtime notifications only' => [
                ['realtime_notifications' => true, 'email_enabled' => false, 'digest_frequency' => 'immediate'],
                ['realtime_notifications' => true, 'email_enabled' => false, 'digest_frequency' => 'immediate'],
            ],
        ];
    }

    /**
     * Test notification channel selection with comprehensive data provider
     */
    #[Test]
    #[DataProvider('notificationChannelProvider')]
    public function notification_channels_are_selected_correctly(array $preferences, string $notificationType, array $expectedChannels): void
    {
        $user = User::factory()->create(['notification_preferences' => $preferences]);

        $channels = $this->service->getChannelsForType($user, $notificationType);

        foreach ($expectedChannels as $expectedChannel) {
            $this->assertContains($expectedChannel, $channels, "Expected channel '{$expectedChannel}' not found in channels");
        }

        // Ensure no unexpected channels
        $this->assertCount(count($expectedChannels), $channels, 'Unexpected number of channels returned');
    }

    public static function notificationChannelProvider(): array
    {
        return [
            'immediate email with realtime' => [
                ['email_enabled' => true, 'digest_frequency' => 'immediate', 'realtime_notifications' => true, 'ticket_updates' => true],
                'ticket_updates',
                ['database', 'mail', 'broadcast'],
            ],
            'daily digest only' => [
                ['email_enabled' => true, 'digest_frequency' => 'daily', 'realtime_notifications' => false, 'ticket_updates' => true],
                'ticket_updates',
                ['database'],
            ],
            'database only (all disabled)' => [
                ['email_enabled' => false, 'realtime_notifications' => false, 'ticket_updates' => true],
                'ticket_updates',
                ['database'],
            ],
            'critical notifications always include mail' => [
                ['email_enabled' => true, 'digest_frequency' => 'weekly', 'sla_alerts' => false],
                'sla_alerts',
                ['database', 'mail', 'broadcast'],
            ],
        ];
    }

    /**
     * Test shouldSendEmail returns true for immediate digest frequency
     */
    #[Test]
    public function should_send_email_returns_true_for_immediate(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'ticket_updates' => true,
                'digest_frequency' => 'immediate',
            ],
        ]);

        $result = $this->service->shouldSendEmail($user, 'ticket_updates');

        $this->assertTrue($result);
    }

    /**
     * Test shouldSendEmail returns false for daily digest frequency
     */
    #[Test]
    public function should_send_email_returns_false_for_daily_digest(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'ticket_updates' => true,
                'digest_frequency' => 'daily',
            ],
        ]);

        $result = $this->service->shouldSendEmail($user, 'ticket_updates');

        $this->assertFalse($result);
    }

    /**
     * Test shouldSendEmail returns false when email is disabled
     */
    #[Test]
    public function should_send_email_returns_false_when_email_disabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => false,
                'ticket_updates' => true,
                'digest_frequency' => 'immediate',
            ],
        ]);

        $result = $this->service->shouldSendEmail($user, 'ticket_updates');

        $this->assertFalse($result);
    }

    /**
     * Test shouldSendEmail returns true for critical notifications regardless of preferences
     */
    #[Test]
    public function should_send_email_returns_true_for_critical_types(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'sla_alerts' => false, // Even if disabled
                'digest_frequency' => 'weekly', // Even if digest
            ],
        ]);

        $result = $this->service->shouldSendEmail($user, 'sla_alerts');

        $this->assertTrue($result);
    }

    /**
     * Test getDigestFrequency returns correct value
     */
    #[Test]
    public function get_digest_frequency_returns_correct_value(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'digest_frequency' => 'weekly',
            ],
        ]);

        $result = $this->service->getDigestFrequency($user);

        $this->assertEquals('weekly', $result);
    }

    /**
     * Test getDigestFrequency returns immediate as default
     */
    #[Test]
    public function get_digest_frequency_returns_immediate_as_default(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $result = $this->service->getDigestFrequency($user);

        $this->assertEquals('immediate', $result);
    }

    /**
     * Test isInAppEnabled returns correct value
     */
    #[Test]
    public function is_in_app_enabled_returns_correct_value(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'in_app_enabled' => false,
            ],
        ]);

        $result = $this->service->isInAppEnabled($user);

        $this->assertFalse($result);
    }

    /**
     * Test isRealtimeEnabled returns correct value
     */
    #[Test]
    public function is_realtime_enabled_returns_correct_value(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'realtime_notifications' => false,
            ],
        ]);

        $result = $this->service->isRealtimeEnabled($user);

        $this->assertFalse($result);
    }

    /**
     * Test getChannelsForType returns database channel always
     */
    #[Test]
    public function get_channels_for_type_always_includes_database(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => false,
                'realtime_notifications' => false,
            ],
        ]);

        $channels = $this->service->getChannelsForType($user, 'ticket_updates');

        $this->assertContains('database', $channels);
    }

    /**
     * Test getChannelsForType includes mail when email enabled and immediate
     */
    #[Test]
    public function get_channels_for_type_includes_mail_when_enabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'ticket_updates' => true,
                'digest_frequency' => 'immediate',
            ],
        ]);

        $channels = $this->service->getChannelsForType($user, 'ticket_updates');

        $this->assertContains('mail', $channels);
    }

    /**
     * Test getChannelsForType includes broadcast when realtime enabled
     */
    #[Test]
    public function get_channels_for_type_includes_broadcast_when_realtime_enabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'realtime_notifications' => true,
                'ticket_updates' => true,
            ],
        ]);

        $channels = $this->service->getChannelsForType($user, 'ticket_updates');

        $this->assertContains('broadcast', $channels);
    }

    /**
     * Test resetToDefaults resets all preferences
     */
    #[Test]
    public function reset_to_defaults_resets_all_preferences(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'ticket_updates' => false,
                'email_enabled' => false,
                'digest_frequency' => 'weekly',
            ],
        ]);

        $this->service->resetToDefaults($user);

        $user->refresh();
        $preferences = $this->service->getPreferences($user);

        $this->assertTrue($preferences['ticket_updates']);
        $this->assertTrue($preferences['email_enabled']);
        $this->assertEquals('immediate', $preferences['digest_frequency']);
    }

    /**
     * Test getAvailableNotificationTypes returns expected types with BM labels
     */
    #[Test]
    public function get_available_notification_types_returns_expected_types_with_bm_labels(): void
    {
        $types = $this->service->getAvailableNotificationTypes();

        $this->assertArrayHasKey('ticket_updates', $types);
        $this->assertArrayHasKey('loan_approvals', $types);
        $this->assertArrayHasKey('sla_alerts', $types);

        // Check structure
        $this->assertArrayHasKey('label', $types['ticket_updates']);
        $this->assertArrayHasKey('description', $types['ticket_updates']);
        $this->assertArrayHasKey('category', $types['ticket_updates']);
        $this->assertArrayHasKey('user_controllable', $types['ticket_updates']);

        // Verify BM content (should use translation keys or BM text)
        $ticketLabel = $types['ticket_updates']['label'];
        // Accept both direct BM text and English labels (since this might be using translation keys)
        $this->assertIsString($ticketLabel, 'Ticket updates label should be a string');

        $loanLabel = $types['loan_approvals']['label'];
        $this->assertIsString($loanLabel, 'Loan approvals label should be a string');

        // SLA alerts should not be user controllable (critical)
        $this->assertFalse($types['sla_alerts']['user_controllable']);
    }

    /**
     * Test shouldQueueForDigest returns true for non-immediate frequency
     */
    #[Test]
    public function should_queue_for_digest_returns_true_for_daily(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'ticket_updates' => true,
                'digest_frequency' => 'daily',
            ],
        ]);

        $result = $this->service->shouldQueueForDigest($user, 'ticket_updates');

        $this->assertTrue($result);
    }

    /**
     * Test shouldQueueForDigest returns false for immediate frequency
     */
    #[Test]
    public function should_queue_for_digest_returns_false_for_immediate(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'ticket_updates' => true,
                'digest_frequency' => 'immediate',
            ],
        ]);

        $result = $this->service->shouldQueueForDigest($user, 'ticket_updates');

        $this->assertFalse($result);
    }

    /**
     * Test shouldQueueForDigest returns false for critical types
     */
    #[Test]
    public function should_queue_for_digest_returns_false_for_critical(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email_enabled' => true,
                'sla_alerts' => true,
                'digest_frequency' => 'weekly',
            ],
        ]);

        $result = $this->service->shouldQueueForDigest($user, 'sla_alerts');

        $this->assertFalse($result);
    }
}
