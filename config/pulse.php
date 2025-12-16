<?php

use Laravel\Pulse\Http\Middleware\Authorize;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders;

return [

    /*
    |--------------------------------------------------------------------------
    | Pulse Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain which the Pulse dashboard will be accessible from.
    | When set to null, the dashboard will reside under the same domain as
    | the application. Remember to configure your DNS entries correctly.
    |
    */

    'domain' => env('PULSE_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Path
    |--------------------------------------------------------------------------
    |
    | This is the path which the Pulse dashboard will be accessible from. Feel
    | free to change this path to anything you'd like. Note that this won't
    | affect the path of the internal API that is never exposed to users.
    |
    */

    'path' => env('PULSE_PATH', 'pulse'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Master Switch
    |--------------------------------------------------------------------------
    |
    | This configuration option may be used to completely disable all Pulse
    | data recorders regardless of their individual configurations. This
    | provides a single option to quickly disable all Pulse recording.
    |
    */

    'enabled' => env('PULSE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Pulse Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines which storage driver will be used
    | while storing entries from Pulse's recorders. In addition, you also
    | may provide any options to configure the selected storage driver.
    |
    | Per Requirement 36.7: Retain Pulse data for 7 days with automatic
    | pruning to manage storage requirements per D03 §8.2.
    |
    */
    'storage' => [
        'driver' => env('PULSE_STORAGE_DRIVER', 'database'),

        'trim' => [
            // Per Requirement 36.7: 7-day data retention with automatic pruning
            'keep' => env('PULSE_STORAGE_KEEP', '7 days'),
        ],

        'database' => [
            'connection' => env('PULSE_DB_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Ingest Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the ingest driver that will be used
    | to capture entries from Pulse's recorders. Ingest drivers are great to
    | free up your request workers quickly by offloading the data storage.
    |
    */

    'ingest' => [
        'driver' => env('PULSE_INGEST_DRIVER', 'storage'),

        'buffer' => env('PULSE_INGEST_BUFFER', 5_000),

        'trim' => [
            'lottery' => [1, 1_000],
            'keep' => env('PULSE_INGEST_KEEP', '7 days'),
        ],

        'redis' => [
            'connection' => env('PULSE_REDIS_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Cache Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the cache driver that will be used
    | for various tasks, including caching dashboard results, establishing
    | locks for events that should only occur on one server and signals.
    |
    */

    'cache' => env('PULSE_CACHE_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Pulse route, giving you the
    | chance to add your own middleware to this list or change any of the
    | existing middleware. Of course, reasonable defaults are provided.
    |
    */

    'middleware' => [
        'web',
        Authorize::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Recorders
    |--------------------------------------------------------------------------
    |
    | The following array lists the "recorders" that will be registered with
    | Pulse, along with their configuration. Recorders gather application
    | event data from requests and tasks to pass to your ingest driver.
    |
    */

    'recorders' => [
        Recorders\CacheInteractions::class => [
            'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_CACHE_INTERACTIONS_SAMPLE_RATE', 1),
            'ignore' => [
                ...Pulse::defaultVendorCacheKeys(),
            ],
            'groups' => [
                '/^job-exceptions:.*/' => 'job-exceptions:*',
                // '/:\d+/' => ':*',
            ],
        ],

        Recorders\Exceptions::class => [
            'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_EXCEPTIONS_SAMPLE_RATE', 1),
            'location' => env('PULSE_EXCEPTIONS_LOCATION', true),
            'ignore' => [
                // '/^Package\\\\Exceptions\\\\/',
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Queue Jobs Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.3: Monitor queue job performance including
        | processing time, failure rates, and retry patterns per D17.
        |
        */
        Recorders\Queues::class => [
            'enabled' => env('PULSE_QUEUES_ENABLED', true),
            'sample_rate' => env('PULSE_QUEUES_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Server Health Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.5: Provide server health metrics including
        | CPU usage, memory consumption, and disk space utilization per D03 §8.2.
        |
        */
        Recorders\Servers::class => [
            'server_name' => env('PULSE_SERVER_NAME', gethostname()),
            'directories' => explode(':', env('PULSE_SERVER_DIRECTORIES', '/')),
        ],

        Recorders\SlowJobs::class => [
            'enabled' => env('PULSE_SLOW_JOBS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_JOBS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_JOBS_THRESHOLD', 1000),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\SlowOutgoingRequests::class => [
            'enabled' => env('PULSE_SLOW_OUTGOING_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_OUTGOING_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_OUTGOING_REQUESTS_THRESHOLD', 1000),
            'ignore' => [
                // '#^http://127\.0\.0\.1:13714#', // Inertia SSR...
            ],
            'groups' => [
                // '#^https://api\.github\.com/repos/.*$#' => 'api.github.com/repos/*',
                // '#^https?://([^/]*).*$#' => '\1',
                // '#/\d+#' => '/*',
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Slow Queries Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.2: Track slow database queries (>500ms threshold)
        | and display in Pulse dashboard with query details, frequency, and
        | execution time per D03 §8.2.
        |
        */
        Recorders\SlowQueries::class => [
            'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_QUERIES_SAMPLE_RATE', 1),
            // Per Requirement 36.2: 500ms threshold for slow query detection
            'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 500),
            'location' => env('PULSE_SLOW_QUERIES_LOCATION', true),
            'max_query_length' => env('PULSE_SLOW_QUERIES_MAX_QUERY_LENGTH'),
            'ignore' => [
                '/(["`])pulse_[\w]+?\1/', // Pulse tables...
                '/(["`])telescope_[\w]+?\1/', // Telescope tables...
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Slow Requests Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.4: Track user request patterns including
        | response times, memory usage, and cache hit rates per D03 §8.2.
        |
        */
        Recorders\SlowRequests::class => [
            'enabled' => env('PULSE_SLOW_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_REQUESTS_THRESHOLD', 1000),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | User Jobs Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.3: Track queue jobs by user for performance
        | analysis and debugging per D17.
        |
        */
        Recorders\UserJobs::class => [
            'enabled' => env('PULSE_USER_JOBS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_JOBS_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | User Requests Recorder
        |----------------------------------------------------------------------
        |
        | Per Requirement 36.4: Track user request patterns for performance
        | monitoring and analysis per D03 §8.2.
        |
        */
        Recorders\UserRequests::class => [
            'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_REQUESTS_SAMPLE_RATE', 1),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
                '#^/api/v1/ollama#', // Ollama AI API endpoints (monitored separately)
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | AI Operations Recorder (Custom for Ollama v3.6.0)
        |----------------------------------------------------------------------
        |
        | Per Requirements 8.7, SRS-AI-019: Monitor AI operations performance
        | including response times, token usage, model routing, and cost estimates
        | for both AWS Bedrock and Ollama integration.
        | Selaras dengan D11 Technical Design Documentation v3.6.0 dan D18 §6.1.
        |
        | trace: D03-SRS-AI-019, D18-§6.1, D11-§8.1
        |
        */
        \App\Pulse\Recorders\AIServiceMetrics::class => [
            'enabled' => env('PULSE_AI_METRICS_ENABLED', true),
            'sample_rate' => env('PULSE_AI_METRICS_SAMPLE_RATE', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Performance Monitoring (v3.6.0)
    |--------------------------------------------------------------------------
    |
    | Configuration for monitoring Ollama AI operations including response
    | times, cache performance, and resource utilization. Selaras dengan
    | Requirements 8.1, 8.7 dan D11 v3.6.0.
    |
    */
    'ai_monitoring' => [
        'enabled' => env('PULSE_AI_MONITORING_ENABLED', true),
        'sample_rate' => env('PULSE_AI_MONITORING_SAMPLE_RATE', 1),
        'slow_threshold' => env('PULSE_AI_SLOW_THRESHOLD', 2000), // 2 seconds
        'track_cache_performance' => true,
        'track_model_usage' => true,
        'track_embedding_generation' => true,
        'ignore_health_checks' => true,
    ],
];
