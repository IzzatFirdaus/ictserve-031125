<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * GuestTicketConfirmation Notification
 *
 * Sent to guest users when they submit a helpdesk ticket without authentication.
 * Supports bilingual email templates (Bahasa Melayu and English).
 *
 * @see D03-FR-001.2 Guest ticket submission
 * @see D03-FR-008.1 Enhanced email workflows
 * @see updated-helpdesk-module/requirements.md - Requirement 1.2, 8.1
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class GuestTicketConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public bool $canClaim = false
    ) {
        // Set queue for 60-second SLA compliance
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('helpdesk.email.guest_confirmation_subject', ['ticket_number' => $this->ticket->ticket_number]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $this->ticket->guest_name]))
            ->line(__('helpdesk.email.guest_ticket_received'))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.ticket_number').':** '.$this->ticket->ticket_number)
            ->line('**'.__('helpdesk.subject').':** '.$this->ticket->subject)
            ->line('**'.__('helpdesk.category').':** '.($this->ticket->category->name ?? __('common.not_specified')))
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority))
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->ticket->status))
            ->when($this->ticket->hasRelatedAsset(), function ($mail) {
                return $mail->line('**'.__('helpdesk.related_asset').':** '.$this->ticket->relatedAsset->name);
            })
            ->line(__('helpdesk.email.guest_next_steps'))
            ->line('• '.__('helpdesk.email.guest_step_email_updates'))
            ->line('• '.__('helpdesk.email.guest_step_reference_number'))
            ->line('• '.__('helpdesk.email.guest_step_response_time'));

        if ($this->canClaim) {
            $mail->line(__('helpdesk.email.guest_can_claim'))
                ->action(__('helpdesk.email.claim_ticket'), url('/helpdesk/claim/'.$this->ticket->ticket_number));
        }

        return $mail->line(__('helpdesk.email.thank_you'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'guest_name' => $this->ticket->guest_name,
            'guest_email' => $this->ticket->guest_email,
            'can_claim' => $this->canClaim,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
        ];
    }
}
