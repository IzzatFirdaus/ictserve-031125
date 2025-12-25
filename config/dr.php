<?php

declare(strict_types=1);

/**
 * Disaster Recovery Configuration
 *
 * PKS Business Continuity (Requirement 29) - DR Site Configuration
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.2, 29.3, 29.4
 */
return [
    /*
    |--------------------------------------------------------------------------
    | DR Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable disaster recovery features. When disabled, DR health
    | checks will report "unknown" status.
    |
    */
    'enabled' => env('DR_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Secondary Host
    |--------------------------------------------------------------------------
    |
    | The hostname or IP address of the secondary/DR database server.
    |
    */
    'secondary_host' => env('DR_SECONDARY_HOST', ''),

    /*
    |--------------------------------------------------------------------------
    | Secondary Port
    |--------------------------------------------------------------------------
    |
    | The port number for the secondary database server.
    |
    */
    'secondary_port' => env('DR_SECONDARY_PORT', 3306),

    /*
    |--------------------------------------------------------------------------
    | Recovery Time Objective (RTO)
    |--------------------------------------------------------------------------
    |
    | Maximum acceptable time to restore service after a disaster.
    | PKS 29.1 requires RTO of 4 hours.
    |
    */
    'rto_hours' => env('DR_RTO_HOURS', 4),

    /*
    |--------------------------------------------------------------------------
    | Recovery Point Objective (RPO)
    |--------------------------------------------------------------------------
    |
    | Maximum acceptable data loss measured in time.
    | PKS 29.1 requires RPO of 24 hours.
    |
    */
    'rpo_hours' => env('DR_RPO_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Health Check Interval
    |--------------------------------------------------------------------------
    |
    | How often to run automated DR health checks (in minutes).
    |
    */
    'health_check_interval' => env('DR_HEALTH_CHECK_INTERVAL', 15),

    /*
    |--------------------------------------------------------------------------
    | Replication Lag Thresholds
    |--------------------------------------------------------------------------
    |
    | Thresholds for replication lag warnings and critical alerts (in seconds).
    |
    */
    'lag_warning_threshold' => env('DR_LAG_WARNING_THRESHOLD', 60),
    'lag_critical_threshold' => env('DR_LAG_CRITICAL_THRESHOLD', 300),

    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Channels to notify when DR issues are detected.
    |
    */
    'notification_channels' => [
        'mail' => env('DR_NOTIFY_MAIL', true),
        'slack' => env('DR_NOTIFY_SLACK', false),
        'sms' => env('DR_NOTIFY_SMS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Recipients
    |--------------------------------------------------------------------------
    |
    | Email addresses to notify for DR alerts.
    |
    */
    'alert_recipients' => array_filter(explode(',', env('DR_ALERT_RECIPIENTS', ''))),

    /*
    |--------------------------------------------------------------------------
    | File Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for file replication to DR site.
    |
    */
    'file_sync' => [
        'enabled' => env('DR_FILE_SYNC_ENABLED', false),
        'method' => env('DR_FILE_SYNC_METHOD', 'rsync'), // rsync, s3, sftp
        'destination' => env('DR_FILE_SYNC_DESTINATION', ''),
        'exclude_patterns' => [
            '*.log',
            '*.tmp',
            'cache/*',
            'sessions/*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failover Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for automated and manual failover procedures.
    |
    */
    'failover' => [
        'auto_failover_enabled' => env('DR_AUTO_FAILOVER', false),
        'auto_failover_threshold' => env('DR_AUTO_FAILOVER_THRESHOLD', 3), // consecutive failures
        'require_approval' => env('DR_FAILOVER_REQUIRE_APPROVAL', true),
        'dns_ttl' => env('DR_DNS_TTL', 300), // seconds
    ],
];
