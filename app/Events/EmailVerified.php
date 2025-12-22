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
 * Email Verified Event
 *
 * Broadcasts when a user successfully verifies their email address.
 * Part of v3.5.0 self-registration flow.
 *
 * @trace D03 SRS-FR-001 (Self-Registration), v3.5.0 Feature
 * @see docs/frontend/00-PLAN-DEVELOPMENT.md - P1 Real-Time Broadcasting
 */
class EmailVerified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    

/**
 * @return array<string, mixed>
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
        return 'email.verified';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'verified_at' => $this->user->email_verified_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
