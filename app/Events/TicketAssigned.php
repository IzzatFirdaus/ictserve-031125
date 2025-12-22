<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TicketAssigned Event
 *
 * Broadcasts when a helpdesk ticket is assigned to an admin.
 * Sends real-time notification via Laravel Reverb WebSocket.
 *
 * @see Requirements 5.4 - Assignment action with WebSocket notification
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 */
class TicketAssigned implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public User $assignedUser,
        public ?User $assignedBy = null,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->assignedUser->id),
            new PrivateChannel('admin.notifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'assigned_to' => [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ],
            'assigned_by' => $this->assignedBy ? [
                'id' => $this->assignedBy->id,
                'name' => $this->assignedBy->name,
            ] : null,
            'assigned_at' => now()->toIso8601String(),
            'sla_due_at' => $this->ticket->sla_resolution_due_at?->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ticket.assigned';
    }
}
