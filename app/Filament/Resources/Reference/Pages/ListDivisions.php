<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Pages;

use App\Filament\Resources\Reference\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisions extends ListRecords
{
    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
