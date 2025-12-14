<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Gred')
                ->schema([
                    TextInput::make('code')
                        ->label('Kod')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(10),
                    TextInput::make('name_ms')
                        ->label('Nama (BM)')
                        ->required()
                        ->maxLength(255),
                    Hidden::make('name_en')
                        ->dehydrateStateUsing(fn (mixed $state, Get $get): mixed => filled($state) ? $state : $get('name_ms')),
                    TextInput::make('level')
                        ->label('Tahap')
                        ->numeric()
                        ->required(),
                    Toggle::make('can_approve_loans')
                        ->label('Boleh Lulus Pinjaman (G41+)')
                        ->inline(false),
                ])
                ->columns(2),
        ]);
    }
}
