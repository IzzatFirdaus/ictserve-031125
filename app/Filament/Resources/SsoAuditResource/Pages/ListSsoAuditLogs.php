<?php

declare(strict_types=1);

namespace App\Filament\Resources\SsoAuditResource\Pages;

use App\Filament\Resources\SsoAuditResource;
use App\Models\SsoAuditLog;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

/**
 * List SSO Audit Logs Page
 *
 * @trace Requirements 3.2 (Admin SSO Management)
 */
class ListSsoAuditLogs extends ListRecords
{
    protected static string $resource = SsoAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.all'))
                ->icon(Heroicon::OutlinedListBullet),

            'successful' => Tab::make(__('admin.successful'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('success', true))
                ->badge(SsoAuditLog::query()->where('success', true)->count())
                ->badgeColor('success'),

            'failed' => Tab::make(__('admin.failed'))
                ->icon(Heroicon::OutlinedXCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('success', false))
                ->badge(SsoAuditLog::query()->where('success', false)->count())
                ->badgeColor('danger'),

            'today' => Tab::make(__('admin.today'))
                ->icon(Heroicon::OutlinedCalendar)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('attempted_at', today()))
                ->badge(SsoAuditLog::query()->whereDate('attempted_at', today())->count()),
        ];
    }
}
