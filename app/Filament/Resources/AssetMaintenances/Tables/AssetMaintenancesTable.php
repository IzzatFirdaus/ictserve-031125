<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMaintenances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Schema jadual untuk Penyelenggaraan Aset
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 55.1, 55.2, 55.3, 55.4, 55.5, 56.1, 56.2, 56.3, 56.4
 */
class AssetMaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label(__('asset_maintenance.columns.asset_tag'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('asset.name')
                    ->label(__('asset_maintenance.columns.asset_name'))
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->asset?->name),

                TextColumn::make('maintenance_type')
                    ->label(__('asset_maintenance.columns.maintenance_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'routine' => 'info',
                        'repair' => 'warning',
                        'upgrade' => 'success',
                        'inspection' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("asset_maintenance.maintenance_types.{$state}"))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('asset_maintenance.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("asset_maintenance.status_options.{$state}"))
                    ->sortable(),

                TextColumn::make('scheduled_date')
                    ->label(__('asset_maintenance.columns.scheduled_date'))
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('completed_date')
                    ->label(__('asset_maintenance.columns.completed_date'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('performedByUser.name')
                    ->label(__('asset_maintenance.columns.performed_by_user'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('performed_by')
                    ->label(__('asset_maintenance.columns.performed_by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('cost')
                    ->label(__('asset_maintenance.columns.cost'))
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('asset_maintenance.columns.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('asset_maintenance.filters.status'))
                    ->options([
                        'scheduled' => __('asset_maintenance.status_options.scheduled'),
                        'in_progress' => __('asset_maintenance.status_options.in_progress'),
                        'completed' => __('asset_maintenance.status_options.completed'),
                        'cancelled' => __('asset_maintenance.status_options.cancelled'),
                    ]),

                SelectFilter::make('maintenance_type')
                    ->label(__('asset_maintenance.filters.maintenance_type'))
                    ->options([
                        'routine' => __('asset_maintenance.maintenance_types.routine'),
                        'repair' => __('asset_maintenance.maintenance_types.repair'),
                        'upgrade' => __('asset_maintenance.maintenance_types.upgrade'),
                        'inspection' => __('asset_maintenance.maintenance_types.inspection'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('asset_maintenance.empty_state.heading'))
            ->emptyStateDescription(__('asset_maintenance.empty_state.description'))
            ->emptyStateIcon('heroicon-o-wrench-screwdriver')
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('asset_maintenance.empty_state.action')),
            ])
            ->defaultSort('scheduled_date', 'desc');
    }
}
