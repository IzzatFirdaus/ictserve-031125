<?php

declare(strict_types=1);

namespace App\Filament\Resources\System;

use App\Filament\Clusters\System as SystemCluster;
use App\Filament\Resources\System\AuditResource\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
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

    protected static ?string $cluster = SystemCluster::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'audit-trail';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.audit_trail');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Maklumat Audit')
                    ->components([
                        Forms\Components\TextInput::make('auditable_type')
                            ->label('Jenis Entiti')
                            ->disabled(),

                        Forms\Components\TextInput::make('auditable_id')
                            ->label('ID Entiti')
                            ->disabled(),

                        Forms\Components\TextInput::make('event')
                            ->label('Tindakan')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_id')
                            ->label('ID Pengguna')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Masa')
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('Ejen Pengguna')
                            ->disabled()
                            ->rows(2),
                    ])
                    ->columns(2),

                Section::make('Butiran Perubahan')
                    ->components([
                        Forms\Components\KeyValue::make('old_values')
                            ->label('Nilai Sebelum')
                            ->disabled(),

                        Forms\Components\KeyValue::make('new_values')
                            ->label('Nilai Baharu')
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
                    ->label('Masa')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->default('Sistem'),

                Tables\Columns\TextColumn::make('event')
                    ->label('Tindakan')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            'retrieved' => 'info',
                            default => 'gray',
                        }
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Jenis Entiti')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID Entiti')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Ejen Pengguna')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (! is_string($state)) {
                            return null;
                        }

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Jenis Tindakan')
                    ->options([
                        'created' => 'Dicipta',
                        'updated' => 'Dikemas kini',
                        'deleted' => 'Dipadam',
                        'retrieved' => 'Diakses',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Jenis Entiti')
                    ->options([
                        'App\\Models\\User' => 'Pengguna',
                        'App\\Models\\HelpdeskTicket' => 'Tiket Helpdesk',
                        'App\\Models\\LoanApplication' => 'Permohonan Pinjaman',
                        'App\\Models\\Asset' => 'Aset',
                        'App\\Models\\Division' => 'Bahagian',
                        'App\\Models\\Grade' => 'Gred',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->options(fn () => User::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->label('Julat Tarikh')
                    ->schema([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Tarikh Dari'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Tarikh Hingga'),
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
                            $indicators['created_from'] = 'Dari: '.$data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Hingga: '.$data['created_until'];
                        }

                        return $indicators;
                    }),

                Tables\Filters\Filter::make('ip_address')
                    ->label('Alamat IP')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->placeholder('192.168.1.1'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['ip_address'],
                            fn (Builder $query, $ip): Builder => $query->where('ip_address', 'like', "%{$ip}%"),
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat Butiran'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\ExportBulkAction::make()
                        ->label('Eksport Dipilih')
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

    /**
     * @return Builder<Audit>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->latest();
    }

    public static function getNavigationBadge(): ?string
    {
        return sprintf('%d', Audit::whereDate('created_at', today())->count());
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
