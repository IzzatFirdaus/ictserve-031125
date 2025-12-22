<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Pulse Dashboard Page
 *
 * Provides integrated access to Laravel Pulse performance monitoring
 * dashboard within the Filament admin panel.
 *
 * Features:
 * - Embedded Laravel Pulse dashboard
 * - Role-based access control (admin and superuser only)
 * - Seamless integration with Filament navigation
 * - WCAG 2.2 AA compliant iframe implementation
 * - Responsive design with proper viewport handling
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R18 (Pulse Dashboard Integration)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D17 Queue Management - Laravel Pulse integration
 *
 * @version 3.6.1
 */
class PulseDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected string $view = 'filament.pages.pulse-dashboard';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 10;

    /**
     * Get the page title in Bahasa Melayu
     */
    public function getTitle(): string|Htmlable
    {
        return 'Dashboard Prestasi (Pulse)';
    }

    /**
     * Get the navigation label in Bahasa Melayu
     */
    public static function getNavigationLabel(): string
    {
        return 'Dashboard Prestasi';
    }

    /**
     * Check if user can access this page (admin and superuser only)
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole(['admin', 'superuser']);
    }

    /**
     * Get the navigation group in Bahasa Melayu
     */
    public static function getNavigationGroup(): ?string
    {
        return 'Pemantauan Sistem';
    }

    /**
     * Get the Pulse URL for embedding
     */
    public function getPulseUrl(): string
    {
        $baseUrl = config('app.url');
        $pulsePath = config('pulse.path', 'pulse');

        return rtrim($baseUrl, '/').'/'.ltrim($pulsePath, '/');
    }

    /**
     * Check if Pulse is enabled
     */
    public function isPulseEnabled(): bool
    {
        return config('pulse.enabled', true);
    }

    /**
     * Get page data for the view
     */
    

/**
 * @return array<string, mixed>
 */
protected function getViewData(): array
    {
        return [
            'pulseUrl' => $this->getPulseUrl(),
            'isPulseEnabled' => $this->isPulseEnabled(),
        ];
    }
}
