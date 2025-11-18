<?php

declare(strict_types=1);

return [
    'accessibility_compliance' => [
        'label' => 'Accessibility & Compliance',
        'group' => 'System Management',
    ],

    'alert_configuration' => [
        'label' => 'Alert Configuration',
        'title' => 'Alert Configuration',
        'group' => 'System Management',
        'sections' => [
            'tickets' => 'Ticket Alerts',
            'tickets_desc' => 'Configure overdue ticket notifications and thresholds.',
            'loans' => 'Loan Alerts',
            'loans_desc' => 'Manage approval delay and overdue loan notifications.',
            'assets' => 'Asset Alerts',
            'assets_desc' => 'Monitor asset shortages and define critical thresholds.',
            'system' => 'System Health',
            'system_desc' => 'Set response time and system health alert thresholds.',
            'delivery' => 'Delivery Channels',
            'delivery_desc' => 'Choose how alerts are delivered to admins and staff.',
        ],
        'fields' => [
            'overdue_tickets_enabled' => 'Enable overdue ticket alerts',
            'overdue_tickets_threshold' => 'Overdue ticket threshold (days)',
            'overdue_tickets_threshold_help' => 'Days after due date before alerts are triggered.',
            'overdue_loans_enabled' => 'Enable overdue loan alerts',
            'overdue_loans_threshold' => 'Overdue loan threshold (days)',
            'overdue_loans_threshold_help' => 'Days after due date before loan alerts are triggered.',
            'approval_delays_enabled' => 'Enable approval delay alerts',
            'approval_delay_hours' => 'Approval delay threshold (hours)',
            'approval_delay_hours_help' => 'Hours before escalation when approvals are pending.',
            'asset_shortages_enabled' => 'Enable asset shortage alerts',
            'critical_asset_shortage_percentage' => 'Critical asset shortage threshold (%)',
            'critical_asset_shortage_percentage_help' => 'Percentage of available assets before alerts trigger.',
            'system_health_enabled' => 'Enable system health alerts',
            'system_health_threshold' => 'System health threshold (%)',
            'system_health_threshold_help' => 'Minimum health score before alerts trigger.',
            'response_time_threshold' => 'Response time threshold (ms)',
            'response_time_threshold_help' => 'Maximum response time before alerts trigger.',
            'email_notifications_enabled' => 'Send email notifications',
            'admin_panel_notifications_enabled' => 'Show admin panel notifications',
            'alert_frequency' => 'Alert frequency',
        ],
        'frequency' => [
            'immediate' => 'Immediate',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
        ],
        'actions' => [
            'save' => 'Save Settings',
            'test' => 'Send Test Alert',
            'reset' => 'Reset to Default',
        ],
        'modals' => [
            'reset_heading' => 'Reset Alert Configuration',
            'reset_description' => 'Reset all alert settings to the default configuration?',
            'reset_submit' => 'Reset Settings',
            'test_heading' => 'Send Test Alert',
            'test_description' => 'Send a sample alert using the current configuration.',
            'test_submit' => 'Send Test',
        ],
        'notifications' => [
            'saved_title' => 'Alert configuration saved',
            'saved_body' => 'Alert preferences updated successfully.',
            'save_failed_title' => 'Unable to save settings',
            'save_failed_body' => 'Please review the form and try again.',
            'reset_title' => 'Alert configuration reset',
            'reset_body' => 'Settings have been restored to defaults.',
            'reset_failed_title' => 'Reset failed',
            'reset_failed_body' => 'Could not reset settings. Please try again.',
            'test_sent_title' => 'Test alert sent',
            'test_sent_body' => 'A test alert was sent using the current configuration.',
            'test_failed_title' => 'Test alert failed',
            'test_failed_body' => 'Unable to send test alert. Check configuration.',
        ],
    ],

    'approval_matrix' => [
        'label' => 'Approval Matrix',
        'title' => 'Approval Matrix Configuration',
        'group' => 'System Management',
    ],

    'bilingual_management' => [
        'label' => 'Bilingual Management',
        'group' => 'System Management',
        'fields' => [
            'export_format' => 'Export format',
            'import_file' => 'Import file',
        ],
        'actions' => [
            'validate' => 'Validate Translations',
            'export' => 'Export Translations',
            'import' => 'Import Translations',
        ],
        'notifications' => [
            'validation_complete_title' => 'Validation complete',
            'validation_complete_body' => 'No missing or empty translations were found.',
            'validation_issues_title' => 'Translation issues detected',
            'validation_issues_body' => ':missing missing, :empty empty translations found.',
            'export_complete_title' => 'Export ready',
            'export_complete_body' => 'Translations exported to :filename.',
            'import_complete_title' => 'Import successful',
            'import_complete_body' => 'Translations imported successfully.',
            'import_failed_title' => 'Import failed',
            'import_failed_body' => 'Unable to import translations. Please verify the file.',
            'no_file_title' => 'No file selected',
            'no_file_body' => 'Please upload a translation file before importing.',
            'language_changed_title' => 'Language switched',
            'language_changed_body' => 'Interface language changed to :language.',
        ],
    ],

    'email_queue' => [
        'label' => 'Email Queue Monitoring',
        'group' => 'System Management',
    ],

    'email_templates' => [
        'label' => 'Email Template Management',
        'group' => 'System Management',
    ],

    'filter_presets' => [
        'label' => 'Filter Presets',
        'title' => 'Saved Filter Presets',
        'group' => 'Reports',
    ],

    'notification_center' => [
        'label' => 'Notification Center',
        'title' => 'Notification Center',
        'group' => 'System Management',
    ],

    'notification_preferences' => [
        'label' => 'Notification Preferences',
        'title' => 'Notification Preferences',
        'group' => 'System Management',
    ],

    'pdpa_dashboard' => [
        'label' => 'PDPA Dashboard',
    ],

    'performance_monitoring' => [
        'label' => 'Performance Monitoring',
        'group' => 'System Management',
    ],

    'report_builder' => [
        'label' => 'Report Builder',
        'title' => 'Report Builder',
        'group' => 'Reports',
    ],

    'two_factor_auth' => [
        'label' => 'Two-Factor Authentication',
        'group' => 'System Management',
    ],

    'unified_search' => [
        'label' => 'Unified Search',
        'title' => 'Unified Search',
        'group' => 'System Management',
    ],

    'workflow_automation' => [
        'label' => 'Workflow Automation',
        'group' => 'System Management',
    ],
];
