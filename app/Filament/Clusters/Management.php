<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use App\Filament\Concerns\HandlesTranslations;
use Filament\Clusters\Cluster;

/**
 * Management Cluster
 *
 * Groups management resources: Users, Divisions, Grades
 *
 * @trace Requirements 16.1 (Navigation organization)
 */
class Management extends Cluster
{
    use HandlesTranslations;

    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('filament.navigation.management', 'Management');
    }
}
