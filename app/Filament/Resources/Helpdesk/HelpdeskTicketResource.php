<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk;

use App\Filament\Clusters\Operations;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\Pages;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;
use App\Filament\Resources\Helpdesk\Schemas\HelpdeskTicketForm;
use App\Filament\Resources\Helpdesk\Schemas\HelpdeskTicketInfolist;
use App\Filament\Resources\Helpdesk\Tables\HelpdeskTicketsTable;
use App\Models\HelpdeskTicket;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

/**
 * Helpdesk Ticket Resource
 *
 * Provides full lifecycle management for helpdesk tickets including assignment,
 * SLA tracking, and bulk workflows. Restricted to admin & superuser roles.
 *
 * @trace Requirements 2.2, 2.5, 3.3, 4.2, 4.3, 13.1-13.5, 22.3
 */
class HelpdeskTicketResource extends Resource
{
    protected static ?string $model = HelpdeskTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $cluster = Operations::class;

    protected static ?int $navigationSort = 1;

    /**
     * Filament will automatically use HelpdeskTicketPolicy for authorization.
     * Policy methods: viewAny(), view(), create(), update(), delete()
     *
     * @see \App\Policies\HelpdeskTicketPolicy
     */

    /**
     * Control navigation visibility based on user permissions.
     * Only show in navigation if user has permission to view helpdesk tickets.
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('viewAny', HelpdeskTicket::class) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return HelpdeskTicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HelpdeskTicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HelpdeskTicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\AssignmentHistoryRelationManager::class,
            RelationManagers\StatusTimelineRelationManager::class,
            RelationManagers\CrossModuleIntegrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHelpdeskTickets::route('/'),
            'create' => Pages\CreateHelpdeskTicket::route('/create'),
            'edit' => Pages\EditHelpdeskTicket::route('/{record}/edit'),
            'view' => Pages\ViewHelpdeskTicket::route('/{record}'),
        ];
    }

    /**
     * @param  HelpdeskTicket  $record
     * @return array<string, string|null>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var HelpdeskTicket $ticket */
        $ticket = $record;

        return [
            __('filament.search.subject') => $ticket->subject,
            __('filament.search.status') => $ticket->status,
            __('filament.search.priority') => $ticket->priority,
            __('filament.search.category') => $ticket->category?->name_ms,
        ];
    }

    /**
     * @return Builder<HelpdeskTicket>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['category', 'division', 'assignedDivision', 'assignedUser']);
    }
}
