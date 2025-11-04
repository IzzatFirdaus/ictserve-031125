<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\SLABreachWarningNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SLANotificationService
{
    public function sendBreachWarning(HelpdeskTicket $ticket): void
    {
        $recipients = $this->resolveRecipients($ticket);

        if ($recipients->isEmpty()) {
            Log::warning('No recipients available for SLA breach warning', [
                'ticket_number' => $ticket->ticket_number,
            ]);

            return;
        }

        Notification::send($recipients, new SLABreachWarningNotification($ticket));

        Log::info('SLA breach warning dispatched', [
            'ticket_number' => $ticket->ticket_number,
            'recipient_count' => $recipients->count(),
        ]);
    }

    /**
     * @return Collection<int, \Illuminate\Notifications\Notifiable>
     */
    protected function resolveRecipients(HelpdeskTicket $ticket): Collection
    {
        $recipients = collect();

        if ($ticket->assignedUser) {
            $recipients->push($ticket->assignedUser);
        }

        $admins = User::query()
            ->whereIn('role', ['admin', 'superuser'])
            ->get();

        return $recipients->merge($admins)->unique('id');
    }
}
