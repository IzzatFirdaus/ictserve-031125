<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetCategoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetCategory extends ViewRecord
{
    protected static string $resource = AssetCategoryResource::class;
}
