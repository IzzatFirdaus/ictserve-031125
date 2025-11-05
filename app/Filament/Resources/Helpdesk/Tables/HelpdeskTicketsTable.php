<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Tables;

use App\Models\Division;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

/**
 * Helpdesk Ticket table definition with SLA indicators and bulk workflows.
 */
class HelpdeskTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable(),

                // Hybrid submission type badge
                Tables\Columns\TextColumn::make('submission_type')
                    ->label('Jenis Penghantaran')
                    ->badge()
                    ->state(fn ($record) => $record->isGuestSubmission() ? 'Guest' : 'Authenticated')
                    ->color(fn ($record) => $record->isGuestSubmission() ? 'warning' : 'success')
                    ->icon(fn ($record) => $record->isGuestSubmission() ? 'heroicon-o-user' : 'heroicon-o-user-circle')
                    ->tooltip(fn ($record) => $record->isGuestSubmission()
                        ? "Guest: {$record->guest_name} ({$record->guest_email})"
                        : "Authenticated: {$record->user->name} ({$record->user->email})")
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("CASE WHEN user_id IS NULL THEN 0 ELSE 1 END {$direction}")),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name_ms')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'gray',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => self::statusColors()[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                // Asset linkage display
                Tables\Columns\TextColumn::make('relatedAsset.name')
                    ->label('Aset Berkaitan')
                    ->placeholder('-')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->tooltip(fn ($record) => $record->relatedAsset
                        ? "Asset Tag: {$record->relatedAsset->asset_tag}"
                        : null)
                    ->toggleable()
                    ->url(fn ($record) => $record->relatedAsset
                        ? route('filament.admin.resources.assets.view', $record->relatedAsset)
                        : null),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Pegawai')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sla_resolution_due_at')
                    ->label('SLA Resolusi')
                    ->formatStateUsing(fn ($state) => $state ? $state->diffForHumans() : '-')
                    ->tooltip(fn ($record) => optional($record->sla_resolution_due_at)?->toDayDateTimeString())
                    ->color(fn ($record) => $record->sla_resolution_due_at && now()->greaterThan($record->sla_resolution_due_at) ? 'danger' : 'success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                // Enhanced filter organization with groups
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::statusLabels())
                    ->label('Status')
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Keutamaan')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name_ms')
                    ->label('Kategori')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Enhanced hybrid submission type filter with better UI
                Tables\Filters\SelectFilter::make('submission_type')
                    ->label('Jenis Penghantaran')
                    ->options([
                        'guest' => '👤 Guest',
                        'authenticated' => '🔐 Authenticated',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'guest') {
                            return $query->whereNull('user_id');
                        }
                        if ($data['value'] === 'authenticated') {
                            return $query->whereNotNull('user_id');
                        }

                        return $query;
                    })
                    ->indicator('Jenis'),

                // Enhanced asset linkage filters
                Tables\Filters\Filter::make('has_asset')
                    ->label('Mempunyai Aset Berkaitan')
                    ->query(fn ($query) => $query->whereNotNull('asset_id'))
                    ->toggle()
                    ->indicator('Aset'),

                Tables\Filters\SelectFilter::make('asset_id')
                    ->relationship('relatedAsset', 'name')
                    ->label('Aset Spesifik')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Enhanced SLA filter with better visibility
                Tables\Filters\Filter::make('sla_breached')
                    ->label('⚠️ SLA Melebihi')
                    ->query(fn ($query) => $query->whereNotNull('sla_resolution_due_at')->where('sla_resolution_due_at', '<', now()))
                    ->toggle()
                    ->indicator('SLA'),

                // Additional useful filters
                Tables\Filters\Filter::make('unassigned')
                    ->label('Belum Ditugaskan')
                    ->query(fn ($query) => $query->whereNull('assigned_to_user'))
                    ->toggle(),

                Tables\Filters\Filter::make('my_tickets')
                    ->label('Tiket Saya')
                    ->query(fn ($query) => $query->where('assigned_to_user', auth()->id()))
                    ->toggle(),
            ])
            ->poll('60s')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('markResolved')
                    ->label('Tanda Selesai')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'resolved')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('assign')
                        ->label('Tugaskan')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Select::make('assigned_to_division')
                                ->options(fn () => Division::query()->orderBy('name_ms')->pluck('name_ms', 'id'))
                                ->label('Bahagian')
                                ->searchable()
                                ->preload(),
                            Select::make('assigned_to_user')
                                ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                                ->label('Pegawai')
                                ->searchable()
                                ->preload(),
                            TextInput::make('assigned_to_agency')
                                ->label('Agensi Luar')
                                ->maxLength(255),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each(
                            fn ($ticket) => $ticket->update([
                                'assigned_to_division' => $data['assigned_to_division'] ?? null,
                                'assigned_to_user' => $data['assigned_to_user'] ?? null,
                                'assigned_to_agency' => $data['assigned_to_agency'] ?? null,
                                'assigned_at' => now(),
                                'status' => $ticket->status === 'open' ? 'assigned' : $ticket->status,
                            ])
                        )),
                    BulkAction::make('update_status')
                        ->label('Kemaskini Status')
                        ->icon('heroicon-o-adjustments-vertical')
                        ->form([
                            Select::make('status')
                                ->options(self::statusLabels())
                                ->required()
                                ->label('Status'),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each(
                            fn ($ticket) => $ticket->update([
                                'status' => $data['status'],
                                'resolved_at' => $data['status'] === 'resolved' ? now() : $ticket->resolved_at,
                                'closed_at' => $data['status'] === 'closed' ? now() : $ticket->closed_at,
                            ])
                        )),
                    BulkAction::make('close')
                        ->label('Tutup Tiket')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(
                            fn ($ticket) => $ticket->update([
                                'status' => 'closed',
                                'closed_at' => now(),
                            ])
                        )),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    private static function statusLabels(): array
    {
        return [
            'open' => 'Open',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'pending_user' => 'Pending User',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

    private static function statusColors(): array
    {
        return [
            'open' => 'gray',
            'assigned' => 'primary',
            'in_progress' => 'warning',
            'pending_user' => 'secondary',
            'resolved' => 'success',
            'closed' => 'gray',
        ];
    }
}
