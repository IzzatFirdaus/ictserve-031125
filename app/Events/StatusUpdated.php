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
 * Status Updated Event - PKS 5.2.1 Compliant
 *
 * Broadcasts real-time status updates for helpdesk tickets and loan applications.
 * Uses authenticated-only channels per PKS 5.2.1:
 * - User channel: private-user.{userId}
 * - Ticket channel: ticket.{userId}.{ticketId}
 * - Loan channel: loan.{userId}.{loanId}
 *
 * NO GUEST CHANNELS - All channels require authenticated user_id per PKS 5.2.1
 *
 * @see .kiro/specs/ictserve-comprehensive-v4/design.md - PKS 5.2.1 Compliant Architecture
 * @see .kiro/specs/ictserve-comprehensive-v4/requirements.md - Requirements 6.4, 6.5, 24.5, 24.6, 25.1
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
     *
     * PKS 5.2.1: All submissions must have user_id (NOT NULL)
     */
    protected function getAuthenticatedUserId(): ?int
    {
        return $this->model->user_id;
    }

    /**
     * Get the entity ID for channel routing
     */
    protected function getEntityId(): ?int
    {
        if ($this->model instanceof HelpdeskTicket || $this->model instanceof LoanApplication) {
            return $this->model->id;
        }

        return null;
    }

    /**
     * Get the entity type for channel naming
     */
    protected function getEntityType(): string
    {
        if ($this->model instanceof HelpdeskTicket) {
            return 'ticket';
        }

        if ($this->model instanceof LoanApplication) {
            return 'loan';
        }

        return 'user';
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            'updated_at' => now()->toISOString(),
        ];
    }
}
