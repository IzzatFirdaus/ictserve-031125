<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\StatusUpdated;
use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ticket Status Broadcasting Integration Test
 *
 * Tests that ticket status changes automatically dispatch broadcasting events
 * through the HelpdeskTicketObserver.
 *
 * PKS 5.2.1 Compliant: Uses StatusUpdated event with authenticated-only channels.
 *
 * @see app/Observers/HelpdeskTicketObserver.php
 * @see app/Events/StatusUpdated.php
 *
 * @requirements 6.1, 6.2, 6.3, 8.1, 8.2, PKS 5.2.1
 */
class TicketStatusBroadcastingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function observer_integration_works_with_status_updates(): void
    {
        // Create a ticket with initial status BEFORE faking events
        // This allows the observer to run during creation
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
        ]);

        // Verify ticket was created successfully
        $this->assertEquals('open', $ticket->status);
        $this->assertNotNull($ticket->ticket_number);

        // Now fake events to capture the status update event
        Event::fake([StatusUpdated::class]);

        // Update the ticket status - this should trigger the observer
        $ticket->update(['status' => 'in_progress']);

        // Verify the status was updated
        $this->assertEquals('in_progress', $ticket->fresh()->status);

        // Verify the StatusUpdated event was dispatched by the observer
        Event::assertDispatched(StatusUpdated::class, fn ($event) => $event->model->id === $ticket->id
            && $event->oldStatus === 'open'
            && $event->newStatus === 'in_progress');
    }

    #[Test]
    public function event_can_be_manually_dispatched(): void
    {
        Event::fake();

        // Create a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
        ]);

        // Manually dispatch the event (simulating what the observer does)
        StatusUpdated::dispatch($ticket, 'open', 'in_progress');

        // Verify the event was dispatched
        Event::assertDispatched(StatusUpdated::class, fn ($event) => $event->model->id === $ticket->id
            && $event->oldStatus === 'open'
            && $event->newStatus === 'in_progress');
    }

    #[Test]
    public function event_has_correct_broadcast_data(): void
    {
        // Create a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'in_progress',
            'subject' => 'Test Ticket Subject',
        ]);

        // Create the event
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        // Test broadcast data
        $broadcastData = $event->broadcastWith();

        $this->assertArrayHasKey('model_type', $broadcastData);
        $this->assertArrayHasKey('model_id', $broadcastData);
        $this->assertArrayHasKey('entity_type', $broadcastData);
        $this->assertArrayHasKey('entity_id', $broadcastData);
        $this->assertArrayHasKey('old_status', $broadcastData);
        $this->assertArrayHasKey('new_status', $broadcastData);

        $this->assertEquals('HelpdeskTicket', $broadcastData['model_type']);
        $this->assertEquals($ticket->id, $broadcastData['model_id']);
        $this->assertEquals('ticket', $broadcastData['entity_type']);
        $this->assertEquals('open', $broadcastData['old_status']);
        $this->assertEquals('in_progress', $broadcastData['new_status']);
    }

    #[Test]
    public function event_broadcasts_to_correct_channels(): void
    {
        // Create a ticket with user (PKS 5.2.1: all tickets have user_id)
        $ticket = HelpdeskTicket::factory()->create();

        // Create the event
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        // Get broadcast channels
        $channels = $event->broadcastOn();

        // Convert to channel names for testing (PrivateChannel adds 'private-' prefix)
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // PKS 5.2.1: Should include user-specific private channel
        $this->assertContains("private-user.{$ticket->user_id}", $channelNames);

        // PKS 5.2.1: Should include ticket-specific channel with user_id
        $this->assertContains("private-ticket.{$ticket->user_id}.{$ticket->id}", $channelNames);
    }

    #[Test]
    public function authenticated_ticket_broadcasts_to_user_channel(): void
    {
        // PKS 5.2.1: All tickets must have user_id (NOT NULL)
        // Create an authenticated ticket with user_id
        $ticket = HelpdeskTicket::factory()->create();

        // Create the event
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        // Get broadcast channels
        $channels = $event->broadcastOn();
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // PKS 5.2.1: All tickets have user_id, so user channel should be included
        $this->assertContains("private-user.{$ticket->user_id}", $channelNames);

        // PKS 5.2.1: Ticket channel includes user_id for authentication
        $this->assertContains("private-ticket.{$ticket->user_id}.{$ticket->id}", $channelNames);
    }
}
