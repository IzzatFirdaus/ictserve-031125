<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GoogleServicesAuditResource\Pages;
use App\Models\GoogleServicesAuditLog;
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
 * Filament Resource: Google Services Audit Log Management
 *
 * Comprehensive admin interface for viewing and filtering all Google services
 * audit logs including SSO authentication and Gmail API operations.
 * Provides filtering by service type, authentication method, and verification status.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace Requirements 6.3, 8.2, 9.1, 16.3 (Audit Logging, Admin Interface)
 *
 * @version 3.6.1
 *
 * @created 2025-12-31
 */
class GoogleServicesAuditResource extends Resource
{
    protected static ?string $model = GoogleServicesAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('admin.google_services_audit_logs');
    }

    public static function getModelLabel(): string
    {
        return __('admin.google_services_audit_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.google_services_audit_logs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.google_services_audit_details'))
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

                        Forms\Components\TextInput::make('service_type')
                            ->label(__('admin.service_type'))
                            ->disabled(),

                        Forms\Components\TextInput::make('operation_type')
                            ->label(__('admin.operation_type'))
                            ->disabled(),

                        Forms\Components\TextInput::make('authentication_method')
                            ->label(__('admin.authentication_method'))
                            ->disabled(),

                        Forms\Components\TextInput::make('verification_status')
                            ->label(__('admin.verification_status'))
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

                        Forms\Components\Textarea::make('metadata')
                            ->label(__('admin.metadata'))
                            ->disabled()
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => \is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                            ->visible(fn ($record) => ! empty($record?->metadata)),

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
                Tables\Columns\TextColumn::make('service_type')
                    ->label(__('admin.service_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GoogleServicesAuditLog::SERVICE_SSO => 'primary',
                        GoogleServicesAuditLog::SERVICE_GMAIL => 'info',
                        GoogleServicesAuditLog::SERVICE_CALENDAR => 'warning',
                        GoogleServicesAuditLog::SERVICE_DRIVE => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('operation_type')
                    ->label(__('admin.operation_type'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('success')
                    ->label(__('admin.status'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('authentication_method')
                    ->label(__('admin.auth_method'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        GoogleServicesAuditLog::AUTH_OAUTH => 'success',
                        GoogleServicesAuditLog::AUTH_SERVICE_ACCOUNT => 'info',
                        GoogleServicesAuditLog::AUTH_SMTP_FALLBACK => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('verification_status')
                    ->label(__('admin.verification'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        GoogleServicesAuditLog::VERIFICATION_VERIFIED => 'success',
                        GoogleServicesAuditLog::VERIFICATION_TESTING => 'warning',
                        GoogleServicesAuditLog::VERIFICATION_PENDING => 'info',
                        GoogleServicesAuditLog::VERIFICATION_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('error_type')
                    ->label(__('admin.error_type'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        GoogleServicesAuditLog::ERROR_DOMAIN => 'warning',
                        GoogleServicesAuditLog::ERROR_OAUTH, GoogleServicesAuditLog::ERROR_OAUTH_STATE => 'danger',
                        GoogleServicesAuditLog::ERROR_NETWORK => 'warning',
                        GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED, GoogleServicesAuditLog::ERROR_RATE_LIMITED => 'danger',
                        GoogleServicesAuditLog::ERROR_VERIFICATION => 'info',
                        default => 'gray',
                    })
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('admin.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('service_type')
                    ->label(__('admin.service_type'))
                    ->options([
                        GoogleServicesAuditLog::SERVICE_SSO => __('admin.service_sso'),
                        GoogleServicesAuditLog::SERVICE_GMAIL => __('admin.service_gmail'),
                        GoogleServicesAuditLog::SERVICE_CALENDAR => __('admin.service_calendar'),
                        GoogleServicesAuditLog::SERVICE_DRIVE => __('admin.service_drive'),
                    ]),

                Tables\Filters\SelectFilter::make('success')
                    ->label(__('admin.status'))
                    ->options([
                        '1' => __('admin.successful'),
                        '0' => __('admin.failed'),
                    ]),

                Tables\Filters\SelectFilter::make('operation_type')
                    ->label(__('admin.operation_type'))
                    ->options([
                        GoogleServicesAuditLog::OPERATION_AUTHENTICATE => __('admin.operation_authenticate'),
                        GoogleServicesAuditLog::OPERATION_SEND_EMAIL => __('admin.operation_send_email'),
                        GoogleServicesAuditLog::OPERATION_AUTHORIZE => __('admin.operation_authorize'),
                        GoogleServicesAuditLog::OPERATION_REFRESH_TOKEN => __('admin.operation_refresh_token'),
                        GoogleServicesAuditLog::OPERATION_LINK_ACCOUNT => __('admin.operation_link_account'),
                        GoogleServicesAuditLog::OPERATION_UNLINK_ACCOUNT => __('admin.operation_unlink_account'),
                    ]),

                Tables\Filters\SelectFilter::make('authentication_method')
                    ->label(__('admin.authentication_method'))
                    ->options([
                        GoogleServicesAuditLog::AUTH_OAUTH => __('admin.auth_oauth'),
                        GoogleServicesAuditLog::AUTH_SERVICE_ACCOUNT => __('admin.auth_service_account'),
                        GoogleServicesAuditLog::AUTH_SMTP_FALLBACK => __('admin.auth_smtp_fallback'),
                    ]),

                Tables\Filters\SelectFilter::make('verification_status')
                    ->label(__('admin.verification_status'))
                    ->options([
                        GoogleServicesAuditLog::VERIFICATION_VERIFIED => __('admin.verification_verified'),
                        GoogleServicesAuditLog::VERIFICATION_TESTING => __('admin.verification_testing'),
                        GoogleServicesAuditLog::VERIFICATION_PENDING => __('admin.verification_pending'),
                        GoogleServicesAuditLog::VERIFICATION_REJECTED => __('admin.verification_rejected'),
                    ]),

                Tables\Filters\SelectFilter::make('error_type')
                    ->label(__('admin.error_type'))
                    ->options([
                        GoogleServicesAuditLog::ERROR_DOMAIN => __('admin.domain_error'),
                        GoogleServicesAuditLog::ERROR_OAUTH => __('admin.oauth_error'),
                        GoogleServicesAuditLog::ERROR_OAUTH_STATE => __('admin.oauth_state_error'),
                        GoogleServicesAuditLog::ERROR_NETWORK => __('admin.network_error'),
                        GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED => __('admin.quota_exceeded'),
                        GoogleServicesAuditLog::ERROR_RATE_LIMITED => __('admin.rate_limited'),
                        GoogleServicesAuditLog::ERROR_VERIFICATION => __('admin.verification_error'),
                        GoogleServicesAuditLog::ERROR_GENERAL => __('admin.general_error'),
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

                Tables\Filters\Filter::make('quota_errors')
                    ->label(__('admin.quota_errors'))
                    ->query(fn (Builder $query): Builder => $query->whereIn('error_type', [
                        GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED,
                        GoogleServicesAuditLog::ERROR_RATE_LIMITED,
                    ])),
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
            'index' => Pages\ListGoogleServicesAuditLogs::route('/'),
            'view' => Pages\ViewGoogleServicesAuditLog::route('/{record}'),
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
