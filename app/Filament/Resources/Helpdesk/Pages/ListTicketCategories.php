<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\TicketCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketCategories extends ListRecords
{
    protected static string $resource = TicketCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
