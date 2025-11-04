<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetUtilizationWidget extends BaseWidget
{
    protected ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $total = Asset::count();
        $loaned = Asset::where('status', AssetStatus::LOANED)->count();
        $maintenance = Asset::whereIn('status', [
            AssetStatus::MAINTENANCE,
            AssetStatus::DAMAGED,
        ])->count();
        $available = Asset::where('status', AssetStatus::AVAILABLE)->count();

        $utilisation = $total > 0 ? round(($loaned / $total) * 100, 1) : 0.0;

        return [
            Stat::make('Jumlah Aset', (string) $total)
                ->description('Inventori berdaftar')
                ->color(Color::Blue),
            Stat::make('Digunakan', (string) $loaned)
                ->description('Sedang dipinjam')
                ->color(Color::Amber),
            Stat::make('Ketersediaan', (string) $available)
                ->description('Sedia untuk pinjaman')
                ->color(Color::Emerald),
            Stat::make('Penyelenggaraan', (string) $maintenance)
                ->description('Perlu perhatian')
                ->color(Color::Rose),
            Stat::make('Kadar Penggunaan', $utilisation.'%')
                ->description('Peratus aset sedang digunakan')
                ->color($utilisation >= 70 ? Color::Amber : Color::Emerald),
        ];
    }
}
