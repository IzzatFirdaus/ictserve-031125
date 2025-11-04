<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::enumOptions(AssetStatus::cases())),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Keadaan')
                    ->options(self::enumOptions(AssetCondition::cases())),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->label('Kategori'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markMaintenance')
                    ->label('Tanda Penyelenggaraan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => AssetStatus::MAINTENANCE])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('set_status')
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
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
