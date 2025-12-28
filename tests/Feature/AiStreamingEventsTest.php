<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\AiStreamingChunk;
use App\Events\AiStreamingCompleted;
use App\Events\AiStreamingErrorOccurred;
use App\Events\AiStreamingStarted;
use App\Models\BedrockConversation;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * AI Streaming Events Test
 *
 * Tests the AI streaming events for real-time broadcasting functionality.
 * Verifies event structure, channel routing, and payload sanitization.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 6.1, 6.2, 6.3, 6.5
 */
class AiStreamingEventsTest extends TestCase
{
    #[Test]
    public function ai_streaming_started_event_broadcasts_correctly(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'model' => 'claude-3-sonnet',
        ]);

        $event = new AiStreamingStarted($conversation, 'claude-3-sonnet');

        // Test channel routing for authenticated user
        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-user.{$user->id}", $channels[0]->name);

        // Test broadcast name
        $this->assertEquals('ai.streaming.started', $event->broadcastAs());

        // Test payload structure
        $payload = $event->broadcastWith();
        $this->assertArrayHasKey('conversation_id', $payload);
        $this->assertArrayHasKey('model', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertEquals($conversation->id, $payload['conversation_id']);
        $this->assertEquals('claude-3-sonnet', $payload['model']);
    }

    #[Test]
    public function ai_streaming_chunk_event_broadcasts_correctly(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'model' => 'claude-3-haiku',
        ]);

        $event = new AiStreamingChunk($conversation, 'Hello world', false);

        // Test channel routing for authenticated user
        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-user.{$user->id}", $channels[0]->name);

        // Test broadcast name
        $this->assertEquals('ai.streaming.chunk', $event->broadcastAs());

        // Test payload structure
        $payload = $event->broadcastWith();
        $this->assertArrayHasKey('conversation_id', $payload);
        $this->assertArrayHasKey('chunk', $payload);
        $this->assertArrayHasKey('is_final', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertEquals($conversation->id, $payload['conversation_id']);
        $this->assertEquals('Hello world', $payload['chunk']);
        $this->assertFalse($payload['is_final']);
    }

    #[Test]
    public function ai_streaming_completed_event_broadcasts_correctly(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'model' => 'claude-3-opus',
            'total_tokens' => 150,
        ]);

        $event = new AiStreamingCompleted($conversation, 150, 2.5);

        // Test channel routing for authenticated user
        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-user.{$user->id}", $channels[0]->name);

        // Test broadcast name
        $this->assertEquals('ai.streaming.completed', $event->broadcastAs());

        // Test payload structure
        $payload = $event->broadcastWith();
        $this->assertArrayHasKey('conversation_id', $payload);
        $this->assertArrayHasKey('total_tokens', $payload);
        $this->assertArrayHasKey('duration', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertEquals($conversation->id, $payload['conversation_id']);
        $this->assertEquals(150, $payload['total_tokens']);
        $this->assertEquals(2.5, $payload['duration']);
    }

    #[Test]
    public function ai_streaming_error_occurred_event_broadcasts_correctly(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'model' => 'claude-3-sonnet',
        ]);

        $event = new AiStreamingErrorOccurred($conversation, 'Rate limit exceeded', true, 'RATE_LIMIT');

        // Test channel routing for authenticated user
        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-user.{$user->id}", $channels[0]->name);

        // Test broadcast name
        $this->assertEquals('ai.error.occurred', $event->broadcastAs());

        // Test payload structure
        $payload = $event->broadcastWith();
        $this->assertArrayHasKey('conversation_id', $payload);
        $this->assertArrayHasKey('error', $payload);
        $this->assertArrayHasKey('retry_available', $payload);
        $this->assertArrayHasKey('error_code', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertEquals($conversation->id, $payload['conversation_id']);
        $this->assertEquals('Rate limit exceeded', $payload['error']);
        $this->assertTrue($payload['retry_available']);
        $this->assertEquals('RATE_LIMIT', $payload['error_code']);
    }

    #[Test]
    public function guest_conversation_events_route_to_conversation_channel(): void
    {
        Event::fake();

        // Create guest conversation (no user_id)
        $conversation = BedrockConversation::factory()->create([
            'user_id' => null,
            'model' => 'claude-3-haiku',
        ]);

        $event = new AiStreamingStarted($conversation, 'claude-3-haiku');

        // Test channel routing for guest user
        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-conversation.{$conversation->id}", $channels[0]->name);
    }

    #[Test]
    public function events_exclude_sensitive_data_from_payload(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'model' => 'claude-3-sonnet',
        ]);

        $events = [
            new AiStreamingStarted($conversation, 'claude-3-sonnet'),
            new AiStreamingChunk($conversation, 'Test chunk', false),
            new AiStreamingCompleted($conversation, 100, 1.5),
            new AiStreamingErrorOccurred($conversation, 'Test error', false),
        ];

        $sensitiveFields = ['email', 'phone', 'ic_number', 'password', 'api_key'];

        foreach ($events as $event) {
            $payload = json_encode($event->broadcastWith());

            foreach ($sensitiveFields as $field) {
                $this->assertStringNotContainsString(
                    $field,
                    strtolower($payload),
                    "Payload should not contain sensitive field: {$field}"
                );
            }
        }
    }
}
