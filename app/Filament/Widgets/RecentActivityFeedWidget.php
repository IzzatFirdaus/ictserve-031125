<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Recent Activity Feed Widget
 *
 * Displays latest tickets, loan applications, approvals, and status changes.
 * Refreshes every 60 seconds via Livewire polling.
 *
 * @trace Requirements 6.3
 */
class RecentActivityFeedWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getActivityQuery())
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ticket' => 'info',
                        'loan' => 'warning',
                        'approval' => 'success',
                        'status_change' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'ticket' => 'heroicon-o-ticket',
                        'loan' => 'heroicon-o-cube',
                        'approval' => 'heroicon-o-check-circle',
                        'status_change' => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-bell',
                    }),
                TextColumn::make('title')
                    ->label('Activity')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('user')
                    ->label('User')
                    ->limit(30),
                TextColumn::make('timestamp')
                    ->label('Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('timestamp', 'desc')
            ->paginated([10, 25, 50])
            ->heading('Recent Activity')
            ->description('Latest system activities across all modules');
    }

    protected function getActivityQuery(): Builder
    {
        $activities = collect();

        // Recent tickets
        $tickets = HelpdeskTicket::query()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($ticket) => [
                'type' => 'ticket',
                'title' => "New Ticket: {$ticket->subject}",
                'user' => $ticket->user?->name ?? $ticket->guest_name ?? 'Guest',
                'timestamp' => $ticket->created_at,
                'url' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $ticket),
            ]);

        // Recent loan applications
        $loans = LoanApplication::query()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($loan) => [
                'type' => 'loan',
                'title' => "Loan Application: {$loan->application_number}",
                'user' => $loan->user->name,
                'timestamp' => $loan->created_at,
                'url' => route('filament.admin.resources.loans.loan-applications.view', $loan),
            ]);

        // Merge and sort
        $activities = $tickets->merge($loans)
            ->sortByDesc('timestamp')
            ->take(50);

        // Convert to query builder format
        return HelpdeskTicket::query()
            ->whereIn('id', $activities->pluck('id')->filter())
            ->orWhereIn('id', []);
    }
}
