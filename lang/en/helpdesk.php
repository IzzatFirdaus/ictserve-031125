<?php

declare(strict_types=1);

return [
    // Page titles and descriptions
    'submit_ticket' => 'Submit Helpdesk Ticket',
    'submit_ticket_description' => 'Describe your technical issue or question and we will assign it to the right support team.',
    'quick_submission' => 'Let us know about your technical question. Our ICT team will respond promptly.',
    'wizard_progress' => 'Ticket submission progress',

    // Steps
    'step_1_title' => 'Contact Information',
    'step_2_title' => 'Issue Details',
    'step_3_title' => 'Attachments',
    'step_4_title' => 'Confirmation',

    // Headings
    'your_information' => 'Your information',

    // Step 1 fields
    'full_name' => 'Full name',
    'email_address' => 'Email address',
    'phone_number' => 'Phone number',
    'staff_id' => 'Staff ID (optional)',
    'division' => 'Division / department',
    'select_division' => 'Select a division',

    // Step 2 fields
    'category' => 'Issue category',
    'select_category' => 'Select a category',
    'priority' => 'Priority',
    'priority_low' => 'Low',
    'priority_normal' => 'Normal',
    'priority_high' => 'High',
    'priority_urgent' => 'Urgent',
    'subject' => 'Subject',
    'description' => 'Issue description',
    'problem_description' => 'Please describe the issue in detail',
    'related_asset' => 'Related ICT asset (optional)',
    'no_asset' => 'No related asset',
    'internal_notes' => 'Internal notes (optional)',
    'internal_notes_help' => 'Visible only to the ICT support team. Use for follow-up actions or extra context.',

    // Step 3 fields
    'attachments' => 'Attach files',
    'optional' => 'Optional',
    'click_to_upload' => 'Click to upload',
    'or_drag_and_drop' => 'or drag and drop files',
    'file_types' => 'Allowed file types',
    'max_size' => 'Maximum size',
    'uploading' => 'Uploading',
    'uploaded_files' => 'Uploaded files',
    'remove_file' => 'Remove :name',

    // Confirmation
    'confirmation' => 'Submission confirmation',
    'ticket_submitted' => 'Your ticket has been submitted',
    'ticket_number' => 'Ticket number',
    'confirmation_email_sent' => 'A confirmation email has been sent. Use the ticket number to track progress.',
    'submit_another' => 'Submit another ticket',
    'return_home' => 'Return to home',

    // Buttons / navigation
    'form_navigation' => 'Form navigation',
    'previous' => 'Previous',
    'next' => 'Next',
    'submit_button' => 'Submit ticket',
    'submitting' => 'Submitting',

    // Loading / statuses
    'processing' => 'Processing',
    'loading' => 'Loading',

    // Validation and errors
    'validation_errors' => 'Please fix the highlighted fields.',
    'name_required' => 'Full name is required.',
    'email_required' => 'Email address is required.',
    'email_invalid' => 'Please enter a valid email address.',
    'phone_required' => 'Phone number is required.',
    'category_required' => 'Please choose an issue category.',
    'subject_required' => 'Subject is required.',
    'description_required' => 'Issue description is required.',
    'description_min' => 'Description must be at least 10 characters.',
    'description_max' => 'Description cannot exceed 5000 characters.',
    'submission_failed' => 'Ticket submission failed. Please try again.',
    'upload_failed' => 'File upload failed. Please try again.',

    // Success
    'ticket_created_success' => 'Ticket created successfully.',
];
