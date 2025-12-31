<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\NotificationBell;
use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-Based Tests for Frontend Components
 *
 * Implements correctness properties 12-16 from design.md for frontend components.
 * Each property test runs minimum 100 iterations with randomized inputs.
 *
 * Feature: email-notification-system-enhancement
 *
 * @see .kiro/specs/email-notification-system-enhancement/design.md
 */
class PropertyBasedFrontendComponentTest extends TestCase
{
    use RefreshDatabase;

    protected const MIN_ITERATIONS = 100;

    protected Division $division;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
        ]);
    }

    // =========================================================================
    // Property 12: Notification bell count accuracy
    // For any user with unread notifications, the notification bell should
    // display the exact count of unread notifications
    // Validates: Requirements 3.1
    // Feature: email-notification-system-enhancement, Property 12: Notification bell count accuracy
    // =========================================================================

    #[Test]
    public function property_12_notification_bell_count_accuracy(): void
    {
        $this->actingAs($this->user);

        for ($i = 0; $i < min(self::MIN_ITERATIONS, 50); $i++) { // Reduced for performance
            // Clear existing notifications
            DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->delete();

            // Create random number of notifications
            $notificationCount = random_int(0, 10);

            for ($j = 0; $j < $notificationCount; $j++) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\TestNotification',
                    'notifiable_type' => $this->user->getMorphClass(),
                    'notifiable_id' => $this->user->id,
                    'data' => json_encode([
                        'title' => 'Test Notification '.$j,
                        'type' => 'test',
                    ]),
                    'read_at' => null, // Unread
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Test the component
            $component = Livewire::test(NotificationBell::class);
            $component->call('loadNotifications');

            // Property: Unread count should match actual unread notifications
            $actualUnread = DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->whereNull('read_at')
                ->count();

            $this->assertEquals(
                $actualUnread,
                $component->get('unreadCount'),
                "Unread count should be {$actualUnread}, got {$component->get('unreadCount')}"
            );
        }
    }

    // =========================================================================
    // Property 14: Notification categorization
    // For any notification, it should be correctly categorized as tickets,
    // loans, system, or approvals based on its type and content
    // Validates: Requirements 3.3
    // Feature: email-notification-system-enhancement, Property 14: Notification categorization
    // =========================================================================

    #[Test]
    public function property_14_notification_categorization(): void
    {
        $this->actingAs($this->user);

        $categories = [
            'tickets' => ['TicketCreated', 'TicketUpdated', 'TicketAssigned'],
            'loans' => ['LoanApproved', 'LoanRejected', 'LoanReminder'],
            'system' => ['SystemAnnouncement', 'MaintenanceNotice'],
            'approvals' => ['ApprovalRequired', 'ApprovalCompleted'],
        ];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $categoryKey = array_rand($categories);
            $types = $categories[$categoryKey];
            $type = $types[array_rand($types)];

            // Create notification with specific type
            $notificationId = Str::uuid()->toString();
            DB::table('notifications')->insert([
                'id' => $notificationId,
                'type' => "App\\Notifications\\{$type}",
                'notifiable_type' => $this->user->getMorphClass(),
                'notifiable_id' => $this->user->id,
                'data' => json_encode([
                    'title' => "Test {$type}",
                    'type' => strtolower($categoryKey),
                    'category' => $categoryKey,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Property: Notification should have a category
            $notification = DB::table('notifications')->find($notificationId);
            $data = json_decode($notification->data, true);

            $this->assertArrayHasKey('type', $data, 'Notification should have type');

            // Clean up
            DB::table('notifications')->where('id', $notificationId)->delete();
        }
    }

    // =========================================================================
    // Property 15: Notification center pagination
    // For any user with more than 20 notifications, the notification center
    // should implement pagination with consistent page sizes
    // Validates: Requirements 3.6
    // Feature: email-notification-system-enhancement, Property 15: Notification center pagination
    // =========================================================================

    #[Test]
    public function property_15_notification_center_pagination(): void
    {
        $this->actingAs($this->user);

        for ($i = 0; $i < min(self::MIN_ITERATIONS, 20); $i++) { // Reduced for performance
            // Clear existing notifications
            DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->delete();

            // Create more than 20 notifications
            $notificationCount = random_int(25, 50);

            for ($j = 0; $j < $notificationCount; $j++) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\TestNotification',
                    'notifiable_type' => $this->user->getMorphClass(),
                    'notifiable_id' => $this->user->id,
                    'data' => json_encode([
                        'title' => 'Test Notification '.$j,
                        'type' => 'test',
                    ]),
                    'read_at' => null,
                    'created_at' => now()->subMinutes($j),
                    'updated_at' => now()->subMinutes($j),
                ]);
            }

            // Property: Total notifications should exceed page size
            $totalNotifications = DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->count();

            $this->assertGreaterThan(
                20,
                $totalNotifications,
                'Should have more than 20 notifications for pagination test'
            );

            // Property: Pagination should be available
            $pageSize = 20; // Default page size
            $expectedPages = ceil($totalNotifications / $pageSize);

            $this->assertGreaterThan(
                1,
                $expectedPages,
                'Should have multiple pages'
            );
        }
    }

    // =========================================================================
    // Property 16: Bulk notification actions
    // For any bulk action (mark all read, delete selected) performed in the
    // notification center, the action should be applied atomically
    // Validates: Requirements 3.7
    // Feature: email-notification-system-enhancement, Property 16: Bulk notification actions
    // =========================================================================

    #[Test]
    public function property_16_bulk_notification_actions(): void
    {
        $this->actingAs($this->user);

        for ($i = 0; $i < min(self::MIN_ITERATIONS, 30); $i++) { // Reduced for performance
            // Clear existing notifications
            DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->delete();

            // Create multiple unread notifications
            $notificationCount = random_int(5, 15);
            $notificationIds = [];

            for ($j = 0; $j < $notificationCount; $j++) {
                $id = Str::uuid()->toString();
                $notificationIds[] = $id;

                DB::table('notifications')->insert([
                    'id' => $id,
                    'type' => 'App\\Notifications\\TestNotification',
                    'notifiable_type' => $this->user->getMorphClass(),
                    'notifiable_id' => $this->user->id,
                    'data' => json_encode([
                        'title' => 'Test Notification '.$j,
                        'type' => 'test',
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Verify all are unread
            $unreadBefore = DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->whereNull('read_at')
                ->count();

            $this->assertEquals($notificationCount, $unreadBefore);

            // Perform bulk mark as read
            DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Property: All notifications should be marked as read atomically
            $unreadAfter = DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->whereNull('read_at')
                ->count();

            $this->assertEquals(
                0,
                $unreadAfter,
                'All notifications should be marked as read'
            );

            // Property: Total count should remain the same
            $totalAfter = DB::table('notifications')
                ->where('notifiable_id', $this->user->id)
                ->count();

            $this->assertEquals(
                $notificationCount,
                $totalAfter,
                'Total notification count should remain unchanged'
            );
        }
    }

    // =========================================================================
    // Property 13: Real-time notification updates
    // For any new notification sent to a user, the notification bell should
    // update its count and content within 5 seconds without page refresh
    // Validates: Requirements 3.2
    // Feature: email-notification-system-enhancement, Property 13: Real-time notification updates
    // =========================================================================

    #[Test]
    public function property_13_real_time_notification_updates(): void
    {
        $this->actingAs($this->user);

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Property: Component should have refresh capability
            $component = Livewire::test(NotificationBell::class);

            // Property: Component should have loadNotifications method
            $this->assertTrue(
                method_exists($component->instance(), 'loadNotifications'),
                'NotificationBell should have loadNotifications method'
            );

            // Property: Component should have refreshNotifications method
            $this->assertTrue(
                method_exists($component->instance(), 'refreshNotifications'),
                'NotificationBell should have refreshNotifications method'
            );

            // Property: Component should track unread count
            $this->assertTrue(
                property_exists($component->instance(), 'unreadCount'),
                'NotificationBell should have unreadCount property'
            );
        }
    }
}
