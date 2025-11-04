<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanApplication extends ViewRecord
{
    protected static string $resource = LoanApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
