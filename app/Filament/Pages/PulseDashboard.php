<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Laravel Pulse Monitoring Dashboard (Filament Integration)
 *
 * Provides access to Laravel Pulse performance monitoring for admin and superuser roles.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-NFR-003 (Performance Monitoring)
 * @trace D16 (Broadcasting Setup)
 * @trace Requirements 4.1, 4.2, 14.1, 14.2, 16.1, 16.2, 16.3, 16.4, 16.5
 *
 * @version 3.6.0
 *
 * @created 2025-12-07
 *
 * @updated 2025-12-16
 */
class PulseDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.pulse-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('admin.pulse_dashboard');
    }

    public function getTitle(): string
    {
        return __('admin.pulse_dashboard_title');
    }

    public function getHeading(): string
    {
        return __('admin.pulse_monitoring');
    }

    /**
     * Check if user can access Pulse dashboard.
     *
     * Per Requirements 4.1, 4.2: Admin and superuser roles can access Pulse
     */
    public static function canAccess(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Admin and superuser roles can access Pulse dashboard
        return $user->isAdmin() || $user->isSuperuser();
    }

    public function mount(): void
    {
        // Check authorization
        abort_unless(static::canAccess(), 403);
    }
}
