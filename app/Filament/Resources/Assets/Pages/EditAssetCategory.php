<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetCategory extends EditRecord
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
