<?php

declare(strict_types=1);

return [
    'navigation' => [
        'operations' => 'Operations',
        'inventory' => 'Inventory',
        'management' => 'Management',
        'system' => 'System',
        'asset_management' => 'Asset Management',
        'helpdesk_management' => 'Helpdesk',
        'loan_management' => 'Asset Loans',
        'reference_data' => 'Reference Data',
        'system_management' => 'System Management',
        'user_management' => 'User Management',
        'reports' => 'Reports & Analytics',
        'go_to_portal' => 'Go to Portal',
    ],

    'labels' => [
        'application_number' => 'Application No.',
        'form_reference' => 'Form Reference',
        'applicant' => 'Applicant',
        'division' => 'Division',
        'status' => 'Status',
        'priority' => 'Priority',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'overdue_status' => 'Overdue Status',
        'total_value' => 'Total Value',
        'maintenance_required' => 'Maintenance Required',
        'approval_status' => 'Approval Status',
        'submission_type' => 'Submission Type',
        'responsible_officer' => 'Responsible Officer',
        'created_from' => 'Created From',
        'created_until' => 'Created Until',
        'asset_type' => 'Asset Type',
        'category' => 'Category',
        'approval_method' => 'Approval Method',
        'submission_type_filter' => 'Submission Type',
    ],

    'status' => [
        'overdue_days' => ':count days overdue',
        'due_soon' => 'Due soon',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'pending' => 'Pending',
        'not_submitted' => 'Not Submitted',
        'applicant_is_responsible' => 'Applicant Responsible',
        'different_officer' => 'Different Officer: :name',
    ],

    'tooltips' => [
        'approval_approved' => 'Approval 1: :name1 | Approval 2: :name2',
        'approval_rejected' => 'Rejected by: :name',
        'approval_pending' => 'Pending approval',
        'approval_not_submitted' => 'Not submitted for approval',
        'applicant_responsible' => 'Applicant is responsible',
        'different_responsible_officer' => 'Different officer responsible',
    ],

    'date_filters' => [
        'select_start_date' => 'Select start date',
        'select_end_date' => 'Select end date',
        'from_date' => 'From Date',
        'until_date' => 'Until Date',
        'category_filter' => 'Filter by category',
    ],

    'asset_categories' => [
        'computer' => 'Computer',
        'laptop' => 'Laptop',
        'printer' => 'Printer',
        'projector' => 'Projector',
        'camera' => 'Camera',
        'other' => 'Other',
    ],

    'filters' => [
        'pending_approval' => 'Pending Approval',
        'approval_indicator' => 'Approval Indicator',
        'approved' => 'Approved',
        'overdue' => 'Overdue',
        'overdue_indicator' => 'Overdue Indicator',
        'guest_submission' => 'Guest',
        'authenticated_submission' => 'Authenticated User',
        'email_approval' => 'Email',
        'portal_approval' => 'Portal',
    ],

    'actions' => [
        'send_for_approval' => 'Send for Approval',
        'approve' => 'Approve',
        'approval_remarks' => 'Approval Remarks',
        'decline' => 'Decline',
        'rejection_reason' => 'Rejection Reason',
        'extend' => 'Extend',
        'new_date' => 'New Date',
        'instructions' => 'Instructions',
        'export_pdf' => 'Export PDF',
        'export_excel' => 'Export Excel',
        'export_report' => 'Export Report',
        'reason' => 'Reason',
    ],
];
