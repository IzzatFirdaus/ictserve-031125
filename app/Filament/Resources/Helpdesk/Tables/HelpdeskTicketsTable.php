<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Tables;

use App\Models\Division;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
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
                Tables\Columns\IconColumn::make('isGuestSubmission')
                    ->label('Tetamu')
                    ->state(fn ($record) => $record->isGuestSubmission())
                    ->boolean()
                    ->alignCenter()
                    ->tooltip(fn ($record) => $record->isGuestSubmission() ? 'Guest submission' : 'Authenticated submission'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::statusLabels())
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Keutamaan')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name_ms')
                    ->label('Kategori'),
                Tables\Filters\Filter::make('sla_breached')
                    ->label('SLA Melebihi')
                    ->query(fn ($query) => $query->whereNotNull('sla_resolution_due_at')->where('sla_resolution_due_at', '<', now())),
            ])
            ->poll('60s')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markResolved')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign')
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
                    Tables\Actions\BulkAction::make('update_status')
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
                    Tables\Actions\BulkAction::make('close')
                        ->label('Tutup Tiket')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(
                            fn ($ticket) => $ticket->update([
                                'status' => 'closed',
                                'closed_at' => now(),
                            ])
                        )),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
