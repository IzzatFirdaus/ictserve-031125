<?php

// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details

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
];
