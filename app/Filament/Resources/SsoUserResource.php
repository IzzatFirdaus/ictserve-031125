<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SsoUserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Filament Resource: SSO User Management
 *
 * Admin interface for managing users with Google SSO linked accounts.
 * Provides viewing, filtering, and unlinking capabilities.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-003 (SSO User Management)
 * @trace Requirements 3.1, 3.3 (Admin SSO Management)
 *
 * @version 3.6.0
 *
 * @created 2025-12-13
 */
class SsoUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('admin.sso_users');
    }

    public static function getModelLabel(): string
    {
        return __('admin.sso_user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.sso_users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.sso_user_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('admin.name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->label(__('admin.email'))
                            ->disabled(),

                        Forms\Components\TextInput::make('google_id')
                            ->label(__('admin.google_id'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label(__('admin.email_verified_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label(__('admin.created_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label(__('admin.updated_at'))
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('google_id')
                    ->label(__('admin.google_id'))
                    ->searchable()
                    ->toggleable()
                    ->limit(20),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('admin.verified'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('ssoAuditLogs_count')
                    ->label(__('admin.login_count'))
                    ->counts('ssoAuditLogs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ssoAuditLogs.attempted_at')
                    ->label(__('admin.last_sso_login'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder(__('admin.never')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label(__('admin.verified_only'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Tables\Filters\Filter::make('unverified')
                    ->label(__('admin.unverified_only'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),

                Tables\Filters\Filter::make('recent_login')
                    ->label(__('admin.recent_login'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('ssoAuditLogs', function (Builder $q) {
                        $q->where('attempted_at', '>=', now()->subDays(7))
                            ->where('success', true);
                    })),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\Action::make('unlink_sso')
                    ->label(__('admin.unlink_sso'))
                    ->icon(Heroicon::OutlinedLinkSlash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.unlink_sso_confirm_title'))
                    ->modalDescription(__('admin.unlink_sso_confirm_description'))
                    ->modalSubmitActionLabel(__('admin.unlink_sso_confirm_button'))
                    ->action(function (User $record): void {
                        $record->update([
                            'google_id' => null,
                            'google_token' => null,
                            'google_refresh_token' => null,
                        ]);

                        Notification::make()
                            ->title(__('admin.sso_unlinked_success'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (User $record): bool => $record->google_id !== null),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('bulk_unlink_sso')
                        ->label(__('admin.bulk_unlink_sso'))
                        ->icon(Heroicon::OutlinedLinkSlash)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->google_id !== null) {
                                    $record->update([
                                        'google_id' => null,
                                        'google_token' => null,
                                        'google_refresh_token' => null,
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title(__('admin.bulk_sso_unlinked_success', ['count' => $count]))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSsoUsers::route('/'),
            'view' => Pages\ViewSsoUser::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('google_id')
            ->with(['ssoAuditLogs' => function ($query) {
                $query->where('success', true)
                    ->latest('attempted_at')
                    ->limit(1);
            }]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && ($user->hasRole('admin') || $user->hasRole('superuser'));
    }
}
