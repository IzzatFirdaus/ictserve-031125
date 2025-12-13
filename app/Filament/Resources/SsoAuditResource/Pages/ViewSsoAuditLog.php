<?php

declare(strict_types=1);

namespace App\Filament\Resources\SsoAuditResource\Pages;

use App\Filament\Resources\SsoAuditResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * View SSO Audit Log Page
 *
 * @trace Requirements 3.2 (Admin SSO Management)
 */
class ViewSsoAuditLog extends ViewRecord
{
    protected static string $resource = SsoAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
