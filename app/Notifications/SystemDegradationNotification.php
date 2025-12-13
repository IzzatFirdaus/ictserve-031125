<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Pemberitahuan Degradasi Sistem untuk ICTServe v3.6.0
 *
 * Pemberitahuan ini dihantar kepada pentadbir apabila sistem AI
 * mengalami degradasi prestasi.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 Technical Design Documentation v3.6.0
 */
class SystemDegradationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private const TIER_NAMES = [
        1 => 'Operasi Penuh',
        2 => 'Operasi Dikurangkan',
        3 => 'Cache Sahaja',
        4 => 'Mod Kecemasan',
    ];

    public function __construct(
        private readonly int $tier,
        private readonly string $reason
    ) {}

    /**
     * Dapatkan saluran penghantaran pemberitahuan
     * Get the notification's delivery channels
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Dapatkan representasi mel pemberitahuan
     * Get the mail representation of the notification
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tierName = self::TIER_NAMES[$this->tier] ?? 'Tidak Diketahui';
        $urgency = $this->tier >= 3 ? 'SEGERA' : 'PERHATIAN';

        return (new MailMessage)
            ->subject("[{$urgency}] Degradasi Sistem AI ICTServe - {$tierName}")
            ->greeting('Salam Sejahtera,')
            ->line("Sistem AI ICTServe telah berubah ke mod **{$tierName}** (Tier {$this->tier}).")
            ->line("**Sebab:** {$this->reason}")
            ->line('**Masa:** '.now()->format('d/m/Y H:i:s'))
            ->when($this->tier >= 3, function (MailMessage $message) {
                return $message->line('⚠️ Tindakan segera mungkin diperlukan untuk memulihkan perkhidmatan.');
            })
            ->action('Lihat Dashboard Prestasi', url('/admin/ollama/performance'))
            ->line('Sila pantau sistem dan ambil tindakan yang sewajarnya.')
            ->salutation('Sistem ICTServe');
    }

    /**
     * Dapatkan representasi array pemberitahuan
     * Get the array representation of the notification
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'system_degradation',
            'tier' => $this->tier,
            'tier_name' => self::TIER_NAMES[$this->tier] ?? 'Tidak Diketahui',
            'reason' => $this->reason,
            'timestamp' => now()->toIso8601String(),
            'action_url' => '/admin/ollama/performance',
        ];
    }
}
