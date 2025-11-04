<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Pages;

use App\Filament\Resources\Reference\DivisionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;
}
