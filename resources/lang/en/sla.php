<?php

declare(strict_types=1);

return [
    // SLA Status Labels
    'status' => [
        'ok' => 'On Track',
        'warning' => 'Warning',
        'critical' => 'Critical',
        'breached' => 'Breached',
        'completed' => 'Completed',
    ],

    // SLA Dashboard
    'dashboard' => [
        'title' => 'SLA Monitoring',
        'summary' => 'SLA Summary',
        'total_pending' => 'Total Pending',
        'on_track' => 'On Track',
        'at_risk' => 'At Risk',
        'breached' => 'Breached',
        'compliance_rate' => 'Compliance Rate',
        'hours_elapsed' => 'Hours Elapsed',
        'hours_remaining' => 'Hours Remaining',
        'no_pending' => 'No pending applications',
    ],

    // SLA Thresholds
    'thresholds' => [
        'warning' => '24 hours (1 business day)',
        'critical' => '48 hours (2 business days)',
        'breach' => '72 hours (3 business days)',
    ],

    // SLA Alerts
    'alerts' => [
        'warning_title' => 'SLA Warning',
        'warning_message' => 'This application has been pending for :hours hours. Please review soon.',
        'critical_title' => 'SLA Critical',
        'critical_message' => 'This application is at risk of SLA breach. Only :hours hours remaining.',
        'breached_title' => 'SLA Breached',
        'breached_message' => 'This application has exceeded the SLA threshold of 72 business hours.',
    ],

    // Email Templates
    'email' => [
        'title' => 'SLA Alert - Loan Application Pending Review',
        'subject_warning' => '[Warning] Loan Application :number Requires Attention',
        'subject_critical' => '[Critical] Loan Application :number - SLA At Risk',
        'subject_breached' => '[Urgent] Loan Application :number - SLA Breached',
        'subject_reminder' => 'Reminder: Loan Application :number Pending Review',
        'greeting' => 'Dear :name,',
        'intro' => 'A loan application assigned to you requires your attention.',
        'application_details' => 'Application Details',
        'application_number' => 'Application Number',
        'applicant' => 'Applicant',
        'submitted_at' => 'Submitted At',
        'hours_elapsed' => 'Business Hours Elapsed',
        'hours_remaining' => 'Business Hours Remaining',
        'hours' => 'hours',
        'review_button' => 'Review Application',
        'footer' => 'Please review and take action on this application as soon as possible.',
        'regards' => 'Best regards',
        'warning_notice' => 'This application has been pending for over 24 business hours.',
        'critical_notice' => 'This application is at risk of SLA breach. Immediate action required.',
        'breached_notice' => 'This application has exceeded the 72-hour SLA threshold.',
    ],

    // Tooltip/Help Text
    'help' => [
        'sla_indicator' => 'SLA status indicates how long the application has been pending approval.',
        'business_hours' => 'Business hours are calculated from 8:00 AM to 6:00 PM, excluding weekends.',
    ],

    'form' => [
        'escalation' => [
            'fieldset' => 'Escalation rules',
            'enabled' => 'Enable escalation',
            'threshold_percent' => 'Escalate when remaining time is below (%)',
            'helper' => 'Escalate when the SLA resolution window is mostly consumed.',
            'roles' => [
                'label' => 'Escalation roles',
                'options' => [
                    'admin' => 'Administrator',
                    'superuser' => 'Superuser',
                    'support_manager' => 'Support manager',
                ],
            ],
            'auto_assign' => 'Auto-assign escalated tickets',
        ],
        'notifications' => [
            'fieldset' => 'Notifications',
            'enabled' => 'Enable SLA notifications',
            'warning' => 'Warning interval',
            'critical' => 'Critical interval',
            'overdue' => 'Overdue interval',
            'recipients' => [
                'assignee' => 'Notify assignee',
                'supervisor' => 'Notify supervisor',
                'admin' => 'Notify admin',
            ],
        ],
        'categories' => [
            'suffix' => [
                'minutes' => 'minutes',
            ],
        ],
        'business_hours' => [
            'fieldset' => 'Business hours',
            'enabled' => 'Use business hours',
            'timezone' => 'Timezone',
            'timezones' => [
                'Asia/Kuala_Lumpur' => 'Kuala Lumpur (GMT+8)',
                'UTC' => 'UTC',
            ],
            'start' => 'Start time',
            'end' => 'End time',
            'working_days' => 'Working days',
            'days' => [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                0 => 'Sunday',
            ],
            'exclude_weekends' => 'Exclude weekends',
            'exclude_holidays' => 'Exclude public holidays',
        ],
    ],

    'actions' => [
        'save' => 'Save settings',
        'test' => 'Test SLA logic',
        'reset' => 'Reset to defaults',
        'export' => 'Export thresholds',
        'import' => 'Import thresholds',
    ],

    'notifications' => [
        'save_success' => 'SLA thresholds saved successfully.',
        'save_error' => 'Failed to save SLA thresholds.',
        'reset_success' => 'SLA thresholds reset to defaults.',
        'import_success' => 'SLA thresholds imported successfully.',
        'import_error' => 'Failed to import SLA thresholds.',
        'test_title' => 'SLA tests executed',
        'test_body' => 'Generated :count SLA test scenarios.',
    ],

    'upload' => [
        'label' => 'Upload SLA JSON',
        'invalid' => 'The uploaded file is not a valid SLA JSON.',
    ],

    'modals' => [
        'reset' => [
            'heading' => 'Reset SLA thresholds?',
            'description' => 'This will revert all SLA settings to their defaults.',
        ],
    ],

    'navigation' => [
        'label' => 'SLA Thresholds',
        'group' => 'Configuration',
        'title' => 'SLA Threshold Management',
    ],
];
