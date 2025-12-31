<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoogleVerificationResource\Pages;

use App\Filament\Resources\GoogleVerificationResource;
use App\Services\GoogleOAuthVerificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

/**
 * List Google OAuth Verification Records Page
 *
 * Displays all OAuth verification records with filtering and actions.
 * Includes header actions for adding test users and refreshing status.
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.1
 */
class ListGoogleVerifications extends ListRecords
{
    protected static string $resource = GoogleVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                    } else {
                        Notification::make()
                            ->title(__('admin.test_user_add_failed'))
                            ->body(__('admin.test_user_add_failed_description'))
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('refresh_status')
                ->label(__('admin.refresh_status'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->action(function (): void {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);
                    $service->clearCache();

                    Notification::make()
                        ->title(__('admin.status_refreshed'))
                        ->body(__('admin.verification_cache_cleared'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('view_requirements')
                ->label(__('admin.view_requirements'))
                ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                ->color('info')
                ->modalHeading(__('admin.verification_requirements'))
                ->modalContent(function (): \Illuminate\Contracts\View\View {
                    /** @var GoogleOAuthVerificationService $service */
                    $service = app(GoogleOAuthVerificationService::class);

                    return view('filament.pages.partials.verification-requirements', [
                        'requirements' => $service->getVerificationRequirements(),
                        'details' => $service->getVerificationDetails(),
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('admin.close')),
        ];
    }
}
