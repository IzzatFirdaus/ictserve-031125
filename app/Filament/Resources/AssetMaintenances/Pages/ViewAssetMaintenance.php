<?php

namespace App\Filament\Resources\AssetMaintenances\Pages;

use App\Filament\Resources\AssetMaintenances\AssetMaintenanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetMaintenance extends ViewRecord
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
