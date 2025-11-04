<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Pages;

use App\Filament\Resources\Reference\GradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
