<?php

declare(strict_types=1);

namespace App\Mail;

class AdminAssetPreparationNotification extends BaseMailable
{
    public function __construct(public \App\Models\LoanApplication $loanApplication) {}

    public function build(): \Illuminate\Mail\Mailable
    {
        return $this->subject('Asset Preparation Required')
            ->view('emails.admin.asset-preparation');
    }
}
