<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DataRetentionAlertWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $oldRecordsCount = Audit::where('created_at', '<', now()->subYears(7))->count();

        return [
            Stat::make('Rekod Melebihi 7 Tahun', $oldRecordsCount)
                ->description('Rekod perlu diarkib atau dipadam')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($oldRecordsCount > 0 ? 'danger' : 'success'),
        ];
    }
}
