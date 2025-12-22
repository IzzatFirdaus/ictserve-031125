<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

    /**
     * @param  array<string, mixed>  $conditions
     */
    public static function configure(Schema $schema, array $statuses, array $conditions): Schema
    {
        return $schema->components([
            Section::make(__('filament.asset_form.asset_info'))
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('asset_tag')
                            ->label(__('filament.asset_form.asset_tag'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label(__('filament.labels.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label(__('filament.labels.category'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('brand')
                            ->label(__('filament.labels.brand'))
                            ->maxLength(255),
                        TextInput::make('model')
                            ->label(__('filament.labels.model'))
                            ->maxLength(255),
                        TextInput::make('serial_number')
                            ->label(__('filament.asset_form.serial_number'))
                            ->maxLength(255),
                    ]),
                    Grid::make(3)->schema([
                        Select::make('status')
                            ->label(__('filament.labels.status'))
                            ->options(self::enumOptions($statuses))
                            ->required(),
                        Select::make('condition')
                            ->label(__('filament.labels.condition'))
                            ->options(self::enumOptions($conditions))
                            ->required(),
                        TextInput::make('location')
                            ->label(__('filament.labels.location'))
                            ->maxLength(255),
                    ]),
                ]),
            Section::make(__('filament.asset_form.financial_info'))
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('purchase_date')
                            ->label(__('filament.asset_form.purchase_date'))
                            ->required(),
                        TextInput::make('purchase_value')
                            ->label(__('filament.asset_form.purchase_value'))
                            ->numeric()
                            ->required(),
                        TextInput::make('current_value')
                            ->label(__('filament.asset_form.current_value'))
                            ->numeric(),
                    ]),
                    DatePicker::make('warranty_expiry')
                        ->label(__('filament.asset_form.warranty_expiry')),
                ]),
            Section::make(__('filament.asset_form.maintenance_attachments'))
                ->schema([
                    Grid::make(2)->schema([
                        DatePicker::make('last_maintenance_date')
                            ->label(__('filament.asset_form.last_maintenance')),
                        DatePicker::make('next_maintenance_date')
                            ->label(__('filament.asset_form.next_maintenance')),
                    ]),
                    KeyValue::make('specifications')
                        ->label(__('filament.asset_form.specifications'))
                        ->keyLabel(__('filament.asset_form.parameter'))
                        ->valueLabel(__('filament.asset_form.details'))
                        ->reorderable(),
                    KeyValue::make('accessories')
                        ->label(__('filament.asset_form.accessories'))
                        ->keyLabel(__('filament.asset_form.accessory'))
                        ->valueLabel(__('filament.asset_form.quantity_notes'))
                        ->reorderable(),
                    Textarea::make('notes')
                        ->columnSpanFull()
                        ->rows(3)
                        ->label(__('filament.asset_form.additional_notes')),
                ]),
        ]);
    }

    /**
     * @param  array<int, AssetStatus|AssetCondition>  $enumCases
     * @return array<string, string>
     */

    /**
     * @param  array<string, mixed>  $enumCases
     */
    private static function enumOptions(array $enumCases): array
    {
        return collect($enumCases)
            ->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }
}
