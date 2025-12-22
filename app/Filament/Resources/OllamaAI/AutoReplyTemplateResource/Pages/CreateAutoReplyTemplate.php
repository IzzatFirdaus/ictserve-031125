<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\AutoReplyTemplateResource\Pages;

use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman Cipta Template Auto-Reply
 *
 * @trace Requirements 3.4, 5.1
 */
class CreateAutoReplyTemplate extends CreateRecord
{
    protected static string $resource = AutoReplyTemplateResource::class;

    public function getTitle(): string
    {
        return __('ollama.template.create_title');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
