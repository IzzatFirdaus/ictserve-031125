<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Widgets\AssetAvailabilityWidget;
use App\Filament\Resources\Assets\Widgets\AssetUtilizationWidget;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
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
                ->url(fn ($record) => LoanApplicationResource::getUrl('index', [
                    'tableFilters' => ['asset_id' => ['value' => $record->id]],
                ]))
                ->openUrlInNewTab(),
            Action::make('viewTickets')
                ->label('Lihat Tiket')
                ->icon('heroicon-o-ticket')
                ->url(fn ($record) => HelpdeskTicketResource::getUrl('index', [
                    'tableFilters' => ['asset_id' => ['value' => $record->id]],
                ]))
                ->openUrlInNewTab(),
            Action::make('generateQrCode')
                ->label('Generate QR Code')
                ->icon('heroicon-o-qr-code')
                ->color('gray')
                ->modalHeading('Asset QR Code')
                ->modalDescription(fn ($record) => "QR Code for {$record->asset_tag}")
                ->modalContent(function ($record) {
                    $qrCode = app(\App\Services\AssetQrCodeService::class)->generateQrCode($record);
                    return view('filament.modals.asset-qr-code', ['qrCode' => $qrCode, 'asset' => $record]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
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
