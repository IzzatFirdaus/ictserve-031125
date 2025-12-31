<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI;

use App\Filament\Clusters\OllamaAI;
use App\Filament\Resources\OllamaAI\BedrockModelConfigResource\Pages;
use App\Models\BedrockModelConfig;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Sumber Filament untuk Konfigurasi Model Bedrock
 *
 * Menyediakan antara muka pengurusan untuk:
 * - Konfigurasi model AI (Claude 4.5, Nova, Titan)
 * - Tetapan kos per token
 * - Peraturan penghalaan (routing rules)
 * - Kawalan bajet dan had kadar
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 9.6 (Hybrid Configuration Management)
 * @trace D18-§4.1 (AWS Bedrock Integration)
 */
class BedrockModelConfigResource extends Resource
{
    protected static ?string $model = BedrockModelConfig::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('ollama.bedrock.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ollama.bedrock.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ollama.bedrock.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.bedrock.section_model_info'))
                    ->description(__('ollama.bedrock.section_model_info_description'))
                    ->schema([
                        Forms\Components\TextInput::make('model_id')
                            ->label(__('ollama.bedrock.model_id'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('us.anthropic.claude-haiku-4-5-20251001-v1:0')
                            ->helperText(__('ollama.bedrock.model_id_helper'))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('model_name')
                            ->label(__('ollama.bedrock.model_name'))
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Claude Haiku 4.5')
                            ->helperText(__('ollama.bedrock.model_name_helper')),

                        Forms\Components\Select::make('provider')
                            ->label(__('ollama.bedrock.provider'))
                            ->required()
                            ->options([
                                'anthropic' => 'Anthropic (Claude)',
                                'amazon' => 'Amazon (Nova/Titan)',
                                'meta' => 'Meta (Llama)',
                                'cohere' => 'Cohere',
                                'ai21' => 'AI21 Labs',
                            ])
                            ->default('anthropic')
                            ->helperText(__('ollama.bedrock.provider_helper')),
                    ])
                    ->columns(2),

                Section::make(__('ollama.bedrock.section_task_routing'))
                    ->description(__('ollama.bedrock.section_task_routing_description'))
                    ->schema([
                        Forms\Components\CheckboxList::make('task_types')
                            ->label(__('ollama.bedrock.task_types'))
                            ->options([
                                'faq_simple' => 'FAQ Mudah (< 50 perkataan)',
                                'faq_complex' => 'FAQ Kompleks (> 50 perkataan)',
                                'document_analysis' => 'Analisis Dokumen',
                                'auto_reply' => 'Penjanaan Auto-Reply',
                                'code_analysis' => 'Analisis Kod',
                                'summarization' => 'Ringkasan Teks',
                                'translation' => 'Terjemahan',
                            ])
                            ->columns(2)
                            ->helperText(__('ollama.bedrock.task_types_helper')),
                    ]),

                Section::make(__('ollama.bedrock.section_cost_limits'))
                    ->description(__('ollama.bedrock.section_cost_limits_description'))
                    ->schema([
                        Forms\Components\TextInput::make('cost_per_token')
                            ->label(__('ollama.bedrock.cost_per_token'))
                            ->numeric()
                            ->step(0.00000001)
                            ->minValue(0)
                            ->prefix('USD')
                            ->placeholder('0.00025')
                            ->helperText(__('ollama.bedrock.cost_per_token_helper')),

                        Forms\Components\TextInput::make('max_tokens')
                            ->label(__('ollama.bedrock.max_tokens'))
                            ->numeric()
                            ->minValue(100)
                            ->maxValue(200000)
                            ->default(4096)
                            ->helperText(__('ollama.bedrock.max_tokens_helper')),

                        Forms\Components\Toggle::make('enabled')
                            ->label(__('ollama.bedrock.enabled'))
                            ->default(true)
                            ->helperText(__('ollama.bedrock.enabled_helper')),
                    ])
                    ->columns(3),

                Section::make(__('ollama.bedrock.section_advanced'))
                    ->description(__('ollama.bedrock.section_advanced_description'))
                    ->schema([
                        Forms\Components\KeyValue::make('configuration')
                            ->label(__('ollama.bedrock.configuration'))
                            ->keyLabel(__('ollama.bedrock.config_key'))
                            ->valueLabel(__('ollama.bedrock.config_value'))
                            ->addActionLabel(__('ollama.bedrock.add_config'))
                            ->helperText(__('ollama.bedrock.configuration_helper'))
                            ->columnSpanFull(),

                        Forms\Components\Select::make('created_by')
                            ->label(__('ollama.bedrock.created_by'))
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled(fn (?BedrockModelConfig $record) => $record !== null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('model_name')
                    ->label(__('ollama.bedrock.model_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('model_id')
                    ->label(__('ollama.bedrock.model_id'))
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (BedrockModelConfig $record): string => $record->model_id)
                    ->toggleable(),

                TextColumn::make('provider')
                    ->label(__('ollama.bedrock.provider'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'anthropic' => 'primary',
                        'amazon' => 'warning',
                        'meta' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('task_types')
                    ->label(__('ollama.bedrock.task_types'))
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('cost_per_token')
                    ->label(__('ollama.bedrock.cost_per_token'))
                    ->money('USD', 8)
                    ->sortable(),

                TextColumn::make('max_tokens')
                    ->label(__('ollama.bedrock.max_tokens'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('enabled')
                    ->label(__('ollama.bedrock.enabled'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('ollama.bedrock.created_by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('ollama.bedrock.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label(__('ollama.bedrock.provider'))
                    ->options([
                        'anthropic' => 'Anthropic (Claude)',
                        'amazon' => 'Amazon (Nova/Titan)',
                        'meta' => 'Meta (Llama)',
                    ]),

                Tables\Filters\TernaryFilter::make('enabled')
                    ->label(__('ollama.bedrock.enabled')),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('toggle_enabled')
                    ->label(fn (BedrockModelConfig $record): string => $record->enabled
                        ? __('ollama.bedrock.disable')
                        : __('ollama.bedrock.enable'))
                    ->icon(fn (BedrockModelConfig $record): string => $record->enabled
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn (BedrockModelConfig $record): string => $record->enabled
                        ? 'danger'
                        : 'success')
                    ->requiresConfirmation()
                    ->action(function (BedrockModelConfig $record): void {
                        $record->update(['enabled' => ! $record->enabled]);
                        Cache::forget('bedrock_model_configs');
                        Notification::make()
                            ->title($record->enabled
                                ? __('ollama.bedrock.model_enabled')
                                : __('ollama.bedrock.model_disabled'))
                            ->success()
                            ->send();
                    }),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('model_name', 'asc')
            ->searchPlaceholder(__('ollama.bedrock.search_placeholder'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBedrockModelConfigs::route('/'),
            'create' => Pages\CreateBedrockModelConfig::route('/create'),
            'view' => Pages\ViewBedrockModelConfig::route('/{record}'),
            'edit' => Pages\EditBedrockModelConfig::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.bedrock.section_model_info'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('model_name')
                                ->label(__('ollama.bedrock.model_name')),
                            TextEntry::make('model_id')
                                ->label(__('ollama.bedrock.model_id'))
                                ->copyable(),
                            TextEntry::make('provider')
                                ->label(__('ollama.bedrock.provider'))
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'anthropic' => 'primary',
                                    'amazon' => 'warning',
                                    default => 'gray',
                                }),
                            TextEntry::make('enabled')
                                ->label(__('ollama.bedrock.enabled'))
                                ->badge()
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                ->formatStateUsing(fn (bool $state): string => $state
                                    ? __('ollama.bedrock.yes')
                                    : __('ollama.bedrock.no')),
                            TextEntry::make('task_types')
                                ->label(__('ollama.bedrock.task_types'))
                                ->badge()
                                ->separator(',')
                                ->columnSpan(2),
                            TextEntry::make('cost_per_token')
                                ->label(__('ollama.bedrock.cost_per_token'))
                                ->money('USD', 8),
                            TextEntry::make('max_tokens')
                                ->label(__('ollama.bedrock.max_tokens'))
                                ->numeric(),
                            TextEntry::make('creator.name')
                                ->label(__('ollama.bedrock.created_by')),
                            TextEntry::make('updated_at')
                                ->label(__('ollama.bedrock.updated_at'))
                                ->dateTime('d M Y H:i'),
                        ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * @return Builder<BedrockModelConfig>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['creator'])
            ->withTrashed();
    }
}
