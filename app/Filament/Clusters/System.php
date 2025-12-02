<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

/**
 * System Cluster
 *
 * Groups system resources: Audit Trail, Settings, Email Logs, Reports
 *
 * @trace Requirements 16.1 (Navigation organization)
 */
class System extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.system');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }
}
