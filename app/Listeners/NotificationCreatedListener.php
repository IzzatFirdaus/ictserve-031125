<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Models\User;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

/**
 * Notification Created Listener
 *
 * Listens for database notifications being sent and broadcasts them via WebSocket.
 * Routes to appropriate channel based on user_id for authenticated users.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.2
 */
class NotificationCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        // Only handle database notifications
        if ($event->channel !== 'database') {
            return;
        }

        // Get the user from the notifiable
        $user = $event->notifiable instanceof User ? $event->notifiable : null;

        // Get the notification instance
        $notification = $event->notification;

        // For database notifications, we need to get the actual DatabaseNotification instance
        if ($user && $notification) {
            // Get the latest notification for this user
            $databaseNotification = $user->notifications()
                ->where('type', get_class($notification))
                ->latest()
                ->first();

            if ($databaseNotification) {
                // Dispatch the NotificationCreated event for broadcasting
                NotificationCreated::dispatch($user, $databaseNotification);

                Log::info('Database notification broadcasted', [
                    'user_id' => $user->id,
                    'notification_id' => $databaseNotification->id,
                    'notification_type' => get_class($notification),
                ]);
            }
        }
    }
}
