<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GoogleVerificationResource\Pages;
use App\Models\GoogleOAuthVerification;
use App\Models\User;
use App\Services\GoogleOAuthVerificationService;
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
 * Filament Resource: Google OAuth Verification Management
 *
 * Admin interface for managing OAuth verification status and test users.
 * Provides verification status monitoring, test user administration,
 * and verification requirements checklist display.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace Requirements 4.1, 4.4, 8.3 (OAuth Verification Management)
 *
 * @version 3.6.1
 *
 * @created 2025-12-31
 */
class GoogleVerificationResource extends Resource
{
    protected static ?string $model = GoogleOAuthVerification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?int $navigationSort = 9;

    public static function getNavigationLabel(): string
    {
        return __('admin.google_verification');
    }

    public static function getModelLabel(): string
    {
        return __('admin.google_verification_record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.google_verification_records');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.verification_details'))
                    ->schema([
                        Forms\Components\TextInput::make('client_id')
                            ->label(__('admin.client_id'))
                            ->disabled(),

                        Forms\Components\Select::make('verification_status')
                            ->label(__('admin.verification_status'))
                            ->options([
                                GoogleOAuthVerificationService::STATUS_VERIFIED => __('admin.status_verified'),
                                GoogleOAuthVerificationService::STATUS_TESTING => __('admin.status_testing'),
                                GoogleOAuthVerificationService::STATUS_PENDING => __('admin.status_pending'),
                                GoogleOAuthVerificationService::STATUS_REJECTED => __('admin.status_rejected'),
                            ])
                            ->required(),

                        Forms\Components\DateTimePicker::make('verification_submitted_at')
                            ->label(__('admin.verification_submitted_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('verification_approved_at')
                            ->label(__('admin.verification_approved_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_status_check')
                            ->label(__('admin.last_status_check'))
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make(__('admin.test_users_section'))
                    ->schema([
                        Forms\Components\TagsInput::make('test_users')
                            ->label(__('admin.test_users'))
                            ->helperText(__('admin.test_users_helper'))
                            ->placeholder(__('admin.add_test_user_email'))
                            ->splitKeys(['Tab', ',', ' '])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('admin.quota_limits_section'))
                    ->schema([
                        Forms\Components\KeyValue::make('quota_limits')
                            ->label(__('admin.quota_limits'))
                            ->keyLabel(__('admin.quota_type'))
                            ->valueLabel(__('admin.quota_value'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('admin.verification_documents_section'))
                    ->schema([
                        Forms\Components\KeyValue::make('verification_documents')
                            ->label(__('admin.verification_documents'))
                            ->keyLabel(__('admin.document_type'))
                            ->valueLabel(__('admin.document_url'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')
                    ->label(__('admin.client_id'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('verification_status')
                    ->label(__('admin.verification_status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GoogleOAuthVerificationService::STATUS_VERIFIED => 'success',
                        GoogleOAuthVerificationService::STATUS_TESTING => 'warning',
                        GoogleOAuthVerificationService::STATUS_PENDING => 'info',
                        GoogleOAuthVerificationService::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        GoogleOAuthVerificationService::STATUS_VERIFIED => __('admin.status_verified'),
                        GoogleOAuthVerificationService::STATUS_TESTING => __('admin.status_testing'),
                        GoogleOAuthVerificationService::STATUS_PENDING => __('admin.status_pending'),
                        GoogleOAuthVerificationService::STATUS_REJECTED => __('admin.status_rejected'),
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('test_users')
                    ->label(__('admin.test_users_count'))
                    ->formatStateUsing(fn ($state): string => \is_array($state) ? (string) \count($state) : '0')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('verification_submitted_at')
                    ->label(__('admin.submitted_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('verification_approved_at')
                    ->label(__('admin.approved_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('last_status_check')
                    ->label(__('admin.last_checked'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.updated_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label(__('admin.verification_status'))
                    ->options([
                        GoogleOAuthVerificationService::STATUS_VERIFIED => __('admin.status_verified'),
                        GoogleOAuthVerificationService::STATUS_TESTING => __('admin.status_testing'),
                        GoogleOAuthVerificationService::STATUS_PENDING => __('admin.status_pending'),
                        GoogleOAuthVerificationService::STATUS_REJECTED => __('admin.status_rejected'),
                    ]),

                Tables\Filters\Filter::make('has_test_users')
                    ->label(__('admin.has_test_users'))
                    ->query(fn (Builder $query): Builder => $query->whereRaw('JSON_LENGTH(test_users) > 0')),

                Tables\Filters\Filter::make('recently_checked')
                    ->label(__('admin.recently_checked'))
                    ->query(fn (Builder $query): Builder => $query->where('last_status_check', '>=', now()->subDay())),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoogleVerifications::route('/'),
            'view' => Pages\ViewGoogleVerification::route('/{record}'),
            'edit' => Pages\EditGoogleVerification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasRole('superuser');
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && ($user->hasRole('admin') || $user->hasRole('superuser'));
    }
}
