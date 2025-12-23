<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\BroadcastsToHybridChannels;
use App\Models\BedrockConversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AI Streaming Error Occurred Event
 *
 * Broadcasts when an error occurs during AI response streaming.
 * Uses hybrid channel strategy: authenticated users get private-user.{id} channels,
 * guests get private-conversation.{uuid} channels.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - AI Streaming Events
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 6.5
 */
class AiStreamingErrorOccurred implements ShouldBroadcast
{
    use BroadcastsToHybridChannels;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public BedrockConversation $conversation,
        public string $error,
        public bool $retryAvailable = false,
        public ?string $errorCode = null
    ) {}

    /**
     * Get the authenticated user ID for channel routing
     */
    protected function getAuthenticatedUserId(): ?int
    {
        return $this->conversation->user_id;
    }

    /**
     * Get the guest channel UUID for channel routing
     */
    protected function getGuestChannelUuid(): ?string
    {
        if ($this->conversation->user_id !== null) {
            return null; // Authenticated submission
        }

        // For guest conversations, we'll use the conversation ID as UUID
        // This assumes guest conversations have some form of session token
        return (string) $this->conversation->id;
    }

    /**
     * Get the guest channel type for channel naming
     */
    protected function getGuestChannelType(): string
    {
        return 'conversation';
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'ai.error.occurred';
    }

    /**
     * Get the data to broadcast
     *
     * Excludes PII and credentials from payload as per security requirements.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'conversation_uuid' => (string) $this->conversation->id, // Using ID as UUID for now
            'error' => $this->error,
            'error_code' => $this->errorCode,
            'retry_available' => $this->retryAvailable,
            'model_used' => $this->conversation->model,
            'timestamp' => now()->toISOString(),
        ];
    }
}
