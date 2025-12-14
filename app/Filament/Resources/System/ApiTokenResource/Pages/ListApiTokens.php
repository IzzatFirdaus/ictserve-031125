<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\ApiTokenResource\Pages;

use App\Filament\Resources\System\ApiTokenResource;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * List API Tokens Page
 *
 * Displays paginated API tokens with filtering and management capabilities.
 * Admin and superuser access only.
 *
 * @trace Requirements 37.1, 37.2, 37.3
 */
class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Token API';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_token')
                ->label('Cipta Token')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->label('Pengguna')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->helperText('Pilih pengguna yang akan menjadi pemilik token ini.'),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Token')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: API Produksi, Aplikasi Mudah Alih')
                        ->helperText('Nama deskriptif untuk mengenal pasti token ini.'),

                    Forms\Components\CheckboxList::make('abilities')
                        ->label('Keizinan Token')
                        ->options([
                            'read:tickets' => 'Baca Tiket - Lihat tiket helpdesk',
                            'write:tickets' => 'Tulis Tiket - Cipta/kemaskini tiket helpdesk',
                            'read:loans' => 'Baca Pinjaman - Lihat permohonan pinjaman',
                            'write:loans' => 'Tulis Pinjaman - Cipta/kemaskini permohonan pinjaman',
                            'admin:all' => 'Pentadbir - Akses pentadbiran penuh',
                        ])
                        ->default(['read:tickets', 'read:loans'])
                        ->columns(1)
                        ->helperText('Pilih keizinan yang perlu diberikan kepada token ini.'),

                    Forms\Components\Select::make('expiration_days')
                        ->label('Tamat Tempoh Token')
                        ->options([
                            7 => '7 hari',
                            30 => '30 hari (lalai)',
                            90 => '90 hari',
                            180 => '180 hari',
                            365 => '1 tahun',
                            0 => 'Tiada tamat tempoh',
                        ])
                        ->default(30)
                        ->required()
                        ->helperText('Tempoh sehingga token ini tamat.'),
                ])
                ->action(function (array $data): void {
                    /** @var User|null $user */
                    $user = User::find($data['user_id']);

                    if ($user === null) {
                        Notification::make()
                            ->danger()
                            ->title('Ralat')
                            ->body('Pengguna tidak ditemui.')
                            ->send();

                        return;
                    }

                    $service = app(ApiTokenService::class);

                    $abilities = $data['abilities'] ?? ['*'];
                    $expirationDays = (int) $data['expiration_days'];

                    // Convert 0 to null for never expires
                    $expirationDays = $expirationDays === 0 ? null : $expirationDays;

                    $token = $service->createToken(
                        user: $user,
                        name: $data['name'],
                        abilities: $abilities,
                        expirationDays: $expirationDays
                    );

                    // Show the plain text token to the user (only time it's visible)
                    Notification::make()
                        ->success()
                        ->title('Token berjaya dicipta')
                        ->body('Salin token ini sekarang. Token ini tidak akan dipaparkan lagi: '.$token->plainTextToken)
                        ->persistent()
                        ->send();
                })
                ->modalHeading('Cipta Token API Baharu')
                ->modalDescription('Cipta token API baharu dengan keizinan dan tempoh tamat tertentu.')
                ->modalSubmitActionLabel('Cipta Token'),

            Action::make('revoke_expired')
                ->label('Batalkan Token Luput')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Semua Token Luput')
                ->modalDescription('Tindakan ini akan memadam semua token API yang telah luput secara kekal. Tindakan ini tidak boleh diundur.')
                ->action(function (): void {
                    $count = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', Carbon::now())
                        ->delete();

                    Notification::make()
                        ->success()
                        ->title('Token luput dibatalkan')
                        ->body("{$count} token luput telah dibatalkan.")
                        ->send();
                }),

            Action::make('usage_stats')
                ->label('Statistik Penggunaan')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading('Statistik Penggunaan Token API')
                ->modalContent(function (): \Illuminate\Contracts\View\View {
                    $totalTokens = PersonalAccessToken::where('tokenable_type', User::class)->count();
                    $activeTokens = PersonalAccessToken::where('tokenable_type', User::class)
                        ->where(function ($query): void {
                            $query->whereNull('expires_at')
                                ->orWhere('expires_at', '>', Carbon::now());
                        })
                        ->count();
                    $expiredTokens = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', Carbon::now())
                        ->count();
                    $expiringSoon = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', Carbon::now())
                        ->where('expires_at', '<=', Carbon::now()->addDays(7))
                        ->count();

                    return view('filament.modals.api-token-stats', [
                        'totalTokens' => $totalTokens,
                        'activeTokens' => $activeTokens,
                        'expiredTokens' => $expiredTokens,
                        'expiringSoon' => $expiringSoon,
                    ]);
                })
                ->modalActions([
                    Action::make('close')
                        ->label('Tutup')
                        ->color('gray'),
                ]),
        ];
    }
}
