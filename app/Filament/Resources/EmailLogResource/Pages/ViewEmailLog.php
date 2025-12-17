<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailLogResource\Pages;

use App\Filament\Resources\EmailLogResource;
use App\Services\EmailNotificationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property \App\Models\EmailLog $record
 */
class ViewEmailLog extends ViewRecord
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label('Cuba Semula E-mel')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Cuba Semula Penghantaran E-mel')
                ->modalDescription('Adakah anda pasti mahu cuba semula menghantar e-mel ini?')
                ->action(function (): void {
                    $service = app(EmailNotificationService::class);

                    $recordId = (int) ($this->record->id ?? 0);

                    if ($recordId === 0) {
                        return;
                    }

                    if ($service->retryEmailDelivery($recordId)) {
                        Notification::make()
                            ->title('E-mel ditambah ke barisan cuba semula')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal cuba semula e-mel')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->status === 'failed' && $this->record->retry_attempts < 3),
        ];
    }
}
