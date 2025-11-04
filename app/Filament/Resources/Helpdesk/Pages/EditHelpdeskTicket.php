<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHelpdeskTicket extends EditRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recalculateSla')
                ->label('Kira Semula SLA')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->action(fn (HelpdeskTicket $record) => $record->calculateSLADueDates()),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->previousStatus = $this->getRecord()->status;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->previousStatus !== null && $this->previousStatus !== $this->record->status) {
            app(HybridHelpdeskService::class)->sendStatusUpdateNotification(
                $this->record,
                $this->previousStatus,
                $this->record->resolution_notes
            );
        }
    }
}
