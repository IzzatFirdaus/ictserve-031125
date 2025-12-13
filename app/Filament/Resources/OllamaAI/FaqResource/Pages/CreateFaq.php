<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\FaqResource\Pages;

use App\Filament\Resources\OllamaAI\FaqResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman Cipta FAQ
 *
 * Borang untuk mencipta FAQ baharu.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 1.1, 5.1
 */
class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;

    public function getTitle(): string
    {
        return __('ollama.faq.create_title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
