<?php

namespace App\Filament\Resources\AssetTransfers;

use App\Filament\Resources\AssetTransfers\Pages\CreateAssetTransfer;
use App\Filament\Resources\AssetTransfers\Pages\EditAssetTransfer;
use App\Filament\Resources\AssetTransfers\Pages\ListAssetTransfers;
use App\Filament\Resources\AssetTransfers\Pages\ViewAssetTransfer;
use App\Filament\Resources\AssetTransfers\Schemas\AssetTransferForm;
use App\Filament\Resources\AssetTransfers\Schemas\AssetTransferInfolist;
use App\Filament\Resources\AssetTransfers\Tables\AssetTransfersTable;
use App\Models\AssetTransfer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssetTransferResource extends Resource
{
    protected static ?string $model = AssetTransfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Aset';

    public static function getNavigationLabel(): string
    {
        return 'Pemindahan Aset';
    }

    public static function getModelLabel(): string
    {
        return 'Pemindahan Aset';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pemindahan Aset';
    }

    public static function form(Schema $schema): Schema
    {
        return AssetTransferForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetTransferInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetTransfersTable::configure($table);
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
            'index' => ListAssetTransfers::route('/'),
            'create' => CreateAssetTransfer::route('/create'),
            'view' => ViewAssetTransfer::route('/{record}'),
            'edit' => EditAssetTransfer::route('/{record}/edit'),
        ];
    }
}
