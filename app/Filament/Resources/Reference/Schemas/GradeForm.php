<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Gred')
                ->schema([
                    TextInput::make('code')
                        ->label(__('filament.reference.code'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(10),
                    TextInput::make('name_ms')
                        ->label(__('filament.reference.name_ms'))
                        ->required()
                        ->maxLength(255),
                    Hidden::make('name_en')
                        ->dehydrateStateUsing(fn (mixed $state, callable $get): mixed => filled($state) ? $state : $get('name_ms')),
                    TextInput::make('level')
                        ->label(__('filament.reference.level'))
                        ->numeric()
                        ->required(),
                    Toggle::make('can_approve_loans')
                        ->label(__('filament.reference.can_approve_loans'))
                        ->inline(false),
                ])
                ->columns(2),
        ]);
    }
}
