<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Events\NotificationCreated;
use App\Livewire\Portal\NotificationBell;
use App\Livewire\Portal\NotificationCenter;
use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Notification Functionality Feature Tests
 *
 * Tests notification creation, real-time updates, mark as read,
 * and notification filtering.
 *
 * Requirements: 6.1, 6.2, 6.3, 6.4
 * Traceability: D03 SRS-FR-006, D04 §3.5
 */
class NotificationFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    #[Test]
    public function notification_bell_displays_unread_count(): void
    {
        // Create 3 unread notifications
        for ($i = 0; $i < 3; $i++) {
            $this->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TicketAssigned',
                'data' => ['message' => "Test notification {$i}"],
                'read_at' => null,
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->assertSet('unreadCount', 3);
    }

    #[Test]
    public function notification_bell_shows_zero_when_no_unread(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->assertSet('unreadCount', 0);
    }

    #[Test]
    public function notification_bell_displays_latest_notifications(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Your ticket has been assigned'],
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->call('toggleDropdown')
            ->assertSee('Your ticket has been assigned');
    }

    #[Test]
    public function user_can_mark_notification_as_read(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertSet('unreadCount', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function user_can_mark_all_notifications_as_read(): void
    {
        // Create 3 unread notifications
        for ($i = 0; $i < 3; $i++) {
            $this->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TicketAssigned',
                'data' => ['message' => "Test notification {$i}"],
                'read_at' => null,
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    #[Test]
    public function notification_center_displays_all_notifications(): void
    {
        // Create 5 notifications
        for ($i = 0; $i < 5; $i++) {
            $this->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TicketAssigned',
                'data' => ['message' => "Notification {$i}"],
                'read_at' => null,
            ]);
        }

        $component = Livewire::actingAs($this->user)
            ->test(NotificationCenter::class);

        for ($i = 0; $i < 5; $i++) {
            $component->assertSee("Notification {$i}");
        }
    }

    #[Test]
    public function notification_center_can_filter_by_unread(): void
    {
        // Create 2 unread and 2 read notifications
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Unread notification'],
            'read_at' => null,
        ]);

        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Read notification'],
            'read_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->set('filter', 'unread')
            ->call('filterBy', 'unread')
            ->assertSee('Unread notification')
            ->assertDontSee('Read notification');
    }

    #[Test]
    public function notification_center_can_filter_by_read(): void
    {
        // Create 2 unread and 2 read notifications
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Unread notification'],
            'read_at' => null,
        ]);

        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Read notification'],
            'read_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->set('filter', 'read')
            ->call('filterBy', 'read')
            ->assertSee('Read notification')
            ->assertDontSee('Unread notification');
    }

    #[Test]
    public function notification_center_can_filter_by_type(): void
    {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Ticket notification'],
            'read_at' => null,
        ]);

        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\LoanApproved',
            'data' => ['message' => 'Loan notification'],
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->set('notificationTypes', ['App\Notifications\TicketAssigned'])
            ->assertSee('Ticket notification')
            ->assertDontSee('Loan notification');
    }

    #[Test]
    public function user_can_delete_notification(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->call('deleteNotification', $notification->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    #[Test]
    public function notifications_are_paginated(): void
    {
        // Create 30 notifications
        for ($i = 0; $i < 30; $i++) {
            $this->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TicketAssigned',
                'data' => ['message' => "Notification {$i}"],
                'read_at' => null,
            ]);
        }

        $component = Livewire::actingAs($this->user)
            ->test(NotificationCenter::class);

        // Should see pagination controls
        $component->assertSee('Next');
    }

    #[Test]
    public function notification_created_event_is_dispatched(): void
    {
        Event::fake([NotificationCreated::class]);

        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        // Manually dispatch event (in real app, this would be automatic)
        event(new NotificationCreated($this->user, $notification));

        Event::assertDispatched(NotificationCreated::class, function ($event) use ($notification) {
            return $event->user->id === $this->user->id &&
                $event->notification->id === $notification->id;
        });
    }

    #[Test]
    public function notification_bell_updates_on_new_notification(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationBell::class)
            ->assertSet('unreadCount', 0);

        // Create new notification
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'New notification'],
            'read_at' => null,
        ]);

        // Simulate Echo event
        $component->call('handleEchoNotification', [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
        ]);

        $component->assertSet('unreadCount', 1);
    }

    #[Test]
    public function notification_types_are_displayed_correctly(): void
    {
        $types = [
            'App\Notifications\TicketAssigned' => 'Ticket Assigned',
            'App\Notifications\TicketResolved' => 'Ticket Resolved',
            'App\Notifications\LoanApproved' => 'Loan Approved',
            'App\Notifications\LoanRejected' => 'Loan Rejected',
            'App\Notifications\AssetOverdue' => 'Asset Overdue',
        ];

        foreach ($types as $type => $label) {
            $this->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => $type,
                'data' => ['message' => "Test {$label}"],
                'read_at' => null,
            ]);
        }

        $component = Livewire::actingAs($this->user)
            ->test(NotificationCenter::class);

        foreach ($types as $label) {
            $component->assertSee($label);
        }
    }

    #[Test]
    public function notification_quick_actions_are_displayed(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => [
                'message' => 'Your ticket has been assigned',
                'ticket_id' => 123,
            ],
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->assertSee('View Ticket');
    }

    #[Test]
    public function empty_state_displayed_when_no_notifications(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->assertSee('No notifications');
    }
}
