<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Models\HelpdeskTicket;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Recent Tickets Table Widget
 *
 * Displays the most recent helpdesk tickets with hybrid submission indicators.
 * Provides quick access to recent ticket activity for dashboard overview.
 * Now redirects to Portal for ticket viewing instead of Filament.
 *
 * @trace D03-FR-003.2 (Helpdesk Dashboard)
 * @trace D04 §5.0.1 (Widget Portal Redirects)
 * @trace D10 §7 (Component Documentation)
 *
 * @version 2.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 *
 * @updated 2025-11-26
 */
class RecentTicketsTable extends TableWidget
{
    use WidgetMetadata;

    protected static ?int $sort = 4;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration';
    }

    protected static bool $isLazy = true; // Non-critical - lazy load

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s'; // Refresh every minute

    public function table(Table $table): Table
    {
        return $table
            ->query(
                HelpdeskTicket::query()
                    ->with(['user', 'user.division', 'category', 'assignedUser', 'relatedAsset'])
                    ->latest()
                    ->limit(10)
            )
            ->heading(__('widgets.recent_tickets'))
            ->columns([
                TextColumn::make('ticket_number')
                    ->label(__('widgets.ticket_number'))
                    ->searchable()
                    ->sortable(),

                // Hybrid submission indicator
                TextColumn::make('submission_type')
                    ->label(__('widgets.type'))
                    ->badge()
                    ->state(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'Tetamu' : 'Berdaftar')
                    ->color(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'warning' : 'success')
                    ->icon(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'heroicon-o-user' : 'heroicon-o-user-circle'),

                TextColumn::make('subject')
                    ->label(__('widgets.subject'))
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('widgets.reported_by'))
                    ->description(fn (HelpdeskTicket $record): ?string => $record->user?->division?->name)
                    ->placeholder($this->getGuestPlaceholder()),

                TextColumn::make('category.name_ms')
                    ->label(__('widgets.category'))
                    ->badge(),

                TextColumn::make('priority')
                    ->label(__('widgets.priority'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('status')
                    ->label(__('widgets.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'gray',
                        'assigned' => 'primary',
                        'in_progress' => 'warning',
                        'pending_user' => 'secondary',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                // Asset linkage indicator
                TextColumn::make('relatedAsset.name')
                    ->label(__('widgets.asset'))
                    ->placeholder('-')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->limit(20),

                TextColumn::make('created_at')
                    ->label(__('widgets.created'))
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('review_in_portal')
                    ->label(__('widgets.review_in_portal'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (HelpdeskTicket $record): string => route('helpdesk.authenticated.ticket.show', $record))
                    ->openUrlInNewTab(false)
                    ->color(Color::Amber),
            ])
            ->paginated(false);
    }

    /**
     * Get placeholder text for guest submissions.
     */
    protected function getGuestPlaceholder(): string
    {
        return __('widgets.guest_submission');
    }
}
