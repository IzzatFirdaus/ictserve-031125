<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * AuthenticatedTicketConfirmation Notification
 *
 * Sent to authenticated users when they submit a helpdesk ticket through the portal.
 * Supports bilingual email templates (Bahasa Melayu and English).
 *
 * @see D03-FR-001.4 Authenticated ticket submission
 * @see D03-FR-008.1 Enhanced email workflows
 * @see updated-helpdesk-module/requirements.md - Requirement 1.4, 8.1
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class AuthenticatedTicketConfirmation extends Notification implements ShouldQueue
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
        // Check user notification preferences
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
        return (new MailMessage)
            ->subject(__('helpdesk.email.authenticated_confirmation_subject', ['ticket_number' => $this->ticket->ticket_number]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $notifiable->name]))
            ->line(__('helpdesk.email.authenticated_ticket_received'))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.ticket_number').':** '.$this->ticket->ticket_number)
            ->line('**'.__('helpdesk.subject').':** '.$this->ticket->subject)
            ->line('**'.__('helpdesk.category').':** '.($this->ticket->category->name ?? __('common.not_specified')))
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority))
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->ticket->status))
            ->when($this->ticket->hasRelatedAsset(), function ($mail) {
                return $mail->line('**'.__('helpdesk.related_asset').':** '.$this->ticket->relatedAsset->name);
            })
            ->line(__('helpdesk.email.authenticated_features'))
            ->line('• '.__('helpdesk.email.feature_real_time_tracking'))
            ->line('• '.__('helpdesk.email.feature_internal_comments'))
            ->line('• '.__('helpdesk.email.feature_submission_history'))
            ->line('• '.__('helpdesk.email.feature_instant_notifications'))
            ->action(__('helpdesk.email.view_ticket_portal'), url('/helpdesk/tickets/'.$this->ticket->id))
            ->line(__('helpdesk.email.sla_notice', [
                'response_time' => $this->ticket->category->sla_response_hours ?? 24,
                'resolution_time' => $this->ticket->category->sla_resolution_hours ?? 72,
            ]))
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
            'category' => $this->ticket->category->name ?? null,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'has_asset' => $this->ticket->hasRelatedAsset(),
            'sla_response_due_at' => $this->ticket->sla_response_due_at?->toIso8601String(),
            'sla_resolution_due_at' => $this->ticket->sla_resolution_due_at?->toIso8601String(),
        ];
    }
}
