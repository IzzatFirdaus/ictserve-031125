<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers;

use App\Filament\Resources\AssetTransfers\Pages\CreateAssetTransfer;
use App\Filament\Resources\AssetTransfers\Pages\EditAssetTransfer;
use App\Filament\Resources\AssetTransfers\Pages\ListAssetTransfers;
use App\Filament\Resources\AssetTransfers\Pages\ViewAssetTransfer;
use App\Filament\Resources\AssetTransfers\Schemas\AssetTransferForm;
use App\Filament\Resources\AssetTransfers\Schemas\AssetTransferInfolist;
use App\Filament\Resources\AssetTransfers\Tables\AssetTransfersTable;
use App\Models\AssetTransfer;
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
 * Sumber Filament untuk Pengurusan Pemindahan Aset
 *
 * Menyediakan antara muka CRUD untuk pemindahan aset dengan ciri:
 * - Penjejakan pemindahan antara bahagian
 * - Rekod sejarah pemindahan
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 3.3, 3.4
 */
class AssetTransferResource extends Resource
{
    protected static ?string $model = AssetTransfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

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

    /**
     * Check if the current user can view any asset transfers.
     * Only admin and superuser roles have access.
     */
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasAnyRole(['admin', 'superuser', 'staff']);
    }

    /**
     * @return Builder<AssetTransfer>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['asset', 'fromDivision', 'toDivision', 'transferredBy', 'approvedBy']);
    }
}
