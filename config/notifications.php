<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | This array defines all available notification types in the system.
    | Each type has metadata including display name, description, category,
    | and default preference settings.
    |
    */

    'types' => [
        'ticket_status_changed' => [
            'name' => 'Perubahan Status Tiket',
            'description' => 'Notifikasi apabila status tiket berubah',
            'category' => 'tickets',
            'default_enabled' => true,
        ],
        'ticket_assigned' => [
            'name' => 'Tiket Ditugaskan',
            'description' => 'Notifikasi apabila tiket ditugaskan kepada anda',
            'category' => 'tickets',
            'default_enabled' => true,
        ],
        'ticket_comment' => [
            'name' => 'Komen Tiket Baharu',
            'description' => 'Notifikasi apabila komen baharu ditambah pada tiket',
            'category' => 'tickets',
            'default_enabled' => true,
        ],
        'loan_approval_required' => [
            'name' => 'Kelulusan Pinjaman Diperlukan',
            'description' => 'Notifikasi apabila pinjaman memerlukan kelulusan',
            'category' => 'loans',
            'default_enabled' => true,
        ],
        'loan_status_changed' => [
            'name' => 'Perubahan Status Pinjaman',
            'description' => 'Notifikasi apabila status pinjaman berubah',
            'category' => 'loans',
            'default_enabled' => true,
        ],
        'loan_return_reminder' => [
            'name' => 'Peringatan Pemulangan Pinjaman',
            'description' => 'Notifikasi peringatan untuk memulangkan aset yang dipinjam',
            'category' => 'loans',
            'default_enabled' => true,
        ],
        'submission_status_changed' => [
            'name' => 'Perubahan Status Submission',
            'description' => 'Notifikasi apabila status submission berubah',
            'category' => 'submissions',
            'default_enabled' => true,
        ],
        'system_alert' => [
            'name' => 'Makluman Sistem',
            'description' => 'Makluman penting berkaitan sistem',
            'category' => 'system',
            'default_enabled' => true,
        ],
        'realtime_notifications' => [
            'name' => 'Notifikasi Masa Nyata',
            'description' => 'Notifikasi yang dihantar melalui websocket untuk paparan segera',
            'category' => 'system',
            'default_enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Critical Notification Types
    |--------------------------------------------------------------------------
    |
    | These notification types ALWAYS send to ALL channels (email, database,
    | broadcast) regardless of user preferences. Used for security alerts,
    | compliance notifications, and system-critical information.
    |
    | These bypass user preference settings and cannot be disabled.
    |
    */

    'critical_types' => [
        'system_alert',
        'loan_approval_required',
        'loan_status_changed',
        'loan_return_reminder',
        'ticket_assigned',
        'submission_status_changed',
        'ticket_status_changed',
        'ticket_comment',
        'realtime_notifications',
    ],

    /*
    |--------------------------------------------------------------------------
    | High Priority Notification Types
    |--------------------------------------------------------------------------
    |
    | These notification types are sent by default unless the user explicitly
    | disables them in their preferences. Used for important but non-critical
    | notifications.
    |
    | Users can opt-out of these notifications in their preference settings.
    |
    */

    'high_priority_types' => [
        'loan_approval_required',
        'loan_status_changed',
        'ticket_assigned',
        'submission_status_changed',
        'ticket_status_changed',
        'ticket_comment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic retry of failed email notifications.
    | The system will retry failed emails with exponential backoff.
    |
    */

    'email_retry' => [
        'enabled' => env('NOTIFICATION_RETRY_ENABLED', true),
        'max_attempts' => env('NOTIFICATION_MAX_RETRY_ATTEMPTS', 3),
        'backoff_delays' => [60, 300, 900], // seconds: 1min, 5min, 15min
        'permanent_failure_after' => env('NOTIFICATION_PERMANENT_FAILURE_AFTER', 24), // hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for notification channels (email, database, broadcast).
    | Define queue names and timeout settings for each channel.
    |
    */

    'channels' => [
        'email' => [
            'queue' => env('NOTIFICATION_EMAIL_QUEUE', 'emails'),
            'timeout' => env('NOTIFICATION_EMAIL_TIMEOUT', 30), // seconds
            'enabled' => env('NOTIFICATION_EMAIL_ENABLED', true),
        ],
        'database' => [
            'queue' => env('NOTIFICATION_DATABASE_QUEUE', 'notifications'),
            'timeout' => env('NOTIFICATION_DATABASE_TIMEOUT', 10), // seconds
            'enabled' => env('NOTIFICATION_DATABASE_ENABLED', true),
            'keep_days' => env('NOTIFICATION_DATABASE_KEEP_DAYS', 90), // retention period
        ],
        'broadcast' => [
            'queue' => env('NOTIFICATION_BROADCAST_QUEUE', 'broadcasts'),
            'timeout' => env('NOTIFICATION_BROADCAST_TIMEOUT', 5), // seconds
            'enabled' => env('NOTIFICATION_BROADCAST_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Channel Order
    |--------------------------------------------------------------------------
    |
    | The order in which channels are attempted when dispatching notifications.
    | If one channel fails, the system continues with the next channel.
    |
    */

    'channel_order' => ['database', 'broadcast', 'email'],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Enable audit logging for notification preference decisions and
    | notification dispatching events.
    |
    */

    'audit' => [
        'enabled' => env('NOTIFICATION_AUDIT_ENABLED', true),
        'log_channel' => env('NOTIFICATION_AUDIT_LOG_CHANNEL', 'stack'),
        'log_preference_decisions' => true,
        'log_dispatch_events' => true,
        'log_failed_deliveries' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration to prevent notification spam.
    | Define maximum notifications per user per time period.
    |
    */

    'rate_limiting' => [
        'enabled' => env('NOTIFICATION_RATE_LIMITING_ENABLED', true),
        'max_per_minute' => env('NOTIFICATION_MAX_PER_MINUTE', 10),
        'max_per_hour' => env('NOTIFICATION_MAX_PER_HOUR', 100),
        'max_per_day' => env('NOTIFICATION_MAX_PER_DAY', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for batch notification dispatching when sending to
    | multiple users simultaneously.
    |
    */

    'batch' => [
        'enabled' => env('NOTIFICATION_BATCH_ENABLED', true),
        'chunk_size' => env('NOTIFICATION_BATCH_CHUNK_SIZE', 100),
        'delay_between_chunks' => env('NOTIFICATION_BATCH_DELAY', 5), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Categories
    |--------------------------------------------------------------------------
    |
    | Categories for organizing notification types. Used in preference
    | management UI and reporting.
    |
    */

    'categories' => [
        'tickets' => [
            'name' => 'Tiket Helpdesk',
            'description' => 'Notifikasi berkaitan tiket sokongan teknikal',
            'icon' => 'heroicon-o-ticket',
        ],
        'loans' => [
            'name' => 'Pinjaman Aset',
            'description' => 'Notifikasi berkaitan pinjaman dan pemulangan aset',
            'icon' => 'heroicon-o-archive-box',
        ],
        'submissions' => [
            'name' => 'Submission',
            'description' => 'Notifikasi berkaitan submission dan kelulusan',
            'icon' => 'heroicon-o-document-text',
        ],
        'system' => [
            'name' => 'Sistem',
            'description' => 'Makluman sistem dan notifikasi pentadbiran',
            'icon' => 'heroicon-o-cog-6-tooth',
        ],
    ],

];
