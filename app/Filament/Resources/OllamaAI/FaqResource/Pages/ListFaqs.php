<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\FaqResource\Pages;

use App\Filament\Resources\OllamaAI\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman Senarai FAQ
 *
 * Memaparkan senarai FAQ dengan ciri carian, penapisan, dan tindakan pukal.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 1.1, 5.1
 */
class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('ollama.faq.create')),

            Actions\Action::make('import_csv')
                ->label(__('ollama.faq.import_csv'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->action(function (): void {
                    // Import logic akan dilaksanakan
                }),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.faq.list_title');
    }
}
