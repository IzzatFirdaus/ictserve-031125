<?php

declare(strict_types=1);

namespace App\Events\Concerns;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Broadcasts to Hybrid Channels Trait
 *
 * Implements channel selection logic for ICTServe's True Hybrid Architecture.
 * Routes events to authenticated user channels (private-user.{id}) or
 * guest UUID-based channels (private-ticket.{uuid}, private-loan.{uuid}).
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Dual Channel Strategy
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 4.1, 4.2
 */
trait BroadcastsToHybridChannels
{
    /**
     * Get the authenticated user ID for channel routing
     *
     * @return int|null User ID if authenticated submission, null for guest
     */
    abstract protected function getAuthenticatedUserId(): ?int;

    /**
     * Get the guest channel UUID for channel routing
     *
     * @return string|null UUID for guest channel, null if authenticated
     */
    abstract protected function getGuestChannelUuid(): ?string;

    /**
     * Get the guest channel type for channel naming
     *
     * @return string Channel type (ticket, loan, conversation)
     */
    abstract protected function getGuestChannelType(): string;

    /**
     * Get the channels the event should broadcast on
     *
     * Implements dual channel strategy:
     * - Authenticated users: private-user.{userId}
     * - Guest users: private-{type}.{uuid}
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $userId = $this->getAuthenticatedUserId();

        if ($userId !== null) {
            return [new PrivateChannel("user.{$userId}")];
        }

        $uuid = $this->getGuestChannelUuid();
        $type = $this->getGuestChannelType();

        if ($uuid !== null && $type !== '') {
            return [new PrivateChannel("{$type}.{$uuid}")];
        }

        // Fallback: no valid channel found
        return [];
    }
}
