<?php

declare(strict_types=1);

namespace App\Events\Concerns;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Broadcasts to Authenticated Channels Trait - PKS 5.2.1 Compliant
 *
 * Implements channel selection logic for ICTServe's PKS 5.2.1 Compliant Architecture.
 * Routes events to authenticated user channels ONLY:
 * - User channel: private-user.{userId}
 * - Ticket channel: ticket.{userId}.{ticketId}
 * - Loan channel: loan.{userId}.{loanId}
 *
 * NO GUEST CHANNELS - All channels require authenticated user_id per PKS 5.2.1
 *
 * @see .kiro/specs/ictserve-comprehensive-v4/design.md - PKS 5.2.1 Compliant Architecture
 * @see .kiro/specs/ictserve-comprehensive-v4/requirements.md - Requirements 6.4, 6.5, 24.5, 24.6, 25.1
 */
trait BroadcastsToHybridChannels
{
    /**
     * Get the authenticated user ID for channel routing
     *
     * PKS 5.2.1: All submissions must have user_id (NOT NULL)
     *
     * @return int|null User ID - must be non-null for valid broadcasts
     */
    abstract protected function getAuthenticatedUserId(): ?int;

    /**
     * Get the entity ID for channel routing (ticket_id or loan_id)
     *
     * @return int|null Entity ID for specific channel routing
     */
    protected function getEntityId(): ?int
    {
        return null;
    }

    /**
     * Get the entity type for channel naming
     *
     * @return string Channel type (ticket, loan)
     */
    protected function getEntityType(): string
    {
        return 'user';
    }

    /**
     * Get the channels the event should broadcast on
     *
     * PKS 5.2.1 Compliant - Authenticated-only channels:
     * - User channel: private-user.{userId}
     * - Entity channel: {type}.{userId}.{entityId}
     *
     * NO GUEST CHANNELS per PKS 5.2.1
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $userId = $this->getAuthenticatedUserId();

        // PKS 5.2.1: All broadcasts require authenticated user_id
        if ($userId === null) {
            // Log warning for debugging - this should not happen in PKS 5.2.1 compliant system
            \Illuminate\Support\Facades\Log::warning('BroadcastsToHybridChannels: Attempted broadcast without user_id', [
                'event_class' => static::class,
            ]);

            return [];
        }

        $channels = [new PrivateChannel("user.{$userId}")];

        // Add entity-specific channel if available
        $entityId = $this->getEntityId();
        $entityType = $this->getEntityType();

        if ($entityId !== null && $entityType !== 'user') {
            $channels[] = new PrivateChannel("{$entityType}.{$userId}.{$entityId}");
        }

        return $channels;
    }

    /**
     * @deprecated Use getEntityId() instead - Guest channels removed per PKS 5.2.1
     */
    protected function getGuestChannelUuid(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use getEntityType() instead - Guest channels removed per PKS 5.2.1
     */
    protected function getGuestChannelType(): string
    {
        return $this->getEntityType();
    }
}
