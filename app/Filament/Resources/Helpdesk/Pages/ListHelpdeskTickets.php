<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Exports\HelpdeskTicketExporter;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHelpdeskTickets extends ListRecords
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(HelpdeskTicketExporter::class)
                ->label('Export Tickets')
                ->icon('heroicon-o-arrow-down-tray'),
            Actions\CreateAction::make(),
        ];
    }
}
