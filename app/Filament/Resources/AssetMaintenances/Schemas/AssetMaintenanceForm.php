<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMaintenances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Schema borang untuk Penyelenggaraan Aset
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 54.1, 54.2, 54.3, 54.4
 */
class AssetMaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('asset_maintenance.sections.maintenance_details'))
                    ->schema([
                        Select::make('asset_id')
                            ->label(__('asset_maintenance.fields.asset_id'))
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText(__('asset_maintenance.helpers.asset_id'))
                            ->placeholder(__('asset_maintenance.placeholders.asset_id')),

                        Select::make('maintenance_type')
                            ->label(__('asset_maintenance.fields.maintenance_type'))
                            ->options([
                                'routine' => __('asset_maintenance.maintenance_types.routine'),
                                'repair' => __('asset_maintenance.maintenance_types.repair'),
                                'upgrade' => __('asset_maintenance.maintenance_types.upgrade'),
                                'inspection' => __('asset_maintenance.maintenance_types.inspection'),
                            ])
                            ->required()
                            ->helperText(__('asset_maintenance.helpers.maintenance_type'))
                            ->placeholder(__('asset_maintenance.placeholders.maintenance_type')),

                        Select::make('status')
                            ->label(__('asset_maintenance.fields.status'))
                            ->options([
                                'scheduled' => __('asset_maintenance.status_options.scheduled'),
                                'in_progress' => __('asset_maintenance.status_options.in_progress'),
                                'completed' => __('asset_maintenance.status_options.completed'),
                                'cancelled' => __('asset_maintenance.status_options.cancelled'),
                            ])
                            ->default('scheduled')
                            ->required()
                            ->live()
                            ->helperText(__('asset_maintenance.helpers.status'))
                            ->placeholder(__('asset_maintenance.placeholders.status')),

                        DatePicker::make('scheduled_date')
                            ->label(__('asset_maintenance.fields.scheduled_date'))
                            ->default(now())
                            ->required()
                            ->helperText(__('asset_maintenance.helpers.scheduled_date')),

                        DatePicker::make('completed_date')
                            ->label(__('asset_maintenance.fields.completed_date'))
                            ->visible(fn (callable $get): bool => $get('status') === 'completed')
                            ->requiredIf('status', 'completed')
                            ->helperText(__('asset_maintenance.helpers.completed_date')),

                        TextInput::make('cost')
                            ->label(__('asset_maintenance.fields.cost'))
                            ->numeric()
                            ->prefix('RM')
                            ->step(0.01)
                            ->minValue(0)
                            ->placeholder(__('asset_maintenance.placeholders.cost'))
                            ->helperText(__('asset_maintenance.helpers.cost')),
                    ])
                    ->columns(2),

                Section::make(__('asset_maintenance.sections.performer_info'))
                    ->schema([
                        Radio::make('performer_mode')
                            ->label(__('asset_maintenance.fields.performer_mode'))
                            ->options([
                                'internal' => __('asset_maintenance.performer_options.internal'),
                                'external' => __('asset_maintenance.performer_options.external'),
                            ])
                            ->default('internal')
                            ->live()
                            ->helperText(__('asset_maintenance.helpers.performer_mode'))
                            ->columnSpanFull(),

                        Select::make('performed_by_user_id')
                            ->label(__('asset_maintenance.fields.performed_by_user_id'))
                            ->relationship('performedByUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get): bool => $get('performer_mode') === 'internal')
                            ->helperText(__('asset_maintenance.helpers.performed_by_user_id'))
                            ->placeholder(__('asset_maintenance.placeholders.performed_by_user_id')),

                        TextInput::make('performed_by')
                            ->label(__('asset_maintenance.fields.performed_by'))
                            ->maxLength(255)
                            ->visible(fn (callable $get): bool => $get('performer_mode') === 'external')
                            ->helperText(__('asset_maintenance.helpers.performed_by'))
                            ->placeholder(__('asset_maintenance.placeholders.performed_by')),
                    ])
                    ->columns(2),

                Section::make(__('asset_maintenance.sections.additional_info'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('asset_maintenance.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText(__('asset_maintenance.helpers.notes'))
                            ->placeholder(__('asset_maintenance.placeholders.notes')),
                    ]),
            ]);
    }
}
