<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * TicketCategory Form Schema
 *
 * Captures bilingual labels, hierarchy, and SLA configuration for helpdesk categories.
 */
class TicketCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category Details')
                ->schema([
                    TextInput::make('code')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->label('Kod Kategori'),
                    TextInput::make('name_ms')
                        ->required()
                        ->maxLength(255)
                        ->label('Nama (Bahasa Melayu)'),
                    TextInput::make('name_en')
                        ->required()
                        ->maxLength(255)
                        ->label('Name (English)'),
                    Select::make('parent_id')
                        ->relationship('parent', 'name_ms')
                        ->preload()
                        ->searchable()
                        ->label('Induk'),
                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Aktif'),
                ])
                ->columns(2),
            Section::make('Descriptions & SLA')
                ->schema([
                    Textarea::make('description_ms')
                        ->rows(3)
                        ->label('Deskripsi (Bahasa Melayu)'),
                    Textarea::make('description_en')
                        ->rows(3)
                        ->label('Description (English)'),
                    TextInput::make('sla_response_hours')
                        ->integer()
                        ->minValue(1)
                        ->maxValue(240)
                        ->default(8)
                        ->label('SLA Respons (Jam)')
                        ->helperText('Masa respons maksimum mengikut SLA'),
                    TextInput::make('sla_resolution_hours')
                        ->integer()
                        ->minValue(1)
                        ->maxValue(720)
                        ->default(24)
                        ->label('SLA Penyelesaian (Jam)'),
                ])
                ->columns(2),
        ]);
    }
}
