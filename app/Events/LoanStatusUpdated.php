<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Loan Status Updated Event
 *
 * Broadcasts real-time status updates for loan applications via Laravel Reverb.
 * Sent to both user's private channel and loan-specific channel for guest tracking.
 *
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 * @trace D03 SRS-FR-008; D04 §5.3
 */
class LoanStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public LoanApplication $loanApplication,
        public ?string $oldStatus = null,
        public ?string $newStatus = null
    ) {
        // If statuses not provided, use current and original
        $this->oldStatus = $oldStatus ?? $loanApplication->getOriginal('status') ?? 'unknown';
        $this->newStatus = $newStatus ?? (is_object($loanApplication->status) ? $loanApplication->status->value : $loanApplication->status) ?? 'unknown';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to loan-specific channel for guest tracking
        if ($this->loanApplication->uuid) {
            $channels[] = new PrivateChannel("loan.{$this->loanApplication->uuid}");
        }

        // Broadcast to user's private channel if authenticated submission
        if ($this->loanApplication->user_id) {
            $channels[] = new PrivateChannel("user.{$this->loanApplication->user_id}");
        }

        // Broadcast to approver's channel if pending approval
        if ($this->newStatus === 'pending_approval' && $this->loanApplication->approver_id) {
            $channels[] = new PrivateChannel("user.{$this->loanApplication->approver_id}");
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'loan.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'loan_id' => $this->loanApplication->id,
            'application_number' => $this->loanApplication->application_number,
            'uuid' => $this->loanApplication->uuid,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'purpose' => $this->loanApplication->purpose,
            'updated_at' => now()->toISOString(),
            'message' => __('notifications.loan_status_changed', [
                'application' => $this->loanApplication->application_number,
                'status' => is_object($this->loanApplication->status) ? __("loans.status.{$this->newStatus}") : $this->newStatus,
            ]),
        ];
    }
}
