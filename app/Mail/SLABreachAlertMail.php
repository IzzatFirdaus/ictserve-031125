<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * SLA Breach Alert Email
 *
 * Sent to supervisors and admins when a ticket is within 25% of SLA breach.
 * Provides escalation details and action links with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email alert for SLA breach warnings
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-008.3 SLA management
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 8.1, 8.3, 10.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class SLABreachAlertMail extends BaseMailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public int $remainingMinutes,
        public int $breachThresholdPercentage = 25
    ) {
        parent::__construct();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.sla_breach_alert_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.sla-breach-alert',
            with: [
                'ticket' => $this->ticket,
                'remainingMinutes' => $this->remainingMinutes,
                'breachThresholdPercentage' => $this->breachThresholdPercentage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
