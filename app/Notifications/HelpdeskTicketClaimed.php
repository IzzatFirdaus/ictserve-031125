<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * HelpdeskTicketClaimed Notification
 *
 * Sent to authenticated user when they successfully claim a guest ticket.
 * Supports bilingual email templates (Bahasa Melayu and English).
 *
 * @see D03-FR-008.1 Enhanced email workflows
 * @see D03-FR-001.3 Ticket claiming process
 * @see updated-helpdesk-module/requirements.md - Requirement 1.3, 8.1
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class HelpdeskTicketClaimed extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject(__('helpdesk.email.ticket_claimed_subject', ['ticket_number' => $this->ticket->ticket_number]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $notifiable->name]))
            ->line(__('helpdesk.email.ticket_claimed_success', [
                'ticket_number' => $this->ticket->ticket_number,
            ]))
            ->line(__('helpdesk.email.ticket_claimed_benefits'))
            ->line('✓ '.__('helpdesk.email.benefit_tracking'))
            ->line('✓ '.__('helpdesk.email.benefit_history'))
            ->line('✓ '.__('helpdesk.email.benefit_comments'))
            ->line('✓ '.__('helpdesk.email.benefit_notifications'))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.subject').':** '.$this->ticket->subject)
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->ticket->status))
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority))
            ->action(__('helpdesk.email.view_ticket_portal'), url('/helpdesk/tickets/'.$this->ticket->id))
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
            'claimed_at' => now()->toIso8601String(),
            'status' => $this->ticket->status,
            'priority' => $this->ticket->priority,
        ];
    }
}
