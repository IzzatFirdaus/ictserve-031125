<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Clusters\Operations;
use App\Filament\Resources\Loans\Pages\CreateLoanApplication;
use App\Filament\Resources\Loans\Pages\EditLoanApplication;
use App\Filament\Resources\Loans\Pages\ListLoanApplications;
use App\Filament\Resources\Loans\Pages\ViewLoanApplication;
use App\Filament\Resources\Loans\Schemas\LoanApplicationForm;
use App\Filament\Resources\Loans\Schemas\LoanApplicationInfolist;
use App\Filament\Resources\Loans\Tables\LoanApplicationsTable;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\DualApprovalService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Loan Application Resource (Alias)
 *
 * @deprecated This is an alias resource for backward compatibility.
 * @see \App\Filament\Resources\LoanApplications\LoanApplicationResource (canonical)
 *
 * Navigation is disabled to prevent duplicate entries in the admin panel.
 * This resource should only be used for direct URL access or API compatibility.
 */
class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $cluster = Operations::class;

    protected static ?int $navigationSort = 2;

    /**
     * Filament will automatically use LoanApplicationPolicy for authorization.
     * Policy methods: viewAny(), view(), create(), update(), delete(), approve()
     *
     * @see \App\Policies\LoanApplicationPolicy
     */

    /**
     * Control navigation visibility based on user permissions.
     *
     * @deprecated This is an alias resource. Navigation disabled to prevent duplicates.
     * @see \App\Filament\Resources\LoanApplications\LoanApplicationResource (canonical)
     */
    public static function shouldRegisterNavigation(): bool
    {
        // Disable navigation for alias resource to prevent duplicate entries
        // The canonical resource is LoanApplications/LoanApplicationResource
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Permohonan Pinjaman';
    }

    public static function getModelLabel(): string
    {
        return 'Permohonan Pinjaman';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Permohonan Pinjaman';
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

    /**
     * @return Builder<LoanApplication>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['division', 'loanItems.asset', 'transactions'])
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function sendForApproval(LoanApplication $application): void
    {
        app(DualApprovalService::class)->sendApprovalRequest($application);
    }
}
