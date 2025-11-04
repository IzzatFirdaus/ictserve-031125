<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\HelpdeskComment;
use App\Notifications\TicketCommentAddedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Helpdesk Comment Observer
 *
 * Handles comment events and triggers notifications.
 *
 * @trace Requirements 10.1, 22.3
 */
class HelpdeskCommentObserver
{
    /**
     * Handle the HelpdeskComment "created" event.
     */
    public function created(HelpdeskComment $comment): void
    {
        // Don't send notifications for internal comments
        if ($comment->is_internal) {
            return;
        }

        $ticket = $comment->helpdeskTicket;

        if (! $ticket) {
            return;
        }

        try {
            // Send notification to submitter
            if ($ticket->isGuestSubmission()) {
                Notification::route('mail', $ticket->guest_email)
                    ->notify(new TicketCommentAddedNotification($ticket, $comment));
            } elseif ($ticket->user) {
                $ticket->user->notify(new TicketCommentAddedNotification($ticket, $comment));
            }

            // Also notify assigned user if comment is from submitter
            if ($ticket->assignedUser && $comment->user_id !== $ticket->assigned_to_user) {
                $ticket->assignedUser->notify(new TicketCommentAddedNotification($ticket, $comment));
            }

            Log::info('Comment notification sent', [
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send comment notification', [
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
