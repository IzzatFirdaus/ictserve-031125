<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DivisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Bahagian')
                ->schema([
                    TextInput::make('code')
                        ->label(__('filament.reference.code'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    TextInput::make('name_ms')
                        ->label(__('filament.reference.name_ms'))
                        ->required()
                        ->maxLength(255),
                    Hidden::make('name_en')
                        ->dehydrateStateUsing(fn (mixed $state, callable $get): mixed => filled($state) ? $state : $get('name_ms')),
                    Select::make('parent_id')
                        ->relationship('parent', 'name_ms')
                        ->label(__('filament.reference.parent_division'))
                        ->searchable()
                        ->preload(),
                    Toggle::make('is_active')
                        ->label(__('filament.reference.active'))
                        ->default(true),
                ])
                ->columns(2),
            Section::make('Penerangan')
                ->schema([
                    TextInput::make('description_ms')
                        ->label(__('filament.reference.description_ms'))
                        ->maxLength(255),
                    Hidden::make('description_en')
                        ->dehydrateStateUsing(fn (mixed $state, callable $get): mixed => filled($state) ? $state : $get('description_ms')),
                ])
                ->columns(2),
        ]);
    }
}
