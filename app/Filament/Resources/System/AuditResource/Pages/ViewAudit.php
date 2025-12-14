<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\AuditResource\Pages;

use App\Filament\Resources\System\AuditResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use OwenIt\Auditing\Models\Audit;

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
        if (! $this->record instanceof Audit) {
            return 'Rekod Audit';
        }

        return 'Rekod Audit #'.$this->record->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_record')
                ->label('Eksport Rekod')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('format')
                        ->label('Format Eksport')
                        ->options([
                            'pdf' => 'PDF',
                            'json' => 'JSON',
                        ])
                        ->default('pdf')
                        ->required(),
                ])
                ->action(function (array $data) {
                    if (! $this->record instanceof Audit) {
                        return;
                    }

                    $exportService = app(\App\Services\AuditExportService::class);

                    $filename = $exportService->exportSingleAuditRecord(
                        $this->record,
                        $data['format']
                    );

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Rekod audit berjaya dieksport.')
                        ->send();

                    return response()->download(\Illuminate\Support\Facades\Storage::path($filename));
                }),

            Action::make('view_related')
                ->label('Lihat Rekod Berkaitan')
                ->icon('heroicon-o-link')
                ->color('info')
                ->visible(fn () => $this->record instanceof Audit && $this->record->auditable_type && $this->record->auditable_id)
                ->url(function () {
                    if (! $this->record instanceof Audit) {
                        return null;
                    }
                    $model = $this->record->auditable_type;
                    $id = $this->record->auditable_id;

                    // Map model to Filament resource URL
                    $resourceMap = [
                        \App\Models\User::class => 'users',
                        \App\Models\HelpdeskTicket::class => 'helpdesk-tickets',
                        \App\Models\LoanApplication::class => 'loan-applications',
                        \App\Models\Asset::class => 'assets',
                    ];

                    $resource = $resourceMap[$model] ?? null;

                    if ($resource) {
                        return "/admin/{$resource}/{$id}";
                    }

                    return null;
                })
                ->openUrlInNewTab(),

            Action::make('view_user_activity')
                ->label('Lihat Aktiviti Pengguna')
                ->icon('heroicon-o-user')
                ->color('warning')
                ->visible(fn () => $this->record instanceof Audit && $this->record->user_id)
                ->url(fn () => $this->record instanceof Audit
                    ? "/admin/audit-trail?tableFilters[user_id][value]={$this->record->user_id}"
                    : null)
                ->openUrlInNewTab(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
