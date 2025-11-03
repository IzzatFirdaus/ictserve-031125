<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * MaintenanceTicketCreated Notification
 *
 * Sent to maintenance team when automatic maintenance ticket is created from asset return.
 * Supports bilingual email templates (Bahasa Melayu and English).
 *
 * @see D03-FR-002.3 Automatic maintenance ticket creation
 * @see D03-FR-008.4 Cross-module event notifications
 * @see updated-helpdesk-module/requirements.md - Requirement 2.3, 8.4
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class MaintenanceTicketCreated extends Notification implements ShouldQueue
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
        $asset = $this->ticket->relatedAsset;
        $integration = $this->ticket->crossModuleIntegrations()
            ->where('integration_type', \App\Models\CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST)
            ->first();

        return (new MailMessage)
            ->subject(__('helpdesk.email.maintenance_ticket_subject', [
                'ticket_number' => $this->ticket->ticket_number,
                'asset_name' => $asset->name ?? 'N/A',
            ]))
            ->greeting(__('helpdesk.email.greeting', ['name' => $notifiable->name]))
            ->line(__('helpdesk.email.maintenance_ticket_created'))
            ->line(__('helpdesk.email.maintenance_ticket_description'))
            ->line(__('helpdesk.email.asset_details'))
            ->line('**'.__('asset.name').':** '.($asset->name ?? 'N/A'))
            ->line('**'.__('asset.asset_tag').':** '.($asset->asset_tag ?? 'N/A'))
            ->line('**'.__('asset.condition').':** '.($this->ticket->damage_type ?? 'N/A'))
            ->line(__('helpdesk.email.ticket_details'))
            ->line('**'.__('helpdesk.ticket_number').':** '.$this->ticket->ticket_number)
            ->line('**'.__('helpdesk.priority').':** '.ucfirst($this->ticket->priority))
            ->line('**'.__('helpdesk.status').':** '.ucfirst($this->ticket->status))
            ->when($integration, function ($mail) use ($integration) {
                $loanData = $integration->integration_data;

                return $mail
                    ->line(__('helpdesk.email.loan_details'))
                    ->line('**'.__('loan.application_number').':** '.($loanData['loan_application_id'] ?? 'N/A'))
                    ->line('**'.__('loan.returned_by').':** '.($loanData['returned_by'] ?? 'N/A'));
            })
            ->action(__('helpdesk.email.view_ticket'), url('/admin/helpdesk-tickets/'.$this->ticket->id))
            ->line(__('helpdesk.email.maintenance_priority_notice'))
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
            'asset_id' => $this->ticket->asset_id,
            'asset_name' => $this->ticket->relatedAsset->name ?? null,
            'damage_type' => $this->ticket->damage_type,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'is_cross_module' => true,
        ];
    }
}
