<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Models\User;
use App\Services\NotificationPreferenceRepository;
use App\Services\UnifiedNotificationDispatcher;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

/**
 * Notification Created Listener
 *
 * Listens for database notifications being sent and broadcasts them via WebSocket.
 * Routes to appropriate channel based on user_id for authenticated users.
 * Respects user notification preferences for broadcast channel.
 *
 * Note: This listener handles notifications sent directly via $user->notify().
 * Notifications sent through UnifiedNotificationDispatcher are handled by the dispatcher itself.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.2
 */
class NotificationCreatedListener
{
    public function __construct(
        private NotificationPreferenceRepository $preferences
    ) {}

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        // Only handle database notifications
        if ($event->channel !== 'database') {
            return;
        }

        // Skip if this notification was sent through UnifiedNotificationDispatcher
        // The dispatcher handles broadcast directly with proper preference checking
        if (UnifiedNotificationDispatcher::isDispatchingNotification()) {
            return;
        }

        // Get the user from the notifiable
        $user = $event->notifiable instanceof User ? $event->notifiable : null;

        // Get the notification instance
        $notification = $event->notification;

        // For database notifications, we need to get the actual DatabaseNotification instance
        if ($user && $notification) {
            // Infer notification type from class name for preference checking
            $notificationType = $this->inferNotificationType($notification);

            // Check if user wants broadcast notifications
            if (! $this->preferences->shouldSendBroadcast($user, $notificationType)) {
                Log::info('Broadcast notification skipped (user preference)', [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                ]);

                return;
            }

            // Get the latest notification for this user
            $databaseNotification = $user->notifications()
                ->where('type', \get_class($notification))
                ->latest()
                ->first();

            if ($databaseNotification) {
                // Dispatch the NotificationCreated event for broadcasting
                NotificationCreated::dispatch($user, $databaseNotification);

                Log::info('Database notification broadcasted', [
                    'user_id' => $user->id,
                    'notification_id' => $databaseNotification->id,
                    'notification_type' => \get_class($notification),
                ]);
            }
        }
    }

    /**
     * Infer notification type from notification class name.
     */
    private function inferNotificationType(object $notification): string
    {
        $className = class_basename($notification);

        // Ticket-related notifications
        if (str_contains($className, 'TicketStatus') || str_contains($className, 'TicketUpdate')) {
            return 'ticket_updates';
        }
        if (str_contains($className, 'TicketAssign')) {
            return 'ticket_assignments';
        }
        if (str_contains($className, 'Comment')) {
            return 'ticket_comments';
        }
        if (str_contains($className, 'SLA')) {
            return 'sla_alerts';
        }

        // Loan-related notifications
        if (str_contains($className, 'LoanApproval') || str_contains($className, 'LoanApproved')) {
            return 'loan_approvals';
        }
        if (str_contains($className, 'LoanReminder') || str_contains($className, 'Overdue')) {
            return 'loan_reminders';
        }
        if (str_contains($className, 'LoanUpdate') || str_contains($className, 'LoanStatus')) {
            return 'loan_updates';
        }

        // System notifications
        if (str_contains($className, 'System') || str_contains($className, 'Announcement')) {
            return 'system_announcements';
        }

        // Default fallback
        return 'general_notification';
    }
}
