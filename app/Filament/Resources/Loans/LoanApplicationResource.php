<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\Pages\CreateLoanApplication;
use App\Filament\Resources\Loans\Pages\EditLoanApplication;
use App\Filament\Resources\Loans\Pages\ListLoanApplications;
use App\Filament\Resources\Loans\Pages\ViewLoanApplication;
use App\Filament\Resources\Loans\Schemas\LoanApplicationForm;
use App\Filament\Resources\Loans\Schemas\LoanApplicationInfolist;
use App\Filament\Resources\Loans\Tables\LoanApplicationsTable;
use App\Models\LoanApplication;
use App\Services\DualApprovalService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Loan Management';

    protected static ?int $navigationSort = 0;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()?->hasAdminAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return LoanApplicationForm::configure($schema, LoanStatus::cases(), LoanPriority::cases());
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoanApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanApplications::route('/'),
            'create' => CreateLoanApplication::route('/create'),
            'view' => ViewLoanApplication::route('/{record}'),
            'edit' => EditLoanApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['division', 'loanItems', 'transactions'])
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function sendForApproval(LoanApplication $application): void
    {
        app(DualApprovalService::class)->sendApprovalRequest($application);
    }
}
