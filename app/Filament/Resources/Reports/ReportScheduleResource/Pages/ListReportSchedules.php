<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\ReportScheduleResource\Pages;

use App\Filament\Resources\Reports\ReportScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReportSchedules extends ListRecords
{
    protected static string $resource = ReportScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
