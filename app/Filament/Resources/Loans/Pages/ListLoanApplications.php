<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * List Loan Applications Page
 *
 * Enhanced with analytics widget for comprehensive reporting.
 *
 * @trace Requirements 3.1, 3.3, 3.4, 8.1, 8.2
 */
class ListLoanApplications extends ListRecords
{
    protected static string $resource = LoanApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoanApplicationResource\Widgets\LoanAnalyticsWidget::class,
        ];
    }
}
