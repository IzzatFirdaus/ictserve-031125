<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

/**
 * Operations Cluster
 *
 * Groups operational resources: Helpdesk Tickets, Loan Applications, Asset Availability
 *
 * @trace Requirements 16.1 (Navigation organization)
 */
class Operations extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.operations');
    }
}
