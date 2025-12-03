<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\HelpdeskTicketCreated;
use App\Mail\TicketAssignedMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendTicketNotification Job
 *
 * Unified job for dispatching helpdesk ticket notifications.
 * Supports multiple notification types: created, assigned, status_updated.
 * Processes within 30-second queue SLA per D17.
 *
 * @trace D03-FR-001.2; D03-FR-008.1; D04 §12.1
 * @trace Requirements 10.4, 13.3
 */
class SendTicketNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public int $backoff = 10;

    /**
     * @param  array<string,mixed>  $payload  Notification data
     *                                        - ticket_id: int (required)
     *                                        - type: string (created|assigned|status_updated)
     *                                        - recipient_email: string (optional, defaults to submitter)
     *                                        - recipient_name: string (optional)
     *                                        - assigned_admin_id: int (optional, for assigned type)
     *                                        - old_status: string (optional, for status_updated)
     *                                        - new_status: string (optional, for status_updated)
     */
    public function __construct(private array $payload)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $ticketId = $this->payload['ticket_id'] ?? null;
        $type = $this->payload['type'] ?? 'created';

        if ($ticketId === null) {
            Log::warning('SendTicketNotification missing ticket_id in payload', [
                'payload' => $this->payload,
            ]);

            return;
        }

        /** @var HelpdeskTicket|null $ticket */
        $ticket = HelpdeskTicket::find($ticketId);
        if (! $ticket instanceof HelpdeskTicket) {
            Log::warning('SendTicketNotification ticket not found', [
                'ticket_id' => $ticketId,
            ]);

            return;
        }

        try {
            match ($type) {
                'created' => $this->sendCreatedNotification($ticket),
                'assigned' => $this->sendAssignedNotification($ticket),
                'status_updated' => $this->sendStatusUpdatedNotification($ticket),
                default => Log::warning('SendTicketNotification unknown type', [
                    'ticket_id' => $ticketId,
                    'type' => $type,
                ]),
            };
        } catch (\Throwable $e) {
            Log::error('Failed dispatching ticket notification', [
                'ticket_id' => $ticket->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    /**
     * Send ticket created notification to submitter.
     */
    private function sendCreatedNotification(HelpdeskTicket $ticket): void
    {
        $email = $this->payload['recipient_email']
            ?? $ticket->user?->email
            ?? $ticket->{'submitter_email'}
            ?? '';
        $name = $this->payload['recipient_name']
            ?? $ticket->user?->name
            ?? $ticket->{'submitter_name'}
            ?? 'Guest';

        if (empty($email)) {
            Log::warning('SendTicketNotification no recipient email for created notification', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        Mail::to($email, $name)->queue(new HelpdeskTicketCreated($ticket));

        Log::info('Ticket created notification queued', [
            'ticket_id' => $ticket->id,
            'recipient' => $email,
        ]);
    }

    /**
     * Send ticket assigned notification to admin.
     */
    private function sendAssignedNotification(HelpdeskTicket $ticket): void
    {
        $adminId = $this->payload['assigned_admin_id'] ?? $ticket->assigned_admin_id;

        if ($adminId === null) {
            Log::warning('SendTicketNotification no admin assigned', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        /** @var User|null $admin */
        $admin = User::find($adminId);
        if (! $admin instanceof User) {
            Log::warning('SendTicketNotification assigned admin not found', [
                'ticket_id' => $ticket->id,
                'admin_id' => $adminId,
            ]);

            return;
        }

        Mail::to($admin->email, $admin->name)->queue(new TicketAssignedMail($ticket, $admin));

        Log::info('Ticket assigned notification queued', [
            'ticket_id' => $ticket->id,
            'admin_id' => $admin->id,
        ]);
    }

    /**
     * Send ticket status updated notification to submitter.
     */
    private function sendStatusUpdatedNotification(HelpdeskTicket $ticket): void
    {
        $email = $this->payload['recipient_email']
            ?? $ticket->user?->email
            ?? $ticket->{'submitter_email'}
            ?? '';
        $name = $this->payload['recipient_name']
            ?? $ticket->user?->name
            ?? $ticket->{'submitter_name'}
            ?? 'Guest';

        if (empty($email)) {
            Log::warning('SendTicketNotification no recipient email for status update', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        $previousStatus = $this->payload['old_status'] ?? $ticket->status;
        $comment = $this->payload['comment'] ?? null;

        Mail::to($email, $name)->queue(new TicketStatusUpdatedMail($ticket, $previousStatus, $comment));

        Log::info('Ticket status updated notification queued', [
            'ticket_id' => $ticket->id,
            'recipient' => $email,
            'previous_status' => $previousStatus,
        ]);
    }
}
