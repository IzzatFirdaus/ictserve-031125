<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications;

use App\Filament\Clusters\Operations;
use App\Filament\Resources\LoanApplications\Pages\AssignAssets;
use App\Filament\Resources\LoanApplications\Pages\CreateLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\EditLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\ListLoanApplications;
use App\Filament\Resources\LoanApplications\Pages\RecordReturn;
use App\Filament\Resources\LoanApplications\Pages\ViewLoanApplication;
use App\Filament\Resources\LoanApplications\Schemas\LoanApplicationForm;
use App\Filament\Resources\LoanApplications\Schemas\LoanApplicationInfolist;
use App\Filament\Resources\LoanApplications\Tables\LoanApplicationsTable;
use App\Models\LoanApplication;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

/**
 * Loan Application Resource v3.6.0
 *
 * Provides comprehensive loan application management with calendar widget,
 * issuance/return actions, condition tracking, and approval workflow integration.
 *
 * @see D03 Requirements 8.3, 8.5, 11.1, 11.2
 * @see D04 Software Design - Filament Resources
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
 */
class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
     * Only show in navigation if user has permission to view loan applications.
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('viewAny', LoanApplication::class) ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.loan_application.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.loan_application.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.loan_application.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return LoanApplicationForm::configure($schema);
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
        return [
            // RelationManagers\LoanItemsRelationManager::class,
            // RelationManagers\ApprovalHistoryRelationManager::class,
            // RelationManagers\CrossModuleIntegrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanApplications::route('/'),
            'create' => CreateLoanApplication::route('/create'),
            'view' => ViewLoanApplication::route('/{record}'),
            'edit' => EditLoanApplication::route('/{record}/edit'),
            'assign-assets' => AssignAssets::route('/{record}/assign-assets'),
            'record-return' => RecordReturn::route('/{record}/record-return'),
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Pemohon' => $record->applicant_name,
            'Status' => $record->status->getLabel(),
            'Tarikh Mula' => $record->loan_start_date?->format('d M Y'),
            'Tarikh Tamat' => $record->loan_end_date?->format('d M Y'),
        ];
    }

    /**
     * @return Builder<LoanApplication>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['division', 'approver', 'loanItems.asset']);
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
