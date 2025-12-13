<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\AutoReplyTemplateResource\Pages;

use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman Edit Template Auto-Reply
 *
 * @trace Requirements 3.4, 5.1
 */
class EditAutoReplyTemplate extends EditRecord
{
    protected static string $resource = AutoReplyTemplateResource::class;

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
        return __('ollama.template.edit_title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
