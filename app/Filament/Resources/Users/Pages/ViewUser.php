<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Component name: View User Page
 * Description: Filament resource page for viewing user details in admin panel
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 (User Management)
 * @trace D04 §3.1 (Admin Panel)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
