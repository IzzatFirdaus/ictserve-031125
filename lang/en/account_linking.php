<?php

declare(strict_types=1);

/**
 * Account Linking Translations (English)
 *
 * Translation strings for the Account Linking feature in ICTServe v3.5.0.
 * This feature allows staff to link their historical guest submissions
 * to their newly registered account.
 *
 * @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
 * @see D02 FR-050 Optional account linking
 * @see D15_LANGUAGE_MS_EN.md Bilingual localization
 */

return [
    // Page Header
    'title' => 'Link Historical Submissions',
    'description' => 'Link your previous guest submissions (helpdesk tickets and loan applications) to your account to view them in your submission history.',

    // Statistics
    'statistics_title' => 'Your Submission Statistics',
    'linked_tickets' => 'Linked Tickets',
    'linked_loans' => 'Linked Loans',
    'unlinked_tickets' => 'Unlinked Tickets',
    'unlinked_loans' => 'Unlinked Loans',

    // How It Works
    'how_it_works_title' => 'How Account Linking Works',
    'how_it_works_description' => 'If you submitted helpdesk tickets or loan applications as a guest before registering, you can link those submissions to your account. Simply enter the email address you used for those submissions, select the ones you want to link, and click the Link button.',

    // Search Form
    'search_title' => 'Search for Unlinked Submissions',
    'email_label' => 'Email Address',
    'email_placeholder' => 'Enter the email used for guest submissions',
    'email_help' => 'Enter the email address you used when submitting tickets or loan applications as a guest.',
    'search_button' => 'Search',

    // Results
    'found_submissions' => ':count submission(s) found',
    'select_all' => 'Select All',
    'deselect_all' => 'Deselect All',
    'select_submission' => 'Select submission :reference',
    'type_ticket' => 'Ticket',
    'type_loan' => 'Loan',
    'submitted_on' => 'Submitted on',

    // Selection
    'selected_count' => '{0} No submissions selected|{1} :count submission selected|[2,*] :count submissions selected',

    // Actions
    'link_button' => 'Link Selected Submissions',
    'linking' => 'Linking...',
    'back_to_dashboard' => 'Back to Dashboard',

    // Messages
    'no_submissions_found' => 'No unlinked submissions found for this email address.',
    'no_submissions_selected' => 'Please select at least one submission to link.',
    'submissions_linked_success' => '{1} :count submission has been linked to your account.|[2,*] :count submissions have been linked to your account.',
    'linking_failed' => 'Failed to link submissions. Please try again.',
    'linking_error' => 'An error occurred while linking submissions. Please try again later.',

    // No Results
    'no_results_title' => 'No Submissions Found',
    'no_results_description' => 'We couldn\'t find any unlinked submissions for this email address. The submissions may have already been linked or the email address may be different.',
    'try_different_email' => 'Try a different email address',
];
