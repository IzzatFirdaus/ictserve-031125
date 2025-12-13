<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'System Configuration',
        'group' => 'System',
        'title' => 'Superuser Configuration',
    ],

    'actions' => [
        'manage_sla' => 'Manage SLA',
        'manage_email' => 'Email Templates',
        'manage_approval' => 'Approval Matrix',
        'view_audit' => 'Audit Log',
    ],

    'stats' => [
        'sla_categories' => 'SLA Categories',
        'approval_rules' => 'Approval Rules',
        'email_templates' => 'Email Templates',
        'expired_tokens' => 'Expired Tokens',
    ],

    'sections' => [
        'sla' => [
            'title' => 'SLA Configuration',
            'description' => 'Response and resolution time thresholds for helpdesk tickets.',
            'manage' => 'Manage SLA',
            'minutes' => 'min',
            'hours' => 'hrs',
        ],
        'approval' => [
            'title' => 'Approval Workflow',
            'description' => 'Loan application approval matrix and routing rules.',
            'manage' => 'Manage Approval',
            'pending_approvals' => 'Pending Approvals',
            'active_rules' => 'Active Rules',
            'token_validity' => 'Token Validity',
            'hours' => 'hours',
        ],
    ],

    'token_regeneration' => [
        'title' => 'Token Regeneration',
        'description' => 'Regenerate expired approval tokens for loan applications pending supervisor approval.',
        'loan_reference' => 'Select Loan Application',
        'helper' => 'Only loan applications with expired or missing approval tokens are shown.',
        'reason' => 'Reason for Regeneration',
        'reason_helper' => 'Provide a reason for regenerating the token. This will be logged for audit purposes.',
        'regenerate_button' => 'Regenerate Token',
        'regenerating' => 'Regenerating...',
        'note' => 'A new 72-hour approval token will be generated and the approver will be notified.',
        'expired_at' => 'Expired: :date',
        'no_token' => 'No token generated',
    ],

    'recent_changes' => [
        'title' => 'Recent Configuration Changes',
        'view_all' => 'View All',
        'no_changes' => 'No recent configuration changes.',
        'system' => 'System',
    ],

    'guidelines' => [
        'title' => 'Configuration Guidelines',
        'sla' => [
            'title' => 'SLA Thresholds:',
            'description' => 'Configure response and resolution times for each priority level. SLA calculations consider business hours if enabled. Escalation triggers when remaining time falls below the threshold percentage.',
        ],
        'approval' => [
            'title' => 'Approval Workflow:',
            'description' => 'Define approval rules based on asset value, applicant grade, and loan duration. Rules are evaluated in priority order. Auto-approval can be enabled for low-value loans.',
        ],
        'token' => [
            'title' => 'Token Regeneration:',
            'description' => 'Approval tokens expire after 72 hours. Use this feature to regenerate tokens for pending applications. All regeneration actions are logged for audit compliance.',
        ],
        'audit' => [
            'title' => 'Audit Trail:',
            'description' => 'All configuration changes are logged using the dual audit system. Review the unified audit log for compliance reporting and change tracking.',
        ],
    ],

    'notifications' => [
        'select_loan' => 'Please select a loan application.',
        'reason_required' => 'Please provide a reason for token regeneration.',
        'loan_not_found' => 'Loan application not found.',
        'token_regenerated' => 'Token Regenerated Successfully',
        'token_regenerated_body' => 'A new approval token has been generated for :reference. The token will expire on :expires_at.',
        'token_error' => 'Error Regenerating Token',
    ],
];
