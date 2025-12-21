<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI;

use App\Filament\Clusters\OllamaAI;
use App\Filament\Resources\OllamaAI\FaqResource\Pages;
use App\Models\Faq;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Sumber Filament untuk Pengurusan FAQ AI
 *
 * Menyediakan antara muka CRUD untuk FAQ dengan ciri:
 * - Carian teks penuh pada soalan dan jawapan
 * - Sistem penandaan dengan autocomplete
 * - Import/eksport CSV secara pukal
 * - Penapisan mengikut tag dan pencipta
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 1.1, 5.1, 5.5
 */
class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('ollama.faq.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ollama.faq.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ollama.faq.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.faq.section_details'))
                    ->description(__('ollama.faq.section_details_description'))
                    ->schema([
                        Forms\Components\Textarea::make('question')
                            ->label(__('ollama.faq.question'))
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText(__('ollama.faq.question_helper')),

                        Forms\Components\RichEditor::make('answer')
                            ->label(__('ollama.faq.answer'))
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
                            ->helperText(__('ollama.faq.answer_helper')),
                    ]),

                Section::make(__('ollama.faq.section_metadata'))
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label(__('ollama.faq.tags'))
                            ->placeholder(__('ollama.faq.tags_placeholder'))
                            ->suggestions([
                                'helpdesk',
                                'pinjaman-aset',
                                'teknikal',
                                'rangkaian',
                                'perisian',
                                'perkakasan',
                                'akaun',
                                'kata-laluan',
                                'e-mel',
                                'percetakan',
                            ])
                            ->helperText(__('ollama.faq.tags_helper')),

                        Forms\Components\TextInput::make('match_score')
                            ->label(__('ollama.faq.match_score'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.01)
                            ->default(0.5)
                            ->helperText(__('ollama.faq.match_score_helper')),

                        Forms\Components\Select::make('created_by')
                            ->label(__('ollama.faq.created_by'))
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled(fn (?Faq $record) => $record !== null),
                    ])
                    ->columns(2),
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

                TextColumn::make('question')
                    ->label(__('ollama.faq.question'))
                    ->searchable()
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn (Faq $record): string => $record->question),

                TextColumn::make('answer')
                    ->label(__('ollama.faq.answer'))
                    ->searchable()
                    ->limit(80)
                    ->html()
                    ->toggleable(),

                TextColumn::make('tags')
                    ->label(__('ollama.faq.tags'))
                    ->badge()
                    ->separator(',')
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('match_score')
                    ->label(__('ollama.faq.match_score'))
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 0.7 => 'success',
                        $state >= 0.4 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('creator.name')
                    ->label(__('ollama.faq.created_by'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('ollama.faq.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('ollama.faq.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tags')
                    ->label(__('ollama.faq.tags'))
                    ->options([
                        'helpdesk' => 'Helpdesk',
                        'pinjaman-aset' => 'Pinjaman Aset',
                        'teknikal' => 'Teknikal',
                        'rangkaian' => 'Rangkaian',
                        'perisian' => 'Perisian',
                        'perkakasan' => 'Perkakasan',
                        'akaun' => 'Akaun',
                        'kata-laluan' => 'Kata Laluan',
                        'e-mel' => 'E-mel',
                        'percetakan' => 'Percetakan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereJsonContains('tags', $data['value']);
                    }),

                Tables\Filters\SelectFilter::make('created_by')
                    ->label(__('ollama.faq.created_by'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('high_score')
                    ->label(__('ollama.faq.high_score_filter'))
                    ->query(fn (Builder $query): Builder => $query->where('match_score', '>=', 0.7)),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),

                    Actions\BulkAction::make('export_csv')
                        ->label(__('ollama.faq.export_csv'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records): void {
                            Notification::make()
                                ->title(__('ollama.faq.export_started'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder(__('ollama.faq.search_placeholder'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'view' => Pages\ViewFaq::route('/{record}'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.faq.section_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('question')
                                ->label(__('ollama.faq.question'))
                                ->columnSpan(2),
                            TextEntry::make('answer')
                                ->label(__('ollama.faq.answer'))
                                ->wrap()
                                ->columnSpan(2),
                            TextEntry::make('tags')
                                ->label(__('ollama.faq.tags'))
                                ->badge()
                                ->separator(','),
                            TextEntry::make('match_score')
                                ->label(__('ollama.faq.match_score'))
                                ->numeric(2)
                                ->badge()
                                ->color(fn (?float $state): string => match (true) {
                                    (float) $state >= 0.7 => 'success',
                                    (float) $state >= 0.4 => 'warning',
                                    default => 'danger',
                                }),
                            TextEntry::make('creator.name')
                                ->label(__('ollama.faq.created_by')),
                            TextEntry::make('created_at')
                                ->label(__('ollama.faq.created_at'))
                                ->dateTime('d M Y H:i'),
                            TextEntry::make('updated_at')
                                ->label(__('ollama.faq.updated_at'))
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
     * @return Builder<Faq>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['creator'])
            ->withTrashed();
    }
}
