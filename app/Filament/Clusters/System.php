<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use App\Filament\Concerns\HandlesTranslations;
use App\Models\User;
use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

/**
 * System Cluster
 *
 * Groups system resources: Audit Trail, Settings, Email Logs, Reports
 *
 * @trace Requirements 16.1 (Navigation organization)
 */
class System extends Cluster
{
    use HandlesTranslations;

    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('filament.navigation.system', 'System');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasRole('superuser');
    }
}
