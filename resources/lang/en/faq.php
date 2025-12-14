<?php

declare(strict_types=1);

/**
 * FAQ Translations (English)
 *
 * Contextual Help and Documentation per Requirement 23.4
 */

return [
    // Helpdesk FAQ
    'helpdesk' => [
        'how_to_submit' => 'How do I submit a helpdesk ticket?',
        'how_to_submit_answer' => 'You can submit a ticket by filling out the form at the Helpdesk page. If you are logged in, your information will be automatically filled. Otherwise, you can submit as a guest.',
        'how_to_track' => 'How can I track my ticket status?',
        'how_to_track_answer' => 'Use the ticket number provided in your confirmation email to track your ticket status on the Track Ticket page.',
        'response_time' => 'How long will it take to get a response?',
        'response_time_answer' => 'Response times vary based on priority. High priority tickets are typically responded to within 4 hours, while normal priority tickets within 24 hours.',
    ],

    // Loan FAQ
    'loan' => [
        'how_to_apply' => 'How do I apply for equipment loan?',
        'how_to_apply_answer' => 'Fill out the loan application form, select the equipment you need, and specify the loan period. Your supervisor will receive an approval request via email.',
        'approval_process' => 'How does the approval process work?',
        'approval_process_answer' => 'Your supervisor (Grade 41+) will receive an email with a secure link to approve or reject your application. You will be notified of their decision.',
        'return_process' => 'How do I return borrowed equipment?',
        'return_process_answer' => 'Return the equipment to the BPM office before the due date. An admin will inspect the equipment and complete the check-in process.',
    ],

    // General FAQ
    'general' => [
        'account_registration' => 'How do I register for an account?',
        'account_registration_answer' => 'Click on Register and use your @motac.gov.my email address. You will receive a verification email to activate your account.',
        'forgot_password' => 'What if I forgot my password?',
        'forgot_password_answer' => 'Click on "Forgot Password" on the login page and follow the instructions sent to your email.',
    ],

    // AI Chat Integration
    'ai_chat' => [
        'title' => 'Ask AI Bedrock',
        'description' => 'Can\'t find the answer you\'re looking for? Chat with AI Bedrock for personalized assistance and more detailed answers.',
        'chat_button' => 'Chat with AI',
        'powered_by' => 'Powered by AWS Bedrock',
        'suggestions_title' => 'Frequently Asked Questions',
        'ask_question' => 'Ask questions about ICT services...',
    ],
];
