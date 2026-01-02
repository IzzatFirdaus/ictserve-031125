<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use App\Filament\Concerns\HandlesTranslations;
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
    use HandlesTranslations;

    protected static ?string $slug = 'operations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('filament.navigation.operations', 'Operasi');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::trans('filament.navigation.operations', 'Operasi');
    }
}
