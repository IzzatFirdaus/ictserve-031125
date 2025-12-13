<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\TwoFactorAuthService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
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
class TwoFactorAuthentication extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.two-factor-authentication';

    public ?string $secretKey = null;

    public ?string $qrCodeUrl = null;

    /** @var array<int, string> */
    public array $backupCodes = [];

    public bool $showSetup = false;

    public bool $showBackupCodes = false;

    public string $verification_code = '';

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->two_factor_enabled) {
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

        if ($user?->two_factor_enabled) {
            return [
                Action::make('regenerate_backup_codes')
                    ->label('Regenerate Backup Codes')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        $service = app(TwoFactorAuthService::class);
                        $user = Auth::user();
                        if (! $user) {
                            return;
                        }

                        $this->backupCodes = $service->regenerateBackupCodes($user);
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
                        $user = Auth::user();

                        if (! $user) {
                            return;
                        }

                        $result = $service->disable2FA($user, $data['verification_code']);

                        if ($result['success']) {
                            Notification::make()
                                ->title(is_string($result['message'] ?? null) ? $result['message'] : 'Two-factor authentication disabled.')
                                ->success()
                                ->send();

                            $this->redirect(request()->header('Referer'));
                        } else {
                            Notification::make()
                                ->title(is_string($result['message'] ?? null) ? $result['message'] : 'Verification failed.')
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
                ->form([
                    Forms\Components\Placeholder::make('qr_code')
                        ->label('QR Code')
                        ->content(fn () => new HtmlString('<div class="flex justify-center p-4 border rounded-lg bg-white dark:bg-gray-800"><img src="'.$this->qrCodeUrl.'" alt="QR Code" class="w-48 h-48 object-contain" /></div>')),
                    Forms\Components\TextInput::make('secret_key')
                        ->label('Secret Key')
                        ->default(fn () => $this->secretKey)
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('verification_code')
                        ->label('Verification Code')
                        ->required()
                        ->length(6)
                        ->numeric()
                        ->placeholder('Enter 6-digit code'),
                ])
                ->action(function (array $data): void {
                    $this->verification_code = $data['verification_code'];
                    $this->enable2FA();
                })
                ->before(function (): void {
                    $this->startSetup();
                }),
        ];
    }

    public function startSetup(): void
    {
        $service = app(TwoFactorAuthService::class);
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->secretKey = $service->generateSecretKey();
        $otpauthUrl = $service->generateQrCodeUrl($user, $this->secretKey);

        // Generate QR code using external API
        $this->qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($otpauthUrl);
        $this->showSetup = true;
    }

    public function enable2FA(): void
    {
        $service = app(TwoFactorAuthService::class);
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $result = $service->enable2FA($user, (string) $this->secretKey, $this->verification_code);

        if ($result['success']) {
            /** @var array<int, string> $backupCodes */
            $backupCodes = is_array($result['backup_codes'] ?? null) ? array_values($result['backup_codes']) : [];
            $this->backupCodes = $backupCodes;
            $this->showBackupCodes = true;
            $this->showSetup = false;

            $message = is_string($result['message'] ?? null) ? $result['message'] : 'Two-factor authentication enabled.';

            Notification::make()
                ->title($message)
                ->success()
                ->send();
        } else {
            $message = is_string($result['message'] ?? null) ? $result['message'] : 'Verification failed.';

            Notification::make()
                ->title($message)
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function get2FAStatus(): array
    {
        $user = Auth::user();
        $service = app(TwoFactorAuthService::class);

        if (! $user) {
            return [
                'enabled' => false,
                'enabled_at' => null,
                'backup_codes_count' => 0,
                'should_prompt' => false,
            ];
        }

        return [
            'enabled' => $user->two_factor_enabled,
            'enabled_at' => $user->two_factor_enabled_at,
            'backup_codes_count' => $service->getRemainingBackupCodesCount($user),
            'should_prompt' => $service->shouldPromptFor2FA($user),
        ];
    }
}
