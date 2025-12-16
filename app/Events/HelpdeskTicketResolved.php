<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\HelpdeskTicket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a helpdesk ticket is resolved.
 *
 * @trace D03-SRS-HELP-001, Requirements 16.4
 *
 * @version 3.6.0
 */
class HelpdeskTicketResolved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}
}
