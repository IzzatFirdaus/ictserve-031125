<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\NotificationPreferenceServiceInterface;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function serviceIsBoundInContainer(): void
    {
        $service = app(NotificationPreferenceServiceInterface::class);

        $this->assertInstanceOf(NotificationPreferenceService::class, $service);
    }

    /**
     * Test getPreferences returns default preferences for new user
     */
    #[Test]
    public function getPreferencesReturnsDefaultsForNewUser(): void
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
    public function getPreferencesMergesWithDefaults(): void
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
    public function updatePreferencesUpdatesUser(): void
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
    public function updatePreferencesThrowsForInvalidKeys(): void
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
    #[Test]
    public function updatePreferencesThrowsForInvalidDigestFrequency(): void
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
    #[Test]
    public function shouldSendEmailReturnsTrueForImmediate(): void
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
    public function shouldSendEmailReturnsFalseForDailyDigest(): void
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
    public function shouldSendEmailReturnsFalseWhenEmailDisabled(): void
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
    public function shouldSendEmailReturnsTrueForCriticalTypes(): void
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
    public function getDigestFrequencyReturnsCorrectValue(): void
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
    public function getDigestFrequencyReturnsImmediateAsDefault(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $result = $this->service->getDigestFrequency($user);

        $this->assertEquals('immediate', $result);
    }

    /**
     * Test isInAppEnabled returns correct value
     */
    #[Test]
    public function isInAppEnabledReturnsCorrectValue(): void
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
    public function isRealtimeEnabledReturnsCorrectValue(): void
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
    public function getChannelsForTypeAlwaysIncludesDatabase(): void
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
    public function getChannelsForTypeIncludesMailWhenEnabled(): void
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
    public function getChannelsForTypeIncludesBroadcastWhenRealtimeEnabled(): void
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
    public function resetToDefaultsResetsAllPreferences(): void
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
    #[Test]
    public function getAvailableNotificationTypesReturnsExpectedTypes(): void
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
    #[Test]
    public function shouldQueueForDigestReturnsTrueForDaily(): void
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
    public function shouldQueueForDigestReturnsFalseForImmediate(): void
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
    public function shouldQueueForDigestReturnsFalseForCritical(): void
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
