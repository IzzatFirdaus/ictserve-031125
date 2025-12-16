<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a loan application is approved.
 *
 * @trace D03-SRS-LOAN-001, Requirements 16.4
 *
 * @version 3.6.0
 */
class LoanApplicationApproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public LoanApplication $application
    ) {}
}
