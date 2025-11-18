<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)  // Disable automatic record URLs to prevent empty links
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label(__('filament.labels.tag'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.labels.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label(__('filament.labels.brand'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model')
                    ->label(__('filament.labels.model'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label(__('filament.labels.serial_number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament.labels.category'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.labels.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof AssetStatus ? $state->color() : 'primary')
                    ->formatStateUsing(fn ($state) => $state instanceof AssetStatus
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label(__('filament.labels.condition'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof AssetCondition ? $state->color() : 'secondary')
                    ->formatStateUsing(fn ($state) => $state instanceof AssetCondition
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('filament.labels.location'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label(__('filament.labels.purchase_date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_value')
                    ->label(__('filament.labels.current_value'))
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(),

                // Enhanced maintenance tracking
                Tables\Columns\TextColumn::make('next_maintenance_date')
                    ->label(__('filament.labels.next_maintenance_date'))
                    ->sortable()
                    ->url(fn () => null)
                    ->formatStateUsing(fn ($state) => $state ? $state->translatedFormat('d M Y') : '-')
                    ->color(function ($record) {
                        if (! $record->next_maintenance_date) {
                            return 'gray';
                        }
                        $daysUntil = now()->diffInDays($record->next_maintenance_date, false);
                        if ($daysUntil < 0) {
                            return 'danger'; // Overdue
                        }
                        if ($daysUntil <= 7) {
                            return 'warning'; // Due soon
                        }

                        return 'success'; // OK
                    })
                    ->icon(function ($record) {
                        if (! $record->next_maintenance_date) {
                            return null;
                        }
                        $daysUntil = now()->diffInDays($record->next_maintenance_date, false);
                        if ($daysUntil < 0) {
                            return 'heroicon-o-exclamation-triangle';
                        }
                        if ($daysUntil <= 7) {
                            return 'heroicon-o-clock';
                        }

                        return 'heroicon-o-check-circle';
                    })
                    ->tooltip(fn ($record) => self::maintenanceTooltip($record))
                    ->extraCellAttributes(fn ($record) => [
                        'aria-label' => self::maintenanceAccessibleLabel($record),
                    ])
                    ->toggleable(),

                // Warranty status
                Tables\Columns\TextColumn::make('warranty_expiry')
                    ->label(__('filament.labels.warranty_expiry'))
                    ->sortable()
                    ->url(fn () => null)
                    ->formatStateUsing(fn ($state) => $state ? $state->translatedFormat('d M Y') : '-')
                    ->color(function ($record) {
                        if (! $record->warranty_expiry) {
                            return 'gray';
                        }
                        if ($record->warranty_expiry->isPast()) {
                            return 'danger';
                        }
                        if ($record->warranty_expiry->diffInMonths() <= 3) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->icon(function ($record) {
                        if (! $record->warranty_expiry) {
                            return null;
                        }
                        if ($record->warranty_expiry->isPast()) {
                            return 'heroicon-o-x-circle';
                        }
                        if ($record->warranty_expiry->diffInMonths() <= 3) {
                            return 'heroicon-o-exclamation-circle';
                        }

                        return 'heroicon-o-shield-check';
                    })
                    ->tooltip(fn ($record) => self::warrantyTooltip($record))
                    ->extraCellAttributes(fn ($record) => [
                        'aria-label' => self::warrantyAccessibleLabel($record),
                    ])
                    ->toggleable(),

                // Asset age
                Tables\Columns\TextColumn::make('age')
                    ->label(__('filament.labels.age'))
                    ->state(fn ($record) => $record->purchase_date ? $record->purchase_date->diffForHumans() : '-')
                    ->tooltip(fn ($record) => $record->purchase_date ? 'Dibeli: '.$record->purchase_date->format('d M Y') : null)
                    ->toggleable(),
            ])
            ->filters([
                // Enhanced filter organization
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.labels.status'))
                    ->options(self::enumOptions(AssetStatus::cases()))
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('condition')
                    ->label(__('filament.labels.condition'))
                    ->options(self::enumOptions(AssetCondition::cases()))
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label(__('filament.labels.category')),

                // Enhanced maintenance filters
                Tables\Filters\Filter::make('needs_maintenance')
                    ->label(__('filament.filters.needs_maintenance'))
                    ->query(fn ($query) => $query->where('status', AssetStatus::MAINTENANCE->value)
                        ->orWhere('condition', AssetCondition::DAMAGED->value)
                        ->orWhereNotNull('next_maintenance_date')
                        ->where('next_maintenance_date', '<=', now()->addDays(30)))
                    ->toggle()
                    ->indicator(__('filament.filters.maintenance_indicator')),

                Tables\Filters\Filter::make('available')
                    ->label(__('filament.filters.available'))
                    ->query(fn ($query) => $query->where('status', AssetStatus::AVAILABLE->value)
                        ->where('condition', AssetCondition::GOOD->value))
                    ->toggle(),

                Tables\Filters\Filter::make('in_use')
                    ->label(__('filament.filters.in_use'))
                    ->query(fn ($query) => $query->where('status', AssetStatus::LOANED->value))
                    ->toggle(),

                // Warranty filter
                Tables\Filters\Filter::make('warranty_expiring')
                    ->label(__('filament.filters.warranty_expiring'))
                    ->query(fn ($query) => $query->whereNotNull('warranty_expiry')
                        ->whereBetween('warranty_expiry', [now(), now()->addMonths(3)]))
                    ->toggle(),

                // Location filter
                Tables\Filters\SelectFilter::make('location')
                    ->label(__('filament.labels.location'))
                    ->options(function () {
                        return \App\Models\Asset::query()
                            ->whereNotNull('location')
                            ->distinct()
                            ->pluck('location', 'location')
                            ->all();
                    })
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                \App\Filament\Resources\Assets\Actions\UpdateConditionAction::make(),
                Action::make('markMaintenance')
                    ->label(__('filament.actions.mark_maintenance'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => AssetStatus::MAINTENANCE])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('set_status')
                        ->label(__('filament.actions.update_status'))
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label(__('filament.labels.status'))
                                ->options(self::enumOptions(AssetStatus::cases()))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record instanceof Asset) {
                                    $record->update(['status' => $data['status']]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            fn (Collection $records) => \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(__('filament.notifications.status_updated'))
                                ->body(__('filament.notifications.assets_updated', ['count' => $records->count()]))
                        ),

                    BulkAction::make('set_condition')
                        ->label(__('filament.actions.update_condition'))
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->form([
                            Select::make('condition')
                                ->label(__('filament.labels.condition'))
                                ->options(self::enumOptions(AssetCondition::cases()))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record instanceof Asset) {
                                    $record->update(['condition' => $data['condition']]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            fn (Collection $records) => \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(__('filament.notifications.condition_updated'))
                                ->body(__('filament.notifications.assets_updated', ['count' => $records->count()]))
                        ),

                    BulkAction::make('update_location')
                        ->label(__('filament.actions.update_location'))
                        ->icon('heroicon-o-map-pin')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('location')
                                ->label(__('filament.actions.new_location'))
                                ->required()
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record instanceof Asset) {
                                    $record->update(['location' => $data['location']]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(__('filament.notifications.location_updated'))
                                ->body(__('filament.notifications.assets_updated_simple'))
                        ),

                    ExportBulkAction::make()
                        ->label(__('filament.actions.export'))
                        ->icon('heroicon-o-arrow-down-tray'),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->deferLoading()
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    /**
     * @param  array<AssetStatus|AssetCondition>  $cases
     * @return array<string, string>
     */
    private static function enumOptions(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }

    /**
     * @param  mixed  $record
     */
    private static function maintenanceTooltip($record): string
    {
        if (! is_object($record) || ! isset($record->next_maintenance_date)) {
            return __('filament.status.no_maintenance_schedule');
        }

        $daysUntil = (int) now()->diffInDays($record->next_maintenance_date, false);

        if ($daysUntil < 0) {
            return __('filament.status.overdue_maintenance', ['days' => abs($daysUntil)]);
        }

        if ($daysUntil === 0) {
            return __('filament.status.due_today');
        }

        return __('filament.status.due_in_days', ['days' => $daysUntil]);
    }

    /**
     * @param  mixed  $record
     */
    private static function maintenanceAccessibleLabel($record): string
    {
        if (! is_object($record) || ! isset($record->next_maintenance_date)) {
            return self::maintenanceTooltip($record);
        }

        $formattedDate = $record->next_maintenance_date->translatedFormat('d M Y');

        return __('filament.tooltips.maintenance_next', [
            'date' => $formattedDate,
            'status' => self::maintenanceTooltip($record),
        ]);
    }

    /**
     * @param  mixed  $record
     */
    private static function warrantyTooltip($record): string
    {
        if (! is_object($record) || ! isset($record->warranty_expiry)) {
            return __('filament.status.no_warranty');
        }

        return $record->warranty_expiry->isPast()
            ? __('filament.status.warranty_expired')
            : __('filament.status.warranty_expires_in', ['time' => $record->warranty_expiry->diffForHumans()]);
    }

    /**
     * @param  mixed  $record
     */
    private static function warrantyAccessibleLabel($record): string
    {
        if (! is_object($record) || ! isset($record->warranty_expiry)) {
            return self::warrantyTooltip($record);
        }

        $formattedDate = $record->warranty_expiry->translatedFormat('d M Y');

        return $record->warranty_expiry->isPast()
            ? __('filament.tooltips.warranty_expired_on', ['date' => $formattedDate])
            : __('filament.tooltips.warranty_expires_on', [
                'date' => $formattedDate,
                'time' => $record->warranty_expiry->diffForHumans(),
            ]);
    }
}
