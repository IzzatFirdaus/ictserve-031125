<?php

declare(strict_types=1);

/**
 * English Helpdesk Module Translations
 *
 * Helpdesk ticket submission, management, and notifications
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.2
 * @wcag-level AA
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
];
