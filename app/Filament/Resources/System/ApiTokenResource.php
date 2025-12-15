<?php

declare(strict_types=1);

namespace App\Filament\Resources\System;

use App\Filament\Clusters\System as SystemCluster;
use App\Filament\Resources\System\ApiTokenResource\Pages;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * API Token Management Resource
 *
 * Provides comprehensive API token management for ICTServe admin panel.
 * Allows admin and superuser roles to create, view, and revoke API tokens.
 *
 * Features:
 * - Token creation with abilities selection
 * - Token list with usage statistics
 * - Token revocation action
 * - Expiration status display
 *
 * @trace Requirements 37.1, 37.2, 37.3
 *
 * @see D03 SRS-API-001 API Authentication Requirements
 * @see D09 §4.6 Dual Audit System
 */
class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static ?string $cluster = SystemCluster::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'api-tokens';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-key';
    }

    public static function getNavigationLabel(): string
    {
        return 'Token API';
    }

    public static function getModelLabel(): string
    {
        return 'Token API';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Token API';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canCreate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Token Information'))
                    ->description('Cipta token API baharu dengan kebolehan dan tempoh luput tertentu.')
                    ->components([
                        Forms\Components\Hidden::make('tokenable_type')
                            ->default(User::class)
                            ->required(),

                        Forms\Components\Select::make('tokenable_id')
                            ->label('Pengguna')
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih pengguna yang akan memiliki token ini.'),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Token')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('cth: API Produksi, Aplikasi Mudah Alih')
                            ->helperText('Nama deskriptif untuk mengenal pasti token ini.'),

                        Forms\Components\CheckboxList::make('abilities')
                            ->label('Kebolehan Token')
                            ->options([
                                'read:tickets' => 'Baca Tiket - Lihat tiket helpdesk',
                                'write:tickets' => 'Tulis Tiket - Cipta/kemaskini tiket helpdesk',
                                'read:loans' => 'Baca Pinjaman - Lihat permohonan pinjaman',
                                'write:loans' => 'Tulis Pinjaman - Cipta/kemaskini permohonan pinjaman',
                                'admin:all' => 'Admin Penuh - Akses pentadbiran penuh',
                            ])
                            ->default(['read:tickets', 'read:loans'])
                            ->columns(2)
                            ->helperText('Pilih kebenaran yang perlu ada pada token ini.'),

                        Forms\Components\Select::make('expiration_days')
                            ->label('Tempoh Luput Token')
                            ->options([
                                7 => '7 hari',
                                30 => '30 hari (lalai)',
                                90 => '90 hari',
                                180 => '180 hari',
                                365 => '1 tahun',
                                0 => 'Tiada tempoh luput',
                            ])
                            ->default(30)
                            ->required()
                            ->helperText('Tempoh sebelum token ini luput.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Token')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('abilities')
                    ->label('Kebolehan')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if (\is_array($state)) {
                            if (\in_array('*', $state, true)) {
                                return 'Semua';
                            }

                            return \implode(', ', \array_map(
                                fn ($ability) => \str_replace(['read:', 'write:', 'admin:'], ['R:', 'W:', 'A:'], $ability),
                                $state
                            ));
                        }

                        return (string) $state;
                    })
                    ->color(function ($state): string {
                        if (\is_array($state) && (\in_array('*', $state, true) || \in_array('admin:all', $state, true))) {
                            return 'danger';
                        }

                        return 'info';
                    }),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Luput')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->badge()
                    ->color(function (?string $state): string {
                        if ($state === null) {
                            return 'gray';
                        }

                        $expiresAt = Carbon::parse($state);
                        if ($expiresAt->isPast()) {
                            return 'danger';
                        }
                        if ($expiresAt->diffInDays(Carbon::now()) <= 7) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->formatStateUsing(function (?string $state): string {
                        if ($state === null) {
                            return 'Tiada';
                        }

                        $expiresAt = Carbon::parse($state);
                        if ($expiresAt->isPast()) {
                            return 'Luput';
                        }

                        return $expiresAt->format('d/m/Y H:i');
                    }),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Terakhir Digunakan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Tiada')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tokenable_id')
                    ->label('Pengguna')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expired')
                    ->label('Token Luput')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', Carbon::now())),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Akan Luput (7 hari)')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', Carbon::now())
                        ->where('expires_at', '<=', Carbon::now()->addDays(7))),

                Tables\Filters\Filter::make('never_expires')
                    ->label('Tiada Tempoh Luput')
                    ->query(fn (Builder $query): Builder => $query->whereNull('expires_at')),
            ])
            ->recordActions([
                ViewAction::make()->label('Lihat'),
                Action::make('revoke')
                    ->label('Batal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batal Token API')
                    ->modalDescription('Adakah anda pasti mahu membatalkan token ini? Tindakan ini tidak boleh diundur dan token akan berhenti berfungsi serta-merta.')
                    ->action(function (PersonalAccessToken $record): void {
                        /** @var User|null $tokenOwner */
                        $tokenOwner = $record->tokenable;

                        if ($tokenOwner !== null) {
                            $service = app(ApiTokenService::class);
                            $service->revokeToken($tokenOwner, $record->id);

                            Notification::make()
                                ->success()
                                ->title('Token Dibatalkan')
                                ->body('Token API telah berjaya dibatalkan.')
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Batal Dipilih')
                        ->modalHeading('Batal Token Dipilih')
                        ->modalDescription('Adakah anda pasti mahu membatalkan semua token yang dipilih? Tindakan ini tidak boleh diundur.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiTokens::route('/'),
            'create' => Pages\CreateApiToken::route('/create'),
            'view' => Pages\ViewApiToken::route('/{record}'),
        ];
    }

    /**
     * @return Builder<PersonalAccessToken>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tokenable_type', User::class)
            ->with(['tokenable'])
            ->latest();
    }

    public static function getNavigationBadge(): ?string
    {
        $activeCount = PersonalAccessToken::where('tokenable_type', User::class)
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->count();

        return (string) $activeCount;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $expiringSoon = PersonalAccessToken::where('tokenable_type', User::class)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', Carbon::now())
            ->where('expires_at', '<=', Carbon::now()->addDays(7))
            ->count();

        return $expiringSoon > 0 ? 'warning' : 'success';
    }
}
