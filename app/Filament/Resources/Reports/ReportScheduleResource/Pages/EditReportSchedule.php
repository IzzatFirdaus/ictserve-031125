<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\ReportScheduleResource\Pages;

use App\Filament\Resources\Reports\ReportScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportSchedule extends EditRecord
{
    protected static string $resource = ReportScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
