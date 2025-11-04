<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHelpdeskTicket extends ViewRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
