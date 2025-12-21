<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Laravel Telescope Debugging Dashboard (Filament Integration)
 *
 * Provides access to Laravel Telescope system debugging for superuser role only.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-NFR-003 (System Debugging)
 * @trace Requirements 4.2, 12.1, 14.1, 17.1, 17.2, 17.3, 17.4, 17.5
 *
 * @version 3.6.0
 *
 * @created 2025-12-16
 */
class TelescopeDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBugAnt;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.telescope-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('admin.telescope_dashboard');
    }

    public function getTitle(): string
    {
        return __('admin.telescope_dashboard_title');
    }

    public function getHeading(): string
    {
        return __('admin.telescope_debugging');
    }

    /**
     * Check if user can access Telescope dashboard.
     *
     * Per Requirements 17.1: Superuser-only access to Telescope
     */
    public static function canAccess(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Only superuser role can access Telescope dashboard
        return $user->isSuperuser();
    }

    public function mount(): void
    {
        // Check authorization
        abort_unless(static::canAccess(), 403);
    }
}
