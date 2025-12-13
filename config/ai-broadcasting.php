<?php

declare(strict_types=1);

/**
 * Konfigurasi Broadcasting AI untuk ICTServe v3.6.0
 *
 * Fail konfigurasi ini mengandungi tetapan untuk broadcasting real-time
 * untuk operasi AI menggunakan Laravel Reverb. Selaras dengan D16 v3.6.0.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D16 Broadcasting Setup v3.6.0
 * @requirements D16, 8.4, 11.1, 11.2
 */

return [
    /*
    |--------------------------------------------------------------------------
    | AI Broadcasting Channels
    |--------------------------------------------------------------------------
    |
    | Saluran WebSocket untuk operasi AI yang berbeza. Setiap saluran
    | mempunyai tahap kebenaran dan jenis acara yang berbeza.
    |
    */
    'channels' => [
        'ai-status' => [
            'name' => 'ai-status',
            'description' => 'Status pemprosesan AI (document ingestion, FAQ processing)',
            'auth_required' => true,
            'roles' => ['admin', 'superuser'], // Hanya admin dan superuser
            'events' => [
                'AIProcessingStarted',
                'AIProcessingCompleted',
                'AIProcessingFailed',
            ],
        ],

        'ai-alerts' => [
            'name' => 'ai-alerts',
            'description' => 'Amaran sistem AI (performance degradation, errors)',
            'auth_required' => true,
            'roles' => ['admin', 'superuser'],
            'events' => [
                'AIPerformanceAlert',
                'AIErrorOccurred',
                'AIServiceDegraded',
                'AIServiceRestored',
            ],
        ],

        'ai-performance' => [
            'name' => 'ai-performance',
            'description' => 'Metrik prestasi AI masa nyata',
            'auth_required' => true,
            'roles' => ['admin', 'superuser'],
            'events' => [
                'AIPerformanceUpdate',
                'AICacheStatsUpdate',
                'AIResourceUsageUpdate',
            ],
        ],

        'ai-approvals' => [
            'name' => 'ai-approvals',
            'description' => 'Notifikasi kelulusan auto-reply AI',
            'auth_required' => true,
            'roles' => ['approver', 'admin', 'superuser'],
            'events' => [
                'AutoReplyDraftCreated',
                'AutoReplyApprovalRequired',
                'AutoReplyApproved',
                'AutoReplyRejected',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk setiap jenis acara AI yang akan di-broadcast
    | melalui Laravel Reverb.
    |
    */
    'events' => [
        'AIProcessingStarted' => [
            'queue' => true,
            'delay' => 0,
            'retry_attempts' => 3,
            'timeout' => 30,
        ],

        'AIProcessingCompleted' => [
            'queue' => true,
            'delay' => 0,
            'retry_attempts' => 3,
            'timeout' => 30,
        ],

        'AIProcessingFailed' => [
            'queue' => true,
            'delay' => 0,
            'retry_attempts' => 5, // Lebih banyak percubaan untuk ralat
            'timeout' => 60,
        ],

        'AIPerformanceAlert' => [
            'queue' => false, // Immediate untuk amaran kritikal
            'delay' => 0,
            'retry_attempts' => 5,
            'timeout' => 15,
        ],

        'AIErrorOccurred' => [
            'queue' => false, // Immediate untuk ralat
            'delay' => 0,
            'retry_attempts' => 5,
            'timeout' => 15,
        ],

        'AutoReplyDraftCreated' => [
            'queue' => true,
            'delay' => 0,
            'retry_attempts' => 3,
            'timeout' => 30,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Tetapan untuk pemantauan prestasi broadcasting AI menggunakan
    | Laravel Pulse integration.
    |
    */
    'monitoring' => [
        'enabled' => env('AI_BROADCASTING_MONITORING_ENABLED', true),
        'track_message_delivery' => true,
        'track_connection_count' => true,
        'track_channel_subscriptions' => true,
        'slow_delivery_threshold' => 1000, // milliseconds
        'pulse_integration' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Tetapan keselamatan untuk broadcasting AI mengikut keperluan
    | D16 v3.6.0 dan standard keselamatan ICTServe.
    |
    */
    'security' => [
        'encrypt_sensitive_data' => true,
        'sanitize_error_messages' => true,
        'rate_limit_per_user' => 100, // Mesej per minit
        'max_concurrent_connections' => 50,
        'allowed_origins' => [
            env('APP_URL'),
            'http://127.0.0.1:8000',
            'http://localhost:8000',
        ],
        'csrf_protection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Formatting
    |--------------------------------------------------------------------------
    |
    | Format standard untuk mesej broadcasting AI dalam Bahasa Melayu sahaja
    | mengikut D15 v3.6.0.
    |
    */
    'message_format' => [
        'locale' => 'ms', // Bahasa Melayu sahaja (D15 v3.6.0)
        'timestamp_format' => 'Y-m-d H:i:s',
        'timezone' => 'Asia/Kuala_Lumpur',
        'include_request_id' => true, // Untuk audit trail
        'include_user_context' => true,
        'sanitize_pii' => true, // Redaksi PII dalam mesej
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk degradasi anggun apabila broadcasting gagal
    | atau tidak tersedia.
    |
    */
    'fallback' => [
        'enabled' => true,
        'use_email_notifications' => true,
        'use_database_notifications' => true,
        'retry_failed_broadcasts' => true,
        'max_retry_attempts' => 3,
        'retry_delay' => 5, // saat
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Tetapan khusus untuk persekitaran pembangunan dan debugging.
    |
    */
    'development' => [
        'log_all_events' => env('AI_BROADCASTING_LOG_EVENTS', false),
        'debug_mode' => env('AI_BROADCASTING_DEBUG', false),
        'simulate_delays' => env('AI_BROADCASTING_SIMULATE_DELAYS', false),
        'test_channels' => [
            'ai-test',
            'ai-debug',
        ],
    ],
];
