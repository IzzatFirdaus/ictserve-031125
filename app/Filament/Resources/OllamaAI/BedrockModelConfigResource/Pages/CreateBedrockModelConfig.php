<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\BedrockModelConfigResource\Pages;

use App\Filament\Resources\OllamaAI\BedrockModelConfigResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBedrockModelConfig extends CreateRecord
{
    protected static string $resource = BedrockModelConfigResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
