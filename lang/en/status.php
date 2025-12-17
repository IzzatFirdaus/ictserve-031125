<?php

// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details

declare(strict_types=1);

/**
 * Status Checker Translations (English)
 *
 * Translations for the StatusChecker Livewire component.
 * Supports token-based status lookup for tickets and loans.
 *
 * @see App\Livewire\Status\StatusChecker
 *
 * @requirements 2.1, 2.2
 */

return [
    // Page titles
    'page_title' => 'Check Status',
    'page_tagline' => 'Current Status',
    'title' => 'Check Your Submission Status',
    'subtitle' => 'Enter your status token to view the current status of your helpdesk ticket or loan application.',

    // Form labels
    'form_label' => 'Status check form',
    'form_helper' => 'Enter your token to check your submission status.',
    'token_label' => 'Status Token',
    'token_placeholder' => 'Enter your status token (e.g., abc123def456...)',
    'token_help' => 'The status token was sent to your email when you submitted your request.',
    'type_label' => 'Submission Type (Optional)',
    'type_auto' => 'Auto-detect',
    'type_ticket' => 'Helpdesk Ticket',
    'type_loan' => 'Loan Application',
    'type_help' => 'Leave as auto-detect if you\'re unsure.',

    // Buttons
    'check_button' => 'Check Status',
    'checking' => 'Checking...',
    'clear' => 'Clear',

    // Status messages
    'last_updated' => 'Last updated:',
    'current_status' => 'Current Status',

    // Error messages (bilingual format)
    'token_invalid' => 'Invalid or expired token. Please check and try again.',
    'not_found_title' => 'Submission Not Found / Permohonan Tidak Dijumpai',
    'not_found_message' => 'We could not find a submission matching your token. Please verify the following:',
    'not_found_hint_1' => 'Ensure the token is copied correctly from your email',
    'not_found_hint_2' => 'Check that you\'re using the correct submission type',
    'not_found_hint_3' => 'Contact BPM support if the issue persists',

    // Results section
    'ticket_number' => 'Ticket Number',
    'loan_reference' => 'Loan Application :ref',
    'submitted_on' => 'Submitted on :date',
    'category' => 'Category',
    'priority' => 'Priority',
    'division' => 'Division',
    'applicant' => 'Applicant',
    'loan_period' => 'Loan Period',
    'location' => 'Location',
    'not_specified' => 'Not specified',

    // Timeline
    'timeline_title' => 'Status Timeline',
    'no_timeline' => 'No timeline information available at this time.',

    // Comments
    'comments_title' => 'Updates & Comments',
    'system' => 'System',

    // Loan items
    'loan_items_title' => 'Requested Items',
    'unknown_item' => 'Unknown Item',

    // Description
    'description_title' => 'Request Details',
    'resolution_notes' => 'Resolution Notes',

    // Help section
    'help_text' => 'Need help? Can\'t find your submission?',
    'contact_support' => 'Contact BPM Support',

    // Quick Help sidebar
    'quick_help_title' => 'Quick Help',
    'quick_help_email' => 'BPM support email',
    'quick_help_phone' => 'Helpdesk hotline',
    'quick_help_ticket' => 'Submit a new ticket',
    'quick_help_ticket_cta' => 'Go to helpdesk form',
];
