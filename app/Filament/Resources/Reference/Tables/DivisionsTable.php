<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class DivisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ms')
                    ->label('Nama (BM)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent.name_ms')
                    ->label('Induk')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
