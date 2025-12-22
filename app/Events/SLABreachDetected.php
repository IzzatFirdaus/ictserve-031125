<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\HelpdeskTicket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * SLA Breach Detected Event
 *
 * Broadcasts when a ticket breaches its SLA deadline.
 * Sends immediate email and WebSocket notification to superuser.
 *
 * @see Requirements 8.2 - SLA breach notification immediately
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 */
class SLABreachDetected implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public string $breachType = 'resolution', // 'response' or 'resolution'
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts to admin.notifications channel for all online admins/superusers.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('admin.notifications'),
        ];

        // Also broadcast to assigned admin if exists
        if ($this->ticket->assigned_admin_id) {
            $channels[] = new PrivateChannel('user.'.$this->ticket->assigned_admin_id);
        }

        return $channels;
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
            'subject' => $this->ticket->subject ?? $this->ticket->description,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'breach_type' => $this->breachType,
            'sla_due_at' => $this->ticket->sla_due_at?->toIso8601String(),
            'breached_at' => now()->toIso8601String(),
            'assigned_to' => $this->ticket->assignedAdmin?->name,
            'submitter_name' => $this->ticket->submitter_name,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'sla.breach';
    }
}
