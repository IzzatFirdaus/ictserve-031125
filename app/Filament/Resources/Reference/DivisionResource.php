<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference;

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
use UnitEnum;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Reference Data';

    protected static ?int $navigationSort = 0;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
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
}
