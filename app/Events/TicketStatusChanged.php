<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\HelpdeskTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Ticket Status Changed Event
 *
 * Broadcasts real-time updates when a helpdesk ticket status changes.
 * Supports ICTServe v3.6.0 True Hybrid Architecture with guest and authenticated users.
 *
 * @see D16_BROADCASTING_SETUP.md - Broadcasting configuration
 * @see docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Requirements 6.1, 6.2, 6.3
 *
 * @requirements 6.1, 6.2, 6.3, 8.1, 8.2
 */
class TicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  HelpdeskTicket  $ticket  The ticket that changed status
     * @param  string|null  $oldStatus  Previous status (optional)
     * @param  string|null  $newStatus  New status (optional, defaults to ticket's current status)
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public ?string $oldStatus = null,
        public ?string $newStatus = null
    ) {
        // If new status not provided, use ticket's current status
        $this->newStatus = $newStatus ?? $ticket->status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts to multiple channels for different audiences:
     * - Public channel for general ticket updates
     * - Ticket-specific channel for real-time status tracking
     * - User-specific private channel (if authenticated)
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            // Public channel for all helpdesk updates
            new Channel('helpdesk'),
            // Ticket-specific channel using ticket ID for guest access
            new Channel("ticket.{$this->ticket->id}"),
        ];

        // Add user-specific private channel if ticket has authenticated user
        if ($this->ticket->user_id) {
            $channels[] = new Channel("user.{$this->ticket->user_id}");
        }

        // Broadcast to admin notifications channel for high-priority tickets
        if ($this->ticket->priority && in_array($this->ticket->priority, ['high', 'critical'], true)) {
            $channels[] = new Channel('admin.notifications');
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'subject' => $this->ticket->subject,
            'updated_at' => $this->ticket->updated_at->toISOString(),
            'message' => $this->getStatusMessage(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ticket.status.changed';
    }

    /**
     * Get localized status change message.
     */
    private function getStatusMessage(): string
    {
        $statusMessages = [
            'open' => 'Tiket telah dibuka',
            'assigned' => 'Tiket telah diberikan kepada pegawai',
            'in_progress' => 'Tiket sedang diproses',
            'pending_user' => 'Menunggu maklum balas pengguna',
            'resolved' => 'Tiket telah diselesaikan',
            'closed' => 'Tiket telah ditutup',
        ];

        return $statusMessages[$this->newStatus] ?? "Status tiket telah dikemaskini kepada {$this->newStatus}";
    }
}
