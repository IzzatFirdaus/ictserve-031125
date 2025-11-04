<?php

declare(strict_types=1);

/**
 * English Asset Loan Module Translations
 *
 * Asset loan application, approval, and management
 *
 * @requirements 1.4, 1.5, 12.1-12.5, 15.2
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 */

return [
    // Page Titles
    'submit_application' => 'Submit Asset Loan Application',
    'application_details' => 'Application Details',
    'my_applications' => 'My Applications',
    'application_list' => 'Application List',
    'asset_catalog' => 'Asset Catalog',

    // Form Labels
    'applicant_name' => 'Applicant Name',
    'applicant_email' => 'Email Address',
    'applicant_phone' => 'Phone Number',
    'staff_id' => 'Staff ID (Optional)',
    'grade' => 'Grade',
    'division' => 'Division',
    'asset_selection' => 'Asset Selection',
    'loan_purpose' => 'Loan Purpose',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'loan_period' => 'Loan Period',

    // Help Text
    'name_help' => 'Enter your full name as per MOTAC records',
    'email_help' => 'Approval notifications will be sent to this email',
    'phone_help' => 'Contact number for coordination',
    'staff_id_help' => 'Your MOTAC staff ID if applicable',
    'grade_help' => 'Your current grade level',
    'division_help' => 'Your division within MOTAC',
    'asset_help' => 'Select the asset you wish to borrow',
    'purpose_help' => 'Explain the purpose of borrowing this asset (minimum 10 characters)',
    'start_date_help' => 'When do you need the asset?',
    'end_date_help' => 'When will you return the asset?',

    // Messages
    'application_submitted' => 'Your loan application has been submitted successfully',
    'application_number' => 'Application Number',
    'approval_pending' => 'Your application is pending approval',
    'approval_email_sent' => 'Approval request has been sent to the approving officer',
    'updates_via_email' => 'You will receive updates via email',

    // Application Status
    'application_status' => 'Application Status',
    'status_pending_approval' => 'Pending Approval',
    'status_approved' => 'Approved',
    'status_rejected' => 'Rejected',
    'status_active' => 'Active Loan',
    'status_returned' => 'Returned',
    'status_overdue' => 'Overdue',

    // Asset Information
    'asset_name' => 'Asset Name',
    'asset_category' => 'Category',
    'asset_model' => 'Model',
    'asset_serial' => 'Serial Number',
    'asset_condition' => 'Condition',
    'condition_before' => 'Condition Before Loan',
    'condition_after' => 'Condition After Loan',
    'asset_availability' => 'Availability',
    'available' => 'Available',
    'unavailable' => 'Unavailable',
    'booked' => 'Booked',

    // Approval
    'approver' => 'Approving Officer',
    'approval_method' => 'Approval Method',
    'approval_email' => 'Email-based Approval',
    'approval_portal' => 'Portal-based Approval',
    'approval_remarks' => 'Approval Remarks',
    'approved_by' => 'Approved By',
    'approved_at' => 'Approved At',
    'rejected_by' => 'Rejected By',
    'rejected_at' => 'Rejected At',

    // Actions
    'submit_application_button' => 'Submit Application',
    'clear_form' => 'Clear Form',
    'view_application' => 'View Application',
    'check_availability' => 'Check Availability',
    'browse_assets' => 'Browse Assets',

    // Validation
    'name_required' => 'Applicant name is required',
    'email_required' => 'Email address is required',
    'email_invalid' => 'Please enter a valid email address',
    'phone_required' => 'Phone number is required',
    'grade_required' => 'Grade is required',
    'division_required' => 'Division is required',
    'asset_required' => 'Please select an asset',
    'purpose_required' => 'Loan purpose is required',
    'purpose_min' => 'Purpose must be at least 10 characters',
    'purpose_max' => 'Purpose must not exceed 1000 characters',
    'start_date_required' => 'Start date is required',
    'start_date_future' => 'Start date must be in the future',
    'end_date_required' => 'End date is required',
    'end_date_after_start' => 'End date must be after start date',
    'asset_unavailable' => 'Selected asset is not available for the requested period',

    // Asset Details (for emails & display)
    'asset' => [
        'name' => 'Asset Name',
        'asset_tag' => 'Asset Tag',
        'condition' => 'Asset Condition',
    ],

    // Loan Details (for emails & display)
    'loan' => [
        'application_number' => 'Application Number',
        'returned_by' => 'Returned By',
    ],

    'email' => [
        'application_submitted_subject' => 'Asset Loan Application Received - #:application_number',
        'approval_request_subject' => 'Approval Required: Asset Loan #:application_number',
        'approval_confirmed_subject' => 'Approval Recorded - #:application_number',
        'decline_confirmed_subject' => 'Decision Recorded (Declined) - #:application_number',
        'application_approved_subject' => 'Loan Application Approved - #:application_number',
        'application_declined_subject' => 'Loan Application Declined - #:application_number',
        'status_update_subject' => 'Loan Application Updated - #:application_number',
        'due_today_subject' => 'Asset Return Due Today - #:application_number',
        'return_reminder_subject' => 'Asset Return Reminder - #:application_number',
        'overdue_notification_subject' => 'Overdue Asset Return - #:application_number',
        'asset_preparation_subject' => 'Prepare Asset for Loan - #:application_number',
    ],
];
