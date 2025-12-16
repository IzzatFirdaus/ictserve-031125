<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\BedrockModelConfigResource\Pages;

use App\Filament\Resources\OllamaAI\BedrockModelConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBedrockModelConfig extends EditRecord
{
    protected static string $resource = BedrockModelConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
