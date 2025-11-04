<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Mail\MaintenanceTicketNotification;
use App\Mail\NewTicketNotification;
use App\Mail\TicketCreatedConfirmation;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TicketNotificationService
{
    public function __construct(
        private EmailDispatcher $dispatcher
    ) {}

    public function sendTicketConfirmation(HelpdeskTicket $ticket): void
    {
        $recipientEmail = $ticket->user?->email ?? $ticket->guest_email;
        $recipientName = $ticket->user?->name ?? $ticket->guest_name;

        $this->dispatcher->queue(
            (new TicketCreatedConfirmation($ticket))->onQueue('emails'),
            $recipientEmail,
            $recipientName,
            [
                'ticket_number' => $ticket->ticket_number,
                'is_guest' => $ticket->isGuestSubmission(),
            ]
        );
    }

    public function notifyAdmins(HelpdeskTicket $ticket): void
    {
        $admins = User::query()
            ->whereIn('role', ['admin', 'superuser'])
            ->pluck('email', 'name');

        foreach ($admins as $name => $email) {
            $this->dispatcher->queue(
                (new NewTicketNotification($ticket))->onQueue('notifications'),
                $email,
                is_string($name) ? $name : null,
                [
                    'ticket_number' => $ticket->ticket_number,
                    'priority' => $ticket->priority,
                ]
            );
        }

        Log::info('Admin notifications queued for new ticket', [
            'ticket_number' => $ticket->ticket_number,
            'admin_count' => $admins->count(),
        ]);
    }

    public function sendMaintenanceNotification(
        HelpdeskTicket $ticket,
        Asset $asset,
        LoanApplication $application
    ): void {
        $maintenanceRecipients = User::query()
            ->whereIn('role', ['admin', 'superuser'])
            ->pluck('email', 'name');

        foreach ($maintenanceRecipients as $name => $email) {
            $this->dispatcher->queue(
                new MaintenanceTicketNotification($ticket, $asset, $application),
                $email,
                is_string($name) ? $name : null,
                [
                    'ticket_number' => $ticket->ticket_number,
                    'asset_tag' => $asset->asset_tag,
                ]
            );
        }

        Log::info('Maintenance notifications queued', [
            'ticket_number' => $ticket->ticket_number,
            'asset_id' => $asset->id,
            'recipient_count' => $maintenanceRecipients->count(),
        ]);
    }
}
