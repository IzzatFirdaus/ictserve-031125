<?php

declare(strict_types=1);

return [
    // Form Labels & Placeholders
    'full_name' => 'Full Name',
    'email_address' => 'Email Address',
    'phone_number' => 'Phone Number',
    'staff_id' => 'Staff ID',
    'division' => 'Division',
    'select_division' => '-- Select Division --',
    'category' => 'Category',
    'select_category' => '-- Select Category --',
    'priority' => 'Priority',
    'subject' => 'Subject',
    'description' => 'Description',
    'related_asset' => 'Related Asset',
    'attachments' => 'Attachments',
    'internal_notes' => 'Internal Notes',
    'no_asset' => 'No Asset Related',

    // Priority Options
    'priority_low' => 'Low',
    'priority_normal' => 'Normal',
    'priority_high' => 'High',
    'priority_urgent' => 'Urgent',

    // Step Titles
    'step_1_title' => 'Contact Information',
    'step_2_title' => 'Issue Details',
    'step_3_title' => 'Attachments',
    'confirmation' => 'Confirmation',
    'submit_ticket' => 'Submit Helpdesk Ticket',
    'submit_ticket_description' => 'We\'re here to help. Tell us what you need.',
    'wizard_progress' => 'Wizard Progress',
    'step' => 'Step',
    'of_steps' => 'of :total',
    'step_navigation' => 'Step Navigation',

    // Form Actions
    'next' => 'Next',
    'previous' => 'Previous',
    'submit_button' => 'Submit Ticket',
    'submitting' => 'Submitting',
    'processing' => 'Processing',
    'loading' => 'Loading',

    // Authentication Section
    'your_information' => 'Your Information',
    'guest_info' => 'Guest Information',

    // Validation Messages
    'name_required' => 'Full name is required',
    'email_required' => 'Email address is required',
    'email_invalid' => 'Please provide a valid email address',
    'phone_required' => 'Phone number is required',
    'division_required' => 'Please select your division',
    'category_required' => 'Please select a category',
    'subject_required' => 'Subject is required',
    'description_required' => 'Description is required',
    'description_min' => 'Description must be at least 10 characters',
    'description_max' => 'Description must not exceed 5000 characters',

    // Attachment Messages
    'click_to_upload' => 'Click to upload',
    'or_drag_and_drop' => 'or drag and drop',
    'file_types' => 'Allowed file types',
    'max_size' => 'Max size',
    'uploading' => 'Uploading',
    'optional' => 'Optional',
    'uploaded_files' => 'Uploaded Files',
    'remove_file' => 'Remove :name',

    // Validation Errors
    'validation_errors' => 'Validation Errors',
    'submission_failed' => 'Ticket submission failed. Please try again.',

    // Success Messages
    'ticket_submitted' => 'Ticket Submitted Successfully!',
    'ticket_number' => 'Your Ticket Number',
    'confirmation_email_sent' => 'A confirmation email has been sent to your address.',
    'submit_another' => 'Submit Another Ticket',
    'return_home' => 'Return to Home',

    // Help Text
    'no_divisions_help' => 'No divisions available. Please contact support.',
    'internal_notes_help' => 'Internal notes visible to support team only',

    // Exporter
    'exporter' => [
        'ticket_number' => 'Ticket Number',
        'created_at' => 'Created Date',
        'status' => 'Status',
        'priority' => 'Priority',
        'subject' => 'Subject',
        'submitter_name' => 'Submitter Name',
        'submitter_email' => 'Submitter Email',
        'submission_type' => 'Submission Type',
        'category' => 'Category',
        'assigned_to' => 'Assigned To',
        'assigned_division' => 'Assigned Division',
        'assigned_date' => 'Assigned Date',
        'response_date' => 'Response Date',
        'resolved_date' => 'Resolved Date',
        'closed_date' => 'Closed Date',
        'sla_due_date' => 'SLA Due Date',
        'sla_status' => 'SLA Status',
        'resolution_time_hours' => 'Resolution Time (Hours)',
        'submission_guest' => 'Guest',
        'submission_authenticated' => 'Authenticated',
        'sla_not_applicable' => 'N/A',
        'sla_breached' => 'Breached',
        'sla_in_progress' => 'In Progress',
        'sla_met' => 'Met',
        'completed_body' => 'Your helpdesk ticket export has completed and :successful rows exported.|Your helpdesk ticket export has completed and :successful rows exported. :failed rows failed to export.',
    ],
];
