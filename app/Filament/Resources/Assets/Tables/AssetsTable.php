<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label('Tag')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof AssetStatus ? $state->color() : 'primary')
                    ->formatStateUsing(fn ($state) => $state instanceof AssetStatus
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Keadaan')
                    ->badge()
                    ->color(fn ($state) => $state instanceof AssetCondition ? $state->color() : 'secondary')
                    ->formatStateUsing(fn ($state) => $state instanceof AssetCondition
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Perolehan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_value')
                    ->label('Nilai Semasa')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(),

                // Enhanced maintenance tracking
                Tables\Columns\TextColumn::make('next_maintenance_date')
                    ->label('Penyelenggaraan Seterusnya')
                    ->date('d M Y')
                    ->sortable()
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
                    ->tooltip(function ($record) {
                        if (! $record->next_maintenance_date) {
                            return 'Tiada jadual penyelenggaraan';
                        }
                        $daysUntil = now()->diffInDays($record->next_maintenance_date, false);
                        if ($daysUntil < 0) {
                            return 'Lewat '.abs($daysUntil).' hari';
                        }
                        if ($daysUntil <= 7) {
                            return 'Dalam '.$daysUntil.' hari';
                        }

                        return 'Dalam '.$daysUntil.' hari';
                    })
                    ->toggleable(),

                // Warranty status
                Tables\Columns\TextColumn::make('warranty_expiry')
                    ->label('Waranti')
                    ->date('d M Y')
                    ->sortable()
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
                    ->tooltip(function ($record) {
                        if (! $record->warranty_expiry) {
                            return 'Tiada waranti';
                        }
                        if ($record->warranty_expiry->isPast()) {
                            return 'Waranti tamat';
                        }

                        return 'Tamat dalam '.$record->warranty_expiry->diffForHumans();
                    })
                    ->toggleable(),

                // Asset age
                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->state(fn ($record) => $record->purchase_date ? $record->purchase_date->diffForHumans() : '-')
                    ->tooltip(fn ($record) => $record->purchase_date ? 'Dibeli: '.$record->purchase_date->format('d M Y') : null)
                    ->toggleable(),
            ])
            ->filters([
                // Enhanced filter organization
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::enumOptions(AssetStatus::cases()))
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('condition')
                    ->label('Keadaan')
                    ->options(self::enumOptions(AssetCondition::cases()))
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Kategori'),

                // Enhanced maintenance filters
                Tables\Filters\Filter::make('needs_maintenance')
                    ->label('🔧 Perlu Penyelenggaraan')
                    ->query(fn ($query) => $query->where('status', AssetStatus::MAINTENANCE->value)
                        ->orWhere('condition', AssetCondition::DAMAGED->value)
                        ->orWhereNotNull('next_maintenance_date')
                        ->where('next_maintenance_date', '<=', now()->addDays(30)))
                    ->toggle()
                    ->indicator('Penyelenggaraan'),

                Tables\Filters\Filter::make('available')
                    ->label('✅ Tersedia')
                    ->query(fn ($query) => $query->where('status', AssetStatus::AVAILABLE->value)
                        ->where('condition', AssetCondition::GOOD->value))
                    ->toggle(),

                Tables\Filters\Filter::make('in_use')
                    ->label('📦 Sedang Digunakan')
                    ->query(fn ($query) => $query->where('status', AssetStatus::LOANED->value))
                    ->toggle(),

                // Warranty filter
                Tables\Filters\Filter::make('warranty_expiring')
                    ->label('⚠️ Waranti Hampir Tamat')
                    ->query(fn ($query) => $query->whereNotNull('warranty_expiry')
                        ->whereBetween('warranty_expiry', [now(), now()->addMonths(3)]))
                    ->toggle(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('markMaintenance')
                    ->label('Tanda Penyelenggaraan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => AssetStatus::MAINTENANCE])),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('set_status')
                        ->label('Kemaskini Status')
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options(self::enumOptions(AssetStatus::cases()))
                                ->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each(
                            fn ($record) => $record->update(['status' => $data['status']])
                        )),
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @param  array<AssetStatus|AssetCondition>  $cases
     */
    private static function enumOptions(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }
}
