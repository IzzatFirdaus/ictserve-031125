<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\BroadcastsToHybridChannels;
use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\LoanApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Comment Posted Event - PKS 5.2.1 Compliant
 *
 * Broadcasts real-time comment updates for tickets and loan applications.
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
class CommentPosted implements ShouldBroadcast
{
    use BroadcastsToHybridChannels;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public InternalComment $comment
    ) {}

    /**
     * Get the authenticated user ID for channel routing
     *
     * PKS 5.2.1: All submissions must have user_id (NOT NULL)
     */
    protected function getAuthenticatedUserId(): ?int
    {
        $commentable = $this->comment->commentable;

        if ($commentable instanceof HelpdeskTicket || $commentable instanceof LoanApplication) {
            return $commentable->user_id;
        }

        return null;
    }

    /**
     * Get the entity ID for channel routing
     */
    protected function getEntityId(): ?int
    {
        $commentable = $this->comment->commentable;

        if ($commentable instanceof HelpdeskTicket || $commentable instanceof LoanApplication) {
            return $commentable->id;
        }

        return null;
    }

    /**
     * Get the entity type for channel naming
     */
    protected function getEntityType(): string
    {
        $commentable = $this->comment->commentable;

        if ($commentable instanceof HelpdeskTicket) {
            return 'ticket';
        }

        if ($commentable instanceof LoanApplication) {
            return 'loan';
        }

        return 'user';
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'comment.posted';
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
        $user = $this->comment->user;

        return [
            'comment_id' => $this->comment->id,
            'content' => $this->comment->comment,
            'author' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                // Exclude email and other PII
            ] : null,
            'created_at' => $this->comment->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
