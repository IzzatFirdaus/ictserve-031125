<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

/**
 * Broadcast Event Audit Listener
 *
 * Listens for all broadcast events and logs them to the activity log for audit purposes.
 * Records event type, channel, timestamp, and other relevant metadata.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 7.5
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Property 8: Audit Logging
 */
class BroadcastEventAuditListener
{
    /**
     * Handle the broadcast event for audit logging
     */
    public function handle(BroadcastEvent $event): void
    {
        // Only log events that implement ShouldBroadcast
        if (! $event->event instanceof ShouldBroadcast) {
            return;
        }

        $broadcastEvent = $event->event;

        // Get event class name
        $eventClass = \get_class($broadcastEvent);

        // Get broadcast channels
        $channels = $broadcastEvent->broadcastOn();
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Get broadcast event name
        $eventName = method_exists($broadcastEvent, 'broadcastAs')
            ? $broadcastEvent->broadcastAs()
            : class_basename($eventClass);

        // Get broadcast data (sanitized)
        $broadcastData = method_exists($broadcastEvent, 'broadcastWith')
            ? $broadcastEvent->broadcastWith()
            : [];

        // Prepare audit log properties
        $properties = [
            'event_class' => $eventClass,
            'event_name' => $eventName,
            'channels' => $channelNames,
            'channel_count' => \count($channelNames),
            'broadcast_data_keys' => array_keys($broadcastData),
            'timestamp' => now()->toISOString(),
        ];

        // Add socket ID if available
        if (isset($event->socket)) {
            $properties['socket_id'] = $event->socket;
        }

        // Log to activity log using spatie/laravel-activitylog
        try {
            activity('broadcast_event_dispatched')
                ->withProperties($properties)
                ->log("Broadcast event dispatched: {$eventName} to ".\count($channelNames).' channel(s)');

            Log::debug('Broadcast event logged to activity log', [
                'event_class' => $eventClass,
                'event_name' => $eventName,
                'channels' => $channelNames,
                'properties_count' => \count($properties),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the broadcast
            Log::error('Failed to log broadcast event to activity log', [
                'event_class' => $eventClass,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
