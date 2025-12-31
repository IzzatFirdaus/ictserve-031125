<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoogleServicesAuditResource\Pages;

use App\Filament\Resources\GoogleServicesAuditResource;
use App\Models\GoogleServicesAuditLog;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

/**
 * List Google Services Audit Logs Page
 *
 * Provides tabbed interface for filtering audit logs by service type and status.
 *
 * @trace Requirements 8.2, 16.3 (Admin Interface, Compliance Reporting)
 */
class ListGoogleServicesAuditLogs extends ListRecords
{
    protected static string $resource = GoogleServicesAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.all'))
                ->icon(Heroicon::OutlinedListBullet),

            'sso' => Tab::make(__('admin.sso'))
                ->icon(Heroicon::OutlinedKey)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('service_type', GoogleServicesAuditLog::SERVICE_SSO))
                ->badge(GoogleServicesAuditLog::query()->where('service_type', GoogleServicesAuditLog::SERVICE_SSO)->count())
                ->badgeColor('primary'),

            'gmail' => Tab::make(__('admin.gmail'))
                ->icon(Heroicon::OutlinedEnvelope)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('service_type', GoogleServicesAuditLog::SERVICE_GMAIL))
                ->badge(GoogleServicesAuditLog::query()->where('service_type', GoogleServicesAuditLog::SERVICE_GMAIL)->count())
                ->badgeColor('info'),

            'successful' => Tab::make(__('admin.successful'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('success', true))
                ->badge(GoogleServicesAuditLog::query()->where('success', true)->count())
                ->badgeColor('success'),

            'failed' => Tab::make(__('admin.failed'))
                ->icon(Heroicon::OutlinedXCircle)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('success', false))
                ->badge(GoogleServicesAuditLog::query()->where('success', false)->count())
                ->badgeColor('danger'),

            'today' => Tab::make(__('admin.today'))
                ->icon(Heroicon::OutlinedCalendar)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('attempted_at', today()))
                ->badge(GoogleServicesAuditLog::query()->whereDate('attempted_at', today())->count()),
        ];
    }
}
