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
        return __('API Tokens');
    }

    public static function getModelLabel(): string
    {
        return __('API Token');
    }

    public static function getPluralModelLabel(): string
    {
        return __('API Tokens');
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
                    ->description(__('Create a new API token with specific abilities and expiration.'))
                    ->components([
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->relationship('tokenable', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText(__('Select the user who will own this token.')),

                        Forms\Components\TextInput::make('name')
                            ->label(__('Token Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('e.g., Production API, Mobile App'))
                            ->helperText(__('A descriptive name to identify this token.')),

                        Forms\Components\CheckboxList::make('abilities')
                            ->label(__('Token Abilities'))
                            ->options([
                                'read:tickets' => __('Read Tickets - View helpdesk tickets'),
                                'write:tickets' => __('Write Tickets - Create/update helpdesk tickets'),
                                'read:loans' => __('Read Loans - View loan applications'),
                                'write:loans' => __('Write Loans - Create/update loan applications'),
                                'admin:all' => __('Admin All - Full administrative access'),
                            ])
                            ->default(['read:tickets', 'read:loans'])
                            ->columns(2)
                            ->helperText(__('Select the permissions this token should have.')),

                        Forms\Components\Select::make('expiration_days')
                            ->label(__('Token Expiration'))
                            ->options([
                                7 => __('7 days'),
                                30 => __('30 days (default)'),
                                90 => __('90 days'),
                                180 => __('180 days'),
                                365 => __('1 year'),
                                0 => __('Never expires'),
                            ])
                            ->default(30)
                            ->required()
                            ->helperText(__('How long until this token expires.')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Token Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label(__('User'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('abilities')
                    ->label(__('Abilities'))
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if (\is_array($state)) {
                            if (\in_array('*', $state, true)) {
                                return 'All';
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
                    ->label(__('Expires'))
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
                            return __('Never');
                        }

                        $expiresAt = Carbon::parse($state);
                        if ($expiresAt->isPast()) {
                            return __('Expired');
                        }

                        return $expiresAt->format('d/m/Y H:i');
                    }),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label(__('Last Used'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder(__('Never'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tokenable_id')
                    ->label(__('User'))
                    ->relationship('tokenable', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expired')
                    ->label(__('Expired Tokens'))
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', Carbon::now())),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label(__('Expiring Soon (7 days)'))
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', Carbon::now())
                        ->where('expires_at', '<=', Carbon::now()->addDays(7))),

                Tables\Filters\Filter::make('never_expires')
                    ->label(__('Never Expires'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('expires_at')),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('revoke')
                    ->label(__('Revoke'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Revoke API Token'))
                    ->modalDescription(__('Are you sure you want to revoke this token? This action cannot be undone and the token will immediately stop working.'))
                    ->action(function (PersonalAccessToken $record): void {
                        /** @var User|null $tokenOwner */
                        $tokenOwner = $record->tokenable;

                        if ($tokenOwner !== null) {
                            $service = app(ApiTokenService::class);
                            $service->revokeToken($tokenOwner, $record->id);

                            Notification::make()
                                ->success()
                                ->title(__('Token Revoked'))
                                ->body(__('The API token has been revoked successfully.'))
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Revoke Selected'))
                        ->modalHeading(__('Revoke Selected Tokens'))
                        ->modalDescription(__('Are you sure you want to revoke all selected tokens? This action cannot be undone.')),
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
