<?php

// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details

declare(strict_types=1);

return [
    // Page titles and descriptions
    'title' => 'Approval Delegations',
    'description' => 'Manage temporary delegation of your approval authority to other approvers.',

    // Actions
    'create_delegation' => 'Create Delegation',
    'create' => 'Create Delegation',
    'create_first' => 'Create Your First Delegation',
    'deactivate' => 'Deactivate',
    'confirm_deactivate' => 'Confirm Deactivation',
    'confirm_deactivate_message' => 'Are you sure you want to deactivate this delegation? The delegated approver will no longer be able to approve on your behalf.',

    // Labels
    'delegated_to' => 'Delegated To',
    'delegated_to_me' => 'Delegated To Me',
    'delegated_approver' => 'Delegated Approver',
    'select_approver' => 'Select an approver...',
    'my_delegations' => 'My Delegations',
    'period' => 'Period',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'reason' => 'Reason',
    'reason_placeholder' => 'e.g., Annual leave from 1st to 15th January',
    'reason_help' => 'Provide a brief reason for this delegation (10-500 characters).',
    'status' => 'Status',
    'actions' => 'Actions',
    'from' => 'From',
    'to' => 'to',

    // Status labels
    'active' => 'Active',
    'upcoming' => 'Upcoming',
    'expired' => 'Expired',
    'inactive' => 'Inactive',

    // Filters
    'filter_by_status' => 'Filter by status',
    'filter_all' => 'All',
    'filter_active' => 'Active',
    'filter_upcoming' => 'Upcoming',
    'filter_expired' => 'Expired',
    'filter_inactive' => 'Inactive',

    // Empty states
    'no_delegations' => 'No Delegations',
    'no_delegations_description' => 'You haven\'t created any delegations yet. Create one when you need someone to approve on your behalf.',

    // Success messages
    'created_successfully' => 'Delegation created successfully.',
    'deactivated_successfully' => 'Delegation deactivated successfully.',

    // Approval interface
    'manage_delegations' => 'Manage Delegations',
    'delegated_to_me_info' => 'You are currently receiving delegated approvals from :count approver(s).',
    'on_behalf_of' => 'On behalf of',

    // Error messages
    'creation_failed' => 'Failed to create delegation. Please try again.',
    'deactivation_failed' => 'Failed to deactivate delegation. Please try again.',

    // Validation errors
    'error' => [
        'start_before_end' => 'Start date must be before end date.',
        'start_not_past' => 'Start date cannot be in the past.',
        'original_not_found' => 'Original approver not found.',
        'delegated_not_found' => 'Delegated approver not found.',
        'original_not_approver' => 'Original user must have approver role.',
        'delegated_not_approver' => 'Delegated user must have approver role.',
        'same_user' => 'Cannot delegate to yourself.',
        'overlap' => 'Delegation period overlaps with an existing active delegation.',
    ],

    // Validation messages
    'validation' => [
        'approver_required' => 'Please select a delegated approver.',
        'approver_not_found' => 'Selected approver does not exist.',
        'start_required' => 'Start date is required.',
        'start_not_past' => 'Start date cannot be in the past.',
        'end_required' => 'End date is required.',
        'end_after_start' => 'End date must be after start date.',
        'reason_required' => 'Reason for delegation is required.',
        'reason_min' => 'Reason must be at least 10 characters.',
        'reason_max' => 'Reason cannot exceed 500 characters.',
    ],
];
