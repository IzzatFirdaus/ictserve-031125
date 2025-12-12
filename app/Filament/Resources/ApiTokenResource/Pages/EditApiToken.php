<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit API Token Page
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.4
 *
 * @version 3.5.0
 */
class EditApiToken extends EditRecord
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string
    {
        return __('admin.edit_token');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('admin.revoke_token')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
