<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\InternalComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Comment Posted Event
 *
 * Broadcasts real-time internal comment updates to authenticated users.
 * Sent to private channels based on commentable resource type.
 *
 * @see .kiro/specs/staff-dashboard-profile/tasks.md - Task 7.1.2
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 7.4
 */
class CommentPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public InternalComment $comment
    ) {}

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $commentableType = class_basename($this->comment->commentable_type);
        $commentableId = $this->comment->commentable_id;

        return [
            new PrivateChannel("{$commentableType}.{$commentableId}.comments"),
        ];
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'comment.posted';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->comment->id,
            'comment' => $this->comment->comment,
            'user' => [
                'id' => $this->comment->user->id,
                'name' => $this->comment->user->name,
            ],
            'created_at' => $this->comment->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
