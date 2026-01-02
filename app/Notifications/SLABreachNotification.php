<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * SLA Breach Notification
 *
 * Sent when a ticket has breached its SLA deadline.
 * Notifies admin/superuser roles for immediate action.
 *
 * @see D03-FR-008 SLA management requirements
 * @see D04 §5.3 SLA escalation workflow
 * @see Requirements 18.3
 */
class SLABreachNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $breachType  Type of breach: 'response', 'resolution', or 'both'
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public string $breachType = 'resolution'
    ) {
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
        $breachDescription = $this->getBreachDescription();
        $breachDuration = $this->getBreachDuration();

        return (new MailMessage)
            ->subject("🚨 KRITIKAL: Pelanggaran SLA - {$this->ticket->ticket_number}")
            ->greeting("Salam {$notifiable->name},")
            ->line('**AMARAN KRITIKAL:** Tiket berikut telah melanggar SLA:')
            ->line("**Nombor Tiket:** {$this->ticket->ticket_number}")
            ->line("**Subjek:** {$this->ticket->subject}")
            ->line("**Jenis Pelanggaran:** {$breachDescription}")
            ->line("**Tempoh Pelanggaran:** {$breachDuration}")
            ->line('**Keutamaan:** Segera (Dinaikkan secara automatik)')
            ->line('**Pemohon:** '.($this->ticket->user?->name ?? 'Tetamu'))
            ->line('**Ditugaskan Kepada:** '.($this->ticket->assignedUser?->name ?? 'Belum ditugaskan'))
            ->action('Lihat Tiket Segera', url("/admin/helpdesk-tickets/{$this->ticket->id}"))
            ->line('Sila ambil tindakan segera untuk menyelesaikan tiket ini.')
            ->salutation('Sistem Pengurusan ICTServe');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sla_breach',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'breach_type' => $this->breachType,
            'breach_description' => $this->getBreachDescription(),
            'sla_response_due_at' => $this->ticket->sla_response_due_at?->toIso8601String(),
            'sla_resolution_due_at' => $this->ticket->sla_resolution_due_at?->toIso8601String(),
            'sla_breached_at' => $this->ticket->sla_breached_at?->toIso8601String(),
            'priority' => $this->ticket->priority,
            'assigned_to_user' => $this->ticket->assigned_to_user,
            'assigned_user_name' => $this->ticket->assignedUser?->name,
            'message' => "Tiket #{$this->ticket->ticket_number} telah melanggar SLA ({$this->getBreachDescription()})",
        ];
    }

    /**
     * Get human-readable breach description in Bahasa Melayu.
     */
    private function getBreachDescription(): string
    {
        return match ($this->breachType) {
            'response' => 'Masa Respons',
            'resolution' => 'Masa Penyelesaian',
            'both' => 'Masa Respons & Penyelesaian',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get breach duration in human-readable format.
     */
    private function getBreachDuration(): string
    {
        $dueAt = match ($this->breachType) {
            'response' => $this->ticket->sla_response_due_at,
            'resolution', 'both' => $this->ticket->sla_resolution_due_at,
            default => $this->ticket->sla_resolution_due_at,
        };

        if ($dueAt === null) {
            return 'Tidak dapat dikira';
        }

        return now()->diffForHumans($dueAt, ['parts' => 2]).' melebihi had';
    }
}
