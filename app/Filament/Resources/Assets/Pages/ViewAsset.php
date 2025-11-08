<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Widgets\AssetAvailabilityWidget;
use App\Filament\Resources\Assets\Widgets\AssetUtilizationWidget;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * View Asset Page
 *
 * Enhanced asset view with availability calendar, utilization metrics, and relation tabs.
 *
 * @trace Requirements 2.3, 3.1, 3.2, 7.1, 7.2
 */
class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
            Action::make('viewLoans')
                ->label('Lihat Pinjaman')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(fn ($record) => route('filament.admin.resources.loans.loan-applications.index', [
                    'tableFilters' => ['asset_id' => ['value' => $record->id]],
                ]))
                ->openUrlInNewTab(),
            Action::make('viewTickets')
                ->label('Lihat Tiket')
                ->icon('heroicon-o-ticket')
                ->url(fn ($record) => route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['asset_id' => ['value' => $record->id]],
                ]))
                ->openUrlInNewTab(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\Assets\Widgets\AssetUtilizationAnalyticsWidget::class,
            AssetAvailabilityWidget::class,
            AssetUtilizationWidget::class,
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
