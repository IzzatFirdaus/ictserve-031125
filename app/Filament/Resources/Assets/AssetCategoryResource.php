<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAssetCategory;
use App\Filament\Resources\Assets\Pages\EditAssetCategory;
use App\Filament\Resources\Assets\Pages\ListAssetCategories;
use App\Filament\Resources\Assets\Pages\ViewAssetCategory;
use App\Filament\Resources\Assets\Schemas\AssetCategoryForm;
use App\Filament\Resources\Assets\Tables\AssetCategoriesTable;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Asset Category Resource
 *
 * Manages ICT asset categories, including default loan durations and approval requirements.
 *
 * @trace Requirements 2.3, 3.1, 3.3, 7.1
 */
class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'view' => ViewAssetCategory::route('/{record}'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
        ];
    }
}
