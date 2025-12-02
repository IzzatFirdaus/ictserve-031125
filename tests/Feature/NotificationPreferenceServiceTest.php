<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\NotificationPreferenceServiceInterface;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function test_service_is_bound_in_container(): void
    {
        $service = app(NotificationPreferenceServiceInterface::class);

        $this->assertInstanceOf(NotificationPreferenceService::class, $service);
    }

    /**
     * Test getPreferences returns default preferences for new user
     */
    public function test_get_preferences_returns_defaults_for_new_user(): void
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
    public function test_get_preferences_merges_with_defaults(): void
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
    public function test_update_preferences_updates_user(): void
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
    public function test_update_preferences_throws_for_invalid_keys(): void
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid preference keys');

        $this->service->updatePreferences($user, [
            'invalid_key' => true,
        ]);
    }

    /**
     * Test updatePreferences throws exception for invalid digest frequency
     */
    public function test_update_preferences_throws_for_invalid_digest_frequency(): void
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid digest_frequency value');

        $this->service->updatePreferences($user, [
            'digest_frequency' => 'invalid',
        ]);
    }

    /**
     * Test shouldSendEmail returns true for immediate digest frequency
     */
    public function test_should_send_email_returns_true_for_immediate(): void
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
    public function test_should_send_email_returns_false_for_daily_digest(): void
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
    public function test_should_send_email_returns_false_when_email_disabled(): void
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
    public function test_should_send_email_returns_true_for_critical_types(): void
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
    public function test_get_digest_frequency_returns_correct_value(): void
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
    public function test_get_digest_frequency_returns_immediate_as_default(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $result = $this->service->getDigestFrequency($user);

        $this->assertEquals('immediate', $result);
    }

    /**
     * Test isInAppEnabled returns correct value
     */
    public function test_is_in_app_enabled_returns_correct_value(): void
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
    public function test_is_realtime_enabled_returns_correct_value(): void
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
    public function test_get_channels_for_type_always_includes_database(): void
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
    public function test_get_channels_for_type_includes_mail_when_enabled(): void
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
    public function test_get_channels_for_type_includes_broadcast_when_realtime_enabled(): void
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
    public function test_reset_to_defaults_resets_all_preferences(): void
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
     * Test getAvailableNotificationTypes returns expected types
     */
    public function test_get_available_notification_types_returns_expected_types(): void
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

        // SLA alerts should not be user controllable (critical)
        $this->assertFalse($types['sla_alerts']['user_controllable']);
    }

    /**
     * Test shouldQueueForDigest returns true for non-immediate frequency
     */
    public function test_should_queue_for_digest_returns_true_for_daily(): void
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
    public function test_should_queue_for_digest_returns_false_for_immediate(): void
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
    public function test_should_queue_for_digest_returns_false_for_critical(): void
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
