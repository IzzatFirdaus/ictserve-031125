<?php

declare(strict_types=1);

/**
 * ClamAV Antivirus Scanner Configuration
 *
 * @see Requirements 14.3 - Scan uploads before storage
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable ClamAV Scanning
    |--------------------------------------------------------------------------
    |
    | Set to false to disable ClamAV scanning (useful for development).
    | In production, this should always be true.
    |
    */
    'enabled' => env('CLAMAV_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | ClamAV Socket Path
    |--------------------------------------------------------------------------
    |
    | The Unix socket path for ClamAV daemon (clamd).
    | Default: /var/run/clamav/clamd.sock (Linux)
    |
    */
    'socket_path' => env('CLAMAV_SOCKET_PATH', '/var/run/clamav/clamd.sock'),

    /*
    |--------------------------------------------------------------------------
    | ClamAV Host (TCP Mode)
    |--------------------------------------------------------------------------
    |
    | The hostname for ClamAV daemon when using TCP mode.
    | Set CLAMAV_USE_TCP=true to use TCP instead of Unix socket.
    |
    */
    'host' => env('CLAMAV_HOST', '127.0.0.1'),

    /*
    |--------------------------------------------------------------------------
    | ClamAV Port (TCP Mode)
    |--------------------------------------------------------------------------
    |
    | The port for ClamAV daemon when using TCP mode.
    |
    */
    'port' => env('CLAMAV_PORT', 3310),

    /*
    |--------------------------------------------------------------------------
    | Use TCP Mode
    |--------------------------------------------------------------------------
    |
    | Set to true to use TCP connection instead of Unix socket.
    | Useful for Docker environments or remote ClamAV servers.
    |
    */
    'use_tcp' => env('CLAMAV_USE_TCP', false),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for connecting to ClamAV daemon.
    |
    */
    'timeout' => env('CLAMAV_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes to scan. Files larger than this will be
    | rejected without scanning. Default: 25MB
    |
    */
    'max_file_size' => env('CLAMAV_MAX_FILE_SIZE', 26214400),

    /*
    |--------------------------------------------------------------------------
    | Quarantine Path
    |--------------------------------------------------------------------------
    |
    | Directory to store quarantined infected files for review.
    | Set to null to delete infected files immediately.
    |
    */
    'quarantine_path' => env('CLAMAV_QUARANTINE_PATH', storage_path('app/quarantine')),

    /*
    |--------------------------------------------------------------------------
    | Log Infected Files
    |--------------------------------------------------------------------------
    |
    | Whether to log infected file detections.
    |
    */
    'log_infections' => env('CLAMAV_LOG_INFECTIONS', true),
];
