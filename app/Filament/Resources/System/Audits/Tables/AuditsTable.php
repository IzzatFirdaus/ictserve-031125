<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\Audits\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Audits Table Configuration
 *
 * Comprehensive audit trail table with filters and export.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 */
class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('Timestamp'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->default(__('System'))
                    ->description(fn ($record) => $record->user?->email),

                TextColumn::make('event')
                    ->label(__('Action'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'created' => Heroicon::OutlinedPlus->value,
                        'updated' => Heroicon::OutlinedPencil->value,
                        'deleted' => Heroicon::OutlinedTrash->value,
                        'restored' => Heroicon::OutlinedArrowPath->value,
                        default => Heroicon::OutlinedInformationCircle->value,
                    }),

                TextColumn::make('auditable_type')
                    ->label(__('Entity Type'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->description(fn ($record) => __('ID: :id', ['id' => $record->auditable_id])),

                TextColumn::make('ip_address')
                    ->label(__('IP Address'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage(__('IP address copied'))
                    ->copyMessageDuration(1500),

                TextColumn::make('url')
                    ->label(__('URL'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url)
                    ->copyable()
                    ->copyMessage(__('URL copied'))
                    ->copyMessageDuration(1500),

                TextColumn::make('user_agent')
                    ->label(__('User Agent'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->user_agent),

                TextColumn::make('tags')
                    ->label(__('Tags'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->separator(','),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('From Date'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('Until Date'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('From: :date', ['date' => \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y')]);
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('Until: :date', ['date' => \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y')]);
                        }

                        return $indicators;
                    }),

                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->optionsLimit(50),

                SelectFilter::make('event')
                    ->label(__('Action Type'))
                    ->options([
                        'created' => __('Created'),
                        'updated' => __('Updated'),
                        'deleted' => __('Deleted'),
                        'restored' => __('Restored'),
                    ])
                    ->multiple(),

                SelectFilter::make('auditable_type')
                    ->label(__('Entity Type'))
                    ->options(function () {
                        return \OwenIt\Auditing\Models\Audit::query()
                            ->select('auditable_type')
                            ->distinct()
                            ->pluck('auditable_type', 'auditable_type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('View Details'))
                    ->icon(Heroicon::OutlinedEye->value),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label(__('Export Audit Log'))
                    ->icon(Heroicon::OutlinedArrowDownTray->value)
                    ->color(Color::Gray)
                    ->form([
                        \Filament\Forms\Components\Select::make('format')
                            ->label(__('Export Format'))
                            ->options([
                                'csv' => 'CSV',
                                'json' => 'JSON',
                                'excel' => 'Excel (XLSX)',
                                'pdf' => 'PDF',
                            ])
                            ->default('csv')
                            ->required(),
                    ])
                    ->action(function (array $data, Table $table) {
                        $exportService = app(\App\Services\AuditExportService::class);
                        $filters = $table->getFilters();

                        try {
                            $filepath = match ($data['format']) {
                                'csv' => $exportService->exportToCSV($filters),
                                'json' => $exportService->exportToJSON($filters),
                                'excel' => $exportService->exportToExcel($filters),
                                'pdf' => $exportService->exportToPDF($filters),
                                default => $exportService->exportToCSV($filters),
                            };

                            $fileSize = $exportService->getFileSize($filepath);
                            $downloadUrl = $exportService->getExportUrl($filepath);

                            \Filament\Notifications\Notification::make()
                                ->title(__('Export Completed'))
                                ->body(__('Your audit log export is ready. File size: :size', ['size' => $fileSize]))
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('download')
                                        ->label(__('Download'))
                                        ->url($downloadUrl)
                                        ->openUrlInNewTab(),
                                ])
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('Export Failed'))
                                ->body(__('An error occurred while generating the export: :error', ['error' => $e->getMessage()]))
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('Export Audit Log'))
                    ->modalDescription(__('This will export all audit records matching the current filters. The export may take several minutes for large datasets.'))
                    ->modalSubmitActionLabel(__('Export')),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->deferFilters() // Filament 4 default behavior
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('30s') // Auto-refresh every 30 seconds
            ->emptyStateHeading(__('No audit records found'))
            ->emptyStateDescription(__('Audit records will appear here as users perform actions in the system.'))
            ->emptyStateIcon(Heroicon::OutlinedShieldCheck->value);
    }
}
