<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\FaqResource\Pages;

use App\Filament\Resources\OllamaAI\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman Lihat FAQ
 *
 * Paparan baca sahaja untuk butiran FAQ.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 1.1, 5.1
 */
class ViewFaq extends ViewRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.faq.view_title');
    }
}
