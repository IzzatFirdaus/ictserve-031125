<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

class SensitiveAccessLogWidget extends TableWidget
{
    use WidgetMetadata;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public static function getHeading(): ?string
    {
        return __('admin_pages.pdpa_dashboard.sensitive_access_log');
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasRole('superuser');
    }

    public function table(Table $table): Table
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isSuperuser = $user !== null && $user->hasRole('superuser');

        if (! $isSuperuser) {
            return $table
                ->query(Audit::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('admin_pages.pdpa_dashboard.restricted_to_superuser'))
                ->emptyStateDescription(__('admin_pages.pdpa_dashboard.restricted_description'))
                ->emptyStateIcon('heroicon-o-lock-closed');
        }

        return $table
            ->query(
                Audit::whereIn('auditable_type', [
                    'App\\Models\\User',
                    'App\\Models\\LoanApplication',
                ])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.widget.time'))
                    ->dateTime('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.widget.user'))
                    ->default('Sistem'),
                Tables\Columns\TextColumn::make('event')
                    ->label(__('filament.widget.action'))
                    ->badge(),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label(__('filament.widget.data_type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('filament.widget.ip_address')),
            ]);
    }

    /**
     * Widget roles - restricted access
     */
    public static function getWidgetRoles(): array
    {
        return ['superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System';
    }
}
