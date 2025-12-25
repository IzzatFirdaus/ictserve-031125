<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\BroadcastsToHybridChannels;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

/**
 * Notification Created Event
 *
 * Broadcasts real-time notifications to users via Laravel Echo.
 * Uses hybrid channel strategy: authenticated users get private-user.{id} channels,
 * guests get UUID-based channels for their submissions.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Dual Channel Strategy
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.2, 4.1, 4.2, 4.3, 4.4
 */
class NotificationCreated implements ShouldBroadcast
{
    use BroadcastsToHybridChannels;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public ?User $user,
        public DatabaseNotification $notification,
        public ?string $guestChannelUuid = null,
        public ?string $guestChannelType = null
    ) {}

    /**
     * Get the authenticated user ID for channel routing
     */
    protected function getAuthenticatedUserId(): ?int
    {
        return $this->user?->id;
    }

    /**
     * Get the guest channel UUID for channel routing
     */
    protected function getGuestChannelUuid(): ?string
    {
        return $this->guestChannelUuid;
    }

    /**
     * Get the guest channel type for channel naming
     */
    protected function getGuestChannelType(): string
    {
        return $this->guestChannelType ?? 'notification';
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
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
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'message' => $this->notification->data['message'] ?? '',
            'data' => $this->sanitizeNotificationData($this->notification->data),
            'created_at' => $this->notification->created_at?->toISOString() ?? now()->toISOString(),
            'read_at' => $this->notification->read_at?->toISOString(),
        ];
    }

    /**
     * Sanitize notification data to exclude PII
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeNotificationData(array $data): array
    {
        // Remove PII fields from notification data
        $piiFields = ['email', 'phone', 'ic_number', 'password', 'api_key', 'token', 'staff_id'];

        $sanitized = $data;
        foreach ($piiFields as $field) {
            unset($sanitized[$field]);
        }

        return $sanitized;
    }
}
