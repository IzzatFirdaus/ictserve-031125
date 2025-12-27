<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference;

use App\Filament\Clusters\Management;
use App\Filament\Resources\Reference\Pages\CreateDivision;
use App\Filament\Resources\Reference\Pages\EditDivision;
use App\Filament\Resources\Reference\Pages\ListDivisions;
use App\Filament\Resources\Reference\Pages\ViewDivision;
use App\Filament\Resources\Reference\Schemas\DivisionForm;
use App\Filament\Resources\Reference\Tables\DivisionsTable;
use App\Models\Division;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Division Resource (Pengurusan Bahagian Organisasi)
 *
 * Provides CRUD interface for managing organizational divisions within MOTAC.
 * Supports hierarchical division structure with parent-child relationships.
 *
 * Features:
 * - Division hierarchy management
 * - Parent division assignment
 * - User assignment to divisions
 * - Integration with helpdesk routing
 *
 * @trace D03-FR-010 (Reference data management)
 * @trace D04-§5.2 (Organizational structure)
 * @trace D09-§4.5 (divisions table schema)
 * @trace D12-§7 (Admin UI patterns)
 *
 * @see \App\Models\Division
 * @see \App\Filament\Clusters\Management
 */
class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 2;

    /**
     * Check if the current user can view any divisions.
     * Only admin and superuser roles have access.
     */
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasAdminAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return DivisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DivisionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDivisions::route('/'),
            'create' => CreateDivision::route('/create'),
            'view' => ViewDivision::route('/{record}'),
            'edit' => EditDivision::route('/{record}/edit'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Division>
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent']);
    }
}
