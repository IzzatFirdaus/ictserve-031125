<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DivisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Bahagian')
                ->schema([
                    TextInput::make('code')
                        ->label('Kod')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    TextInput::make('name_ms')
                        ->label('Nama (BM)')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('name_en')
                        ->label('Name (EN)')
                        ->required()
                        ->maxLength(255),
                    Select::make('parent_id')
                        ->relationship('parent', 'name_ms')
                        ->label('Bahagian Induk')
                        ->searchable()
                        ->preload(),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])
                ->columns(2),
            Section::make('Penerangan')
                ->schema([
                    TextInput::make('description_ms')
                        ->label('Deskripsi (BM)')
                        ->maxLength(255),
                    TextInput::make('description_en')
                        ->label('Description (EN)')
                        ->maxLength(255),
                ])
                ->columns(2),
        ]);
    }
}
