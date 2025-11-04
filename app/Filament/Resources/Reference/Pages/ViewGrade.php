<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Pages;

use App\Filament\Resources\Reference\GradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGrade extends ViewRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
