<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
