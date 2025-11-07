<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Pages\ViewAsset;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Filament\Resources\Assets\Schemas\AssetInfolist;
use App\Filament\Resources\Assets\Tables\AssetsTable;
use App\Models\Asset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Asset Resource
 *
 * Comprehensive asset inventory management with maintenance indicators and cross-module context.
 *
 * @trace Requirements 2.3, 3.1, 3.3, 5.1
 */
class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 0;

    /**
     * Filament will automatically use AssetPolicy for authorization.
     * Policy methods: viewAny(), view(), create(), update(), delete(), restore(), forceDelete()
     *
     * @see \App\Policies\AssetPolicy
     */

    /**
     * Control navigation visibility based on user permissions.
     * Only show in navigation if user has permission to view assets.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()?->can('viewAny', Asset::class);
    }

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema, AssetStatus::cases(), AssetCondition::cases());
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Assets\RelationManagers\LoanHistoryRelationManager::class,
            \App\Filament\Resources\Assets\RelationManagers\HelpdeskTicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['category'])
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
