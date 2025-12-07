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
 * @author Pasukan BPM MOTAC
 * @trace D03-NFR-003 (Performance Monitoring)
 * @trace D16 (Broadcasting Setup)
 * @trace Requirements 16.1, 16.2 (Real-time Metrics)
 * @version 3.5.0
 * @created 2025-12-07
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

    public static function canAccess(): bool
    {
        // Only superusers can access Pulse dashboard
        return Auth::check() && Auth::user()->hasRole('superuser');
    }

    public function mount(): void
    {
        // Check authorization
        abort_unless(static::canAccess(), 403);
    }
}
