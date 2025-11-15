<?php

declare(strict_types=1);

return [
    'navigation' => [
        'title' => 'SLA Threshold Management',
        'label' => 'SLA Thresholds',
        'group' => 'System Configuration',
    ],

    'form' => [
        'categories' => [
            'label' => 'SLA Categories',
            'fieldset' => 'Category Details',
            'name' => 'Category Name',
            'description' => 'Description',
            'response_fieldset' => 'Response Time (Hours)',
            'resolution_fieldset' => 'Resolution Time (Hours)',
            'new_label' => 'New Category',
            'add_action' => 'Add Category',
            'suffix' => [
                'hours' => 'hours',
                'minutes' => 'minutes',
            ],
            'levels' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
        ],

        'escalation' => [
            'fieldset' => 'Escalation Configuration',
            'enabled' => 'Enable Automatic Escalation',
            'threshold_percent' => 'Escalation Threshold (%)',
            'helper' => 'Escalation occurs when remaining time is less than this percentage',
            'roles' => [
                'label' => 'Roles for Escalation',
                'options' => [
                    'admin' => 'Administrator',
                    'superuser' => 'Superuser',
                ],
            ],
            'auto_assign' => 'Automatically assign to approvers',
        ],

        'notifications' => [
            'fieldset' => 'Notification Configuration',
            'enabled' => 'Enable SLA Notifications',
            'warning' => 'Warning (Minutes before breach)',
            'critical' => 'Critical (Minutes before breach)',
            'overdue' => 'Overdue (Notification interval)',
            'recipients' => [
                'assignee' => 'Notify assignee',
                'supervisor' => 'Notify supervisor',
                'admin' => 'Notify administrator',
            ],
        ],

        'business_hours' => [
            'fieldset' => 'Business Hours',
            'enabled' => 'Enable Business Hours',
            'timezone' => 'Timezone',
            'start' => 'Start Time',
            'end' => 'End Time',
            'working_days' => 'Working Days',
            'days' => [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',
            ],
            'exclude_weekends' => 'Exclude Weekends',
            'exclude_holidays' => 'Exclude Public Holidays',
            'timezones' => [
                'Asia/Kuala_Lumpur' => 'Asia/Kuala Lumpur (MYT)',
                'Asia/Singapore' => 'Asia/Singapore (SGT)',
                'UTC' => 'UTC',
            ],
        ],
    ],

    'actions' => [
        'save' => 'Save Configuration',
        'test' => 'Test SLA',
        'reset' => 'Reset to Default',
        'export' => 'Export',
        'import' => 'Import',
    ],

    'modals' => [
        'reset' => [
            'heading' => 'Reset SLA Thresholds',
            'description' => 'Are you sure you want to reset SLA thresholds to the default configuration? All changes will be lost.',
        ],
    ],

    'notifications' => [
        'save_success' => 'SLA thresholds updated successfully',
        'save_error' => 'Error saving configuration',
        'reset_success' => 'SLA thresholds have been reset',
        'import_success' => 'SLA thresholds imported successfully',
        'import_error' => 'Error importing SLA thresholds',
        'test_title' => 'SLA test completed',
        'test_body' => 'All :count test cases have been executed',
    ],

    'upload' => [
        'label' => 'JSON File',
        'invalid' => 'Invalid JSON file',
    ],
];
