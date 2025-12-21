<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Models\Audit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DataRetentionAlertWidget extends StatsOverviewWidget
{
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System';
    }

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
