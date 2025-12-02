<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Events\NotificationCreated;
use App\Events\StatusUpdated;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Services\UnifiedNotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Integration tests for UnifiedNotificationDispatcher broadcast integration
 *
 * Verifies that the dispatcher correctly triggers broadcast events that
 * match the payload structure expected by frontend Echo listeners.
 *
 * @trace D03 SRS-FR-043, D04 §6.2 (Multi-channel notifications)
 */
class UnifiedNotificationBroadcastIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private UnifiedNotificationDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        // Use sync queue for tests so notifications execute immediately
        config(['queue.default' => 'sync']);

        $this->user = User::factory()->create();
        $this->dispatcher = app(UnifiedNotificationDispatcher::class);
    }

    /**
     * Test that dispatcher triggers NotificationCreated broadcast event
     */
    public function test_dispatcher_triggers_notification_created_broadcast(): void
    {
        Event::fake([NotificationCreated::class]);

        // Create a notification instance
        $notification = new TicketAssignedNotification(HelpdeskTicket::factory()->create());

        // Dispatch through UnifiedNotificationDispatcher
        $result = $this->dispatcher->dispatch(
            $this->user,
            $notification,
            null,
            ['ticket_id' => 1],
            'ticket_assignments',
            'high'
        );

        // Verify broadcast channel was used
        $this->assertContains('broadcast', $result['channels_used']);

        // Verify NotificationCreated event was dispatched
        Event::assertDispatched(NotificationCreated::class, function ($event) {
            // Verify payload structure for frontend
            $payload = $event->broadcastWith();

            $this->assertArrayHasKey('id', $payload);
            $this->assertArrayHasKey('type', $payload);
            $this->assertArrayHasKey('data', $payload);
            $this->assertArrayHasKey('created_at', $payload);
            $this->assertArrayHasKey('read_at', $payload);

            // Verify channel is private user channel
            $channels = $event->broadcastOn();
            $this->assertEquals("private-user.{$this->user->id}", $channels[0]->name);

            return true;
        });
    }

    /**
     * Test that status updates trigger StatusUpdated broadcast event
     */
    public function test_status_update_triggers_broadcast_event(): void
    {
        Event::fake([StatusUpdated::class]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
        ]);

        // Trigger status update event manually (as it would be from observer/service)
        event(new StatusUpdated($ticket, 'open', 'in_progress', $this->user->id));

        // Verify event was dispatched with correct payload
        Event::assertDispatched(StatusUpdated::class, function ($event) use ($ticket) {
            $payload = $event->broadcastWith();

            // Verify payload matches what portal-echo.js and SubmissionDetail.php expect
            $this->assertEquals('HelpdeskTicket', $payload['model_type']);
            $this->assertEquals($ticket->id, $payload['model_id']);
            $this->assertEquals('open', $payload['old_status']);
            $this->assertEquals('in_progress', $payload['new_status']);
            $this->assertArrayHasKey('updated_at', $payload);

            return true;
        });
    }

    /**
     * Test that broadcast events respect user preferences
     */
    public function test_broadcast_respects_user_preferences(): void
    {
        Event::fake([NotificationCreated::class]);

        // User disables real-time notifications via JSON preferences
        $this->user->update([
            'notification_preferences' => [
                'realtime_notifications' => false, // Disable broadcast
            ],
        ]);

        $notification = new TicketAssignedNotification(HelpdeskTicket::factory()->create());

        $result = $this->dispatcher->dispatch(
            $this->user,
            $notification,
            null,
            [],
            'ticket_assignments'
        );

        // Broadcast should not be in channels_used if disabled
        // Note: Critical notifications bypass preferences, so use non-critical type
        if (! in_array('ticket_assignments', config('notifications.critical_types', []))) {
            $this->assertNotContains('broadcast', $result['channels_used']);
            Event::assertNotDispatched(NotificationCreated::class);
        }
    }

    /**
     * Test that critical notifications always broadcast regardless of preferences
     */
    public function test_critical_notifications_always_broadcast(): void
    {
        Event::fake([NotificationCreated::class]);

        // User disables all notifications by setting realtime preference to false
        $this->user->update([
            'notification_preferences' => [
                'realtime_notifications' => false, // Try to disable critical notifications
            ],
        ]);

        $notification = new TicketAssignedNotification(HelpdeskTicket::factory()->create());

        // Dispatch as critical (bypasses preferences)
        $result = $this->dispatcher->dispatchCritical(
            $this->user,
            $notification,
            null,
            [],
            'system_alert'
        );

        // Critical notifications always use broadcast channel
        $this->assertContains('broadcast', $result['channels_used']);

        Event::assertDispatched(NotificationCreated::class);
    }

    /**
     * Test that loan status updates broadcast correctly
     */
    public function test_loan_status_update_broadcasts_correct_model_type(): void
    {
        Event::fake([StatusUpdated::class]);

        $loan = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        event(new StatusUpdated($loan, 'submitted', 'approved', $this->user->id));

        Event::assertDispatched(StatusUpdated::class, function ($event) use ($loan) {
            $payload = $event->broadcastWith();

            // Verify LoanApplication model type is correctly sent
            $this->assertEquals('LoanApplication', $payload['model_type']);
            $this->assertEquals($loan->id, $payload['model_id']);

            return true;
        });
    }

    /**
     * Test that multiple notification types broadcast correctly
     */
    public function test_multiple_notification_types_broadcast(): void
    {
        Event::fake([NotificationCreated::class]);

        $notificationTypes = [
            'ticket_status_changed',
            'loan_approval_required',
            'system_alert',
        ];

        foreach ($notificationTypes as $type) {
            $notification = new TicketAssignedNotification(HelpdeskTicket::factory()->create());

            $this->dispatcher->dispatch(
                $this->user,
                $notification,
                null,
                [],
                $type
            );
        }

        // Verify all notification types triggered broadcasts
        Event::assertDispatchedTimes(NotificationCreated::class, count($notificationTypes));
    }

    /**
     * Test broadcast payload data structure for frontend compatibility
     */
    public function test_broadcast_payload_structure_for_frontend(): void
    {
        Event::fake([NotificationCreated::class]);

        $notification = new TicketAssignedNotification(HelpdeskTicket::factory()->create([
            'ticket_number' => 'TKT-001',
        ]));

        $this->dispatcher->dispatch(
            $this->user,
            $notification,
            null,
            ['ticket_number' => 'TKT-001'],
            'ticket_assignments'
        );

        Event::assertDispatched(NotificationCreated::class, function ($event) {
            $payload = $event->broadcastWith();

            // Verify all required keys exist for portal-echo.js
            $requiredKeys = ['id', 'type', 'data', 'created_at', 'read_at'];
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $payload, "Missing required key: {$key}");
            }

            // Verify data structure contains notification details
            $this->assertIsArray($payload['data']);

            // Verify timestamps are ISO format strings
            $this->assertIsString($payload['created_at']);
            $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $payload['created_at']);

            return true;
        });
    }
}
