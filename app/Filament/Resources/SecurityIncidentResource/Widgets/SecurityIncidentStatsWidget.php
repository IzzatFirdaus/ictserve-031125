<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityIncidentResource\Widgets;

use App\Models\SecurityIncident;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Security Incident Statistics Widget
 *
 * PKS CSIRT Integration (Requirement 28) - Dashboard Statistics
 *
 * Displays key security incident metrics:
 * - Total open incidents
 * - Critical incidents count
 * - Pending CSIRT escalation
 * - Average resolution time
 *
 * @trace D03-FR-028.3 (Incident log retention)
 * @trace Requirements 28.3
 */
class SecurityIncidentStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $stats = SecurityIncident::getSummaryStats(
            now()->subDays(30)->toDateTimeString(),
            now()->toDateTimeString()
        );

        $openIncidents = SecurityIncident::query()->open()->count();
        $criticalOpen = SecurityIncident::query()
            ->open()
            ->where('severity', SecurityIncident::SEVERITY_CRITICAL)
            ->count();
        $pendingEscalation = SecurityIncident::query()->requiresEscalation()->count();
        $avgResolutionHours = round((float) ($stats['avg_resolution_time_hours'] ?? 0), 1);

        return [
            Stat::make('Insiden Terbuka', (string) $openIncidents)
                ->description('Memerlukan perhatian')
                ->descriptionIcon(Heroicon::OutlinedExclamationCircle)
                ->color($openIncidents > 5 ? 'danger' : ($openIncidents > 0 ? 'warning' : 'success'))
                ->chart($this->getOpenIncidentsTrend()),

            Stat::make('Insiden Kritikal', (string) $criticalOpen)
                ->description('Keutamaan tertinggi')
                ->descriptionIcon(Heroicon::OutlinedShieldExclamation)
                ->color($criticalOpen > 0 ? 'danger' : 'success'),

            Stat::make('Menunggu Eskalasi CSIRT', (string) $pendingEscalation)
                ->description('SLA 15 minit')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pendingEscalation > 0 ? 'danger' : 'success'),

            Stat::make('Purata Masa Penyelesaian', $avgResolutionHours.' jam')
                ->description('30 hari lepas')
                ->descriptionIcon(Heroicon::OutlinedChartBar)
                ->color($avgResolutionHours > 24 ? 'warning' : 'success'),

            Stat::make('Jumlah Insiden (30 hari)', (string) ($stats['total_incidents'] ?? 0))
                ->description('Termasuk diselesaikan')
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->color('gray'),

            Stat::make('Dilaporkan ke NACSA', (string) ($stats['nacsa_reported'] ?? 0))
                ->description('Pematuhan PKS 28.2')
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color('primary'),
        ];
    }

    /**
     * Get trend data for open incidents over the last 7 days
     *
     * @return array<int, int>
     */
    private function getOpenIncidentsTrend(): array
    {
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = SecurityIncident::query()
                ->whereDate('detected_at', '<=', $date)
                ->where(function ($query) use ($date) {
                    $query->whereNull('resolved_at')
                        ->orWhereDate('resolved_at', '>', $date);
                })
                ->count();
            $trend[] = $count;
        }

        return $trend;
    }
}
