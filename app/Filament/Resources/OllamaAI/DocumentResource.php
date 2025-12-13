<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI;

use App\Filament\Clusters\OllamaAI;
use App\Filament\Resources\OllamaAI\DocumentResource\Pages;
use App\Jobs\DocumentIngestJob;
use App\Models\Document;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Sumber Filament untuk Pengurusan Dokumen AI
 *
 * Menyediakan antara muka CRUD untuk dokumen dengan ciri:
 * - Muat naik fail dengan sokongan drag-and-drop
 * - Penjejakan status dengan penunjuk visual
 * - Pratonton dokumen
 * - Paparan chunk dengan pagination
 * - Tindakan re-ingestion untuk dokumen gagal
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 2.1, 2.5, 5.1
 */
class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('ollama.document.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ollama.document.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ollama.document.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.document.section_upload'))
                    ->description(__('ollama.document.section_upload_description'))
                    ->hidden(fn (?Document $record): bool => $record !== null)
                    ->schema([
                        Forms\Components\FileUpload::make('file_upload')
                            ->label(__('ollama.document.file'))
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                            ->maxSize(10240) // 10MB
                            ->directory('documents')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->helperText(__('ollama.document.file_helper'))
                            ->dehydrated(false)
                            ->afterStateUpdated(function (?TemporaryUploadedFile $state, callable $set): void {
                                if (! $state) {
                                    return;
                                }

                                $metadata = [
                                    'original_name' => $state->getClientOriginalName(),
                                    'stored_name' => $state->getFilename(),
                                    'size' => $state->getSize(),
                                    'mime_type' => $state->getMimeType(),
                                    'extension' => $state->getClientOriginalExtension(),
                                    'uploaded_at' => now()->toISOString(),
                                ];

                                $set('filename', $state->getClientOriginalName());
                                $set('metadata', $metadata);
                            }),
                    ]),

                Section::make(__('ollama.document.section_metadata'))
                    ->hidden(fn (?Document $record): bool => $record === null)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('ollama.document.status'))
                            ->options([
                                Document::STATUS_PENDING => __('ollama.document.status_pending'),
                                Document::STATUS_PROCESSING => __('ollama.document.status_processing'),
                                Document::STATUS_COMPLETED => __('ollama.document.status_completed'),
                                Document::STATUS_FAILED => __('ollama.document.status_failed'),
                            ])
                            ->default(Document::STATUS_PENDING)
                            ->disabled(fn (?Document $record) => $record === null),

                        Forms\Components\Select::make('uploaded_by')
                            ->label(__('ollama.document.uploaded_by'))
                            ->relationship('uploader', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled(fn (?Document $record) => $record !== null),

                        Forms\Components\KeyValue::make('metadata')
                            ->label(__('ollama.document.metadata'))
                            ->keyLabel(__('ollama.document.metadata_key'))
                            ->valueLabel(__('ollama.document.metadata_value'))
                            ->addActionLabel(__('ollama.document.metadata_add'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('filename')
                    ->label(__('ollama.document.filename'))
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn (Document $record): string => $record->filename),

                Tables\Columns\TextColumn::make('file_type')
                    ->label(__('ollama.document.file_type'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'docx' => 'primary',
                        'txt' => 'gray',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('file_size')
                    ->label(__('ollama.document.file_size'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('ollama.document.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Document::STATUS_PENDING => 'gray',
                        Document::STATUS_PROCESSING => 'warning',
                        Document::STATUS_COMPLETED => 'success',
                        Document::STATUS_FAILED => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Document::STATUS_PENDING => __('ollama.document.status_pending'),
                        Document::STATUS_PROCESSING => __('ollama.document.status_processing'),
                        Document::STATUS_COMPLETED => __('ollama.document.status_completed'),
                        Document::STATUS_FAILED => __('ollama.document.status_failed'),
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('chunks_count')
                    ->label(__('ollama.document.chunks_count'))
                    ->counts('chunks')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label(__('ollama.document.uploaded_by'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('ollama.document.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('ollama.document.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('ollama.document.status'))
                    ->options([
                        Document::STATUS_PENDING => __('ollama.document.status_pending'),
                        Document::STATUS_PROCESSING => __('ollama.document.status_processing'),
                        Document::STATUS_COMPLETED => __('ollama.document.status_completed'),
                        Document::STATUS_FAILED => __('ollama.document.status_failed'),
                    ]),

                Tables\Filters\SelectFilter::make('uploaded_by')
                    ->label(__('ollama.document.uploaded_by'))
                    ->relationship('uploader', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('failed_only')
                    ->label(__('ollama.document.failed_only_filter'))
                    ->query(fn (Builder $query): Builder => $query->where('status', Document::STATUS_FAILED)),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),

                Actions\Action::make('reprocess')
                    ->label(__('ollama.document.reprocess'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('ollama.document.reprocess_confirm_heading'))
                    ->modalDescription(__('ollama.document.reprocess_confirm_description'))
                    ->visible(fn (Document $record): bool => $record->isFailed() || $record->isCompleted())
                    ->action(function (Document $record): void {
                        $record->update(['status' => Document::STATUS_PENDING]);
                        DocumentIngestJob::dispatch($record);
                        Notification::make()
                            ->title(__('ollama.document.reprocess_started'))
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

                    Actions\BulkAction::make('reprocess_bulk')
                        ->label(__('ollama.document.reprocess_bulk'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function (Document $record): void {
                                if ($record->isFailed() || $record->isCompleted()) {
                                    $record->update(['status' => Document::STATUS_PENDING]);
                                    DocumentIngestJob::dispatch($record);
                                }
                            });
                            Notification::make()
                                ->title(__('ollama.document.reprocess_bulk_started'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder(__('ollama.document.search_placeholder'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('ollama.document.section_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('filename')
                                ->label(__('ollama.document.filename')),
                            TextEntry::make('status')
                                ->label(__('ollama.document.status'))
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    Document::STATUS_PENDING => 'gray',
                                    Document::STATUS_PROCESSING => 'warning',
                                    Document::STATUS_COMPLETED => 'success',
                                    Document::STATUS_FAILED => 'danger',
                                    default => 'secondary',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    Document::STATUS_PENDING => __('ollama.document.status_pending'),
                                    Document::STATUS_PROCESSING => __('ollama.document.status_processing'),
                                    Document::STATUS_COMPLETED => __('ollama.document.status_completed'),
                                    Document::STATUS_FAILED => __('ollama.document.status_failed'),
                                    default => $state,
                                }),
                            TextEntry::make('file_type')
                                ->label(__('ollama.document.file_type')),
                            TextEntry::make('file_size')
                                ->label(__('ollama.document.file_size')),
                            TextEntry::make('uploader.name')
                                ->label(__('ollama.document.uploaded_by')),
                            TextEntry::make('created_at')
                                ->label(__('ollama.document.created_at'))
                                ->dateTime('d M Y H:i'),
                            TextEntry::make('updated_at')
                                ->label(__('ollama.document.updated_at'))
                                ->dateTime('d M Y H:i'),
                        ]),
                    ])
                    ->columns(2),

                Section::make(__('ollama.document.metadata'))
                    ->schema([
                        KeyValueEntry::make('metadata')
                            ->label(__('ollama.document.metadata'))
                            ->columnSpanFull(),
                    ]),
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
     * @return Builder<Document>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['uploader'])
            ->withCount('chunks')
            ->withTrashed();
    }
}
