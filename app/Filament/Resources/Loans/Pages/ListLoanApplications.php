<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Resources\Loans\Widgets\LoanAnalyticsWidget;
use Filament\Actions\CreateAction;
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
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoanAnalyticsWidget::class,
        ];
    }
}
