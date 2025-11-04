<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Recent Tickets Table Widget
 *
 * Displays the most recent helpdesk tickets with hybrid submission indicators.
 * Provides quick access to recent ticket activity for dashboard overview.
 *
 * @trace Requirements: Requirement 3.2
 */
class RecentTicketsTable extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                HelpdeskTicket::query()
                    ->with(['user', 'category', 'assignedUser', 'relatedAsset'])
                    ->latest()
                    ->limit(10)
            )
            ->heading('Tiket Terkini')
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable(),

                // Hybrid submission indicator
                TextColumn::make('submission_type')
                    ->label('Jenis')
                    ->badge()
                    ->state(fn ($record) => $record->isGuestSubmission() ? 'Tetamu' : 'Berdaftar')
                    ->color(fn ($record) => $record->isGuestSubmission() ? 'warning' : 'success')
                    ->icon(fn ($record) => $record->isGuestSubmission() ? 'heroicon-o-user' : 'heroicon-o-user-circle'),

                TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('category.name_ms')
                    ->label('Kategori')
                    ->badge(),

                TextColumn::make('priority')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'gray',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'gray',
                        'assigned' => 'primary',
                        'in_progress' => 'warning',
                        'pending_user' => 'secondary',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),

                // Asset linkage indicator
                TextColumn::make('relatedAsset.name')
                    ->label('Aset')
                    ->placeholder('-')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->limit(20),

                TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->recordAction(
                Action::make('view')
                    ->label('Lihat')
                    ->url(fn ($record) => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $record))
                    ->icon('heroicon-o-eye')
            )
            ->paginated(false);
    }
}
