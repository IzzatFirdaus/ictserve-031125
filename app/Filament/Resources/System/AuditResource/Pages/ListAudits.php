<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\AuditResource\Pages;

use App\Filament\Resources\System\AuditResource;
use App\Services\AuditExportService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

/**
 * List Audits Page
 *
 * Displays paginated audit trail with advanced filtering and export capabilities.
 * Superuser-only access with 7-year data retention compliance.
 */
class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Audit Trail');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_all')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('format')
                        ->label('Export Format')
                        ->options([
                            'csv' => 'CSV',
                            'pdf' => 'PDF',
                            'excel' => 'Excel',
                        ])
                        ->default('csv')
                        ->required(),

                    \Filament\Forms\Components\DatePicker::make('date_from')
                        ->label('From Date')
                        ->default(now()->subDays(30)),

                    \Filament\Forms\Components\DatePicker::make('date_to')
                        ->label('To Date')
                        ->default(now()),

                    \Filament\Forms\Components\Select::make('event_types')
                        ->label('Action Types')
                        ->options([
                            'created' => 'Created',
                            'updated' => 'Updated',
                            'deleted' => 'Deleted',
                            'retrieved' => 'Retrieved',
                        ])
                        ->multiple()
                        ->default(['created', 'updated', 'deleted']),
                ])
                ->action(function (array $data) {
                    $exportService = app(AuditExportService::class);

                    $filename = $exportService->exportAuditLogs(
                        format: $data['format'],
                        dateFrom: $data['date_from'],
                        dateTo: $data['date_to'],
                        eventTypes: $data['event_types'] ?? []
                    );

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Audit logs exported successfully.')
                        ->send();

                    return response()->download(storage_path("app/exports/{$filename}"));
                })
                ->requiresConfirmation()
                ->modalHeading('Export Audit Logs')
                ->modalDescription('Export audit logs with the selected criteria. Large exports may take several minutes.'),

            Action::make('retention_policy')
                ->label('Retention Policy')
                ->icon('heroicon-o-information-circle')
                ->color('info')
                ->modalHeading('Data Retention Policy')
                ->modalContent(view('filament.modals.audit-retention-policy'))
                ->modalActions([
                    Action::make('close')
                        ->label('Close')
                        ->color('gray'),
                ]),

            Action::make('security_summary')
                ->label('Security Summary')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->url(fn (): string => route('filament.admin.pages.security-monitoring'))
                ->openUrlInNewTab(false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AuditResource\Widgets\AuditStatsWidget::class,
        ];
    }
}
