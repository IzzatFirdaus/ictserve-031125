<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ApiTokenResource\Pages;
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
use Laravel\Sanctum\PersonalAccessToken;
use UnitEnum;

/**
 * Filament Resource: API Token Management
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.4 (API Token Management)
 * @trace D04 §6.3 (API Security)
 * @trace Requirements 15.8, 15.9 (Token CRUD, Scopes)
 * @version 3.5.0
 * @created 2025-12-07
 */
class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Security';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('admin.api_tokens');
    }

    public static function getModelLabel(): string
    {
        return __('admin.api_token');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.api_tokens');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.token_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('admin.token_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('admin.token_name_placeholder'))
                            ->helperText(__('admin.token_name_help'))
                            ->autofocus(),

                        Forms\Components\TagsInput::make('abilities')
                            ->label(__('admin.token_scopes'))
                            ->placeholder(__('admin.token_scopes_placeholder'))
                            ->helperText(__('admin.token_scopes_help'))
                            ->suggestions([
                                'read:tickets',
                                'write:tickets',
                                'read:loans',
                                'write:loans',
                                'read:assets',
                                'write:assets',
                                'admin:all',
                            ])
                            ->default(['read:tickets', 'read:loans'])
                            ->required(),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label(__('admin.token_expires_at'))
                            ->nullable()
                            ->helperText(__('admin.token_expires_help'))
                            ->minDate(now())
                            ->default(now()->addMonths(6)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.token_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('abilities')
                    ->label(__('admin.token_scopes'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label(__('admin.last_used'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder(__('admin.never_used')),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('admin.expires'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success')
                    ->placeholder(__('admin.never_expires')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('expired')
                    ->label(__('admin.show_expired'))
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('my_tokens')
                    ->label(__('admin.my_tokens_only'))
                    ->query(fn (Builder $query): Builder => $query->where('tokenable_id', Auth::id())),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiTokens::route('/'),
            'create' => Pages\CreateApiToken::route('/create'),
            'edit' => Pages\EditApiToken::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Non-superusers can only see their own tokens
        if (!Auth::user()->hasRole('superuser')) {
            $query->where('tokenable_id', Auth::id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return Auth::check();
    }

    public static function canEdit($record): bool
    {
        return Auth::id() === $record->tokenable_id || Auth::user()?->hasRole('superuser');
    }

    public static function canDelete($record): bool
    {
        return Auth::id() === $record->tokenable_id || Auth::user()?->hasRole('superuser');
    }
}
