<?php

declare(strict_types=1);

return [
    'nav' => [
        'dashboard' => 'Dashboard',
        'helpdesk' => 'Helpdesk',
        'loans' => 'Loans',
        'profile' => 'Profile',
        'logout' => 'Log out',
        'user_menu' => 'User menu',
        'menu' => 'Portal menu',
    ],
    'dashboard' => [
        'title' => 'Staff Portal Dashboard',
        'subtitle' => 'Track your tickets, loan requests, and approvals in one place.',
        'open_tickets' => 'Open tickets',
        'active_loans' => 'Active loans',
        'pending_approvals' => 'Pending approvals',
        'resolved_this_month' => 'Resolved this month',
        'recent_tickets' => 'Recent tickets',
        'recent_loans' => 'Recent loan applications',
        'no_recent_tickets' => 'No recent ticket activity.',
        'no_recent_loans' => 'No recent loan activity.',
        'unknown_category' => 'Unknown category',
        'unknown_division' => 'Unknown division',
        'unknown_asset' => 'Unknown asset',
        'no_purpose' => 'No purpose provided.',
        'loan_period' => 'Loan period: :start - :end',
    ],
    'profile' => [
        'subtitle' => 'Manage your account details and security preferences.',
    ],
    'claims' => [
        'title' => 'Claim Guest Submission',
        'subtitle' => 'Link guest helpdesk tickets or loan applications to your staff account.',
        'email_label' => 'Your MOTAC email address',
        'email_help' => 'This must match the email used when the submission was created.',
        'type_label' => 'Submission type',
        'type_ticket' => 'Helpdesk ticket',
        'type_loan' => 'Loan application',
        'id_label' => 'Submission reference number',
        'id_help' => 'You can find this in the confirmation email or tracking page.',
        'submit_button' => 'Claim submission',
        'info_heading' => 'How claiming works',
        'info_description' => 'Use this tool to convert guest submissions into staff-managed records.',
        'info_step_ticket' => 'Claim helpdesk tickets that were filed as a guest using your email address.',
        'info_step_loan' => 'Claim guest asset loan applications that are pending approval or processing.',
    ],
];
