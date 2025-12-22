<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Widget Data Updated Event
 *
 * Broadcasts widget data updates to appropriate WebSocket channels based on
 * user authorization and widget visibility. Supports both user-specific and
 * global admin broadcasts with rate limiting and caching integration.
 *
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 * @see Requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 *
 * @version 3.6.1
 *
 * @since 3.6.0
 */
class WidgetDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance
     *
     * @param  string  $widgetId  Widget identifier
     * @param  string  $widgetType  Widget type (e.g., 'stats', 'chart', 'ai_performance')
     * @param  array  $data  Widget data payload
     * @param  int|null  $userId  Target user ID (null for global broadcast)
     * @param  int|null  $refreshInterval  Custom refresh interval in seconds
     */
    

/**
 * @param array<string, mixed> $data
 */
public function __construct(
        public readonly string $widgetId,
        public readonly string $widgetType,
        public readonly array $data,
        public readonly ?int $userId = null,
        public readonly ?int $refreshInterval = null
    ) {}

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        $channels = [];

        if ($this->userId) {
            // User-specific channel for personal dashboard widgets
            $channels[] = new PrivateChannel("dashboard.widgets.{$this->userId}");
        } else {
            // Global channel for admin/system-wide widgets
            $channels[] = new PrivateChannel('dashboard.widgets.global');
        }

        // Also broadcast to widget-specific channel for targeted updates
        $channels[] = new PrivateChannel("dashboard.widgets.{$this->widgetId}");

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     *
     * @return string Event name
     */
    public function broadcastAs(): string
    {
        return 'WidgetDataUpdated';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed> Broadcast payload
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'widget_id' => $this->widgetId,
            'widget_type' => $this->widgetType,
            'data' => $this->data,
            'user_id' => $this->userId,
            'refresh_interval' => $this->refreshInterval,
            'timestamp' => now()->toISOString(),
            'event_id' => uniqid('widget_update_', true),
        ];
    }

    /**
     * Determine if this event should be broadcast
     *
     * @return bool Whether to broadcast the event
     */
    public function shouldBroadcast(): bool
    {
        // Always broadcast widget updates
        // Rate limiting is handled in WidgetRealtimeManager
        return true;
    }

    /**
     * Get the queue connection for broadcasting
     *
     * @return string|null Queue connection name
     */
    public function broadcastQueue(): ?string
    {
        // Use default queue for widget updates
        // Can be customized for high-priority widgets
        return null;
    }

    /**
     * Get tags for the queued broadcast job
     *
     * @return array<int, string> Job tags
     */
    

/**
 * @return array<string, mixed>
 */
public function tags(): array
    {
        $tags = [
            'widget-broadcast',
            "widget:{$this->widgetId}",
            "type:{$this->widgetType}",
        ];

        if ($this->userId) {
            $tags[] = "user:{$this->userId}";
        } else {
            $tags[] = 'global-broadcast';
        }

        return $tags;
    }
}
