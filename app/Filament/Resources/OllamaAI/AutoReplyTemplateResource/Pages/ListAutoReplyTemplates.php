<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\AutoReplyTemplateResource\Pages;

use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman Senarai Template Auto-Reply
 *
 * @trace Requirements 3.4, 5.1
 */
class ListAutoReplyTemplates extends ListRecords
{
    protected static string $resource = AutoReplyTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('ollama.template.create')),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.template.list_title');
    }
}
