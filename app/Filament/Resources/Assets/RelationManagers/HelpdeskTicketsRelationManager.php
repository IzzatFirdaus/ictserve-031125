<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Helpdesk Tickets Relation Manager
 *
 * Displays maintenance tickets and damage reports for an asset.
 *
 * @trace Requirements 3.2, 7.2
 */
class HelpdeskTicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'helpdeskTickets';

    protected static ?string $title = 'Tiket Penyelenggaraan';

    protected static string|\BackedEnum|null $icon = Heroicon::OutlinedTicket;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ticket_number')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('No. tiket disalin')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tajuk')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'maintenance' => 'warning',
                        'asset_damage' => 'danger',
                        'repair' => 'info',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'maintenance' => 'Penyelenggaraan',
                        'asset_damage' => 'Kerosakan Aset',
                        'repair' => 'Pembaikan',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'info',
                        'low' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', (string) $state))),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open' => 'warning',
                        'assigned' => 'info',
                        'in_progress' => 'primary',
                        'pending_user' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', (string) $state))),
                Tables\Columns\TextColumn::make('damage_type')
                    ->label('Jenis Kerosakan')
                    ->badge()
                    ->color('danger')
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Ditugaskan Kepada')
                    ->default('Belum ditugaskan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Dicipta')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Tarikh Diselesaikan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->default('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'maintenance' => 'Penyelenggaraan',
                        'asset_damage' => 'Kerosakan Aset',
                        'repair' => 'Pembaikan',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Keutamaan')
                    ->options([
                        'low' => 'Rendah',
                        'normal' => 'Biasa',
                        'high' => 'Tinggi',
                        'urgent' => 'Segera',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Terbuka',
                        'assigned' => 'Ditugaskan',
                        'in_progress' => 'Dalam Proses',
                        'pending_user' => 'Menunggu Pengguna',
                        'resolved' => 'Diselesaikan',
                        'closed' => 'Ditutup',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('unresolved')
                    ->label('Belum Diselesaikan')
                    ->query(fn ($query) => $query->whereNull('resolved_at'))
                    ->toggle(),
                Tables\Filters\Filter::make('maintenance_only')
                    ->label('Penyelenggaraan Sahaja')
                    ->query(fn ($query) => $query->where('category', 'maintenance'))
                    ->toggle(),
            ])
            ->headerActions([
                // No create action - tickets are created through the helpdesk system
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', [
                        'record' => $record->id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // No bulk actions for helpdesk tickets
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('30s')
            ->emptyStateHeading('Tiada Tiket Penyelenggaraan')
            ->emptyStateDescription('Aset ini tidak mempunyai sebarang tiket penyelenggaraan atau kerosakan.')
            ->emptyStateIcon('heroicon-o-ticket');
    }
}
