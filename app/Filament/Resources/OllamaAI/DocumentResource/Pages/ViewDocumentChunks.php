<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\DocumentResource\Pages;

use App\Filament\Resources\OllamaAI\DocumentResource;
use App\Models\Document;
use App\Models\DocumentChunk;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Halaman Lihat Chunk Dokumen
 *
 * Memaparkan senarai chunk dokumen dengan pagination.
 *
 * @trace Requirements 2.1, 5.1
 */
class ViewDocumentChunks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = DocumentResource::class;

    protected string $view = 'filament.resources.ollama-ai.document-resource.pages.view-document-chunks';

    public Document $record;

    public function getTitle(): string
    {
        return __('ollama.document.view_chunks').': '.$this->record->filename;
    }

    public function getBreadcrumbs(): array
    {
        return [
            DocumentResource::getUrl() => __('ollama.document.plural_label'),
            DocumentResource::getUrl('view', ['record' => $this->record]) => $this->record->filename,
            'Chunks',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('common.back'))
                ->icon('heroicon-o-arrow-left')
                ->url(DocumentResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => DocumentChunk::query()->where('document_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('chunk_index')
                    ->label('Index')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('chunk_text')
                    ->label('Kandungan')
                    ->limit(200)
                    ->wrap()
                    ->tooltip(fn (DocumentChunk $record): string => substr($record->chunk_text, 0, 500).'...'),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_embedding')
                    ->label('Embedding')
                    ->boolean()
                    ->getStateUsing(fn (DocumentChunk $record): bool => ! empty($record->embedding))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('chunk_index', 'asc')
            ->paginated([10, 25, 50, 100]);
    }
}
