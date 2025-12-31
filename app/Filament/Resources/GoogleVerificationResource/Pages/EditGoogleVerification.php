<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoogleVerificationResource\Pages;

use App\Filament\Resources\GoogleVerificationResource;
use App\Services\GoogleOAuthVerificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit Google OAuth Verification Record Page
 *
 * Allows editing verification status and test users.
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.1
 */
class EditGoogleVerification extends EditRecord
{
    protected static string $resource = GoogleVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->hasRole('superuser') ?? false),
        ];
    }

    protected function afterSave(): void
    {
        // Clear verification cache after saving
        /** @var GoogleOAuthVerificationService $service */
        $service = app(GoogleOAuthVerificationService::class);
        $service->clearCache();

        Notification::make()
            ->title(__('admin.verification_updated'))
            ->body(__('admin.verification_cache_cleared'))
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
