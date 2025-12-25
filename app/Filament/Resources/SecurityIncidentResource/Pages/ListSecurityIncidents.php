<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityIncidentResource\Pages;

use App\Filament\Resources\SecurityIncidentResource;
use App\Models\SecurityIncident;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

/**
 * List Security Incidents Page
 *
 * PKS CSIRT Integration (Requirement 28) - Incident List with Tabs
 *
 * Provides tabbed view for incident management with:
 * - All incidents
 * - Open incidents requiring attention
 * - Critical/High severity incidents
 * - Pending CSIRT escalation
 * - Resolved incidents
 *
 * @trace D03-FR-028.3 (Incident log retention)
 * @trace Requirements 28.3
 */
class ListSecurityIncidents extends ListRecords
{
    protected static string $resource = SecurityIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Muat Semula')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(fn () => $this->resetTable()),

            Actions\Action::make('export_report')
                ->label('Eksport Laporan')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->action(function () {
                    // Export functionality
                    $this->dispatch('notify', [
                        'type' => 'info',
                        'message' => 'Laporan sedang dijana...',
                    ]);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon(Heroicon::OutlinedListBullet),

            'open' => Tab::make('Terbuka')
                ->icon(Heroicon::OutlinedExclamationCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->open())
                ->badge(SecurityIncident::query()->open()->count())
                ->badgeColor('warning'),

            'critical' => Tab::make('Kritikal')
                ->icon(Heroicon::OutlinedShieldExclamation)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('severity', SecurityIncident::SEVERITY_CRITICAL)->open())
                ->badge(SecurityIncident::query()->where('severity', SecurityIncident::SEVERITY_CRITICAL)->open()->count())
                ->badgeColor('danger'),

            'pending_escalation' => Tab::make('Menunggu Eskalasi')
                ->icon(Heroicon::OutlinedClock)
                ->modifyQueryUsing(fn (Builder $query) => $query->requiresEscalation())
                ->badge(SecurityIncident::query()->requiresEscalation()->count())
                ->badgeColor('danger'),

            'resolved' => Tab::make('Diselesaikan')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    SecurityIncident::STATUS_RESOLVED,
                    SecurityIncident::STATUS_CLOSED,
                ]))
                ->badge(SecurityIncident::query()->whereIn('status', [
                    SecurityIncident::STATUS_RESOLVED,
                    SecurityIncident::STATUS_CLOSED,
                ])->count())
                ->badgeColor('success'),

            'false_positive' => Tab::make('Positif Palsu')
                ->icon(Heroicon::OutlinedXMark)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_false_positive', true))
                ->badge(SecurityIncident::query()->where('is_false_positive', true)->count())
                ->badgeColor('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SecurityIncidentResource\Widgets\SecurityIncidentStatsWidget::class,
        ];
    }
}
