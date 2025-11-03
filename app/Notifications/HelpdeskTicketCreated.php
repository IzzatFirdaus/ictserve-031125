<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * HelpdeskTicketCreated Notification
 *
 * Sent to admins when a new helpdesk ticket is created (guest or authenticated).
 * Supports bilingual email templates (Bahasa Melayu and English).
 *
 * @see D03-FR-008.1 Enhanced email workflows
 * @see D03-FR-008.2 60-second delivery SLA
 * @see updated-helpdesk-module/requirements.md - Requirement 8.1
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class HelpdeskTicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $locale = app()->getLocale();
        $submissionType = $this->ticket->isGuestSubmission() ? 'Guest' : 'Authenticated';

        return (new MailMessage)
            ->subject(__('helpdesk.email.new_ticket_subject', ['ticket_number' => $this->ticket->ticket_number]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $notifiable->name]))
            ->line(__('helpdesk.email.new_ticket_created', [
                'ticket_number' => $this->ticket->ticket_number,
                'submission_type' => $submissionType,
            ]))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.subject').':** '.$this->ticket->subject)
            ->line('**'.__('helpdesk.submitter').':** '.$this->ticket->getSubmitterName())
            ->line('**'.__('helpdesk.email').':** '.$this->ticket->getSubmitterEmail())
            ->line('**'.__('helpdesk.category').':** '.($this->ticket->category->name ?? __('common.not_specified')))
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority))
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->ticket->status))
            ->when($this->ticket->hasRelatedAsset(), function ($mail) {
                return $mail->line('**'.__('helpdesk.related_asset').':** '.$this->ticket->relatedAsset->name);
            })
            ->action(__('helpdesk.email.view_ticket'), url('/admin/helpdesk-tickets/'.$this->ticket->id))
            ->line(__('helpdesk.email.thank_you'));
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
            'submitter_name' => $this->ticket->getSubmitterName(),
            'submission_type' => $this->ticket->isGuestSubmission() ? 'guest' : 'authenticated',
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'has_asset' => $this->ticket->hasRelatedAsset(),
        ];
    }
}
