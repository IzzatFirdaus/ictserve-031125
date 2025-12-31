<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI;

use App\Filament\Clusters\OllamaAI;
use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource\Pages;
use App\Models\AutoReplyTemplate;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Sumber Filament untuk Pengurusan Template Auto-Reply
 *
 * Menyediakan antara muka untuk:
 * - Editor template dengan sokongan placeholder pembolehubah
 * - Fungsi ujian dan pratonton template
 * - Pengurusan versi template
 * - Antara muka pengurusan aliran kerja kelulusan
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 3.4, 5.1, 5.5
 */
class AutoReplyTemplateResource extends Resource
{
    protected static ?string $model = AutoReplyTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('ollama.template.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ollama.template.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ollama.template.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.template.section_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('ollama.template.name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('ollama.template.name_helper')),

                        Forms\Components\Select::make('status')
                            ->label(__('ollama.template.status'))
                            ->options([
                                AutoReplyTemplate::STATUS_DRAFT => __('ollama.template.status_draft'),
                                AutoReplyTemplate::STATUS_ACTIVE => __('ollama.template.status_active'),
                                AutoReplyTemplate::STATUS_ARCHIVED => __('ollama.template.status_archived'),
                            ])
                            ->default(AutoReplyTemplate::STATUS_DRAFT)
                            ->required(),

                        Forms\Components\Select::make('created_by')
                            ->label(__('ollama.template.created_by'))
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled(fn (?AutoReplyTemplate $record) => $record !== null),
                    ])
                    ->columns(3),

                Section::make(__('ollama.template.section_content'))
                    ->schema([
                        Forms\Components\RichEditor::make('template_content')
                            ->label(__('ollama.template.template_content'))
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->helperText(__('ollama.template.template_content_helper')),
                    ]),

                Section::make(__('ollama.template.section_variables'))
                    ->schema([
                        Forms\Components\KeyValue::make('variables')
                            ->label(__('ollama.template.variables'))
                            ->keyLabel('Nama Pembolehubah')
                            ->valueLabel('Penerangan')
                            ->addActionLabel('Tambah Pembolehubah')
                            ->helperText(__('ollama.template.variables_helper'))
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
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

                TextColumn::make('name')
                    ->label(__('ollama.template.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('ollama.template.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        AutoReplyTemplate::STATUS_DRAFT => 'warning',
                        AutoReplyTemplate::STATUS_ACTIVE => 'success',
                        AutoReplyTemplate::STATUS_ARCHIVED => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        AutoReplyTemplate::STATUS_DRAFT => __('ollama.template.status_draft'),
                        AutoReplyTemplate::STATUS_ACTIVE => __('ollama.template.status_active'),
                        AutoReplyTemplate::STATUS_ARCHIVED => __('ollama.template.status_archived'),
                        default => $state,
                    }),

                TextColumn::make('drafts_count')
                    ->label('Draf')
                    ->counts('drafts')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('creator.name')
                    ->label(__('ollama.template.created_by'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('ollama.template.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('ollama.template.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('ollama.template.status'))
                    ->options([
                        AutoReplyTemplate::STATUS_DRAFT => __('ollama.template.status_draft'),
                        AutoReplyTemplate::STATUS_ACTIVE => __('ollama.template.status_active'),
                        AutoReplyTemplate::STATUS_ARCHIVED => __('ollama.template.status_archived'),
                    ]),

                Tables\Filters\SelectFilter::make('created_by')
                    ->label(__('ollama.template.created_by'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),

                Action::make('activate')
                    ->label(__('ollama.template.activate'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (AutoReplyTemplate $record): void {
                        $record->activate();

                        Notification::make()
                            ->title('Template diaktifkan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (AutoReplyTemplate $record): bool => ! $record->isActive()),

                Action::make('archive')
                    ->label(__('ollama.template.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (AutoReplyTemplate $record): void {
                        $record->archive();

                        Notification::make()
                            ->title('Template diarkibkan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (AutoReplyTemplate $record): bool => ! $record->isArchived()),

                Action::make('duplicate')
                    ->label(__('ollama.template.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (AutoReplyTemplate $record): void {
                        $newTemplate = $record->replicate();
                        $newTemplate->name = $record->name.' (Salinan)';
                        $newTemplate->status = AutoReplyTemplate::STATUS_DRAFT;
                        $newTemplate->created_by = Auth::id();
                        $newTemplate->save();

                        Notification::make()
                            ->title('Template diduplikasi')
                            ->success()
                            ->send();
                    }),

                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->groupedBulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutoReplyTemplates::route('/'),
            'create' => Pages\CreateAutoReplyTemplate::route('/create'),
            'view' => Pages\ViewAutoReplyTemplate::route('/{record}'),
            'edit' => Pages\EditAutoReplyTemplate::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.template.section_details'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('ollama.template.name')),
                        TextEntry::make('status')
                            ->label(__('ollama.template.status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                AutoReplyTemplate::STATUS_DRAFT => 'warning',
                                AutoReplyTemplate::STATUS_ACTIVE => 'success',
                                AutoReplyTemplate::STATUS_ARCHIVED => 'gray',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                AutoReplyTemplate::STATUS_DRAFT => __('ollama.template.status_draft'),
                                AutoReplyTemplate::STATUS_ACTIVE => __('ollama.template.status_active'),
                                AutoReplyTemplate::STATUS_ARCHIVED => __('ollama.template.status_archived'),
                                default => $state,
                            }),
                        TextEntry::make('creator.name')
                            ->label(__('ollama.template.created_by')),
                        TextEntry::make('created_at')
                            ->label(__('ollama.template.created_at'))
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('ollama.template.updated_at'))
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),

                Section::make(__('ollama.template.section_content'))
                    ->schema([
                        TextEntry::make('template_content')
                            ->label(__('ollama.template.template_content'))
                            ->columnSpanFull()
                            ->wrap(),
                    ]),

                Section::make(__('ollama.template.section_variables'))
                    ->schema([
                        KeyValueEntry::make('variables')
                            ->label(__('ollama.template.variables'))
                            ->keyLabel('Nama Pembolehubah')
                            ->valueLabel('Penerangan')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
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
     * @return Builder<AutoReplyTemplate>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['creator'])
            ->withCount('drafts')
            ->withTrashed();
    }
}
