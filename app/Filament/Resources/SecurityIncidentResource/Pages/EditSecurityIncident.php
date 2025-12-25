<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityIncidentResource\Pages;

use App\Filament\Resources\SecurityIncidentResource;
use App\Models\SecurityIncidentLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit Security Incident Page
 *
 * PKS CSIRT Integration (Requirement 28) - Incident Edit
 *
 * Allows updating incident details with audit logging.
 *
 * @trace D03-FR-028 (CSIRT Integration)
 * @trace Requirements 28.3
 */
class EditSecurityIncident extends EditRecord
{
    protected static string $resource = SecurityIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat'),
        ];
    }

    protected function afterSave(): void
    {
        // Log the update
        SecurityIncidentLog::log(
            $this->record->id,
            SecurityIncidentLog::ACTION_STATUS_CHANGED,
            'Insiden dikemaskini melalui panel admin',
            ['changes' => $this->record->getChanges()]
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
