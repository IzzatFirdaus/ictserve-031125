<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reference;

use App\Filament\Clusters\Management;
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

/**
 * Grade Resource (Pengurusan Gred Jawatan)
 *
 * Provides CRUD interface for managing employee grades in the organization.
 * Grades determine access levels and approval authority in workflows.
 *
 * Features:
 * - Grade code and name management
 * - Hierarchical grade structure
 * - Integration with user roles and permissions
 *
 * @trace D03-FR-010 (Reference data management)
 * @trace D04-§5.2 (Reference data architecture)
 * @trace D09-§4.5 (grades table schema)
 * @trace D12-§7 (Admin UI patterns)
 *
 * @see \App\Models\Grade
 * @see \App\Filament\Clusters\Management
 */
class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 3;

    /**
     * Check if the current user can view any grades.
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
