<?php

namespace App\Filament\Resources\LoanApplications;

use App\Filament\Clusters\Operations;
use App\Filament\Resources\LoanApplications\Pages\AssignAssets;
use App\Filament\Resources\LoanApplications\Pages\CreateLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\EditLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\ListLoanApplications;
use App\Filament\Resources\LoanApplications\Pages\RecordReturn;
use App\Filament\Resources\LoanApplications\Schemas\LoanApplicationForm;
use App\Filament\Resources\LoanApplications\Tables\LoanApplicationsTable;
use App\Models\LoanApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = Operations::class;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LoanApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoanApplicationsTable::configure($table);
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
            'index' => ListLoanApplications::route('/'),
            'create' => CreateLoanApplication::route('/create'),
            'edit' => EditLoanApplication::route('/{record}/edit'),
            'assign-assets' => AssignAssets::route('/{record}/assign-assets'),
            'record-return' => RecordReturn::route('/{record}/record-return'),
        ];
    }

    /**
     * @return Builder<LoanApplication>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
