<?php

declare(strict_types=1);

namespace App\Mail\Loans;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Lightweight approval request mail used by synchronous notification flows.
 *
 * Accepts either a User instance or a raw approval token so the tests can
 * construct it without running the full approval pipeline.
 */
class LoanApprovalRequest extends Mailable
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public ?User $approver = null;

    public string $approvalToken;

    public function __construct(
        public LoanApplication $application,
        User|string|null $approverOrToken = null
    ) {
        if ($approverOrToken instanceof User) {
            $this->approver = $approverOrToken;
            $this->approvalToken = $application->approval_token ?? (string) Str::uuid();
        } else {
            $this->approvalToken = is_string($approverOrToken)
                ? $approverOrToken
                : ($application->approval_token ?? (string) Str::uuid());
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.approval_request_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.approval-request',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user?->name
                    ?? $this->application->applicant_name,
                'approverName' => $this->approver?->name,
                'approveUrl' => $this->buildApprovalUrl('loan.approval.approve'),
                'declineUrl' => $this->buildApprovalUrl('loan.approval.decline'),
                'portalUrl' => $this->buildPortalUrl(),
                'tokenExpiresAt' => $this->application->approval_token_expires_at,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    protected function buildApprovalUrl(string $routeName): string
    {
        try {
            return route($routeName, ['token' => $this->approvalToken]);
        } catch (\Throwable) {
            return url('/loan-approvals/'.$routeName.'/'.$this->approvalToken);
        }
    }

    protected function buildPortalUrl(): string
    {
        try {
            return route('staff.approvals.index');
        } catch (\Throwable) {
            return url('/staff/approvals');
        }
    }
}
