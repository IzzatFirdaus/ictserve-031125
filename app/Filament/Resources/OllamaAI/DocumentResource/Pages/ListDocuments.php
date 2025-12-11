<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\DocumentResource\Pages;

use App\Filament\Resources\OllamaAI\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman Senarai Dokumen
 *
 * Memaparkan senarai dokumen dengan ciri carian, penapisan, dan tindakan pukal.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 2.1, 2.5, 5.1
 */
class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('ollama.document.create')),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.document.list_title');
    }
}
