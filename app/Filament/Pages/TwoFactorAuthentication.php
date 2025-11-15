<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\TwoFactorAuthService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Two-Factor Authentication Management Page
 *
 * Superuser-only page for managing 2FA settings, setup wizard,
 * and backup code management.
 *
 * Requirements: 17.3, D03-FR-017.3
 *
 * @see D04 §11.1 Two-factor authentication
 */
class TwoFactorAuthentication extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.two-factor-authentication';

    public ?string $secretKey = null;

    public ?string $qrCodeUrl = null;

    public array $backupCodes = [];

    public bool $showSetup = false;

    public bool $showBackupCodes = false;

    public string $verification_code = '';

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user->two_factor_enabled) {
            $this->startSetup();
        }
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.two_factor_auth.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.two_factor_auth.group');
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return [
                Action::make('regenerate_backup_codes')
                    ->label('Regenerate Backup Codes')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate Backup Codes')
                    ->modalDescription('This will invalidate all existing backup codes. Are you sure?')
                    ->action(function (): void {
                        $service = app(TwoFactorAuthService::class);
                        $this->backupCodes = $service->regenerateBackupCodes(Auth::user());
                        $this->showBackupCodes = true;

                        Notification::make()
                            ->title('Backup codes regenerated')
                            ->success()
                            ->send();
                    }),

                Action::make('disable_2fa')
                    ->label('Disable 2FA')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Disable Two-Factor Authentication')
                    ->modalDescription('This will disable 2FA for your account. You will need to enter your current 2FA code to confirm.')
                    ->form([
                        Forms\Components\TextInput::make('verification_code')
                            ->label('Verification Code')
                            ->required()
                            ->length(6)
                            ->numeric()
                            ->placeholder('Enter 6-digit code'),
                    ])
                    ->action(function (array $data): void {
                        $service = app(TwoFactorAuthService::class);
                        $result = $service->disable2FA(Auth::user(), $data['verification_code']);

                        if ($result['success']) {
                            Notification::make()
                                ->title($result['message'])
                                ->success()
                                ->send();

                            $this->redirect(request()->header('Referer'));
                        } else {
                            Notification::make()
                                ->title($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
            ];
        }

        return [
            Action::make('setup_2fa')
                ->label('Setup 2FA')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->action(function (): void {
                    $this->startSetup();
                }),
        ];
    }

    public function startSetup(): void
    {
        $service = app(TwoFactorAuthService::class);
        $user = Auth::user();

        $this->secretKey = $service->generateSecretKey();
        $this->qrCodeUrl = $service->generateQrCodeUrl($user, $this->secretKey);
        $this->showSetup = true;
    }

    public function enable2FA(): void
    {
        $service = app(TwoFactorAuthService::class);
        $result = $service->enable2FA(Auth::user(), $this->secretKey, $this->verification_code);

        if ($result['success']) {
            $this->backupCodes = $result['backup_codes'];
            $this->showBackupCodes = true;
            $this->showSetup = false;

            Notification::make()
                ->title($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title($result['message'])
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('superuser');
    }

    public function get2FAStatus(): array
    {
        $user = Auth::user();
        $service = app(TwoFactorAuthService::class);

        return [
            'enabled' => $user->two_factor_enabled,
            'enabled_at' => $user->two_factor_enabled_at,
            'backup_codes_count' => $service->getRemainingBackupCodesCount($user),
            'should_prompt' => $service->shouldPromptFor2FA($user),
        ];
    }
}
