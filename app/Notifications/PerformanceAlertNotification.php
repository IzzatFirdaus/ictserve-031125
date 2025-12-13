<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Performance Alert Notification for ICTServe v3.5.0
 *
 * Sends notifications when performance thresholds are exceeded.
 * Per Requirement 36.8: Trigger alerts via configured notification channels per D17 §5.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see D17 §5 Notification channels
 * @see Requirements 36.8
 */
class PerformanceAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $metric,
        public readonly float $currentValue,
        public readonly float $threshold
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
        $severity = $this->getSeverity();
        $severityLabel = strtoupper($severity);

        return (new MailMessage)
            ->subject("[{$severityLabel}] ICTServe Performance Alert: {$this->getMetricLabel()}")
            ->greeting(__('notifications.performance_alert_greeting'))
            ->line(__('notifications.performance_alert_intro', [
                'metric' => $this->getMetricLabel(),
            ]))
            ->line(__('notifications.performance_alert_details', [
                'current' => $this->formatValue($this->currentValue),
                'threshold' => $this->formatValue($this->threshold),
            ]))
            ->line(__('notifications.performance_alert_action'))
            ->action(__('notifications.view_pulse_dashboard'), url('/pulse'))
            ->line(__('notifications.performance_alert_footer'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'performance_alert',
            'metric' => $this->metric,
            'metric_label' => $this->getMetricLabel(),
            'current_value' => $this->currentValue,
            'threshold' => $this->threshold,
            'severity' => $this->getSeverity(),
            'message' => $this->getMessage(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Get human-readable metric label
     */
    private function getMetricLabel(): string
    {
        return match (true) {
            str_contains($this->metric, 'response_time') => __('metrics.response_time'),
            str_contains($this->metric, 'cache_hit_rate') => __('metrics.cache_hit_rate'),
            str_contains($this->metric, 'queue_failure') => __('metrics.queue_failure_rate'),
            str_contains($this->metric, 'slow_queries') => __('metrics.slow_queries'),
            str_contains($this->metric, 'cpu_usage') => __('metrics.cpu_usage'),
            str_contains($this->metric, 'memory_usage') => __('metrics.memory_usage'),
            str_contains($this->metric, 'disk_usage') => __('metrics.disk_usage'),
            default => $this->metric,
        };
    }

    /**
     * Get alert severity based on metric type
     */
    private function getSeverity(): string
    {
        return match (true) {
            str_contains($this->metric, 'disk_usage') => 'critical',
            str_contains($this->metric, 'cpu_usage') => 'high',
            str_contains($this->metric, 'memory_usage') => 'high',
            str_contains($this->metric, 'response_time') => 'high',
            str_contains($this->metric, 'queue_failure') => 'high',
            str_contains($this->metric, 'cache_hit_rate') => 'medium',
            str_contains($this->metric, 'slow_queries') => 'medium',
            default => 'medium',
        };
    }

    /**
     * Format value for display
     */
    private function formatValue(float $value): string
    {
        if (str_contains($this->metric, 'percent') || str_contains($this->metric, 'rate')) {
            return number_format($value, 2).'%';
        }

        if (str_contains($this->metric, 'time') || str_contains($this->metric, '_ms')) {
            return number_format($value, 2).'ms';
        }

        return number_format($value, 2);
    }

    /**
     * Get alert message
     */
    private function getMessage(): string
    {
        return sprintf(
            '%s exceeded threshold: current value %.2f, threshold %.2f',
            $this->getMetricLabel(),
            $this->currentValue,
            $this->threshold
        );
    }
}
