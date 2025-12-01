<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Mail\TicketStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTicketStatusEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketStatusChanged $event): void
    {
        if ($event->ticket->user && $event->ticket->user->email) {
            Mail::to($event->ticket->user->email)
                ->send(new TicketStatusUpdated($event->ticket));
        }
    }
}
