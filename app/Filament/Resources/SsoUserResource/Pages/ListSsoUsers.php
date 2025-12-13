<?php

declare(strict_types=1);

namespace App\Filament\Resources\SsoUserResource\Pages;

use App\Filament\Resources\SsoUserResource;
use Filament\Resources\Pages\ListRecords;

/**
 * List SSO Users Page
 *
 * @trace Requirements 3.1 (Admin SSO Management)
 */
class ListSsoUsers extends ListRecords
{
    protected static string $resource = SsoUserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
