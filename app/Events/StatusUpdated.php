<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\BroadcastsToHybridChannels;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Status Updated Event
 *
 * Broadcasts real-time status updates for helpdesk tickets and loan applications.
 * Uses hybrid channel strategy: authenticated users get private-user.{id} channels,
 * guests get private-ticket.{uuid} or private-loan.{uuid} channels.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Dual Channel Strategy
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.1, 4.1, 4.2, 4.3, 4.4
 */
class StatusUpdated implements ShouldBroadcast
{
    use BroadcastsToHybridChannels;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public Model $model,
        public string $oldStatus,
        public string $newStatus
    ) {}

    /**
     * Get the authenticated user ID for channel routing
     */
    protected function getAuthenticatedUserId(): ?int
    {
        return $this->model->user_id;
    }

    /**
     * Get the guest channel UUID for channel routing
     */
    protected function getGuestChannelUuid(): ?string
    {
        if ($this->model->user_id !== null) {
            return null; // Authenticated submission
        }

        // For guest submissions, use the model's UUID field
        if ($this->model instanceof HelpdeskTicket) {
            return $this->model->uuid ?? null;
        }

        if ($this->model instanceof LoanApplication) {
            return $this->model->uuid ?? null;
        }

        return null;
    }

    /**
     * Get the guest channel type for channel naming
     */
    protected function getGuestChannelType(): string
    {
        if ($this->model instanceof HelpdeskTicket) {
            return 'ticket';
        }

        if ($this->model instanceof LoanApplication) {
            return 'loan';
        }

        return 'unknown';
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
     * Excludes PII and credentials from payload as per security requirements.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $modelType = match (true) {
            $this->model instanceof HelpdeskTicket => 'HelpdeskTicket',
            $this->model instanceof LoanApplication => 'LoanApplication',
            default => 'unknown'
        };

        return [
            'model_type' => $modelType,
            'model_id' => $this->model->getKey(),
            'entity_type' => match (true) {
                $this->model instanceof HelpdeskTicket => 'ticket',
                $this->model instanceof LoanApplication => 'loan',
                default => 'unknown'
            },
            'entity_id' => $this->model->getKey(),
            'entity_uuid' => $this->model->uuid ?? null,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            'updated_at' => now()->toISOString(),
        ];
    }
}
