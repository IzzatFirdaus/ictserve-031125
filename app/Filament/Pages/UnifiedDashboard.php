<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Unified Dashboard Page (Legacy)
 *
 * Legacy unified dashboard page maintained for backward compatibility.
 * Hidden from navigation - users should use AdminDashboard instead.
 *
 * @deprecated Use AdminDashboard for current dashboard functionality
 * @trace D04-§3.2 (Dashboard architecture evolution)
 *
 * @see \App\Filament\Pages\AdminDashboard
 */
class UnifiedDashboard extends Page
{
    protected string $view = 'filament.pages.unified-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from navigation - this is a legacy page
    }
}
