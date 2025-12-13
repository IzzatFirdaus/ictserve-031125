<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\DocumentResource\Pages;

use App\Filament\Resources\OllamaAI\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman Lihat Dokumen
 *
 * Paparan baca sahaja untuk butiran dokumen.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 2.1, 2.5, 5.1
 */
class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.document.view_title');
    }
}
