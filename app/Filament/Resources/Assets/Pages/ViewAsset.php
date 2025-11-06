<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Widgets\AssetAvailabilityWidget;
use App\Filament\Resources\Assets\Widgets\AssetUtilizationWidget;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * View Asset Page
 *
 * Enhanced asset view with availability calendar and utilization metrics.
 *
 * @trace Requirements 2.3, 3.1, 7.1
 */
class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AssetAvailabilityWidget::class,
            AssetUtilizationWidget::class,
        ];
    }
}
