<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\NotificationBell;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for NotificationBell Livewire component
 *
 * Validates Requirements 8.2, 8.4 - In-app notification display and notification center
 *
 * @trace Requirements 8.2, 8.4
 */
class NotificationBellTest extends TestCase
{
    #[Test]
    public function component_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertStatus(200)
            ->assertSet('unreadCount', 0)
            ->assertSet('recentNotifications', []);
    }

    /**
     * Test in-app notification display with BM content
     */
    #[Test]
    public function in_app_notification_display_shows_bm_content(): void
    {
        $user = User::factory()->create();

        // Create notifications with BM content
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\HelpdeskTicketCreated',
            'data' => [
                'title' => 'Tiket Helpdesk Baharu', // BM content
                'message' => 'Tiket anda telah berjaya diwujudkan',
                'ticket_id' => 123,
            ],
        ]);

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\LoanApproved',
            'data' => [
                'title' => 'Permohonan Pinjaman Diluluskan', // BM content
                'message' => 'Permohonan pinjaman aset anda telah diluluskan',
                'loan_id' => 456,
            ],
        ]);

        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 2);

        // Verify BM content is displayed
        $recentNotifications = $component->get('recentNotifications');
        $this->assertCount(2, $recentNotifications);

        // Check for BM content in notifications - titles are at root level, not under 'data'
        $titles = collect($recentNotifications)->pluck('title')->toArray();
        $this->assertContains('Tiket Helpdesk Baharu', $titles);
        $this->assertContains('Permohonan Pinjaman Diluluskan', $titles);
    }

    /**
     * Test notification center for authenticated users with comprehensive scenarios
     */
    #[Test]
    #[DataProvider('notificationCenterProvider')]
    public function notification_center_works_correctly_for_authenticated_users(int $notificationCount, array $notificationTypes, int $expectedUnreadCount): void
    {
        $user = User::factory()->create();

        // Create notifications based on test data
        for ($i = 0; $i < $notificationCount; $i++) {
            $type = $notificationTypes[$i % count($notificationTypes)];
            $isRead = $i >= $expectedUnreadCount; // Mark some as read

            $user->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => $type['class'],
                'data' => $type['data'],
                'read_at' => $isRead ? now() : null,
            ]);
        }

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', $expectedUnreadCount)
            ->assertStatus(200);
    }

    public static function notificationCenterProvider(): array
    {
        return [
            'no notifications' => [
                0,
                [],
                0,
            ],
            'single unread notification' => [
                1,
                [
                    [
                        'class' => 'App\\Notifications\\HelpdeskTicketCreated',
                        'data' => ['title' => 'Tiket Baharu', 'message' => 'Tiket telah diwujudkan'],
                    ],
                ],
                1,
            ],
            'multiple mixed notifications' => [
                5,
                [
                    [
                        'class' => 'App\\Notifications\\HelpdeskTicketCreated',
                        'data' => ['title' => 'Tiket Helpdesk', 'message' => 'Tiket baharu'],
                    ],
                    [
                        'class' => 'App\\Notifications\\LoanApproved',
                        'data' => ['title' => 'Pinjaman Diluluskan', 'message' => 'Permohonan diluluskan'],
                    ],
                ],
                3,
            ],
            'all read notifications' => [
                3,
                [
                    [
                        'class' => 'App\\Notifications\\SystemAnnouncement',
                        'data' => ['title' => 'Pengumuman Sistem', 'message' => 'Kemas kini sistem'],
                    ],
                ],
                0,
            ],
        ];
    }

    /**
     * Test mark as read updates notification count with BM feedback
     */
    #[Test]
    public function mark_as_read_updates_notification_count_with_bm_feedback(): void
    {
        $user = User::factory()->create();
        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\HelpdeskTicketCreated',
            'data' => [
                'title' => 'Tiket Helpdesk Baharu',
                'message' => 'Tiket anda telah berjaya diwujudkan',
            ],
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertHasNoErrors();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * Test mark all as read marks all notifications with BM content
     */
    #[Test]
    public function mark_all_as_read_marks_all_notifications_with_bm_content(): void
    {
        $user = User::factory()->create();

        $firstNotification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\HelpdeskTicketCreated',
            'data' => [
                'title' => 'Tiket Helpdesk 1',
                'message' => 'Tiket pertama anda',
            ],
        ]);

        $secondNotification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\LoanApproved',
            'data' => [
                'title' => 'Pinjaman Diluluskan',
                'message' => 'Permohonan pinjaman anda diluluskan',
            ],
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->call('markAllAsRead')
            ->assertHasNoErrors();

        $this->assertNotNull($firstNotification->fresh()->read_at);
        $this->assertNotNull($secondNotification->fresh()->read_at);
    }

    /**
     * Test notification bell displays correct unread count
     */
    #[Test]
    public function notification_bell_displays_correct_unread_count(): void
    {
        $user = User::factory()->create();

        // Create 3 unread notifications
        for ($i = 1; $i <= 3; $i++) {
            $user->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\TestNotification',
                'data' => [
                    'title' => "Pemberitahuan {$i}",
                    'message' => "Mesej pemberitahuan {$i}",
                ],
            ]);
        }

        // Create 1 read notification
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'data' => [
                'title' => 'Pemberitahuan Dibaca',
                'message' => 'Mesej yang telah dibaca',
            ],
            'read_at' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 3);
    }

    /**
     * Test component handles empty notification state gracefully
     */
    #[Test]
    public function component_handles_empty_notification_state_gracefully(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 0)
            ->assertSet('recentNotifications', [])
            ->assertStatus(200);
    }

    /**
     * Test notification refresh updates counts correctly
     */
    #[Test]
    public function notification_refresh_updates_counts_correctly(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 0);

        // Add a notification after component is loaded
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'data' => [
                'title' => 'Pemberitahuan Baharu',
                'message' => 'Pemberitahuan yang ditambah kemudian',
            ],
        ]);

        // Refresh the component (simulate real-time update)
        $component->call('refreshNotifications')
            ->assertSet('unreadCount', 1);
    }
}
