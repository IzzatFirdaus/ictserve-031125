<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Component name: User Activity Widget
 * Description: Dashboard widget showing user login history and recent actions
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-004.5 (User Activity Monitoring)
 * @trace D04 §3.3 (User Management Dashboard)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @version 1.0.0
 *
 * @created 2025-11-07
 */
class UserActivityWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    /**
     * Widget is only visible to superusers.
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with(['division', 'grade'])
                    ->withCount(['helpdeskTickets', 'loanApplications', 'assignedHelpdeskTickets'])
                    ->orderBy('last_login_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('User'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email),

                Tables\Columns\BadgeColumn::make('role')
                    ->label(__('Role'))
                    ->colors([
                        'secondary' => 'staff',
                        'info' => 'approver',
                        'warning' => 'admin',
                        'danger' => 'superuser',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('division.name')
                    ->label(__('Division'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label(__('Last Login'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(
                        fn (User $record): ?string => $record->last_login_at
                            ? $record->last_login_at->diffForHumans()
                            : null
                    ),

                Tables\Columns\TextColumn::make('helpdesk_tickets_count')
                    ->label(__('Tickets Created'))
                    ->counts('helpdeskTickets')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('assigned_helpdesk_tickets_count')
                    ->label(__('Tickets Assigned'))
                    ->counts('assignedHelpdeskTickets')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('loan_applications_count')
                    ->label(__('Loan Applications'))
                    ->counts('loanApplications')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('Role'))
                    ->options([
                        'staff' => __('Staff'),
                        'approver' => __('Approver'),
                        'admin' => __('Admin'),
                        'superuser' => __('Superuser'),
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),

                Tables\Filters\Filter::make('recently_active')
                    ->label(__('Recently Active (7 days)'))
                    ->query(fn (Builder $query): Builder => $query->where('last_login_at', '>=', now()->subDays(7))),

                Tables\Filters\Filter::make('inactive_users')
                    ->label(__('Inactive (30+ days)'))
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->whereNull('last_login_at')
                            ->orWhere('last_login_at', '<=', now()->subDays(30));
                    })),
            ])
            ->actions([
                Action::make('view_profile')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (User $record): string => route('filament.admin.resources.users.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('last_login_at', 'desc')
            ->poll('60s')
            ->heading(__('User Activity Dashboard'))
            ->description(__('Monitor user login history and activity across the system'));
    }
}
