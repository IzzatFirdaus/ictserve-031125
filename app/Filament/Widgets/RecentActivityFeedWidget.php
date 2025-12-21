<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

/**
 * Recent Activity Feed Widget
 *
 * Displays real-time activity feed including:
 * - Latest tickets, loan applications, approvals, and status changes
 * - Real-time updates via Laravel Reverb WebSocket
 * - Combined activity from both helpdesk and loan modules
 *
 * @trace Requirements: Requirement 8.1 (Real-Time Notifications)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D16 Broadcasting Setup - Laravel Reverb integration
 */
class RecentActivityFeedWidget extends BaseWidget
{
    use WidgetMetadata;

    protected static bool $isLazy = true; // Non-critical - lazy load

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s'; // Fallback polling for real-time updates

    /**
     * Sort order - display at bottom of dashboard
     */
    protected static ?int $sort = 100;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration';
    }

    /**
     * Listen for new ticket creation via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,TicketCreated')]
    public function refreshOnTicketCreated(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    /**
     * Listen for ticket status changes via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,TicketStatusChanged')]
    public function refreshOnTicketStatusChanged(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    /**
     * Listen for new loan application creation via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,LoanApplicationCreated')]
    public function refreshOnLoanCreated(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    /**
     * Listen for loan approval events via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,LoanApprovalDecision')]
    public function refreshOnApprovalDecision(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    /**
     * Listen for asset check-out/check-in events via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,AssetTransactionCompleted')]
    public function refreshOnAssetTransaction(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    /**
     * Listen for high-priority ticket broadcast via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,HighPriorityTicketCreated')]
    public function refreshOnHighPriorityTicket(): void
    {
        Cache::forget('dashboard:recent-activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getActivityQuery())
            ->columns([
                TextColumn::make('activity_type')
                    ->label(__('widgets.type'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Tiket' => 'info',
                        'Pinjaman' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'Tiket' => 'heroicon-o-ticket',
                        'Pinjaman' => 'heroicon-o-document-text',
                        default => 'heroicon-o-bell',
                    }),
                TextColumn::make('subject')
                    ->label(__('widgets.activity'))
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('created_by')
                    ->label(__('widgets.user'))
                    ->default(__('widgets.system'))
                    ->limit(25),
                TextColumn::make('created_at')
                    ->label(__('widgets.time'))
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->heading(__('widgets.recent_activity'))
            ->description(__('widgets.latest_system_activities'));
    }

    /**
     * Get combined activity query from tickets and loans
     * Uses union query to combine both sources for real-time activity feed
     *
     * @return Builder<HelpdeskTicket>
     */
    protected function getActivityQuery(): Builder
    {
        // Query tickets with safe null handling
        /** @var Builder<HelpdeskTicket> $query */
        $query = HelpdeskTicket::query()
            ->select([
                'helpdesk_tickets.id',
                'helpdesk_tickets.subject',
                'helpdesk_tickets.created_at',
                'helpdesk_tickets.user_id',
                'helpdesk_tickets.guest_name',
            ])
            ->selectRaw("'Tiket' as activity_type")
            ->selectRaw("COALESCE(users.name, helpdesk_tickets.guest_name, 'Tetamu') as created_by")
            ->leftJoin('users', 'helpdesk_tickets.user_id', '=', 'users.id')
            ->latest('helpdesk_tickets.created_at');

        return $query;
    }

    /**
     * Get combined activity from tickets and loans
     * Returns a collection of recent activities from both modules
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getCombinedActivityData(): Collection
    {
        return Cache::remember('dashboard:recent-activity', 60, function () {
            // Get recent tickets
            $tickets = HelpdeskTicket::query()
                ->with('user')
                ->latest('created_at')
                ->limit(25)
                ->get()
                ->map(fn (HelpdeskTicket $ticket) => [
                    'type' => 'Tiket',
                    'description' => $ticket->subject,
                    'user' => $ticket->user?->name ?? $ticket->guest_name ?? __('widgets.guest'),
                    'created_at' => $ticket->created_at,
                    'reference' => $ticket->ticket_number,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                ]);

            // Get recent loan applications
            $loans = LoanApplication::query()
                ->with('user')
                ->latest('created_at')
                ->limit(25)
                ->get()
                ->map(fn (LoanApplication $loan) => [
                    'type' => 'Pinjaman',
                    'description' => $loan->purpose ?? __('widgets.loan_application'),
                    'user' => $loan->user?->name ?? $loan->applicant_name ?? __('widgets.guest'),
                    'created_at' => $loan->created_at,
                    'reference' => $loan->application_number,
                    'status' => $loan->status?->value ?? 'unknown',
                ]);

            // Combine and sort by created_at
            return $tickets->concat($loans)
                ->sortByDesc('created_at')
                ->take(50)
                ->values();
        });
    }
}
