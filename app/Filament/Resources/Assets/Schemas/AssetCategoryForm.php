<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * Asset Category form schema.
 */
class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category Details')
                ->schema([
                    TextInput::make('code')
                        ->label('Kod')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])
                ->columns(2),
            Section::make('Loan Defaults')
                ->schema([
                    TextInput::make('default_loan_duration_days')
                        ->label('Tempoh Pinjaman Lalai (hari)')
                        ->numeric()
                        ->minValue(1)
                        ->default(7),
                    TextInput::make('max_loan_duration_days')
                        ->label('Tempoh Maksimum (hari)')
                        ->numeric()
                        ->minValue(1)
                        ->default(30),
                    Toggle::make('requires_approval')
                        ->label('Perlu Kelulusan Pegawai G41+')
                        ->inline(false),
                    TextInput::make('sort_order')
                        ->label('Susunan')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
            Section::make('Specification Template')
                ->schema([
                    KeyValue::make('specification_template')
                        ->label('Spec Template')
                        ->keyLabel('Field')
                        ->valueLabel('Description')
                        ->addButtonLabel('Tambah medan')
                        ->reorderable(),
                ]),
        ]);
    }
}
