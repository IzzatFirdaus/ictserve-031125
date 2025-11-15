<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\Audits\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Audit Infolist Configuration
 *
 * Comprehensive audit record detail view with before/after values.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 */
class AuditInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Audit Information'))
                    ->icon(Heroicon::OutlinedInformationCircle->value)
                    ->description(__('Basic audit record information'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label(__('Audit ID'))
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('event')
                                    ->label(__('Action'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        'restored' => 'warning',
                                        default => 'gray',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'created' => Heroicon::OutlinedPlus->value,
                                        'updated' => Heroicon::OutlinedPencil->value,
                                        'deleted' => Heroicon::OutlinedTrash->value,
                                        'restored' => Heroicon::OutlinedArrowPath->value,
                                        default => Heroicon::OutlinedInformationCircle->value,
                                    }),

                                TextEntry::make('created_at')
                                    ->label(__('Timestamp'))
                                    ->dateTime('d/m/Y H:i:s')
                                    ->description(fn ($record) => $record->created_at->diffForHumans()),
                            ]),
                    ]),

                Section::make(__('User Information'))
                    ->icon(Heroicon::OutlinedUser->value)
                    ->description(__('User who performed the action'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('User Name'))
                                    ->default(__('System'))
                                    ->icon(Heroicon::OutlinedUser->value),

                                TextEntry::make('user.email')
                                    ->label(__('Email'))
                                    ->default(__('N/A'))
                                    ->icon(Heroicon::OutlinedEnvelope->value)
                                    ->copyable()
                                    ->copyMessage(__('Email copied'))
                                    ->copyMessageDuration(1500),

                                TextEntry::make('user.role')
                                    ->label(__('Role'))
                                    ->default(__('N/A'))
                                    ->badge()
                                    ->formatStateUsing(fn ($record) => $record->user?->roles->pluck('name')->join(', ') ?? __('N/A')),
                            ]),
                    ]),

                Section::make(__('Entity Information'))
                    ->icon(Heroicon::OutlinedCube->value)
                    ->description(__('Entity that was modified'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('auditable_type')
                                    ->label(__('Entity Type'))
                                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('auditable_id')
                                    ->label(__('Entity ID'))
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ]),

                Section::make(__('Request Information'))
                    ->icon(Heroicon::OutlinedGlobeAlt->value)
                    ->description(__('Request details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('ip_address')
                                    ->label(__('IP Address'))
                                    ->icon(Heroicon::OutlinedGlobeAlt->value)
                                    ->copyable()
                                    ->copyMessage(__('IP address copied'))
                                    ->copyMessageDuration(1500),

                                TextEntry::make('url')
                                    ->label(__('URL'))
                                    ->icon(Heroicon::OutlinedLink->value)
                                    ->copyable()
                                    ->copyMessage(__('URL copied'))
                                    ->copyMessageDuration(1500)
                                    ->limit(100),
                            ]),

                        TextEntry::make('user_agent')
                            ->label(__('User Agent'))
                            ->icon(Heroicon::OutlinedComputerDesktop->value)
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage(__('User agent copied'))
                            ->copyMessageDuration(1500),

                        TextEntry::make('tags')
                            ->label(__('Tags'))
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull()
                            ->visible(fn ($record) => ! empty($record->tags)),
                    ]),

                Section::make(__('Changes'))
                    ->icon(Heroicon::OutlinedArrowsRightLeft->value)
                    ->description(__('Before and after values'))
                    ->schema([
                        Group::make()
                            ->schema([
                                KeyValueEntry::make('old_values')
                                    ->label(__('Old Values'))
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => ! empty($record->old_values))
                                    ->keyLabel(__('Field'))
                                    ->valueLabel(__('Value'))
                                    ->formatStateUsing(fn ($state) => is_array($state) ? $state : json_decode($state, true)),

                                KeyValueEntry::make('new_values')
                                    ->label(__('New Values'))
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => ! empty($record->new_values))
                                    ->keyLabel(__('Field'))
                                    ->valueLabel(__('Value'))
                                    ->formatStateUsing(fn ($state) => is_array($state) ? $state : json_decode($state, true)),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
