<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

/**
 * Inventory Cluster
 *
 * Groups inventory resources: Assets, Asset Categories
 *
 * @trace Requirements 16.1 (Navigation organization)
 */
class Inventory extends Cluster
{
    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.inventory');
    }
}
