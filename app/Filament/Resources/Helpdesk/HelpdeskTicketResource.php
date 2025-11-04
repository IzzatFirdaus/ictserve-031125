<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;
use App\Filament\Resources\Helpdesk\Pages\CreateHelpdeskTicket;
use App\Filament\Resources\Helpdesk\Pages\EditHelpdeskTicket;
use App\Filament\Resources\Helpdesk\Pages\ListHelpdeskTickets;
use App\Filament\Resources\Helpdesk\Pages\ViewHelpdeskTicket;
use App\Filament\Resources\Helpdesk\Schemas\HelpdeskTicketForm;
use App\Filament\Resources\Helpdesk\Schemas\HelpdeskTicketInfolist;
use App\Filament\Resources\Helpdesk\Tables\HelpdeskTicketsTable;
use App\Models\HelpdeskTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

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

    protected static string|UnitEnum|null $navigationGroup = 'Helpdesk Management';

    protected static ?int $navigationSort = 0;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
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
            RelationManagers\CrossModuleIntegrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHelpdeskTickets::route('/'),
            'create' => CreateHelpdeskTicket::route('/create'),
            'view' => ViewHelpdeskTicket::route('/{record}'),
            'edit' => EditHelpdeskTicket::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Subject' => $record->subject,
            'Status' => $record->status,
            'Priority' => $record->priority,
            'Category' => $record->category?->name_en,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['category', 'division', 'assignedDivision', 'assignedUser']);
    }
}
