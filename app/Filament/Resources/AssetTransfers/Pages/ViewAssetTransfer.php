<?php

namespace App\Filament\Resources\AssetTransfers\Pages;

use App\Filament\Resources\AssetTransfers\AssetTransferResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetTransfer extends ViewRecord
{
    protected static string $resource = AssetTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
