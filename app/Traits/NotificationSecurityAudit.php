<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Notification Security Audit Trait
 *
 * Provides security audit logging capabilities for notification-related models.
 * Automatically logs security-relevant events for compliance and monitoring.
 *
 * @see Requirements 9.6 - Audit logging for all email operations
 *
 * @trace D03 SRS-FR-043 (notification security)
 */
trait NotificationSecurityAudit
{
    /**
     * Boot the trait.
     */
    protected static function bootNotificationSecurityAudit(): void
    {
        static::created(function ($model): void {
            static::logSecurityAudit($model, 'created');
        });

        static::updated(function ($model): void {
            static::logSecurityAudit($model, 'updated', $model->getDirty());
        });

        static::deleted(function ($model): void {
            static::logSecurityAudit($model, 'deleted');
        });
    }

    /**
     * Log a security audit event.
     *
     * @param  mixed  $model  The model instance
     * @param  string  $action  The action performed
     * @param  array<string, mixed>  $changes  Changed attributes (for updates)
     */
    protected static function logSecurityAudit(mixed $model, string $action, array $changes = []): void
    {
        $user = auth()->user();

        $logData = [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => $action,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        // For updates, log what changed (without sensitive values)
        if ($action === 'updated' && ! empty($changes)) {
            $logData['changed_fields'] = array_keys($changes);
        }

        // Log to security channel
        Log::channel('security')->info("Notification audit: {$action}", $logData);

        // Also log to notifications channel for correlation
        Log::channel('notifications')->info("Security audit: {$action}", [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => $action,
        ]);
    }

    /**
     * Log a custom security event.
     *
     * @param  string  $event  Event description
     * @param  array<string, mixed>  $context  Additional context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $user = auth()->user();

        Log::channel('security')->info("Notification security event: {$event}", array_merge([
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log an access attempt.
     *
     * @param  string  $action  The action attempted
     * @param  bool  $authorized  Whether access was authorized
     */
    public function logAccessAttempt(string $action, bool $authorized): void
    {
        $user = auth()->user();
        $level = $authorized ? 'info' : 'warning';

        Log::channel('security')->{$level}('Notification access attempt', [
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'action' => $action,
            'authorized' => $authorized,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
