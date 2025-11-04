<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\LoanApplication;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanApplication extends CreateRecord
{
    protected static string $resource = LoanApplicationResource::class;

    protected function handleRecordCreation(array $data): LoanApplication
    {
        $data['application_number'] = LoanApplication::generateApplicationNumber();

        /** @var LoanApplication $application */
        $application = static::getModel()::create($data);

        return $application;
    }
}
