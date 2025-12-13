<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Asset Overdue Event
 *
 * Broadcasts when a loan asset becomes overdue.
 * Sends reminder notifications at 48 hours before, on due date, and daily after.
 *
 * @see Requirements 8.3 - Overdue asset reminder schedule
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 */
class AssetOverdue implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public LoanApplication $loan,
        public string $reminderType = 'overdue', // 'warning_48h', 'due_today', 'overdue'
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts to admin.notifications and user channel if authenticated.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('admin.notifications'),
        ];

        // Broadcast to user channel if loan has an owner
        if ($this->loan->user_id) {
            $channels[] = new PrivateChannel('user.'.$this->loan->user_id);
        }

        // Also broadcast to loan UUID channel for guests
        if ($this->loan->uuid) {
            $channels[] = new PrivateChannel('loan.'.$this->loan->uuid);
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
            'loan_id' => $this->loan->id,
            'loan_reference' => $this->loan->reference,
            'applicant_name' => $this->loan->applicant_name,
            'applicant_email' => $this->loan->applicant_email,
            'loan_end_date' => $this->loan->loan_end_date?->toDateString(),
            'reminder_type' => $this->reminderType,
            'days_overdue' => $this->loan->loan_end_date
                ? now()->diffInDays($this->loan->loan_end_date, false)
                : 0,
            'status' => $this->loan->status,
            'notified_at' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'asset.overdue';
    }
}
