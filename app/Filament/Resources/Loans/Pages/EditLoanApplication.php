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
        $record = $this->getRecord();

        // Type guard for PHPStan - EditRecord always works with LoanApplication model
        if ($record instanceof \App\Models\LoanApplication) {
            $status = $record->status;
            $this->previousStatus = $status instanceof LoanStatus
                ? $status
                : LoanStatus::tryFrom((string) $status);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // Type guard for PHPStan - EditRecord always works with LoanApplication model
        if ($record instanceof \App\Models\LoanApplication) {
            $current = $record->status;

            if ($this->previousStatus && $this->previousStatus->value !== $current->value) {
                app(NotificationService::class)->sendLoanStatusUpdate($record);
            }
        }
    }
}
