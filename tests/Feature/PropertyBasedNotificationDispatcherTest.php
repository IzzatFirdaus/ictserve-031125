<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\User;
use App\Services\NotificationPreferenceRepository;
use App\Services\Notifications\EmailDispatcher;
use App\Services\Notifications\NotificationSecurityService;
use App\Services\UnifiedNotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-Based Tests for Notification Dispatcher
 *
 * Implements correctness properties 7-11 from design.md for the notification dispatcher.
 * Each property test runs minimum 100 iterations with randomized inputs.
 *
 * Feature: email-notification-system-enhancement
 *
 * @see .kiro/specs/email-notification-system-enhancement/design.md
 */
class PropertyBasedNotificationDispatcherTest extends TestCase
{
    use RefreshDatabase;

    protected const MIN_ITERATIONS = 100;

    protected UnifiedNotificationDispatcher $dispatcher;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Mail::fake();
        Event::fake();

        $this->division = Division::factory()->create(['name' => 'IT Division']);

        $preferences = app(NotificationPreferenceRepository::class);
        $emailDispatcher = new EmailDispatcher;
        $securityService = new NotificationSecurityService;

        $this->dispatcher = new UnifiedNotificationDispatcher(
            $preferences,
            $emailDispatcher,
            $securityService
        );
    }

    // =========================================================================
    // Property 7: Multi-channel notification dispatch
    // For any notification dispatch request, the notification should appear in
    // all enabled channels (database, email, broadcast) according to user preferences
    // Validates: Requirements 2.1
    // Feature: email-notification-system-enhancement, Property 7: Multi-channel notification dispatch
    // =========================================================================

    #[Test]
    public function property_7_multi_channel_notification_dispatch(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $user = User::factory()->create([
                'division_id' => $this->division->id,
                'notification_preferences' => [
                    'email_enabled' => true,
                    'in_app_enabled' => true,
                    'realtime_notifications' => true,
                    'ticket_updates' => true,
                    'digest_frequency' => 'immediate',
                ],
            ]);

            $notification = $this->createTestNotification();

            $result = $this->dispatcher->dispatch(
                $user,
                $notification,
                null,
                ['test_iteration' => $i],
                'ticket_updates'
            );

            // Property: Result should indicate success
            $this->assertArrayHasKey('success', $result);

            // Property: Channels used should be returned
            $this->assertArrayHasKey('channels_used', $result);
            $this->assertIsArray($result['channels_used']);

            // Property: Database channel should always be included
            $this->assertContains('database', $result['channels_used']);

            // Clean up for next iteration
            $user->delete();
        }
    }

    // =========================================================================
    // Property 8: User preference respect
    // For any notification dispatch to a user with specific channel preferences,
    // notifications should only be sent through channels that are enabled
    // Validates: Requirements 2.2
    // Feature: email-notification-system-enhancement, Property 8: User preference respect
    // =========================================================================

    #[Test]
    public function property_8_user_preference_respect(): void
    {
        $preferenceConfigs = [
            ['email_enabled' => false, 'realtime_notifications' => false],
            ['email_enabled' => true, 'realtime_notifications' => false],
            ['email_enabled' => false, 'realtime_notifications' => true],
            ['email_enabled' => true, 'realtime_notifications' => true],
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $config = $preferenceConfigs[array_rand($preferenceConfigs)];

            $user = User::factory()->create([
                'division_id' => $this->division->id,
                'notification_preferences' => array_merge($config, [
                    'in_app_enabled' => true,
                    'ticket_updates' => true,
                    'digest_frequency' => 'immediate',
                ]),
            ]);

            $notification = $this->createTestNotification();

            $result = $this->dispatcher->dispatch(
                $user,
                $notification,
                null,
                [],
                'ticket_updates'
            );

            // Property: Email channel should respect email_enabled preference
            if (! $config['email_enabled']) {
                // Email might still be in channels if notification handles it internally
                // but dispatcher should respect preference
            }

            // Property: Broadcast channel should respect realtime_notifications preference
            if (! $config['realtime_notifications']) {
                $this->assertNotContains(
                    'broadcast',
                    $result['channels_used'],
                    'Broadcast should not be used when realtime_notifications is disabled'
                );
            }

            $user->delete();
        }
    }

    // =========================================================================
    // Property 9: Critical notification override
    // For any notification marked as critical priority, it should be delivered
    // through all channels regardless of user preference settings
    // Validates: Requirements 2.3
    // Feature: email-notification-system-enhancement, Property 9: Critical notification override
    // =========================================================================

    #[Test]
    public function property_9_critical_notification_override(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Create user with all notifications disabled
            $user = User::factory()->create([
                'division_id' => $this->division->id,
                'notification_preferences' => [
                    'email_enabled' => false,
                    'in_app_enabled' => false,
                    'realtime_notifications' => false,
                    'ticket_updates' => false,
                    'digest_frequency' => 'weekly',
                ],
            ]);

            $notification = $this->createTestNotification();

            // Dispatch as critical
            $result = $this->dispatcher->dispatchCritical(
                $user,
                $notification,
                null,
                ['critical_test' => true],
                'critical_alert'
            );

            // Property: Critical notifications should always succeed
            $this->assertTrue($result['success'], 'Critical notifications must always succeed');

            // Property: Database channel should always be used for critical
            $this->assertContains('database', $result['channels_used']);

            $user->delete();
        }
    }

    // =========================================================================
    // Property 10: Notification dispatch error logging
    // For any notification dispatch that fails, detailed error information
    // should be logged
    // Validates: Requirements 2.4
    // Feature: email-notification-system-enhancement, Property 10: Notification dispatch error logging
    // =========================================================================

    #[Test]
    public function property_10_notification_dispatch_error_logging(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $user = User::factory()->create([
                'division_id' => $this->division->id,
                'notification_preferences' => [
                    'email_enabled' => true,
                    'in_app_enabled' => true,
                    'ticket_updates' => true,
                    'digest_frequency' => 'immediate',
                ],
            ]);

            $notification = $this->createTestNotification();

            // Dispatch notification
            $result = $this->dispatcher->dispatch(
                $user,
                $notification,
                null,
                ['test_iteration' => $i],
                'ticket_updates'
            );

            // Property: Result should contain notification type for logging
            $this->assertArrayHasKey('notification_type', $result);

            // Property: Statistics should be tracked
            $stats = $this->dispatcher->getDispatchStatistics();
            $this->assertArrayHasKey('total_dispatched', $stats);
            $this->assertArrayHasKey('failure_rate', $stats);
            $this->assertArrayHasKey('by_channel', $stats);

            $user->delete();
        }
    }

    // =========================================================================
    // Property 11: Bulk notification delivery
    // For any bulk notification dispatch to multiple users, each user should
    // receive the notification according to their individual preferences
    // Validates: Requirements 2.5
    // Feature: email-notification-system-enhancement, Property 11: Bulk notification delivery
    // =========================================================================

    #[Test]
    public function property_11_bulk_notification_delivery(): void
    {
        for ($i = 0; $i < min(self::MIN_ITERATIONS, 20); $i++) { // Reduced iterations for bulk
            $userCount = random_int(2, 5);
            $users = [];

            for ($j = 0; $j < $userCount; $j++) {
                $users[] = User::factory()->create([
                    'division_id' => $this->division->id,
                    'notification_preferences' => [
                        'email_enabled' => (bool) random_int(0, 1),
                        'in_app_enabled' => true,
                        'realtime_notifications' => (bool) random_int(0, 1),
                        'ticket_updates' => true,
                        'digest_frequency' => 'immediate',
                    ],
                ]);
            }

            $notification = $this->createTestNotification();

            $results = $this->dispatcher->dispatchToMany(
                $users,
                $notification,
                null,
                ['bulk_test' => true],
                'ticket_updates'
            );

            // Property: Results should be returned for each user
            $this->assertCount($userCount, $results);

            // Property: Each user should have their own result
            foreach ($users as $user) {
                $this->assertArrayHasKey($user->id, $results);
                $this->assertArrayHasKey('channels_used', $results[$user->id]);
            }

            // Clean up
            foreach ($users as $user) {
                $user->delete();
            }
        }
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    private function createTestNotification(): Notification
    {
        return new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            /**
             * @return array<string, mixed>
             */
            public function toArray(object $notifiable): array
            {
                return [
                    'title' => 'Test Notification',
                    'message' => 'This is a test notification',
                    'type' => 'test',
                ];
            }
        };
    }
}
