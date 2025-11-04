<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Models\HelpdeskTicket;
use Filament\Resources\Pages\CreateRecord;

class CreateHelpdeskTicket extends CreateRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function handleRecordCreation(array $data): HelpdeskTicket
    {
        /** @var HelpdeskTicket $ticket */
        $ticket = static::getModel()::create($data);
        $ticket->update([
            'ticket_number' => $ticket->generateTicketNumber(),
        ]);

        return $ticket;
    }
}
