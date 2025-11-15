<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

/**
 * Status Timeline Relation Manager
 *
 * Displays chronological history of ticket status changes using Laravel Auditing.
 * Shows status transitions, who made the change, and when.
 *
 * @trace Requirements 1.2, 7.1
 */
class StatusTimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'audits';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('helpdesk.status_timeline');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('event')
            ->modifyQueryUsing(function ($query) {
                // Filter audits to only show status-related changes
                return $query->where(function ($q) {
                    $q->whereJsonContains('new_values->status', fn ($value) => $value !== null)
                        ->orWhereJsonContains('new_values->priority', fn ($value) => $value !== null);
                })->latest();
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('helpdesk.date_time'))
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->description(fn (Audit $record): string => $record->created_at->diffForHumans()),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('helpdesk.changed_by'))
                    ->default('System')
                    ->searchable()
                    ->description(fn (Audit $record): ?string => $record->user?->email),

                Tables\Columns\TextColumn::make('old_values')
                    ->label(__('helpdesk.from'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Initial';
                        }

                        $status = $state['status'] ?? null;
                        $priority = $state['priority'] ?? null;

                        if ($status) {
                            return ucfirst(str_replace('_', ' ', $status));
                        }

                        if ($priority) {
                            return ucfirst($priority).' Priority';
                        }

                        return '-';
                    })
                    ->color(fn ($state): string => match ($state['status'] ?? null) {
                        'open' => 'info',
                        'assigned' => 'primary',
                        'in_progress' => 'warning',
                        'pending_user' => 'gray',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('transition')
                    ->label('')
                    ->icon(Heroicon::OutlinedArrowRight)
                    ->color('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('new_values')
                    ->label(__('helpdesk.to'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $status = $state['status'] ?? null;
                        $priority = $state['priority'] ?? null;

                        if ($status) {
                            return ucfirst(str_replace('_', ' ', $status));
                        }

                        if ($priority) {
                            return ucfirst($priority).' Priority';
                        }

                        return '-';
                    })
                    ->color(fn ($state): string => match ($state['status'] ?? null) {
                        'open' => 'info',
                        'assigned' => 'primary',
                        'in_progress' => 'warning',
                        'pending_user' => 'gray',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                        default => match ($state['priority'] ?? null) {
                            'urgent' => 'danger',
                            'high' => 'warning',
                            'normal' => 'info',
                            'low' => 'gray',
                            default => 'gray',
                        },
                    }),

                Tables\Columns\TextColumn::make('new_values')
                    ->label(__('helpdesk.notes'))
                    ->formatStateUsing(fn ($state) => $state['admin_notes'] ?? $state['internal_notes'] ?? '-')
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state['admin_notes'] ?? $state['internal_notes'] ?? null)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('helpdesk.ip_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading(__('helpdesk.no_status_changes'))
            ->emptyStateDescription(__('helpdesk.no_status_changes_description'))
            ->emptyStateIcon(Heroicon::OutlinedChartBar);
    }
}
