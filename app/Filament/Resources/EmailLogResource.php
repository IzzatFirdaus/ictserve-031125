<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use App\Services\EmailNotificationService;
use BackedEnum;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Email Log Resource
 *
 * Filament resource for managing email notification logs with delivery tracking,
 * retry mechanisms, and comprehensive filtering capabilities.
 *
 * Requirements: 17.1, D03-FR-014.1
 *
 * @see D04 §12.1 Email notification management
 */
class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('email_log.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return __('email_log.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make(__('email_log.email_details'))
                    ->schema([
                        Forms\Components\TextInput::make('recipient_email')
                            ->label(__('email_log.recipient_email'))
                            ->email()
                            ->disabled(),

                        Forms\Components\TextInput::make('subject')
                            ->label(__('email_log.subject'))
                            ->disabled(),

                        Forms\Components\Select::make('email_type')
                            ->label(__('email_log.email_type'))
                            ->options([
                                'ticket_created' => __('email_log.type_ticket_created'),
                                'ticket_updated' => __('email_log.type_ticket_updated'),
                                'loan_approved' => __('email_log.type_loan_approved'),
                                'loan_rejected' => __('email_log.type_loan_rejected'),
                                'asset_overdue' => __('email_log.type_asset_overdue'),
                                'maintenance_reminder' => __('email_log.type_maintenance_reminder'),
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label(__('email_log.status'))
                            ->options([
                                'pending' => __('email_log.status_pending'),
                                'delivered' => __('email_log.status_delivered'),
                                'failed' => __('email_log.status_failed'),
                                'bounced' => __('email_log.status_bounced'),
                            ])
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('email_log.delivery_information'))
                    ->schema([
                        Forms\Components\TextInput::make('retry_attempts')
                            ->label(__('email_log.retry_attempts'))
                            ->numeric()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label(__('email_log.delivered_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_retry_at')
                            ->label(__('email_log.last_retry_at'))
                            ->disabled(),

                        Forms\Components\Textarea::make('error_message')
                            ->label(__('email_log.error_message'))
                            ->rows(3)
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('email_log.metadata'))
                    ->schema([
                        Forms\Components\KeyValue::make('data')
                            ->label(__('email_log.email_data'))
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('recipient_email')
                    ->label(__('email_log.recipient'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('email_type')
                    ->label(__('email_log.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ticket_created' => 'primary',
                        'loan_approved' => 'success',
                        'loan_rejected' => 'danger',
                        'asset_overdue' => 'warning',
                        'maintenance_reminder' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        'bounced' => 'secondary',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'delivered' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        'bounced' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('retry_attempts')
                    ->label(__('email_log.retries'))
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery_time')
                    ->label(__('email_log.delivery_time'))
                    ->getStateUsing(function (EmailLog $record): ?string {
                        if ($record->delivered_at && $record->created_at) {
                            $seconds = $record->created_at->diffInSeconds($record->delivered_at);

                            return $seconds.'s';
                        }

                        return null;
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('email_log.sent_at'))
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'bounced' => 'Bounced',
                    ]),

                Tables\Filters\SelectFilter::make('email_type')
                    ->label('Email Type')
                    ->options([
                        'ticket_created' => 'Ticket Created',
                        'ticket_updated' => 'Ticket Updated',
                        'loan_approved' => 'Loan Approved',
                        'loan_rejected' => 'Loan Rejected',
                        'asset_overdue' => 'Asset Overdue',
                        'maintenance_reminder' => 'Maintenance Reminder',
                    ]),

                Tables\Filters\Filter::make('failed_retryable')
                    ->label('Failed (Retryable)')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'failed')
                        ->where('retry_attempts', '<', 3)
                    ),

                Tables\Filters\Filter::make('sla_breach')
                    ->label('SLA Breach (>60s)')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'delivered')
                        ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, delivered_at) > 60')
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),

                Action::make('retry')
                    ->label(__('email_log.retry'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('email_log.retry_email_delivery'))
                    ->modalDescription(__('email_log.retry_email_confirm'))
                    ->action(function (EmailLog $record): void {
                        $service = app(EmailNotificationService::class);

                        if ($service->retryEmailDelivery($record)) {
                            Notification::make()
                                ->title(__('email_log.email_queued'))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('email_log.email_retry_failed'))
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (EmailLog $record): bool => $record->status === 'failed' && $record->retry_attempts < 3
                    ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('retry_selected')
                        ->label(__('email_log.retry_selected'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(__('email_log.retry_selected_heading'))
                        ->modalDescription(__('email_log.retry_selected_description'))
                        ->action(function (Collection $records): void {
                            $service = app(EmailNotificationService::class);
                            $emailIds = $records->pluck('id')->toArray();

                            $results = $service->bulkRetryEmails($emailIds);

                            Notification::make()
                                ->title(__('email_log.retry_summary', ['success' => $results['success'], 'failed' => $results['failed']]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Email logs are created automatically
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Email logs are read-only
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Email logs should not be deleted
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'superuser']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->latest();
    }
}
