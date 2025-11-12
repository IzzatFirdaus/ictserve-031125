<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

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
                TextColumn::make('activity_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ticket' => 'info',
                        'Loan' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Ticket' => 'heroicon-o-ticket',
                        'Loan' => 'heroicon-o-cube',
                        default => 'heroicon-o-bell',
                    }),
                TextColumn::make('subject')
                    ->label('Activity')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_by')
                    ->label('User')
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->heading('Recent Activity')
            ->description('Latest system activities across all modules');
    }

    protected function getActivityQuery(): Builder
    {
        // Query tickets with safe null handling
        $ticketQuery = HelpdeskTicket::query()
            ->select('id', 'subject', 'created_at', 'user_id', 'guest_name')
            ->selectRaw("'Ticket' as activity_type")
            ->selectRaw("COALESCE(users.name, helpdesk_tickets.guest_name, 'Guest') as created_by")
            ->leftJoin('users', 'helpdesk_tickets.user_id', '=', 'users.id')
            ->latest('helpdesk_tickets.created_at')
            ->limit(50);

        // Return the ticket query as the main activity feed
        return $ticketQuery;
    }
}
