<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class GradesTable
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
                    ->toggleable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Tahap')
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_approve_loans')
                    ->label('Boleh Lulus')
                    ->boolean(),
            ])
            ->filters([])
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
