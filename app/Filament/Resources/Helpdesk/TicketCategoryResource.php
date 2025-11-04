<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk;

use App\Filament\Resources\Helpdesk\Pages\CreateTicketCategory;
use App\Filament\Resources\Helpdesk\Pages\EditTicketCategory;
use App\Filament\Resources\Helpdesk\Pages\ListTicketCategories;
use App\Filament\Resources\Helpdesk\Pages\ViewTicketCategory;
use App\Filament\Resources\Helpdesk\Schemas\TicketCategoryForm;
use App\Filament\Resources\Helpdesk\Tables\TicketCategoriesTable;
use App\Models\TicketCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Ticket Category Resource
 *
 * Provides full CRUD capabilities for helpdesk ticket categories including bilingual
 * labels and SLA configuration. Accessible to admin and superuser roles only.
 *
 * @trace D03-FR-002.2 (Helpdesk configuration)
 * @trace D04 A¶3.3 (Filament administration)
 * @trace Requirements 2.2, 2.5, 3.3, 13.1-13.5
 */
class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Helpdesk Management';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return TicketCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketCategories::route('/'),
            'create' => CreateTicketCategory::route('/create'),
            'view' => ViewTicketCategory::route('/{record}'),
            'edit' => EditTicketCategory::route('/{record}/edit'),
        ];
    }
}
