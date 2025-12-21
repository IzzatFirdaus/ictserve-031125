<?php

declare(strict_types=1);

use Illuminate\Support\Env;
use Illuminate\Support\Str;

/**
 * ICTServe Laravel Horizon Configuration
 *
 * Configures queue supervisors for ICTServe modules:
 * - helpdesk: Ticket notifications, SLA alerts, escalations
 * - asset-loan: Approval workflows, reminders, overdue alerts
 * - ai-chatbot: AI processing, document analysis, FAQ responses
 * - reports: Scheduled reports, data exports
 * - notifications: Email notifications, real-time updates
 *
 * @see docs/D17_QUEUE_MANAGEMENT_HORIZON.md
 * @see Requirements 23.1-23.8
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Name
    |--------------------------------------------------------------------------
    */

    'name' => Env::get('HORIZON_NAME', 'ICTServe'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    */

    'domain' => Env::get('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    */

    'path' => Env::get('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    | Updated for XAMPP environment with WSL Redis integration
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    */

    'prefix' => Env::get(
        'HORIZON_PREFIX',
        Str::slug((string) Env::get('APP_NAME', 'ictserve'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    | Requirement 23.5: Automated alerting for long wait times exceeding 60 seconds
    */

    'waits' => [
        'redis:default' => 60,
        'redis:helpdesk' => 60,
        'redis:notifications' => 30,
        'redis:asset-loan' => 120,
        'redis:approvals' => 300,
        'redis:ai-chatbot' => 600,
        'redis:reports' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    */

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    */

    'silenced' => [
        // System maintenance jobs that don't need monitoring
        // App\Jobs\System\CleanupTempFiles::class,
    ],

    'silenced_tags' => [
        'system-maintenance',
        'cleanup',
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    */

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    */

    'memory_limit' => 64,

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'email' => Env::get('HORIZON_NOTIFICATION_EMAIL', 'admin@motac.gov.my'),
        'slack' => Env::get('HORIZON_SLACK_WEBHOOK'),
        'slack_channel' => Env::get('HORIZON_SLACK_CHANNEL', '#ictserve-alerts'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment-Specific Configuration
    |--------------------------------------------------------------------------
    | Requirement 23.2: Configure queue supervisors for ICTServe modules
    | Requirement 23.3: Job balancing with auto-scaling
    | Requirement 23.6: Retry policies with exponential backoff
    */

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 3,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
                'backoff' => [10, 30, 60],
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
            'supervisor-helpdesk' => [
                'connection' => 'redis',
                'queue' => ['helpdesk', 'notifications'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'minProcesses' => 2,
                'maxProcesses' => 8,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 300,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-asset-loan' => [
                'connection' => 'redis',
                'queue' => ['asset-loan', 'approvals'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'size',
                'minProcesses' => 1,
                'maxProcesses' => 4,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 5,
                'timeout' => 180,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-ai' => [
                'connection' => 'redis',
                'queue' => ['ai-chatbot', 'document-processing'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 4,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-reports' => [
                'connection' => 'redis',
                'queue' => ['reports', 'exports', 'digests'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
        ],

        'staging' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 2,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-helpdesk' => [
                'connection' => 'redis',
                'queue' => ['helpdesk', 'notifications'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'minProcesses' => 1,
                'maxProcesses' => 4,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 300,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-asset-loan' => [
                'connection' => 'redis',
                'queue' => ['asset-loan', 'approvals'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'size',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 5,
                'timeout' => 180,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-ai' => [
                'connection' => 'redis',
                'queue' => ['ai-chatbot', 'document-processing'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-reports' => [
                'connection' => 'redis',
                'queue' => ['reports', 'exports'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
        ],

        'local' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 3,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-helpdesk' => [
                'connection' => 'redis',
                'queue' => ['helpdesk', 'notifications'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 300,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-asset-loan' => [
                'connection' => 'redis',
                'queue' => ['asset-loan', 'approvals'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'size',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 5,
                'timeout' => 180,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-ai' => [
                'connection' => 'redis',
                'queue' => ['ai-chatbot', 'document-processing'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
            'supervisor-reports' => [
                'connection' => 'redis',
                'queue' => ['reports', 'exports'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 600,
                'nice' => 0,
                'backoff' => [10, 30, 60],
            ],
        ],
    ],
];
