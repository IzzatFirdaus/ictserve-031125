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
 * Google SSO Linked Event
 *
 * Broadcasts when a user successfully links their Google account via OAuth.
 * Part of v3.5.0 Google SSO integration feature.
 *
 * @trace v3.5.0 Feature (Google OAuth Integration)
 * @see docs/frontend/00-PLAN-DEVELOPMENT.md - P1 Real-Time Broadcasting
 */
class GoogleSsoLinked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     *
     * @param User $user The user who linked their Google account
     * @param string $googleEmail The Google email address linked
     */
    public function __construct(
        public User $user,
        public string $googleEmail
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
        return 'google.sso.linked';
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
            'google_email' => $this->googleEmail,
            'linked_at' => now()->toISOString(),
        ];
    }
}
