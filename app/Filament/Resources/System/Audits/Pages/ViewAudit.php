<?php

namespace App\Filament\Resources\System\Audits\Pages;

use App\Filament\Resources\System\Audits\AuditResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
