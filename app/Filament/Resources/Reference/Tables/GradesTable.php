<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('filament.reference.code'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ms')
                    ->label(__('filament.reference.name_ms'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->label(__('filament.reference.level'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_approve_loans')
                    ->label(__('filament.reference.can_approve'))
                    ->boolean(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
