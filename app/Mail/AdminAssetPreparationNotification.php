<?php

declare(strict_types=1);

namespace App\Mail;

class AdminAssetPreparationNotification extends BaseMailable
{
    public function __construct(public LoanApplication $loanApplication) {}

    public function build()
    {
        return $this->subject('Asset Preparation Required')
            ->view('emails.admin.asset-preparation');
    }
}
