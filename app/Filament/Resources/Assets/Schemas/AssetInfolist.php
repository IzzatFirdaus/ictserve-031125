<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class AssetInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Maklumat Aset')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('asset_tag')->label('Tag Aset'),
                        TextEntry::make('name')->label('Nama'),
                        TextEntry::make('category.name')->label('Kategori'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => method_exists($state, 'color') ? $state->color() : 'primary')
                            ->formatStateUsing(fn ($state) => method_exists($state, 'value')
                                ? ucfirst(str_replace('_', ' ', $state->value))
                                : ucfirst(str_replace('_', ' ', (string) $state))),
                        TextEntry::make('condition')
                            ->badge()
                            ->color(fn ($state) => method_exists($state, 'color') ? $state->color() : 'secondary')
                            ->formatStateUsing(fn ($state) => method_exists($state, 'value')
                                ? ucfirst(str_replace('_', ' ', $state->value))
                                : ucfirst(str_replace('_', ' ', (string) $state))),
                        TextEntry::make('location')->label('Lokasi'),
                    ]),
                    Grid::make(3)->schema([
                        TextEntry::make('brand')->label('Jenama'),
                        TextEntry::make('model')->label('Model'),
                        TextEntry::make('serial_number')->label('Nombor Siri'),
                    ]),
                ]),
            Section::make('Kewangan & Waranti')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('purchase_date')
                            ->label('Tarikh Perolehan')
                            ->date(),
                        TextEntry::make('purchase_value')
                            ->label('Nilai Perolehan')
                            ->money('MYR'),
                        TextEntry::make('current_value')
                            ->label('Nilai Semasa')
                            ->money('MYR'),
                    ]),
                    TextEntry::make('warranty_expiry')
                        ->label('Tamat Waranti')
                        ->date()
                        ->placeholder('-'),
                ]),
            Section::make('Penyenggaraan')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('last_maintenance_date')
                            ->label('Penyenggaraan Terakhir')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('next_maintenance_date')
                            ->label('Penyenggaraan Seterusnya')
                            ->date()
                            ->placeholder('-'),
                    ]),
                    TextEntry::make('maintenance_tickets_count')
                        ->label('Jumlah Tiket Penyelenggaraan')
                        ->numeric()
                        ->placeholder('0'),
                ]),
            Section::make('Spec & Aksesori')
                ->schema([
                    KeyValueEntry::make('specifications')
                        ->label('Spesifikasi')
                        ->placeholder('Tiada data'),
                    KeyValueEntry::make('accessories')
                        ->label('Aksesori')
                        ->placeholder('Tiada data'),
                ]),
        ]);
    }
}
