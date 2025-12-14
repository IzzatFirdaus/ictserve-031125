<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class DivisionsTable
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
                Tables\Columns\TextColumn::make('parent.name_ms')
                    ->label(__('filament.reference.parent'))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament.reference.active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('filament.reference.status')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }
}
