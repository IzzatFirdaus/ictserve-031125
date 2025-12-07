<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * API Token Created Event
 *
 * Broadcasts when a user creates a new API token via Sanctum.
 * Part of v3.5.0 API token management feature.
 *
 * @trace v3.5.0 Feature (API Token Management)
 * @see docs/frontend/00-PLAN-DEVELOPMENT.md - P1 Real-Time Broadcasting
 */
class ApiTokenCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public User $user,
        public PersonalAccessToken $token
    ) {}

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->user->id}"),
        ];
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'api.token.created';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'token_id' => $this->token->id,
            'token_name' => $this->token->name,
            'abilities' => $this->token->abilities ?? [],
            'expires_at' => $this->token->expires_at?->toISOString(),
            'created_at' => $this->token->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
