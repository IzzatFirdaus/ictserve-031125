<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\DocumentResource\Pages;

use App\Filament\Resources\OllamaAI\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman Edit Dokumen
 *
 * Borang untuk mengedit dokumen sedia ada.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 2.1, 2.5, 5.1
 */
class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.document.edit_title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
