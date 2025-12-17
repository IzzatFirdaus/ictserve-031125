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
 * Ticket Status Changed Event
 *
 * Broadcasts real-time status updates for helpdesk tickets via Laravel Reverb.
 * Sent to both user's private channel and ticket-specific channel for guest tracking.
 *
 * @see .kiro/specs/frontend-comprehensive-v3.6/requirements.md - Requirements 10.1, 10.3
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 *
 * @trace D03 SRS-FR-008; D04 §5.3
 */
class TicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The old status before the change.
     */
    public string $oldStatus;

    /**
     * The new status after the change.
     */
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        ?string $oldStatus = null,
        ?string $newStatus = null
    ) {
        $this->oldStatus = $oldStatus ?? $ticket->getOriginal('status') ?? 'unknown';
        $this->newStatus = $newStatus ?? $ticket->status->value ?? 'unknown';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to ticket-specific channel for guest tracking
        if ($this->ticket->uuid) {
            $channels[] = new PrivateChannel("ticket.{$this->ticket->uuid}");
        }

        // Broadcast to user's private channel if authenticated submission
        if ($this->ticket->user_id) {
            $channels[] = new PrivateChannel("user.{$this->ticket->user_id}");
        }

        // Broadcast to admin notifications channel for high-priority tickets
        if ($this->ticket->priority && in_array($this->ticket->priority, ['high', 'critical'], true)) {
            $channels[] = new PrivateChannel('admin.notifications');
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ticket.status.changed';
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
            'uuid' => $this->ticket->uuid,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'updated_at' => now()->toISOString(),
            'message' => __('notifications.ticket_status_changed', [
                'ticket' => $this->ticket->ticket_number,
                'status' => __("tickets.status.{$this->newStatus}"),
            ]),
        ];
    }
}
