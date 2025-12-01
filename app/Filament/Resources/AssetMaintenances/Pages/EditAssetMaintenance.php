<?php

namespace App\Filament\Resources\AssetMaintenances\Pages;

use App\Filament\Resources\AssetMaintenances\AssetMaintenanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetMaintenance extends EditRecord
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
