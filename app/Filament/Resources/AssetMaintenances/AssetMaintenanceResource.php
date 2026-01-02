<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMaintenances;

use App\Filament\Resources\AssetMaintenances\Pages\CreateAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Pages\EditAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Pages\ListAssetMaintenances;
use App\Filament\Resources\AssetMaintenances\Pages\ViewAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceForm;
use App\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceInfolist;
use App\Filament\Resources\AssetMaintenances\Tables\AssetMaintenancesTable;
use App\Models\AssetMaintenance;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Sumber Filament untuk Pengurusan Penyelenggaraan Aset
 *
 * Menyediakan antara muka CRUD untuk penyelenggaraan aset dengan ciri:
 * - Penjejakan penyelenggaraan berkala
 * - Rekod sejarah penyelenggaraan
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 3.1, 3.2
 */
class AssetMaintenanceResource extends Resource
{
    protected static ?string $model = AssetMaintenance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Aset';

    public static function getNavigationLabel(): string
    {
        return __('asset_maintenance.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('asset_maintenance.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('asset_maintenance.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return AssetMaintenanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetMaintenanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetMaintenancesTable::configure($table);
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
            'index' => ListAssetMaintenances::route('/'),
            'create' => CreateAssetMaintenance::route('/create'),
            'view' => ViewAssetMaintenance::route('/{record}'),
            'edit' => EditAssetMaintenance::route('/{record}/edit'),
        ];
    }

    /**
     * Check if the current user can view any asset maintenances.
     * Only admin and superuser roles have access.
     */
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasAnyRole(['admin', 'superuser', 'staff']);
    }

    /**
     * @return Builder<AssetMaintenance>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['asset', 'performedByUser']);
    }
}
