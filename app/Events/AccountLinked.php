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
 * Account Linked Event
 *
 * Broadcasts when guest submissions are successfully linked to an authenticated account.
 * Part of v3.5.0 account linking feature.
 *
 * @trace D03 SRS-FR-001.5 (Account Linking), v3.5.0 Feature
 * @see docs/frontend/00-PLAN-DEVELOPMENT.md - P1 Real-Time Broadcasting
 */
class AccountLinked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     *
     * @param User $user The authenticated user
     * @param int $linkedSubmissionsCount Number of submissions linked
     * @param array<string> $submissionTypes Types of submissions linked (helpdesk, loan)
     */
    public function __construct(
        public User $user,
        public int $linkedSubmissionsCount,
        public array $submissionTypes
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
        return 'account.linked';
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
            'linked_submissions' => $this->linkedSubmissionsCount,
            'submission_types' => $this->submissionTypes,
            'linked_at' => now()->toISOString(),
        ];
    }
}
