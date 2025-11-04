<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference;

use App\Filament\Resources\Reference\Pages\CreateGrade;
use App\Filament\Resources\Reference\Pages\EditGrade;
use App\Filament\Resources\Reference\Pages\ListGrades;
use App\Filament\Resources\Reference\Pages\ViewGrade;
use App\Filament\Resources\Reference\Schemas\GradeForm;
use App\Filament\Resources\Reference\Tables\GradesTable;
use App\Models\Grade;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Reference Data';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'view' => ViewGrade::route('/{record}'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}
