<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHelpdeskTicket extends CreateRecord
{
    protected static string $resource = HelpdeskTicketResource::class;
}
