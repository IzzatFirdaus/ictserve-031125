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
 * High Priority Ticket Created Event
 *
 * Broadcasts when a high-priority (HIGH or CRITICAL) ticket is created.
 * Sends real-time notification to all online admins via Laravel Reverb.
 *
 * @see Requirements 8.1 - High-priority ticket broadcast within 2 seconds
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 */
class HighPriorityTicketCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts to admin.notifications channel for all online admins.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('admin.notifications'),
        ];

        // Also broadcast to user channel if ticket has an owner
        if ($this->ticket->user_id) {
            $channels[] = new PrivateChannel('user.'.$this->ticket->user_id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject ?? $this->ticket->description,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'submitter_name' => $this->ticket->submitter_name,
            'submitter_email' => $this->ticket->submitter_email,
            'created_at' => $this->ticket->created_at?->toIso8601String(),
            'sla_due_at' => $this->ticket->sla_due_at?->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ticket.high-priority';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return in_array($this->ticket->priority, ['HIGH', 'CRITICAL'], true);
    }
}
