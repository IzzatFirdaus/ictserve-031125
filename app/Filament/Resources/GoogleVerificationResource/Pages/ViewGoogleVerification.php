<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoogleVerificationResource\Pages;

use App\Filament\Resources\GoogleVerificationResource;
use App\Services\GoogleOAuthVerificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

/**
 * View Google OAuth Verification Record Page
 *
 * Displays detailed verification record with test user management actions.
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.1
 */
class ViewGoogleVerification extends ViewRecord
{
    protected static string $resource = GoogleVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('add_test_user')
                ->label(__('admin.add_test_user'))
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label(__('admin.email'))
                        ->email()
                        ->required()
                        ->placeholder('user@motac.gov.my')
                        ->helperText(__('admin.test_user_email_helper')),
                ])
                ->action(function (array $data): void {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);

                    if ($service->addTestUser($data['email'])) {
                        Notification::make()
                            ->title(__('admin.test_user_added'))
                            ->body(__('admin.test_user_added_description', ['email' => $data['email']]))
                            ->success()
                            ->send();

                        $this->refreshFormData(['test_users']);
                    } else {
                        Notification::make()
                            ->title(__('admin.test_user_add_failed'))
                            ->body(__('admin.test_user_add_failed_description'))
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('export_test_users')
                ->label(__('admin.export_test_users'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function (): void {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);
                    $export = $service->exportTestUsers();

                    // Store in session for download
                    session(['test_users_export' => $export]);

                    Notification::make()
                        ->title(__('admin.export_ready'))
                        ->body(__('admin.test_users_exported', ['count' => $export['count']]))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('set_production')
                ->label(__('admin.set_production_mode'))
                ->icon(Heroicon::OutlinedRocketLaunch)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.confirm_production_mode'))
                ->modalDescription(__('admin.production_mode_warning'))
                ->visible(fn (): bool => $this->record->verification_status !== GoogleOAuthVerificationService::STATUS_VERIFIED)
                ->action(function (): void {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);

                    if ($service->setVerificationStatus(GoogleOAuthVerificationService::STATUS_VERIFIED)) {
                        Notification::make()
                            ->title(__('admin.status_updated'))
                            ->body(__('admin.production_mode_enabled'))
                            ->success()
                            ->send();

                        $this->refreshFormData(['verification_status']);
                    }
                }),

            Actions\Action::make('set_testing')
                ->label(__('admin.set_testing_mode'))
                ->icon(Heroicon::OutlinedBeaker)
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->verification_status === GoogleOAuthVerificationService::STATUS_VERIFIED)
                ->action(function (): void {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);

                    if ($service->setVerificationStatus(GoogleOAuthVerificationService::STATUS_TESTING)) {
                        Notification::make()
                            ->title(__('admin.status_updated'))
                            ->body(__('admin.testing_mode_enabled'))
                            ->success()
                            ->send();

                        $this->refreshFormData(['verification_status']);
                    }
                }),
        ];
    }
}
