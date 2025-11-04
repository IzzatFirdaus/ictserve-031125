<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Services\NotificationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanApplication extends EditRecord
{
    protected static string $resource = LoanApplicationResource::class;

    protected ?LoanStatus $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $status = $this->getRecord()->status;
        $this->previousStatus = $status instanceof LoanStatus
            ? $status
            : LoanStatus::tryFrom((string) $status);

        return $data;
    }

    protected function afterSave(): void
    {
        $current = $this->record->status instanceof LoanStatus
            ? $this->record->status
            : LoanStatus::tryFrom((string) $this->record->status);

        if ($current !== null && $this->previousStatus?->value !== $current->value) {
            app(NotificationService::class)->sendLoanStatusUpdate($this->record);
        }
    }
}
