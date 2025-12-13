<?php

declare(strict_types=1);

return [
    'stats' => [
        'sla_categories' => 'SLA Categories',
        'approval_rules' => 'Approval Rules',
        'email_templates' => 'Email Templates',
        'expired_tokens' => 'Expired Tokens',
    ],

    'sections' => [
        'sla' => [
            'title' => 'SLA Configuration',
            'manage' => 'Manage SLA',
            'description' => 'View current SLA response and resolution times across all priorities.',
            'minutes' => 'minutes',
            'hours' => 'hours',
        ],
        'approval' => [
            'title' => 'Approval Workflow',
            'manage' => 'Manage Approval Matrix',
            'description' => 'Review approval rules, token validity, and pending approvals.',
            'pending_approvals' => 'Pending approvals',
            'active_rules' => 'Active rules',
            'token_validity' => 'Token validity',
            'hours' => 'hours',
        ],
    ],

    'token_regeneration' => [
        'title' => 'Regenerate Approval Token',
        'description' => 'Regenerate approval tokens for loans stuck due to expired tokens.',
        'loan_reference' => 'Loan reference',
        'helper' => 'Select a loan with an expired or missing approval token.',
        'reason' => 'Reason for regeneration',
        'reason_helper' => 'Provide a justification (e.g. approver requested new link).',
        'regenerate_button' => 'Regenerate token',
        'regenerating' => 'Regenerating...',
        'note' => 'Tokens are valid for 72 hours. Regeneration is logged to the audit trail.',
        'expired_at' => 'Expired at :date',
        'no_token' => 'No token',
    ],

    'notifications' => [
        'select_loan' => 'Please select a loan to regenerate a token.',
        'reason_required' => 'Please provide a reason for regeneration.',
        'token_regenerated' => 'Approval token regenerated successfully.',
        'token_regenerated_body' => 'Loan :reference now has a valid token until :expires_at.',
        'loan_not_found' => 'Loan not found. Please refresh the list.',
        'token_error' => 'Failed to regenerate token.',
    ],

    'actions' => [
        'manage_sla' => 'Manage SLA Thresholds',
        'manage_email' => 'Manage Email Templates',
        'manage_approval' => 'Manage Approval Matrix',
        'view_audit' => 'View Audit Log',
    ],

    'recent_changes' => [
        'title' => 'Recent Configuration Changes',
        'view_all' => 'View audit history',
        'no_changes' => 'No recent configuration changes.',
        'system' => 'System',
    ],

    'guidelines' => [
        'title' => 'Configuration Guidelines',
        'sla' => [
            'title' => 'SLA Thresholds',
            'description' => 'Ensure response and resolution times align with ministry SLAs. Update escalation and notifications when thresholds change.',
        ],
        'approval' => [
            'title' => 'Approval Matrix',
            'description' => 'Keep approver roles current. Token validity defaults to 72 hours for supervisor approvals.',
        ],
        'token' => [
            'title' => 'Token Regeneration',
            'description' => 'Only regenerate tokens when genuinely required. Every action is logged for audit purposes.',
        ],
        'audit' => [
            'title' => 'Audit & Logs',
            'description' => 'Review the Unified Audit Log after major configuration updates to verify recorded actions.',
        ],
    ],

    'navigation' => [
        'label' => 'Superuser Config',
        'group' => 'Configuration',
        'title' => 'Superuser Configuration',
    ],
];
