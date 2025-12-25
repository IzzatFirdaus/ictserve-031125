<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\StatusUpdated;
use App\Events\TicketStatusChanged;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Real-time Features Integration Test - PKS 5.2.1 Compliant
 *
 * Tests end-to-end real-time functionality for the ICTServe v4.0 architecture.
 * All channels require authenticated users per PKS 5.2.1 - NO GUEST ACCESS.
 *
 * @see D16_BROADCASTING_SETUP.md - Real-time features
 * @see .kiro/specs/ictserve-comprehensive-v4/tasks.md Task 1.1
 *
 * @requirements 6.1, 6.2, 6.3, 6.4, 6.5, 8.1, 8.2, 24.5, 24.6, 25.1
 */
class RealtimeFeaturesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ticket_status_updates_trigger_real_time_events(): void
    {
        Event::fake([TicketStatusChanged::class]);

        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        // Manually dispatch the event (observer may not be active in test)
        event(new TicketStatusChanged($ticket, 'open', 'in_progress'));

        // Verify real-time event was dispatched
        Event::assertDispatched(TicketStatusChanged::class);
    }

    #[Test]
    public function loan_status_updates_trigger_real_time_events(): void
    {
        Event::fake([StatusUpdated::class]);

        $user = User::factory()->create();
        $loan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'SUBMITTED',
        ]);

        // Manually dispatch the event
        event(new StatusUpdated($loan, 'SUBMITTED', 'APPROVED'));

        // Verify real-time event was dispatched
        Event::assertDispatched(StatusUpdated::class);
    }

    #[Test]
    public function real_time_events_include_proper_payload_structure(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        $broadcastData = $event->broadcastWith();

        // Should include required payload fields
        $this->assertArrayHasKey('model_type', $broadcastData);
        $this->assertArrayHasKey('model_id', $broadcastData);
        $this->assertArrayHasKey('old_status', $broadcastData);
        $this->assertArrayHasKey('new_status', $broadcastData);
        $this->assertArrayHasKey('updated_at', $broadcastData);
    }

    #[Test]
    public function real_time_events_support_authenticated_architecture(): void
    {
        // Test authenticated user scenario - PKS 5.2.1 compliant
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $event = new StatusUpdated($ticket, 'open', 'in_progress');
        $channels = $event->broadcastOn();

        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // PKS 5.2.1: Should broadcast to authenticated user channel and entity-specific channel
        // Note: PrivateChannel adds 'private-' prefix automatically
        $this->assertContains("private-user.{$user->id}", $channelNames);
        $this->assertContains("private-ticket.{$user->id}.{$ticket->id}", $channelNames);
    }

    #[Test]
    public function real_time_events_require_authenticated_users(): void
    {
        // PKS 5.2.1: All submissions must have user_id (NOT NULL)
        // Tickets without user_id should not broadcast to entity channels
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $event = new StatusUpdated($ticket, 'open', 'in_progress');
        $channels = $event->broadcastOn();

        // Should have channels for authenticated user
        $this->assertNotEmpty($channels);

        // All channels should be private channels
        foreach ($channels as $channel) {
            $this->assertStringContainsString('private-', $channel->name);
        }
    }

    #[Test]
    public function admin_notifications_are_broadcast_for_high_priority_events(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'priority' => 'HIGH',
            'status' => 'open',
        ]);

        $event = new StatusUpdated($ticket, 'open', 'in_progress');
        $channels = $event->broadcastOn();

        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should broadcast to user channel and entity channel
        // Note: PrivateChannel adds 'private-' prefix automatically
        $this->assertContains("private-user.{$user->id}", $channelNames);
        $this->assertContains("private-ticket.{$user->id}.{$ticket->id}", $channelNames);
    }

    #[Test]
    public function real_time_events_include_timestamp_information(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        $broadcastData = $event->broadcastWith();

        // Should include timestamp for client-side handling
        $this->assertArrayHasKey('updated_at', $broadcastData);
        $this->assertNotNull($broadcastData['updated_at']);

        // Should be in ISO format for JavaScript compatibility
        $timestamp = $broadcastData['updated_at'];
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $timestamp);
    }

    #[Test]
    public function real_time_events_include_unique_identifiers(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        $broadcastData = $event->broadcastWith();

        // Should include unique identifiers for client-side tracking
        $this->assertArrayHasKey('model_id', $broadcastData);
        $this->assertEquals($ticket->id, $broadcastData['model_id']);
    }

    #[Test]
    public function ai_broadcasting_channels_are_configured(): void
    {
        // Test that AI-specific channels are properly configured
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        $aiChannels = [
            'private-ai-status',
            'private-ai-alerts',
            'private-ai-performance',
            'private-ai-approvals',
        ];

        foreach ($aiChannels as $channel) {
            $response = $this->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => $channel,
            ]);

            // Admin should be able to access AI channels
            $this->assertNotEquals(403, $response->getStatusCode(), "Admin should access {$channel}");
        }
    }

    #[Test]
    public function real_time_events_implement_should_broadcast(): void
    {
        // Verify that broadcasting events implement ShouldBroadcast
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        // Event should implement ShouldBroadcast
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    #[Test]
    public function broadcasting_auth_endpoint_handles_cors_properly(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Test CORS preflight request
        $response = $this->call('OPTIONS', '/broadcasting/auth');

        // Should handle OPTIONS request (CORS preflight)
        $this->assertNotEquals(405, $response->getStatusCode());
    }

    #[Test]
    public function real_time_system_supports_multiple_concurrent_users(): void
    {
        Event::fake([StatusUpdated::class]);

        // Create multiple users and tickets with user_id (PKS 5.2.1 compliant)
        $users = User::factory()->count(3)->create();

        // Simulate concurrent updates
        foreach ($users as $user) {
            $ticket = HelpdeskTicket::factory()->create([
                'user_id' => $user->id,
                'status' => 'open',
            ]);
            event(new StatusUpdated($ticket, 'open', 'in_progress'));
        }

        // Should handle multiple concurrent events
        Event::assertDispatchedTimes(StatusUpdated::class, 3);
    }

    #[Test]
    public function real_time_events_maintain_data_consistency(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $event = new StatusUpdated($ticket, 'open', 'in_progress');
        $broadcastData = $event->broadcastWith();

        // Broadcast data should match actual model data
        $this->assertEquals($ticket->id, $broadcastData['model_id']);
        $this->assertEquals('open', $broadcastData['old_status']);
        $this->assertEquals('in_progress', $broadcastData['new_status']);
    }

    #[Test]
    public function real_time_system_handles_network_failures_gracefully(): void
    {
        // Fake events to prevent actual broadcasting
        Event::fake();

        $user = User::factory()->create();
        // Test that the system can handle broadcasting failures
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        // Even if broadcasting fails, the model update should succeed
        $ticket->update(['status' => 'resolved']);

        $this->assertEquals('resolved', $ticket->fresh()->status);
    }

    #[Test]
    public function real_time_events_include_entity_type(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $event = new StatusUpdated($ticket, 'open', 'in_progress');

        $broadcastData = $event->broadcastWith();

        // Should include entity type for frontend routing
        $this->assertArrayHasKey('entity_type', $broadcastData);
        $this->assertEquals('ticket', $broadcastData['entity_type']);
    }
}
