<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoogleServicesAuditResource\Pages;

use App\Filament\Resources\GoogleServicesAuditResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * View Google Services Audit Log Page
 *
 * Displays detailed information about a single audit log entry.
 *
 * @trace Requirements 8.2, 16.3 (Admin Interface, Compliance Reporting)
 */
class ViewGoogleServicesAuditLog extends ViewRecord
{
    protected static string $resource = GoogleServicesAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
