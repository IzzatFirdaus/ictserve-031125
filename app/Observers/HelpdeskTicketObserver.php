<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\HelpdeskTicket;
use App\Notifications\TicketStatusUpdatedNotification;
use App\Services\SLATrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Helpdesk Ticket Observer
 *
 * Handles ticket lifecycle events and triggers notifications.
 *
 * @trace Requirements 2.2, 10.1, 22.3
 */
class HelpdeskTicketObserver
{
    public function __construct(
        private SLATrackingService $slaService
    ) {}

    /**
     * Handle the HelpdeskTicket "created" event.
     */
    public function created(HelpdeskTicket $ticket): void
    {
        // Generate ticket number if not set
        if (! $ticket->ticket_number) {
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->saveQuietly();
        }

        // Calculate SLA due dates
        $this->slaService->calculateSLADueDates($ticket);

        Log::info('Ticket created', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'submitter' => $ticket->getSubmitterIdentifier(),
        ]);
    }

    /**
     * Handle the HelpdeskTicket "updated" event.
     */
    public function updated(HelpdeskTicket $ticket): void
    {
        // Check if status changed
        if ($ticket->isDirty('status')) {
            $oldStatus = $ticket->getOriginal('status');
            $newStatus = $ticket->status;

            // Skip notification if old status is null (initial creation)
            if ($oldStatus === null) {
                return;
            }

            // Send notification to submitter
            try {
                if ($ticket->isGuestSubmission()) {
                    // Send to guest email
                    Notification::route('mail', $ticket->guest_email)
                        ->notify(new TicketStatusUpdatedNotification($ticket, $oldStatus, $newStatus));
                } elseif ($ticket->user) {
                    // Send to authenticated user
                    $ticket->user->notify(new TicketStatusUpdatedNotification($ticket, $oldStatus, $newStatus));
                }

                Log::info('Status update notification sent', [
                    'ticket_id' => $ticket->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send status update notification', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Mark as responded if status changed to in_progress
        if ($ticket->isDirty('status') && $ticket->status === 'in_progress') {
            $this->slaService->markAsResponded($ticket);
        }
    }

    /**
     * Handle the HelpdeskTicket "deleting" event.
     */
    public function deleting(HelpdeskTicket $ticket): void
    {
        Log::info('Ticket deleting', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }
}
