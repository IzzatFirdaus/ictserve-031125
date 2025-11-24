<?php

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class GuestApplicationTrackingMail extends Mailable
{
    use Queueable, SerializesModels;

    public LoanApplication $application;
    public string $trackingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(LoanApplication $application, string $trackingUrl)
    {
        $this->application = $application;
        $this->trackingUrl = $trackingUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('Your Loan Application Tracking Link'))
            ->view('mail.guest-application-tracking')
            ->with([
                'application' => $this->application,
                'trackingUrl' => $this->trackingUrl,
            ]);
    }
}
?>
