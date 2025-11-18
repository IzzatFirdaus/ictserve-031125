<?php

return [
    // Navigation Groups
    'navigation' => [
        'asset_management' => 'Asset Management',
        'helpdesk_management' => 'Helpdesk Management',
        'loan_management' => 'Loan Management',
        'user_management' => 'User Management',
        'system_management' => 'System Management',
        'reports' => 'Reports',
        'reference_data' => 'Reference Data',
    ],

    // Common Labels
    'labels' => [
        'tag' => 'Tag',
        'name' => 'Name',
        'brand' => 'Brand',
        'model' => 'Model',
        'serial_number' => 'Serial No.',
        'category' => 'Category',
        'status' => 'Status',
        'condition' => 'Condition',
        'location' => 'Location',
        'purchase_date' => 'Purchase',
        'current_value' => 'Current Value',
        'next_maintenance_date' => 'Next Maintenance',
        'warranty_expiry' => 'Warranty',
        'age' => 'Age',
        'priority' => 'Priority',
        'division' => 'Division',
        'applicant' => 'Applicant',
        'application_number' => 'Application No.',
        'start_date' => 'Start',
        'end_date' => 'End',
        'overdue_status' => 'Overdue Status',
        'total_value' => 'Value (RM)',
        'maintenance_required' => 'Maintenance',
        'approval_status' => 'Approval Status',
        'submission_type' => 'Type',
        'approval_method' => 'Approval Method',
        'created_from' => 'From Date',
        'created_until' => 'Until Date',
        'asset_type' => 'Asset Type',
        'submission_type_filter' => 'Submission Type',
    ],

    // Asset Form Sections
    'asset_form' => [
        'asset_info' => 'Asset Information',
        'financial_info' => 'Financial Information',
        'maintenance_attachments' => 'Maintenance & Attachments',
        'asset_tag' => 'Asset Tag',
        'purchase_value' => 'Purchase Value (RM)',
        'current_value' => 'Current Value (RM)',
        'warranty_expiry' => 'Warranty Expiry',
        'last_maintenance' => 'Last Maintenance',
        'next_maintenance' => 'Next Maintenance',
        'specifications' => 'Specifications',
        'accessories' => 'Accessories',
        'parameter' => 'Parameter',
        'details' => 'Details',
        'accessory' => 'Accessory',
        'quantity_notes' => 'Quantity / Notes',
        'additional_notes' => 'Additional Notes',
        'serial_number' => 'Serial Number',
        'purchase_date' => 'Purchase Date',
    ],

    // Actions
    'actions' => [
        'mark_maintenance' => 'Mark Maintenance',
        'update_status' => 'Update Status',
        'update_condition' => 'Update Condition',
        'update_location' => 'Update Location',
        'new_location' => 'New Location',
        'export' => 'Export',
        'send_for_approval' => 'Send for Approval',
        'approve' => 'Approve',
        'decline' => 'Decline',
        'extend' => 'Extend',
        'approval_remarks' => 'Approval Remarks',
        'rejection_reason' => 'Rejection Reason',
        'new_date' => 'New Date',
        'instructions' => 'Instructions',
        'reason' => 'Reason',
    ],

    // Filters
    'filters' => [
        'needs_maintenance' => 'Needs Maintenance',
        'available' => 'Available',
        'in_use' => 'In Use',
        'warranty_expiring' => 'Warranty Expiring',
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'overdue' => 'Overdue',
        'guest_submission' => 'Guest',
        'authenticated_submission' => 'Authenticated',
        'email_approval' => 'Email',
        'portal_approval' => 'Portal',
        'maintenance_indicator' => 'Maintenance',
        'approval_indicator' => 'Approval',
        'overdue_indicator' => 'Overdue',
    ],

    // Status Messages
    'status' => [
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'pending' => 'Pending',
        'not_submitted' => 'Not Submitted',
        'overdue_days' => 'Overdue :days days',
        'due_soon' => 'Due soon (:days days)',
        'no_maintenance_schedule' => 'No maintenance schedule',
        'overdue_maintenance' => 'Overdue :days days',
        'due_today' => 'Today',
        'due_in_days' => 'In :days days',
        'no_warranty' => 'No warranty',
        'warranty_expired' => 'Warranty expired',
        'warranty_expires_in' => 'Expires in :time',
        'purchased_on' => 'Purchased: :date',
    ],

    // Tooltips
    'tooltips' => [
        'approval_approved' => 'Approved: :date\nBy: :approver\nMethod: :method',
        'approval_rejected' => 'Rejected: :reason',
        'approval_pending' => 'Token sent to: :email\nExpires: :expires',
        'approval_not_submitted' => 'Not submitted for approval',
        'warranty_expired_on' => 'Warranty expired on :date',
        'warranty_expires_on' => 'Warranty expires on :date (:time)',
        'maintenance_next' => 'Next maintenance :date - :status',
    ],

    // Notifications
    'notifications' => [
        'status_updated' => 'Status Updated',
        'condition_updated' => 'Condition Updated',
        'location_updated' => 'Location Updated',
        'assets_updated' => ':count assets updated.',
        'assets_updated_simple' => 'Assets updated.',
    ],

    // Asset Categories
    'asset_categories' => [
        'computer' => 'Computer',
        'laptop' => 'Laptop',
        'printer' => 'Printer',
        'projector' => 'Projector',
        'camera' => 'Camera',
        'other' => 'Other',
    ],

    // Date Filters
    'date_filters' => [
        'from_date' => 'From: :date',
        'until_date' => 'Until: :date',
        'category_filter' => 'Category: :categories',
        'select_start_date' => 'Select start date',
        'select_end_date' => 'Select end date',
    ],
];
