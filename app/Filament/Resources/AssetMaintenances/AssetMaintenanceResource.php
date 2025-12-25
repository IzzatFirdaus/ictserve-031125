<?php

namespace App\Filament\Resources\AssetMaintenances;

use App\Filament\Resources\AssetMaintenances\Pages\CreateAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Pages\EditAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Pages\ListAssetMaintenances;
use App\Filament\Resources\AssetMaintenances\Pages\ViewAssetMaintenance;
use App\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceForm;
use App\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceInfolist;
use App\Filament\Resources\AssetMaintenances\Tables\AssetMaintenancesTable;
use App\Models\AssetMaintenance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssetMaintenanceResource extends Resource
{
    protected static ?string $model = AssetMaintenance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Aset';

    public static function getNavigationLabel(): string
    {
        return 'Penyelenggaraan Aset';
    }

    public static function getModelLabel(): string
    {
        return 'Penyelenggaraan Aset';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penyelenggaraan Aset';
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
}
