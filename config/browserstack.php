<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | BrowserStack Configuration for ICTServe v3.6.1
    |--------------------------------------------------------------------------
    |
    | This configuration file manages BrowserStack integration settings for
    | cross-platform testing with Percy visual testing integration.
    | Compatible with Laravel 12.43.1 and ICTServe's True Hybrid Architecture.
    |
    */

    'enabled' => env('BROWSERSTACK_ENABLED', false),

    'credentials' => [
        'username' => env('BROWSERSTACK_USERNAME'),
        'access_key' => env('BROWSERSTACK_ACCESS_KEY'),
    ],

    'project' => [
        'name' => env('BROWSERSTACK_PROJECT_NAME', 'ICTServe v3.6.1 Visual Testing'),
        'build' => env('BROWSERSTACK_BUILD_NAME', 'Percy Integration Build'),
        'session_name' => env('BROWSERSTACK_SESSION_NAME', 'Percy Visual Test Session'),
    ],

    'capabilities' => [
        'default' => [
            'browserstack.debug' => true,
            'browserstack.console' => 'info',
            'browserstack.networkLogs' => true,
            'browserstack.seleniumLogs' => false,
            'browserstack.local' => false,
            'browserstack.timezone' => 'Asia/Kuala_Lumpur', // ICTServe timezone
        ],

        'desktop' => [
            [
                'browser' => 'Chrome',
                'browser_version' => 'latest',
                'os' => 'Windows',
                'os_version' => '11',
                'resolution' => '1920x1080',
            ],
            [
                'browser' => 'Firefox',
                'browser_version' => 'latest',
                'os' => 'Windows',
                'os_version' => '11',
                'resolution' => '1920x1080',
            ],
            [
                'browser' => 'Safari',
                'browser_version' => 'latest',
                'os' => 'OS X',
                'os_version' => 'Monterey',
                'resolution' => '1920x1080',
            ],
            [
                'browser' => 'Edge',
                'browser_version' => 'latest',
                'os' => 'Windows',
                'os_version' => '11',
                'resolution' => '1920x1080',
            ],
        ],

        'mobile' => [
            [
                'device' => 'iPhone 14',
                'os_version' => '16',
                'real_mobile' => true,
            ],
            [
                'device' => 'Samsung Galaxy S23',
                'os_version' => '13.0',
                'real_mobile' => true,
            ],
            [
                'device' => 'iPad Pro 12.9 2022',
                'os_version' => '16',
                'real_mobile' => true,
            ],
        ],
    ],

    'percy_integration' => [
        'enabled' => env('PERCY_ENABLED', false),
        'token' => env('PERCY_TOKEN'),
        'project' => env('PERCY_PROJECT', 'ictserve-v3.6.1-visual-testing'),
        'widths' => [375, 768, 1024, 1280, 1920],
        'min_height' => 1024,
        'css_overrides' => [
            '.dynamic-timestamp { display: none !important; }',
            '.loading-spinner { visibility: hidden !important; }',
            '.language-switcher { display: none !important; }', // v3.6.0+ Bahasa Melayu only
            '.percy-hide { display: none !important; }',
        ],
    ],

    'test_management' => [
        'enabled' => env('BROWSERSTACK_TEST_MANAGEMENT_ENABLED', false),
        'project_id' => env('BROWSERSTACK_TM_PROJECT_ID'),
        'folder_id' => env('BROWSERSTACK_TM_FOLDER_ID'),
        'auto_create_test_cases' => true,
        'sync_with_percy' => true,
        'api_base_url' => 'https://api.browserstack.com/test-management/v1',

        // Test case organization settings
        'test_case_defaults' => [
            'priority' => 'high',
            'type' => 'visual',
            'status' => 'active',
            'tags' => ['percy', 'visual-testing', 'ictserve-v3.6.1'],
        ],

        // Test run settings
        'test_run_defaults' => [
            'name_prefix' => 'Percy Visual Testing',
            'description_template' => 'Comprehensive Percy visual testing run for ICTServe v3.6.1',
            'auto_complete' => true,
        ],

        // Percy integration settings
        'percy_integration' => [
            'sync_on_complete' => true,
            'include_build_url' => true,
            'track_snapshot_status' => true,
        ],

        // Report settings
        'reporting' => [
            'output_dir' => storage_path('logs/browserstack-test-management'),
            'formats' => ['json', 'markdown'],
            'include_percy_build' => true,
            'include_recommendations' => true,
        ],
    ],

    'accessibility_testing' => [
        'enabled' => env('BROWSERSTACK_ACCESSIBILITY_ENABLED', false),
        'wcag_level' => 'AA', // WCAG 2.2 AA compliance for ICTServe
        'scan_types' => ['automated', 'manual'],
        'combine_with_percy' => true,
        'report_format' => 'json',
    ],

    'live_sessions' => [
        'enabled' => env('BROWSERSTACK_LIVE_ENABLED', false),
        'auto_screenshot' => true,
        'video_recording' => true,
        'debug_mode' => true,
        'network_logs' => true,
        'console_logs' => 'info',
        'timezone' => 'Asia/Kuala_Lumpur', // ICTServe timezone

        // Percy integration for Live sessions
        'percy_integration' => [
            'enabled' => env('PERCY_ENABLED', false),
            'auto_snapshot' => true,
            'snapshot_on_screenshot' => true,
            'default_widths' => [375, 768, 1280, 1920],
        ],

        // Collaborative debugging settings
        'collaborative' => [
            'enabled' => true,
            'max_participants' => 5,
            'chat_enabled' => true,
            'shared_notes_enabled' => true,
        ],

        // Visual issue tracking
        'issue_tracking' => [
            'enabled' => true,
            'auto_create_from_percy' => true,
            'severity_levels' => ['critical', 'major', 'minor', 'cosmetic'],
        ],

        // Debugging workflows
        'workflows' => [
            'enabled' => true,
            'predefined_workflows' => [
                'percy_visual_regression',
                'accessibility_compliance',
                'cross_browser_consistency',
            ],
        ],
    ],

    'performance_testing' => [
        'enabled' => env('BROWSERSTACK_PERFORMANCE_ENABLED', false),
        'network_throttling' => false,
        'cpu_throttling' => false,
        'memory_monitoring' => true,
    ],

    'ictserve_specific' => [
        'version' => '3.6.1',
        'technology_stack' => [
            'laravel' => '12.43.1',
            'livewire' => '3.7.3',
            'filament' => '4.3.1',
            'playwright' => '1.56.1',
            'tailwind' => '4.1.18',
        ],
        'hybrid_architecture' => [
            'guest_testing' => true,
            'authenticated_testing' => true,
            'admin_testing' => true,
            'nullable_user_id_fk' => true,
        ],
        'bahasa_melayu_interface' => [
            'exclusive_language' => true,
            'validate_language_consistency' => true,
            'exclude_language_switcher' => true,
        ],
        'wcag_compliance' => [
            'level' => 'AA',
            'version' => '2.2',
            'validation_required' => true,
        ],
    ],

    'timeouts' => [
        'implicit_wait' => 10,
        'page_load' => 30,
        'script' => 30,
        'element_wait' => 10,
    ],

    'retry_config' => [
        'max_retries' => 3,
        'retry_delay' => 2, // seconds
        'exponential_backoff' => true,
    ],

    'automate' => [
        'enabled' => env('BROWSERSTACK_AUTOMATE_ENABLED', false),
        'debug' => env('BROWSERSTACK_AUTOMATE_DEBUG', true),
        'network_logs' => env('BROWSERSTACK_AUTOMATE_NETWORK_LOGS', true),
        'console_logs' => env('BROWSERSTACK_AUTOMATE_CONSOLE_LOGS', 'info'),
        'video' => env('BROWSERSTACK_AUTOMATE_VIDEO', true),
        'local' => env('BROWSERSTACK_AUTOMATE_LOCAL', false),
        'local_identifier' => env('BROWSERSTACK_AUTOMATE_LOCAL_IDENTIFIER'),

        'retry' => [
            'max_retries' => (int) env('BROWSERSTACK_AUTOMATE_MAX_RETRIES', 3),
            'retry_delay' => (int) env('BROWSERSTACK_AUTOMATE_RETRY_DELAY', 2),
            'exponential_backoff' => env('BROWSERSTACK_AUTOMATE_EXPONENTIAL_BACKOFF', true),
        ],

        'timeouts' => [
            'implicit_wait' => (int) env('BROWSERSTACK_AUTOMATE_IMPLICIT_WAIT', 10),
            'page_load' => (int) env('BROWSERSTACK_AUTOMATE_PAGE_LOAD', 30),
            'script' => (int) env('BROWSERSTACK_AUTOMATE_SCRIPT', 30),
            'element_wait' => (int) env('BROWSERSTACK_AUTOMATE_ELEMENT_WAIT', 10),
        ],

        'percy_integration' => [
            'enabled' => env('PERCY_ENABLED', false),
            'snapshot_on_failure' => true,
            'snapshot_on_success' => true,
            'combined_reporting' => true,
        ],

        'failure_analysis' => [
            'enabled' => true,
            'capture_screenshots' => true,
            'capture_logs' => true,
            'generate_debugging_steps' => true,
        ],

        'cross_browser_testing' => [
            'enabled' => true,
            'parallel_execution' => true,
            'max_parallel_sessions' => 5,
            'browsers' => ['Chrome', 'Firefox', 'Safari', 'Edge'],
        ],

        'performance_testing' => [
            'enabled' => env('BROWSERSTACK_PERFORMANCE_ENABLED', false),
            'capture_metrics' => true,
            'visual_validation' => true,
        ],
    ],

    'logging' => [
        'enabled' => env('BROWSERSTACK_LOGGING_ENABLED', true),
        'level' => env('BROWSERSTACK_LOG_LEVEL', 'info'),
        'file' => storage_path('logs/browserstack.log'),
        'include_screenshots' => true,
        'include_network_logs' => true,
    ],
];
