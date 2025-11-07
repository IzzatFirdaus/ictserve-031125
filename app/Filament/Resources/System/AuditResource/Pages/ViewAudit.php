<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\AuditResource\Pages;

use App\Filament\Resources\System\AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * View Audit Page
 *
 * Displays detailed audit record information including before/after values,
 * user information, and system metadata.
 */
class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Audit Record #:id', ['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_record')
                ->label('Export Record')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('format')
                        ->label('Export Format')
                        ->options([
                            'pdf' => 'PDF',
                            'json' => 'JSON',
                        ])
                        ->default('pdf')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $exportService = app(\App\Services\AuditExportService::class);

                    $filename = $exportService->exportSingleAuditRecord(
                        $this->record,
                        $data['format']
                    );

                    $this->notify('success', 'Audit record exported successfully.');

                    return response()->download(storage_path("app/exports/{$filename}"));
                }),

            Actions\Action::make('view_related')
                ->label('View Related Records')
                ->icon('heroicon-o-link')
                ->color('info')
                ->visible(fn () => $this->record->auditable_type && $this->record->auditable_id)
                ->url(function () {
                    $model = $this->record->auditable_type;
                    $id = $this->record->auditable_id;

                    // Map model to Filament resource URL
                    $resourceMap = [
                        'App\\Models\\User' => 'users',
                        'App\\Models\\HelpdeskTicket' => 'helpdesk-tickets',
                        'App\\Models\\LoanApplication' => 'loan-applications',
                        'App\\Models\\Asset' => 'assets',
                    ];

                    $resource = $resourceMap[$model] ?? null;

                    if ($resource) {
                        return "/admin/{$resource}/{$id}";
                    }

                    return null;
                })
                ->openUrlInNewTab(),

            Actions\Action::make('view_user_activity')
                ->label('View User Activity')
                ->icon('heroicon-o-user')
                ->color('warning')
                ->visible(fn () => $this->record->user_id)
                ->url(fn () => "/admin/audit-trail?tableFilters[user_id][value]={$this->record->user_id}")
                ->openUrlInNewTab(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AuditResource\Widgets\RelatedAuditsWidget::class,
        ];
    }
}
