<?php

namespace App\Filament\Resources\System\Audits\Pages;

use App\Filament\Resources\System\Audits\AuditResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
