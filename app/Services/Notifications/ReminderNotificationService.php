<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Mail\AssetDueTodayReminder;
use App\Mail\AssetOverdueNotification;
use App\Mail\AssetReturnReminder;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;

class ReminderNotificationService
{
    public function __construct(
        private EmailDispatcher $dispatcher
    ) {}

    public function sendReturnReminder(LoanApplication $application): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            new AssetReturnReminder($application),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'loan_end_date' => $application->loan_end_date,
            ]
        );

        Log::info('Return reminder queued', [
            'application_number' => $application->application_number,
        ]);
    }

    public function sendDueTodayReminder(LoanApplication $application): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            new AssetDueTodayReminder($application),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'loan_end_date' => $application->loan_end_date,
            ]
        );

        Log::info('Due-today reminder queued', [
            'application_number' => $application->application_number,
        ]);
    }

    public function sendOverdueNotification(LoanApplication $application): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            new AssetOverdueNotification($application),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'loan_end_date' => $application->loan_end_date,
                'days_overdue' => now()->diffInDays($application->loan_end_date),
            ]
        );

        Log::info('Overdue notification queued', [
            'application_number' => $application->application_number,
        ]);
    }

    public function sendOverdueReminder(LoanApplication $application): void
    {
        $this->sendDueTodayReminder($application);
    }
}
