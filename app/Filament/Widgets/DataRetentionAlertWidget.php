<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Models\Audit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Data Retention Alert Widget (Widget Amaran Pengekalan Data)
 *
 * Monitors audit records exceeding the 7-year retention period as mandated by
 * PDPA 2010 (Personal Data Protection Act). Provides alerts for records requiring
 * archival or deletion to maintain regulatory compliance.
 *
 * Features:
 * - 7-year retention period monitoring
 * - Automated alert generation
 * - Record age tracking
 * - Compliance status indicators
 * - Archival recommendation display
 *
 * @trace D09-§9 (Dual Audit System and PDPA 2010 Compliance)
 * @trace D04-§3.2 (Dashboard Widgets Architecture)
 * @trace D11-§10 (Data Retention and Compliance)
 * @trace D12-§7 (Compliance Dashboard UI)
 *
 * @see \App\Models\Audit
 * @see \App\Filament\Traits\WidgetMetadata
 */
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
