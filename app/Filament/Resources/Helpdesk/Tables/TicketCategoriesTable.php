<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ticket Category Table Definition
 */
class TicketCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_ms')
                    ->label('Nama (BM)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name_ms')
                    ->label('Kategori Induk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sla_response_hours')
                    ->label('SLA Respons')
                    ->suffix(' jam')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sla_resolution_hours')
                    ->label('SLA Penyelesaian')
                    ->suffix(' jam')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Kategori Induk')
                    ->relationship('parent', 'name_ms')
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->defaultSort('name_ms')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
