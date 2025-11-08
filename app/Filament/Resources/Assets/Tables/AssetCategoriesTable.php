<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Tables;

use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;

/**
 * Asset category table.
 */
class AssetCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_loan_duration_days')
                    ->label('Lalai (hari)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_loan_duration_days')
                    ->label('Maks (hari)')
                    ->sortable(),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Perlu Kelulusan')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label('Jumlah Aset')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('requires_approval')
                    ->label('Perlu Kelulusan'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->defaultSort('sort_order')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
