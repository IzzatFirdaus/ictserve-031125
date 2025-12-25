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
 * Comment Posted Event
 *
 * Broadcasts real-time comment updates for tickets and loan applications.
 * Uses hybrid channel strategy: authenticated users get private-user.{id} channels,
 * guests get private-ticket.{uuid} or private-loan.{uuid} channels.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Dual Channel Strategy
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 4.1, 4.2, 4.3, 4.4
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
     * Get the guest channel UUID for channel routing
     */
    protected function getGuestChannelUuid(): ?string
    {
        $commentable = $this->comment->commentable;

        if ($commentable && $commentable->user_id === null) {
            return $commentable->uuid ?? null;
        }

        return null;
    }

    /**
     * Get the guest channel type for channel naming
     */
    protected function getGuestChannelType(): string
    {
        $commentable = $this->comment->commentable;

        if ($commentable instanceof HelpdeskTicket) {
            return 'ticket';
        }

        if ($commentable instanceof LoanApplication) {
            return 'loan';
        }

        return 'comment';
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
