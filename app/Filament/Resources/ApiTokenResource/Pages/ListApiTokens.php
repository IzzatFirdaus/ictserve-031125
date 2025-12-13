<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * List API Tokens Page
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.4
 *
 * @version 3.5.0
 */
class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('admin.create_token')),
        ];
    }

    public function getTitle(): string
    {
        return __('admin.api_tokens');
    }
}
