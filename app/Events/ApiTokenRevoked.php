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

/**
 * API Token Revoked Event
 *
 * Broadcasts when a user revokes/deletes an API token.
 * Part of v3.5.0 API token management feature.
 *
 * @trace v3.5.0 Feature (API Token Management)
 * @see docs/frontend/00-PLAN-DEVELOPMENT.md - P1 Real-Time Broadcasting
 */
class ApiTokenRevoked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     *
     * @param User $user The user who revoked the token
     * @param int $tokenId The ID of the revoked token
     * @param string $tokenName The name of the revoked token
     */
    public function __construct(
        public User $user,
        public int $tokenId,
        public string $tokenName
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
        return 'api.token.revoked';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'token_id' => $this->tokenId,
            'token_name' => $this->tokenName,
            'revoked_at' => now()->toISOString(),
        ];
    }
}
