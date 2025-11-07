<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Status Updated Event
 *
 * Broadcasts real-time status updates for helpdesk tickets and loan applications.
 * Sent to owner's private user channel for immediate notification.
 *
 * @see .kiro/specs/staff-dashboard-profile/tasks.md - Task 7.1.2
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 6.1
 */
class StatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public Model $model,
        public string $oldStatus,
        public string $newStatus,
        public int $userId
    ) {}

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->userId}"),
        ];
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $modelType = class_basename($this->model);

        return [
            'model_type' => $modelType,
            'model_id' => $this->model->getKey(),
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'updated_at' => now()->toISOString(),
        ];
    }
}
