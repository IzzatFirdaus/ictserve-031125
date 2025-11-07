<?php

declare(strict_types=1);

namespace App\Filament\Resources\System;

use App\Filament\Resources\System\AuditResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Models\Audit;

/**
 * Audit Trail Management Resource
 *
 * Provides comprehensive audit trail management for ICTServe admin panel.
 * Displays all system changes with 7-year retention for PDPA 2010 compliance.
 *
 * Requirements: 9.1, 9.2
 *
 * @see D03-FR-007.1 Audit trail logging
 * @see D09 §9 Audit requirements
 */
class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'audit-trail';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationLabel(): string
    {
        return 'Audit Trail';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System Configuration';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Audit Information')
                    ->schema([
                        Forms\Components\TextInput::make('auditable_type')
                            ->label('Entity Type')
                            ->disabled(),

                        Forms\Components\TextInput::make('auditable_id')
                            ->label('Entity ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('event')
                            ->label('Action')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Timestamp')
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->rows(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Change Details')
                    ->schema([
                        Forms\Components\KeyValue::make('old_values')
                            ->label('Previous Values')
                            ->disabled(),

                        Forms\Components\KeyValue::make('new_values')
                            ->label('New Values')
                            ->disabled(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('System'),

                Tables\Columns\BadgeColumn::make('event')
                    ->label('Action')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'info' => 'retrieved',
                    ])
                    ->searchable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Entity Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Entity ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Action Type')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'retrieved' => 'Retrieved',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Entity Type')
                    ->options([
                        'App\\Models\\User' => 'User',
                        'App\\Models\\HelpdeskTicket' => 'Helpdesk Ticket',
                        'App\\Models\\LoanApplication' => 'Loan Application',
                        'App\\Models\\Asset' => 'Asset',
                        'App\\Models\\Division' => 'Division',
                        'App\\Models\\Grade' => 'Grade',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->label('Date Range')
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'From: '.$data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Until: '.$data['created_until'];
                        }

                        return $indicators;
                    }),

                Tables\Filters\Filter::make('ip_address')
                    ->label('IP Address')
                    ->form([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->placeholder('192.168.1.1'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['ip_address'],
                            fn (Builder $query, $ip): Builder => $query->where('ip_address', 'like', "%{$ip}%"),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->deferFilters()
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->latest();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $todayCount = static::getModel()::whereDate('created_at', today())->count();

        return match (true) {
            $todayCount > 1000 => 'danger',
            $todayCount > 500 => 'warning',
            default => 'success',
        };
    }
}
