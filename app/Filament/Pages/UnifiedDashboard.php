<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UnifiedDashboard extends Page
{
    protected string $view = 'filament.pages.unified-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from navigation - this is a legacy page
    }
}
