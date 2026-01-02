<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Table Schema untuk Pemindahan Aset
 *
 * Menyediakan jadual untuk memaparkan senarai pemindahan aset dengan:
 * - Lajur utama yang sentiasa dipaparkan
 * - Lajur tersembunyi yang boleh ditogol
 * - Penapis untuk status, pengguna dan tarikh
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 60.1, 60.2, 60.3, 60.4, 60.5
 */
class AssetTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Default Visible Columns
                TextColumn::make('asset.asset_tag')
                    ->label(__('asset_transfer.columns.asset_tag'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('asset.name')
                    ->label(__('asset_transfer.columns.asset_name'))
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->asset?->name)
                    ->toggleable(),

                TextColumn::make('toUser.name')
                    ->label(__('asset_transfer.columns.to_user'))
                    ->sortable()
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn ($record): ?string => $record->toUser?->name)
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('asset_transfer.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("asset_transfer.status.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('transfer_date')
                    ->label(__('asset_transfer.columns.transfer_date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                // Hidden-by-Default Columns
                TextColumn::make('fromUser.name')
                    ->label(__('asset_transfer.columns.from_user'))
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('from_location')
                    ->label(__('asset_transfer.columns.from_location'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('to_location')
                    ->label(__('asset_transfer.columns.to_location'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('initiator.name')
                    ->label(__('asset_transfer.columns.initiated_by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approver.name')
                    ->label(__('asset_transfer.columns.approved_by'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('asset_transfer.columns.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('asset_transfer.filters.status'))
                    ->options([
                        'pending' => __('asset_transfer.status.pending'),
                        'approved' => __('asset_transfer.status.approved'),
                        'completed' => __('asset_transfer.status.completed'),
                        'cancelled' => __('asset_transfer.status.cancelled'),
                    ]),

                SelectFilter::make('to_user_id')
                    ->label(__('asset_transfer.filters.to_user'))
                    ->relationship('toUser', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('transfer_date')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('asset_transfer.filters.date_from')),
                        DatePicker::make('until')
                            ->label(__('asset_transfer.filters.date_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transfer_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transfer_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transfer_date', 'desc')
            ->emptyStateHeading(__('asset_transfer.empty_state.heading'))
            ->emptyStateDescription(__('asset_transfer.empty_state.description'))
            ->emptyStateIcon('heroicon-o-arrows-right-left');
    }
}
