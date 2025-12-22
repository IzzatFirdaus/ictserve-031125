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
 * Loan Status Changed Event
 *
 * Broadcasts real-time status updates for loan applications via Laravel Reverb.
 * Sent to both user's private channel and loan-specific channel for guest tracking.
 *
 * @see .kiro/specs/frontend-comprehensive-v3.6/requirements.md - Requirements 10.1, 10.3
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 *
 * @trace D03 SRS-FR-008; D04 §5.3
 */
class LoanStatusChanged implements ShouldBroadcast
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
        public LoanApplication $loanApplication,
        ?string $oldStatus = null,
        ?string $newStatus = null
    ) {
        $this->oldStatus = $oldStatus ?? $loanApplication->getOriginal('status') ?? 'unknown';
        $this->newStatus = $newStatus ?? $loanApplication->status->value ?? 'unknown';
    }

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
        return 'loan.status.changed';
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
            'loan_id' => $this->loanApplication->id,
            'application_number' => $this->loanApplication->application_number,
            'uuid' => $this->loanApplication->uuid,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'purpose' => $this->loanApplication->purpose,
            'updated_at' => now()->toISOString(),
            'message' => __('notifications.loan_status_changed', [
                'application' => $this->loanApplication->application_number,
                'status' => __("loans.status.{$this->newStatus}"),
            ]),
        ];
    }
}
