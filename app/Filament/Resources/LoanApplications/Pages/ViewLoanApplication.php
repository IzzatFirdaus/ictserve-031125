<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Pages;

use App\Filament\Resources\LoanApplications\LoanApplicationResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * View Loan Application Page v3.6.0
 *
 * Displays comprehensive loan application details with approval workflow,
 * asset assignments, and cross-module integration information.
 *
 * @see D03 Requirements 8.3, 8.5
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
 */
class ViewLoanApplication extends ViewRecord
{
    protected static string $resource = LoanApplicationResource::class;
}
