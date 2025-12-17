<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\BedrockModelConfigResource\Pages;

use App\Filament\Resources\OllamaAI\BedrockModelConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBedrockModelConfigs extends ListRecords
{
    protected static string $resource = BedrockModelConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
