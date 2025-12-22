<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TicketCreatedConfirmation;
use App\Models\EmailLog;
use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendTicketCreatedEmail Job
 *
 * Dispatches the TicketCreatedConfirmation mailable for a helpdesk ticket creation event.
 * Accepts the serialized EmailLog->data payload used by retry logic. Falls back gracefully
 * if referenced ticket no longer exists.
 *
 * @trace D03-FR-001.2; D03-FR-008.1; D04 §12.1
 */
class SendTicketCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string,mixed>  $payload  Data originally stored on EmailLog->data
     */
    

/**
 * @param array<string, mixed> $payload
 */
public function __construct(private array $payload) {}

    public function handle(): void
    {
        $ticketId = $this->payload['ticket_id'] ?? null;
        if ($ticketId === null) {
            Log::warning('SendTicketCreatedEmail missing ticket_id in payload', [
                'payload' => $this->payload,
            ]);

            return;
        }

        /** @var HelpdeskTicket|null $ticket */
        $ticket = HelpdeskTicket::find($ticketId);
        if (! $ticket instanceof HelpdeskTicket) {
            Log::warning('SendTicketCreatedEmail ticket not found', [
                'ticket_id' => $ticketId,
            ]);

            return;
        }

        try {
            // Access dynamic properties using string access for PHPStan
            $email = $ticket->user->email ?? $ticket->{'guest_email'} ?? '';
            $name = $ticket->user->name ?? $ticket->{'guest_name'} ?? 'Guest';

            Mail::to($email, $name)
                ->queue(new TicketCreatedConfirmation($ticket));

            Log::info('Ticket created confirmation email queued (job)', [
                'ticket_id' => $ticket->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed dispatching ticket created confirmation (job)', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
