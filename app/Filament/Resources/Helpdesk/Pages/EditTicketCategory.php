<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\TicketCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketCategory extends EditRecord
{
    protected static string $resource = TicketCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
