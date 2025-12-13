<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\MessageLogResource\Pages;

use App\Filament\Resources\OllamaAI\MessageLogResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman Senarai Log Mesej AI
 *
 * @trace Requirements 4.1, 4.2, 6.5
 */
class ListMessageLogs extends ListRecords
{
    protected static string $resource = MessageLogResource::class;

    public function getTitle(): string
    {
        return __('ollama.message_log.list_title');
    }
}
