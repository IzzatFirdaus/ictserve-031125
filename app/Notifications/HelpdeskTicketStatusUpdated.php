<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * HelpdeskTicketStatusUpdated Notification
 *
 * Sent when a helpdesk ticket status changes (both guest and authenticated).
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
class HelpdeskTicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public string $oldStatus,
        public string $newStatus,
        public ?string $comment = null
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
        // For guest submissions, only email
        if ($this->ticket->isGuestSubmission()) {
            return ['mail'];
        }

        // For authenticated users, check preferences
        $channels = ['database'];

        if ($notifiable->wantsEmailNotifications('ticket_updates')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('helpdesk.email.status_update_subject', ['ticket_number' => $this->ticket->ticket_number]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $this->getRecipientName($notifiable)]))
            ->line(__('helpdesk.email.status_updated', [
                'ticket_number' => $this->ticket->ticket_number,
                'old_status' => ucfirst($this->oldStatus),
                'new_status' => ucfirst($this->newStatus),
            ]))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.subject').':** '.$this->ticket->subject)
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->newStatus))
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority));

        if ($this->comment) {
            $mail->line(__('helpdesk.email.update_comment'))
                ->line('> '.$this->comment);
        }

        if ($this->ticket->assigned_to_user) {
            $mail->line('**'.__('helpdesk.assigned_to').':** '.$this->ticket->assignedUser->name);
        }

        // Add action button based on submission type
        if ($this->ticket->isAuthenticatedSubmission()) {
            $mail->action(__('helpdesk.email.view_ticket_portal'), url('/helpdesk/tickets/'.$this->ticket->id));
        } else {
            $mail->line(__('helpdesk.email.guest_status_info'));
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'comment' => $this->comment,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get recipient name based on submission type
     */
    private function getRecipientName(object $notifiable): string
    {
        if ($this->ticket->isGuestSubmission()) {
            return $this->ticket->guest_name;
        }

        return $notifiable->name ?? $this->ticket->user->name;
    }
}
