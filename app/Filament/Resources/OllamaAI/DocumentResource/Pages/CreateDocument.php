<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\DocumentResource\Pages;

use App\Filament\Resources\OllamaAI\DocumentResource;
use App\Services\DocumentService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Halaman Cipta Dokumen
 *
 * Borang untuk memuat naik dokumen baharu.
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 *
 * @trace Requirements 2.1, 2.5, 5.1
 */
class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    public function getTitle(): string
    {
        return __('ollama.document.create_title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle document creation via DocumentService to ensure storage & metadata are consistent.
     */
    

/**
 * @param array<string, mixed> $data
 */
protected function handleRecordCreation(array $data): Model
    {
        /** @var TemporaryUploadedFile|null $file */
        $file = $this->form->getState()['file_upload'] ?? null;

        if (! $file instanceof TemporaryUploadedFile) {
            throw new \InvalidArgumentException('Fail diperlukan untuk memuat naik dokumen.');
        }

        /** @var DocumentService $service */
        $service = app(DocumentService::class);

        /** @var Authenticatable|null $user */
        $user = Auth::user();

        return $service->uploadDocument($file, $user?->getAuthIdentifier());
    }
}
