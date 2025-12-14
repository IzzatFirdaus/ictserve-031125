<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\ApiTokenResource\Pages;

use App\Filament\Resources\System\ApiTokenResource;
use App\Models\ApiTokenUsageLog;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * View API Token Page
 *
 * Displays detailed information about an API token including usage statistics.
 *
 * @trace Requirements 37.1, 37.2, 37.3, 37.5
 */
class ViewApiToken extends ViewRecord
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string|Htmlable
    {
        /** @var PersonalAccessToken $record */
        $record = $this->record;

        return "Token API: {$record->name}";
    }

    public function form(Schema $schema): Schema
    {
        /** @var PersonalAccessToken $record */
        $record = $this->record;

        return $schema
            ->components([
                Section::make('Maklumat Token')
                    ->components([
                        Placeholder::make('name')
                            ->label('Nama Token')
                            ->content(fn () => $record->name ?? '-'),

                        Placeholder::make('owner')
                            ->label('Pemilik')
                            ->content(fn () => $record->tokenable?->name ?? '-'),

                        Placeholder::make('abilities')
                            ->label('Keizinan')
                            ->content(function () use ($record): string {
                                $abilities = $record->abilities ?? [];
                                if (\is_array($abilities)) {
                                    return \implode(', ', $abilities);
                                }

                                return (string) $abilities;
                            }),

                        Placeholder::make('created_at')
                            ->label('Dicipta')
                            ->content(fn () => $record->created_at?->format('d/m/Y H:i:s') ?? '-'),

                        Placeholder::make('expires_at')
                            ->label('Tamat Tempoh')
                            ->content(function () use ($record): string {
                                if ($record->expires_at === null) {
                                    return 'Tiada';
                                }

                                $expiresAt = Carbon::parse($record->expires_at);
                                if ($expiresAt->isPast()) {
                                    return 'Luput pada '.$expiresAt->format('d/m/Y H:i');
                                }

                                $daysRemaining = (int) Carbon::now()->diffInDays($expiresAt);

                                return $expiresAt->format('d/m/Y H:i')." ({$daysRemaining} hari lagi)";
                            }),

                        Placeholder::make('last_used_at')
                            ->label('Digunakan Kali Terakhir')
                            ->content(fn () => $record->last_used_at?->format('d/m/Y H:i:s') ?? 'Belum pernah digunakan'),
                    ])
                    ->columns(2),

                Section::make('Statistik Penggunaan (30 Hari Terakhir)')
                    ->components([
                        Placeholder::make('usage_total')
                            ->label('Jumlah Permintaan')
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->count();
                            }),

                        Placeholder::make('usage_successful')
                            ->label('Permintaan Berjaya')
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->where('response_status', '<', 400)
                                    ->count();
                            }),

                        Placeholder::make('usage_failed')
                            ->label('Permintaan Gagal')
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->where('response_status', '>=', 400)
                                    ->count();
                            }),

                        Placeholder::make('usage_endpoints')
                            ->label('Endpoint Unik')
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->distinct('endpoint')
                                    ->count('endpoint');
                            }),
                    ])
                    ->columns(4),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revoke')
                ->label('Batalkan Token')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Token API')
                ->modalDescription('Adakah anda pasti mahu membatalkan token ini? Tindakan ini tidak boleh diundur dan token akan berhenti berfungsi serta-merta.')
                ->action(function (): void {
                    /** @var PersonalAccessToken $record */
                    $record = $this->record;

                    /** @var User|null $tokenOwner */
                    $tokenOwner = $record->tokenable;

                    if ($tokenOwner !== null) {
                        $service = app(ApiTokenService::class);
                        $service->revokeToken($tokenOwner, $record->id);

                        Notification::make()
                            ->success()
                            ->title('Token dibatalkan')
                            ->body('Token API telah berjaya dibatalkan.')
                            ->send();

                        $this->redirect($this->getResource()::getUrl('index'));
                    }
                }),
        ];
    }
}
