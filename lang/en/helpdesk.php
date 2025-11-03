<?php

declare(strict_types=1);

/**
 * English Helpdesk Module Translations
 *
 * Helpdesk ticket submission, management, and notifications
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.2
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 */

return [
    // Page Titles
    'submit_ticket' => 'Submit Helpdesk Ticket',
    'ticket_details' => 'Ticket Details',
    'my_tickets' => 'My Tickets',
    'ticket_list' => 'Ticket List',

    // Form Labels
    'full_name' => 'Full Name',
    'email_address' => 'Email Address',
    'phone_number' => 'Phone Number',
    'staff_id' => 'Staff ID (Optional)',
    'division' => 'Division',
    'issue_category' => 'Issue Category',
    'subject' => 'Subject',
    'problem_description' => 'Problem Description',
    'attachments_optional' => 'Attachments (Optional)',

    // Help Text
    'name_help' => 'Enter your full name as per MOTAC records',
    'email_help' => 'We will send updates to this email address',
    'phone_help' => 'Contact number for urgent matters',
    'staff_id_help' => 'Your MOTAC staff ID if applicable',
    'category_help' => 'Select the category that best describes your issue',
    'description_help' => 'Provide detailed information about the problem (minimum 10 characters)',
    'attachments_help' => 'Upload screenshots or documents (max 10MB per file)',

    // Messages
    'ticket_submitted' => 'Your ticket has been submitted successfully',
    'ticket_number' => 'Ticket Number',
    'confirmation_email' => 'You will receive a confirmation email shortly',
    'no_login_required' => 'No login required',
    'quick_submission' => 'Quick submission for all MOTAC staff',

    // Ticket Status
    'ticket_status' => 'Ticket Status',
    'status_open' => 'Open',
    'status_assigned' => 'Assigned',
    'status_in_progress' => 'In Progress',
    'status_pending_user' => 'Pending User Response',
    'status_resolved' => 'Resolved',
    'status_closed' => 'Closed',

    // Priority
    'priority_low' => 'Low',
    'priority_medium' => 'Medium',
    'priority_high' => 'High',
    'priority_critical' => 'Critical',

    // Categories
    'category_hardware' => 'Hardware Issue',
    'category_software' => 'Software Issue',
    'category_network' => 'Network Issue',
    'category_email' => 'Email Issue',
    'category_access' => 'Access/Permission Issue',
    'category_other' => 'Other',

    // Actions
    'submit_ticket_button' => 'Submit Ticket',
    'clear_form' => 'Clear Form',
    'view_ticket' => 'View Ticket',
    'add_comment' => 'Add Comment',
    'upload_attachment' => 'Upload Attachment',

    // Validation
    'name_required' => 'Full name is required',
    'email_required' => 'Email address is required',
    'email_invalid' => 'Please enter a valid email address',
    'phone_required' => 'Phone number is required',
    'category_required' => 'Please select an issue category',
    'subject_required' => 'Subject is required',
    'description_required' => 'Problem description is required',
    'description_min' => 'Description must be at least 10 characters',
    'description_max' => 'Description must not exceed 5000 characters',
    'file_too_large' => 'File size must not exceed 10MB',
    'invalid_file_type' => 'Invalid file type. Allowed: images, PDF, DOC, DOCX',

    // Email Notifications
    'email' => [
        'new_ticket_subject' => 'New Helpdesk Ticket Submitted - #:ticket_number',
        'maintenance_ticket_subject' => 'Maintenance Request Created - #:ticket_number',
        'status_update_subject' => 'Helpdesk Ticket Updated - #:ticket_number',
        'ticket_claimed_subject' => 'Ticket Claimed Successfully - #:ticket_number',
        'guest_confirmation_subject' => 'Ticket Confirmation - #:ticket_number',
        'authenticated_confirmation_subject' => 'Ticket Received - #:ticket_number',
        'greeting' => 'Hello :name,',
        'new_ticket_created' => 'A new helpdesk ticket has been created (:submission_type submission) with the following details:',
        'maintenance_ticket_created' => 'A maintenance request has been created with the following details:',
        'maintenance_ticket_description' => 'The asset requires maintenance:',
        'maintenance_priority_notice' => 'This is a maintenance request and may be prioritized accordingly.',
        'status_updated' => 'Your ticket status has been updated. Please see the details below:',
        'ticket_details' => 'Ticket Details',
        'asset_details' => 'Asset Information',
        'loan_details' => 'Loan Information',
        'update_comment' => 'Update Comment',
        'view_ticket' => 'View Ticket',
        'view_ticket_portal' => 'View in Portal',
        'guest_status_info' => 'Please use your ticket number as reference when inquiring about this ticket.',
        'guest_ticket_received' => 'We have received your support request. Your ticket number is **#:ticket_number**.',
        'guest_next_steps' => 'What Happens Next',
        'guest_step_email_updates' => 'You will receive email updates as your ticket is processed',
        'guest_step_reference_number' => 'Keep your ticket number for reference - you will need it to claim your account',
        'guest_step_response_time' => 'We aim to respond within the SLA timeframe',
        'guest_can_claim' => 'You can claim your ticket by creating an account:',
        'claim_ticket' => 'Claim Your Ticket',
        'authenticated_ticket_received' => 'We have received your support request. Your ticket number is **#:ticket_number**.',
        'authenticated_features' => 'As a registered user, you now have access to:',
        'feature_real_time_tracking' => 'Real-time ticket tracking and status updates',
        'feature_internal_comments' => 'Internal comments visible only to you and support staff',
        'feature_submission_history' => 'Full submission history and revision tracking',
        'feature_instant_notifications' => 'Instant notifications for all ticket updates',
        'sla_notice' => 'We are committed to responding within our Service Level Agreement (SLA).',
        'ticket_claimed_success' => 'Thank you! You have successfully claimed the ticket **#:ticket_number**.',
        'ticket_claimed_benefits' => 'Now that you are registered, you have access to enhanced features:',
        'benefit_tracking' => 'Track ticket progress in real-time',
        'benefit_history' => 'View complete ticket history and revisions',
        'benefit_comments' => 'Add internal comments for coordination',
        'benefit_notifications' => 'Receive instant notifications for all updates',
        'thank_you' => 'Thank you for using our support system.',
        'assigned_to' => 'Assigned to',
    ],
];
