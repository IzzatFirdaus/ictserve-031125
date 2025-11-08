<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\ReportScheduleResource\Pages;

use App\Filament\Resources\Reports\ReportScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReportSchedule extends ViewRecord
{
    protected static string $resource = ReportScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
