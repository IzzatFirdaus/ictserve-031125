<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI;

use App\Filament\Clusters\OllamaAI;
use App\Filament\Resources\OllamaAI\MessageLogResource\Pages;
use App\Models\MessageLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Sumber Filament untuk Log Mesej AI
 *
 * Menyediakan antara muka baca sahaja untuk:
 * - Paparan log terperinci
 * - Penapisan mengikut jenis operasi, julat tarikh, pengguna
 * - Carian pada input disanitasi dan ringkasan respons
 * - Pagination (25 rekod setiap halaman)
 * - Paparan lineage data
 *
 * Selaras dengan D09 v3.6.0: Dual Audit System
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 4.1, 4.2, 4.4, 6.5
 */
class MessageLogResource extends Resource
{
    protected static ?string $model = MessageLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('ollama.message_log.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ollama.message_log.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ollama.message_log.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.message_log.section_request'))
                    ->schema([
                        Forms\Components\TextInput::make('request_id')
                            ->label(__('ollama.message_log.request_id'))
                            ->disabled(),

                        Forms\Components\Select::make('operation_type')
                            ->label(__('ollama.message_log.operation_type'))
                            ->options([
                                'faq_query' => __('ollama.message_log.operation_faq_query'),
                                'document_analysis' => __('ollama.message_log.operation_document_analysis'),
                                'auto_reply_generation' => __('ollama.message_log.operation_auto_reply_generation'),
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('user.name')
                            ->label(__('ollama.message_log.user'))
                            ->disabled()
                            ->default(fn (?MessageLog $record): string => $record?->user?->name ?? __('ollama.common.guest')),

                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label(__('ollama.message_log.processed_at'))
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make(__('ollama.message_log.section_response'))
                    ->schema([
                        Forms\Components\Textarea::make('sanitized_input')
                            ->label(__('ollama.message_log.sanitized_input'))
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('response_summary')
                            ->label(__('ollama.message_log.response_summary'))
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('metadata')
                            ->label(__('ollama.message_log.metadata'))
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('ollama.message_log.section_audit'))
                    ->schema([
                        Forms\Components\TextInput::make('hash')
                            ->label(__('ollama.message_log.hash'))
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('previous_hash')
                            ->label(__('ollama.message_log.previous_hash'))
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_id')
                    ->label(__('ollama.message_log.request_id'))
                    ->searchable()
                    ->sortable()
                    ->limit(12)
                    ->tooltip(fn (MessageLog $record): string => $record->request_id)
                    ->copyable()
                    ->copyMessage('ID disalin'),

                Tables\Columns\TextColumn::make('operation_type')
                    ->label(__('ollama.message_log.operation_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'faq_query' => 'primary',
                        'document_analysis' => 'info',
                        'auto_reply_generation' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'faq_query' => __('ollama.message_log.operation_faq_query'),
                        'document_analysis' => __('ollama.message_log.operation_document_analysis'),
                        'auto_reply_generation' => __('ollama.message_log.operation_auto_reply_generation'),
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('ollama.message_log.user'))
                    ->default(__('ollama.common.guest'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sanitized_input')
                    ->label(__('ollama.message_log.sanitized_input'))
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('response_summary')
                    ->label(__('ollama.message_log.response_summary'))
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label(__('ollama.message_log.processed_at'))
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('ollama.message_log.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label(__('ollama.message_log.filter_operation_type'))
                    ->options([
                        'faq_query' => __('ollama.message_log.operation_faq_query'),
                        'document_analysis' => __('ollama.message_log.operation_document_analysis'),
                        'auto_reply_generation' => __('ollama.message_log.operation_auto_reply_generation'),
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('ollama.message_log.filter_user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date_range')
                    ->schema([
                        Forms\Components\DatePicker::make('from_date')
                            ->label(__('ollama.message_log.filter_from_date')),
                        Forms\Components\DatePicker::make('until_date')
                            ->label(__('ollama.message_log.filter_until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '>=', $date),
                            )
                            ->when(
                                $data['until_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
            ])
            ->groupedBulkActions([])
            ->defaultSort('processed_at', 'desc')
            ->paginated([25, 50, 100])
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageLogs::route('/'),
            'view' => Pages\ViewMessageLog::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('ollama.message_log.section_request'))
                ->schema([
                    TextEntry::make('request_id')
                        ->label(__('ollama.message_log.request_id'))
                        ->copyable()
                        ->copyMessage('ID disalin'),
                    TextEntry::make('operation_type')
                        ->label(__('ollama.message_log.operation_type'))
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'faq_query' => 'primary',
                            'document_analysis' => 'info',
                            'auto_reply_generation' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'faq_query' => __('ollama.message_log.operation_faq_query'),
                            'document_analysis' => __('ollama.message_log.operation_document_analysis'),
                            'auto_reply_generation' => __('ollama.message_log.operation_auto_reply_generation'),
                            default => $state,
                        }),
                    TextEntry::make('user.name')
                        ->label(__('ollama.message_log.user'))
                        ->default(__('ollama.common.guest')),
                    TextEntry::make('processed_at')
                        ->label(__('ollama.message_log.processed_at'))
                        ->dateTime('d M Y H:i:s'),
                ])
                ->columns(2),

            Section::make(__('ollama.message_log.section_response'))
                ->schema([
                    TextEntry::make('sanitized_input')
                        ->label(__('ollama.message_log.sanitized_input'))
                        ->columnSpanFull()
                        ->wrap(),
                    TextEntry::make('response_summary')
                        ->label(__('ollama.message_log.response_summary'))
                        ->columnSpanFull()
                        ->wrap(),
                    KeyValueEntry::make('metadata')
                        ->label(__('ollama.message_log.metadata'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('ollama.message_log.section_audit'))
                ->schema([
                    TextEntry::make('hash')
                        ->label(__('ollama.message_log.hash')),
                    TextEntry::make('previous_hash')
                        ->label(__('ollama.message_log.previous_hash')),
                ])
                ->columns(2),
        ]);
    }

    public static function canCreate(): bool
    {
        return false; // Log mesej dicipta secara automatik
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Log mesej adalah baca sahaja
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Log mesej tidak boleh dipadam
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * @return Builder<MessageLog>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->latest('processed_at');
    }
}
