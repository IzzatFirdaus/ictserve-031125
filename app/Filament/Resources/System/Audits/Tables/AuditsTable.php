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
use OwenIt\Auditing\Models\Audit;

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
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Cap Masa')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->description(fn (Audit $record): string => $record->created_at?->diffForHumans() ?? ''),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->default('Sistem')
                    ->description(fn (Audit $record) => $record->user instanceof \App\Models\User ? $record->user->email : null),

                TextColumn::make('event')
                    ->label('Tindakan')
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
                    ->label('Jenis Entiti')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->description(fn (Audit $record): string => 'ID: '.$record->auditable_id),

                TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Alamat IP disalin')
                    ->copyMessageDuration(1500),

                TextColumn::make('url')
                    ->label('URL')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url)
                    ->copyable()
                    ->copyMessage('URL disalin')
                    ->copyMessageDuration(1500),

                TextColumn::make('user_agent')
                    ->label('Ejen Pengguna')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->user_agent),

                TextColumn::make('tags')
                    ->label('Tag')
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
                            ->label('Tarikh Dari')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Tarikh Hingga')
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
                            $indicators[] = 'Dari: '.\Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Hingga: '.\Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->optionsLimit(50),

                SelectFilter::make('event')
                    ->label('Jenis Tindakan')
                    ->options([
                        'created' => 'Dicipta',
                        'updated' => 'Dikemaskini',
                        'deleted' => 'Dipadam',
                        'restored' => 'Dipulihkan',
                    ])
                    ->multiple(),

                SelectFilter::make('auditable_type')
                    ->label('Jenis Entiti')
                    ->options(function () {
                        /** @var array<int, string> $types */
                        $types = Audit::query()
                            ->select('auditable_type')
                            ->distinct()
                            ->pluck('auditable_type')
                            ->filter()
                            ->toArray();

                        return collect($types)
                            ->mapWithKeys(fn (string $type, int|string $key): array => [$type => class_basename($type)])
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat Butiran')
                    ->icon(Heroicon::OutlinedEye->value),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Eksport Log Audit')
                    ->icon(Heroicon::OutlinedArrowDownTray->value)
                    ->color(Color::Gray)
                    ->form([
                        \Filament\Forms\Components\Select::make('format')
                            ->label('Format Eksport')
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
                                ->title('Eksport Selesai')
                                ->body('Eksport log audit sedia dimuat turun. Saiz fail: '.$fileSize)
                                ->success()
                                ->actions([
                                    Action::make('download')
                                        ->label('Muat turun')
                                        ->url($downloadUrl)
                                        ->openUrlInNewTab(),
                                ])
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Eksport Gagal')
                                ->body('Ralat semasa menjana eksport: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Eksport Log Audit')
                    ->modalDescription('Ini akan mengeksport semua rekod audit yang sepadan dengan penapis semasa. Proses ini mungkin mengambil beberapa minit untuk set data yang besar.')
                    ->modalSubmitActionLabel('Eksport'),
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
            ->emptyStateHeading('Tiada rekod audit ditemui')
            ->emptyStateDescription('Rekod audit akan dipaparkan di sini apabila pengguna melakukan tindakan dalam sistem.')
            ->emptyStateIcon(Heroicon::OutlinedShieldCheck->value);
    }
}
