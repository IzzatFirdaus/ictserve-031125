<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Models\HelpdeskTicket;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHelpdeskTicket extends EditRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculateSla')
                ->label('Kira Semula SLA')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->action(fn (HelpdeskTicket $record) => $record->calculateSLADueDates()),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // Type guard for PHPStan - EditRecord always works with HelpdeskTicket model
        if ($record instanceof HelpdeskTicket) {
            $this->previousStatus = $record->status;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Status change notifications handled by observers/events
        // TODO: Implement TicketStatusChanged event if needed
    }
}
