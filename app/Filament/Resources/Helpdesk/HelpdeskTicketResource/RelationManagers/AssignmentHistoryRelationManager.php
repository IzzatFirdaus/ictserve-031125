<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

/**
 * Assignment History Relation Manager
 *
 * Displays chronological history of ticket assignments using Laravel Auditing.
 * Shows who assigned the ticket, to whom, and when.
 *
 * @trace Requirements 1.2, 7.1
 */
class AssignmentHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'audits';

    protected static ?string $title = 'Sejarah Tugasan';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('event')
            ->modifyQueryUsing(function ($query) {
                // Filter audits to only show assignment-related changes
                return $query->where(function ($q) {
                    $q->whereJsonContains('new_values->assigned_to_user', fn ($value) => $value !== null)
                        ->orWhereJsonContains('new_values->assigned_to_division', fn ($value) => $value !== null)
                        ->orWhereJsonContains('new_values->assigned_to_agency', fn ($value) => $value !== null);
                })->latest();
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh & Masa')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Ditugaskan Oleh')
                    ->default('Sistem')
                    ->searchable(),

                Tables\Columns\TextColumn::make('new_values')
                    ->label('Ditugaskan Kepada')
                    ->formatStateUsing(function ($state, Audit $record) {
                        $parts = [];

                        if (isset($state['assigned_to_user'])) {
                            $user = \App\Models\User::find($state['assigned_to_user']);
                            $parts[] = 'Pengguna: '.($user->name ?? 'Tidak diketahui');
                        }

                        if (isset($state['assigned_to_division'])) {
                            $division = \App\Models\Division::find($state['assigned_to_division']);
                            $parts[] = 'Bahagian: '.($division->name_ms ?? 'Tidak diketahui');
                        }

                        if (isset($state['assigned_to_agency'])) {
                            $parts[] = 'Agensi: '.$state['assigned_to_agency'];
                        }

                        return implode(' | ', $parts) ?: '-';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('old_values')
                    ->label('Tugasan Sebelumnya')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Belum Ditugaskan';
                        }

                        $parts = [];

                        if (isset($state['assigned_to_user'])) {
                            $user = \App\Models\User::find($state['assigned_to_user']);
                            $parts[] = 'Pengguna: '.($user->name ?? 'Tidak diketahui');
                        }

                        if (isset($state['assigned_to_division'])) {
                            $division = \App\Models\Division::find($state['assigned_to_division']);
                            $parts[] = 'Bahagian: '.($division->name_ms ?? 'Tidak diketahui');
                        }

                        if (isset($state['assigned_to_agency'])) {
                            $parts[] = 'Agensi: '.$state['assigned_to_agency'];
                        }

                        return implode(' | ', $parts) ?: 'Belum Ditugaskan';
                    })
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('event')
                    ->label('Tindakan')
                    ->icon(fn (string $state): string => match ($state) {
                        'created' => Heroicon::OutlinedPlusCircle->value,
                        'updated' => Heroicon::OutlinedArrowPath->value,
                        default => Heroicon::OutlinedInformationCircle->value,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Tiada Sejarah Tugasan')
            ->emptyStateDescription('Tiket ini belum ditugaskan.')
            ->emptyStateIcon(Heroicon::OutlinedClock);
    }
}
