<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SsoAuditResource\Pages;
use App\Models\SsoAuditLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
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
 * Filament Resource: SSO Audit Log Management
 *
 * Admin interface for viewing and filtering SSO authentication logs.
 * Provides comprehensive audit trail for security monitoring.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace /D03-FR-004 (SSO Audit Logging)
 * @trace /Requirements 3.2, 10.3 (Admin SSO Management, Audit Reports)
 *
 * @version 3.6.0
 *
 * @created 2025-12-13
 */
class SsoAuditResource extends Resource
{
    protected static ?string $model = SsoAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('admin.sso_audit_logs');
    }

    public static function getModelLabel(): string
    {
        return __('admin.sso_audit_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.sso_audit_logs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.sso_audit_details'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('admin.email'))
                            ->disabled(),

                        Forms\Components\TextInput::make('user.name')
                            ->label(__('admin.user'))
                            ->disabled(),

                        Forms\Components\TextInput::make('google_id')
                            ->label(__('admin.google_id'))
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label(__('admin.ip_address'))
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label(__('admin.user_agent'))
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('success')
                            ->label(__('admin.success'))
                            ->disabled(),

                        Forms\Components\TextInput::make('error_type')
                            ->label(__('admin.error_type'))
                            ->disabled()
                            ->visible(fn ($record) => $record?->error_type !== null),

                        Forms\Components\Textarea::make('error_message')
                            ->label(__('admin.error_message'))
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->error_message !== null),

                        Forms\Components\DateTimePicker::make('attempted_at')
                            ->label(__('admin.attempted_at'))
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('admin.guest')),

                Tables\Columns\IconColumn::make('success')
                    ->label(__('admin.status'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('error_type')
                    ->label(__('admin.error_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'domain_error' => 'warning',
                        'oauth_error' => 'danger',
                        'oauth_state_error' => 'danger',
                        'network_error' => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('admin.ip_address'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('attempted_at')
                    ->label(__('admin.attempted_at'))
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('success')
                    ->label(__('admin.status'))
                    ->options([
                        '1' => __('admin.successful'),
                        '0' => __('admin.failed'),
                    ]),

                Tables\Filters\SelectFilter::make('error_type')
                    ->label(__('admin.error_type'))
                    ->options([
                        'domain_error' => __('admin.domain_error'),
                        'oauth_error' => __('admin.oauth_error'),
                        'oauth_state_error' => __('admin.oauth_state_error'),
                        'network_error' => __('admin.network_error'),
                        'general_error' => __('admin.general_error'),
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('admin.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('attempted_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('admin.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('admin.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attempted_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attempted_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('recent')
                    ->label(__('admin.last_24_hours'))
                    ->query(fn (Builder $query): Builder => $query->where('attempted_at', '>=', now()->subDay())),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\ExportBulkAction::make()
                        ->label(__('admin.export_selected')),
                ]),
            ])
            ->defaultSort('attempted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSsoAuditLogs::route('/'),
            'view' => Pages\ViewSsoAuditLog::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user');
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
