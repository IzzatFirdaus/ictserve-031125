<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
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
                TextColumn::make('activity_type')
                    ->label(__('widgets.type'))
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
                    ->label(__('widgets.activity'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_by')
                    ->label(__('widgets.user'))
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label(__('widgets.time'))
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->heading(__('widgets.recent_activity'))
            ->description(__('widgets.latest_system_activities'));
    }

    protected function getActivityQuery(): Builder
    {
        // Query tickets with safe null handling
        $ticketQuery = HelpdeskTicket::query()
            ->select('helpdesk_tickets.id', 'helpdesk_tickets.subject', 'helpdesk_tickets.created_at', 'helpdesk_tickets.user_id', 'helpdesk_tickets.guest_name')
            ->selectRaw("'Ticket' as activity_type")
            ->selectRaw("COALESCE(users.name, helpdesk_tickets.guest_name, 'Guest') as created_by")
            ->leftJoin('users', 'helpdesk_tickets.user_id', '=', 'users.id')
            ->latest('helpdesk_tickets.created_at')
            ->limit(50);

        // Return the ticket query as the main activity feed
        return $ticketQuery;
    }
}
