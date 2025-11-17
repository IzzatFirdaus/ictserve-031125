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
                ->label('Retry Email')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Retry Email Delivery')
                ->modalDescription('Are you sure you want to retry sending this email?')
                ->action(function (): void {
                    $service = app(EmailNotificationService::class);

                    $recordId = (int) ($this->record->id ?? 0);

                    if ($recordId === 0) {
                        return;
                    }

                    if ($service->retryEmailDelivery($recordId)) {
                        Notification::make()
                            ->title('Email queued for retry')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed to retry email')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->status === 'failed' && $this->record->retry_attempts < 3),
        ];
    }
}
