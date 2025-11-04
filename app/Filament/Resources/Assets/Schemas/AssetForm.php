<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Asset form definition.
 */
class AssetForm
{
    /**
     * @param  array<int, AssetStatus>  $statuses
     * @param  array<int, AssetCondition>  $conditions
     */
    public static function configure(Schema $schema, array $statuses, array $conditions): Schema
    {
        return $schema->components([
            Section::make('Maklumat Aset')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('asset_tag')
                            ->label('Tag Aset')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('brand')
                            ->label('Jenama')
                            ->maxLength(255),
                        TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255),
                        TextInput::make('serial_number')
                            ->label('Nombor Siri')
                            ->maxLength(255),
                    ]),
                    Grid::make(3)->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(self::enumOptions($statuses))
                            ->required(),
                        Select::make('condition')
                            ->label('Keadaan')
                            ->options(self::enumOptions($conditions))
                            ->required(),
                        TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),
                    ]),
                ]),
            Section::make('Maklumat Kewangan')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('purchase_date')
                            ->label('Tarikh Perolehan')
                            ->required(),
                        TextInput::make('purchase_value')
                            ->label('Nilai Perolehan (RM)')
                            ->numeric()
                            ->required(),
                        TextInput::make('current_value')
                            ->label('Nilai Semasa (RM)')
                            ->numeric(),
                    ]),
                    DatePicker::make('warranty_expiry')
                        ->label('Waranti Tamat'),
                ]),
            Section::make('Penyenggaraan & Lampiran')
                ->schema([
                    Grid::make(2)->schema([
                        DatePicker::make('last_maintenance_date')
                            ->label('Penyenggaraan Terakhir'),
                        DatePicker::make('next_maintenance_date')
                            ->label('Penyenggaraan Seterusnya'),
                    ]),
                    KeyValue::make('specifications')
                        ->label('Spesifikasi')
                        ->keyLabel('Parameter')
                        ->valueLabel('Butiran')
                        ->reorderable(),
                    KeyValue::make('accessories')
                        ->label('Aksesori')
                        ->keyLabel('Aksesori')
                        ->valueLabel('Kuantiti / Nota')
                        ->reorderable(),
                    Textarea::make('notes')
                        ->columnSpanFull()
                        ->rows(3)
                        ->label('Nota Tambahan'),
                ]),
        ]);
    }

    /**
     * @param  array<int, AssetStatus|AssetCondition>  $enumCases
     */
    private static function enumOptions(array $enumCases): array
    {
        return collect($enumCases)
            ->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }
}
