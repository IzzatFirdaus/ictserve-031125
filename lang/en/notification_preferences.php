<?php

declare(strict_types=1);

/**
 * En - Notification Preferences
 */

return [
    'delivery_methods' => 'Delivery Methods',
    'choose_how_receive' => 'Choose how you want to receive notifications',

    'email_notifications' => 'Email Notifications',
    'receive_via_email' => 'Receive notifications via email',

    'in_app_notifications' => 'In-App Notifications',
    'show_in_admin_panel' => 'Show notifications in the admin panel',

    'sms_notifications' => 'SMS Notifications',
    'receive_via_sms' => 'Receive critical notifications via SMS',

    'desktop_notifications' => 'Desktop Notifications',
    'show_desktop_notifications' => 'Show browser desktop notifications',

    'helpdesk_section' => 'Helpdesk Notifications',
    'helpdesk_desc' => 'Configure helpdesk-related notifications',
    'notification_types' => 'Notification Types',
    'ticket_assigned' => 'Ticket Assigned to Me',
    'ticket_status_changed' => 'Ticket Status Changes',
    'sla_breach' => 'SLA Breach Alerts',
    'overdue_tickets' => 'Overdue Ticket Reminders',
    'new_comments' => 'New Comments on My Tickets',
    'escalation_alerts' => 'Ticket Escalation Alerts',

    'loan_section' => 'Asset Loan Notifications',
    'loan_desc' => 'Configure asset loan-related notifications',
    'new_loan_applications' => 'New Loan Applications',
    'application_approved' => 'Application Approvals',
    'application_rejected' => 'Application Rejections',
    'asset_overdue' => 'Overdue Asset Alerts',
    'return_reminder' => 'Return Reminders',
    'damage_reports' => 'Asset Damage Reports',

    'security_section' => 'Security Notifications',
    'security_desc' => 'Configure security-related notifications',
    'security_incidents' => 'Security Incidents',
    'failed_logins' => 'Failed Login Attempts',
    'role_changes' => 'User Role Changes',
    'config_changes' => 'System Configuration Changes',
    'suspicious_activity' => 'Suspicious Activity Alerts',
    'audit_alerts' => 'Audit Trail Alerts',

    'system_section' => 'System Notifications',
    'system_desc' => 'Configure system-related notifications',
    'maintenance_alerts' => 'Maintenance Alerts',
    'performance_alerts' => 'Performance Alerts',
    'backup_status' => 'Backup Status Updates',
    'update_notifications' => 'System Update Notifications',
    'integration_alerts' => 'Integration Failure Alerts',
    'queue_alerts' => 'Queue Processing Alerts',

    'frequency_section' => 'Frequency & Timing',
    'frequency_desc' => 'Configure when and how often you receive notifications',
    'digest_frequency' => 'Digest Frequency',
    'digest_immediate' => 'Immediate (Real-time)',
    'digest_hourly' => 'Hourly Digest',
    'digest_daily' => 'Daily Digest',
    'digest_weekly' => 'Weekly Digest',
    'enable_quiet_hours' => 'Enable Quiet Hours',
    'quiet_hours_start' => 'Quiet Hours Start',
    'quiet_hours_end' => 'Quiet Hours End',
    'weekend_notifications' => 'Weekend Notifications',

    'priority_section' => 'Priority Settings',
    'priority_desc' => 'Configure notification priority filtering',
    'urgent_only_mode' => 'Urgent Only Mode',
    'priority_threshold' => 'Minimum Priority Level',
    'only_receive_notifications_at_or_above_this_priority_level' => 'Only receive notifications at or above this priority level',
    'low_and_above' => 'Low and above',
    'medium_and_above' => 'Medium and above',
    'high_and_above' => 'High and above',
    'urgent_only' => 'Urgent only',

    'save_preferences' => 'Save Preferences',
    'reset_to_defaults' => 'Reset to Defaults',
    'reset_modal_heading' => 'Reset Notification Preferences',
    'reset_modal_desc' => 'Are you sure you want to reset all notification preferences to their default values?',
    'test_notifications' => 'Test Notifications',

    'preferences_saved' => 'Notification preferences saved successfully.',
    'preferences_reset' => 'Notification preferences reset to defaults.',
    'test_notifications_sent' => 'Test notifications sent successfully. Please check your configured delivery methods.',
];
