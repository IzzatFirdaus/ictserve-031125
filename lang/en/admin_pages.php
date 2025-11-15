<?php

declare(strict_types=1);

return [
    'email_templates' => [
        'title' => 'Email Template Management',
        'label' => 'Email Templates',
        'group' => 'Email Management',
    ],

    'notification_center' => [
        'title' => 'Notification Center',
        'label' => 'Notifications',
        'group' => 'System',
    ],

    'notification_preferences' => [
        'title' => 'Notification Preferences',
        'label' => 'Notification Preferences',
        'group' => 'User Settings',
    ],

    'pdpa_dashboard' => [
        'title' => 'PDPA Dashboard',
        'label' => 'PDPA Dashboard',
        'group' => 'Compliance',
    ],

    'performance_monitoring' => [
        'title' => 'Performance Monitoring',
        'label' => 'Performance Monitoring',
        'group' => 'System',
    ],

    'report_builder' => [
        'title' => 'Report Builder',
        'label' => 'Report Builder',
        'group' => 'Reports',
    ],

    'security_monitoring' => [
        'title' => 'Security Monitoring',
        'label' => 'Security Monitoring',
        'group' => 'Security',
    ],

    'filter_presets' => [
        'title' => 'Filter Presets',
        'label' => 'Filter Presets',
        'group' => 'User Settings',
    ],

    'email_queue' => [
        'title' => 'Email Queue Monitoring',
        'label' => 'Email Queue Monitoring',
        'group' => 'Email Management',
    ],

    'bilingual_management' => [
        'title' => 'Language Management',
        'label' => 'Language Management',
        'group' => 'System Configuration',
        'fields' => [
            'export_format' => 'Export Format',
            'import_file' => 'Import File',
        ],
        'actions' => [
            'validate' => 'Validate Translations',
            'export' => 'Export Translations',
            'import' => 'Import Translations',
        ],
        'notifications' => [
            'validation_complete_title' => 'Translation Validation Completed',
            'validation_complete_body' => 'No issues found. All translations are complete.',
            'validation_issues_title' => 'Translation Issues Found',
            'validation_issues_body' => 'Missing translations: :missing, Empty translations: :empty',
            'export_complete_title' => 'Export Completed',
            'export_complete_body' => 'Translations exported as :filename',
            'no_file_title' => 'No File Selected',
            'no_file_body' => 'Please select a file to import',
            'import_complete_title' => 'Import Completed',
            'import_complete_body' => 'Translations imported successfully',
            'import_failed_title' => 'Import Failed',
            'import_failed_body' => 'Failed to import translations. Please check the file format.',
            'language_changed_title' => 'Language Changed',
            'language_changed_body' => 'Interface language changed to :language',
        ],
    ],

    'approval_matrix' => [
        'title' => 'Approval Matrix Configuration',
        'label' => 'Approval Matrix',
        'group' => 'System Configuration',
    ],

    'accessibility_compliance' => [
        'title' => 'Accessibility Compliance',
        'label' => 'Accessibility Compliance',
        'group' => 'Compliance',
    ],

    'unified_search' => [
        'title' => 'Global Search',
        'label' => 'Global Search',
        'group' => 'System',
    ],

    'workflow_automation' => [
        'title' => 'Workflow Automation Configuration',
        'label' => 'Workflow Automation',
        'group' => 'System Configuration',
    ],

    'two_factor_auth' => [
        'title' => '2FA Management',
        'label' => '2FA Management',
        'group' => 'Security',
    ],

    'alert_configuration' => [
        'title' => 'Alert System Configuration',
        'label' => 'Alert Configuration',
        'group' => 'System',
        'sections' => [
            'tickets' => 'Ticket Alert Configuration',
            'tickets_desc' => 'Set thresholds and configuration for helpdesk ticket alerts',
            'loans' => 'Loan Alert Configuration',
            'loans_desc' => 'Set thresholds and configuration for asset loan alerts',
            'assets' => 'Asset Alert Configuration',
            'assets_desc' => 'Set thresholds and configuration for asset and inventory alerts',
            'system' => 'System Alert Configuration',
            'system_desc' => 'Set thresholds for overall system health alerts',
            'delivery' => 'Delivery Configuration',
            'delivery_desc' => 'Set alert delivery methods and frequency',
        ],
        'fields' => [
            'overdue_tickets_enabled' => 'Enable Overdue Ticket Alerts',
            'overdue_tickets_threshold' => 'Overdue Tickets Threshold',
            'overdue_tickets_threshold_help' => 'Number of overdue tickets before alert is sent',
            'overdue_loans_enabled' => 'Enable Overdue Loan Alerts',
            'overdue_loans_threshold' => 'Overdue Loans Threshold',
            'overdue_loans_threshold_help' => 'Number of overdue loans before alert is sent',
            'approval_delays_enabled' => 'Enable Approval Delay Alerts',
            'approval_delay_hours' => 'Approval Delay Threshold (Hours)',
            'approval_delay_hours_help' => 'Number of hours before approval delay alert is sent',
            'asset_shortages_enabled' => 'Enable Asset Shortage Alerts',
            'critical_asset_shortage_percentage' => 'Critical Asset Shortage Threshold (%)',
            'critical_asset_shortage_percentage_help' => 'Minimum availability percentage before alert is sent',
            'system_health_enabled' => 'Enable System Health Alerts',
            'system_health_threshold' => 'System Health Score Threshold (%)',
            'system_health_threshold_help' => 'Minimum health score before alert is sent',
            'response_time_threshold' => 'Response Time Threshold (Seconds)',
            'response_time_threshold_help' => 'Maximum response time before performance alert is sent',
            'email_notifications_enabled' => 'Enable Email Notifications',
            'admin_panel_notifications_enabled' => 'Enable Admin Panel Notifications',
            'alert_frequency' => 'Alert Check Frequency',
        ],
        'frequency' => [
            'immediate' => 'Immediate (Real-time)',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
        ],
        'actions' => [
            'save' => 'Save Configuration',
            'test' => 'Test Alerts',
            'reset' => 'Reset to Defaults',
        ],
        'modals' => [
            'test_heading' => 'Test Alert System',
            'test_description' => 'Are you sure you want to test the alert system? This will send test alerts to configured recipients.',
            'test_submit' => 'Yes, Test Alerts',
            'reset_heading' => 'Reset Configuration',
            'reset_description' => 'Are you sure you want to reset all configurations to default values?',
            'reset_submit' => 'Yes, Reset',
        ],
        'notifications' => [
            'saved_title' => 'Configuration Saved',
            'saved_body' => 'Alert configuration has been successfully saved and will take effect immediately.',
            'save_failed_title' => 'Save Failed',
            'save_failed_body' => 'Error while saving configuration: :error',
            'test_sent_title' => 'Test Alert Sent',
            'test_sent_body' => 'Test alert has been sent to all configured recipients. Check email and admin panel to confirm receipt.',
            'test_failed_title' => 'Test Failed',
            'test_failed_body' => 'Error while testing alerts: :error',
            'reset_title' => 'Configuration Reset',
            'reset_body' => 'All alert configurations have been reset to default values.',
            'reset_failed_title' => 'Reset Failed',
            'reset_failed_body' => 'Error while resetting configuration: :error',
        ],
    ],
];
