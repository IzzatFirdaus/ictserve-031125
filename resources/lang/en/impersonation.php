<?php

declare(strict_types=1);

/**
 * Impersonation Translations - English
 *
 * Translation keys for user impersonation features and security messages.
 *
 * @trace D03-FR-002.5 (Impersonation Security)
 * @trace D04 §5.0.3 (Impersonation Middleware)
 *
 * @version 1.0.0
 *
 * @created 2025-11-26
 */

return [
    // Security Messages
    'action_blocked' => 'Action Blocked',
    'action_blocked_message' => 'This action is not allowed while impersonating another user. Please stop impersonation to perform security-sensitive actions.',

    // Banner Messages
    'impersonation_active' => 'Impersonation Active',
    'impersonating_user' => 'You are currently impersonating :name',
    'logged_in_as_admin' => 'Logged in as administrator: :admin',
    'stop_impersonation' => 'Stop Impersonation',
    'return_to_admin' => 'Return to Admin',

    // Audit Messages
    'impersonation_started' => 'User impersonation started',
    'impersonation_ended' => 'User impersonation ended',
    'action_blocked_audit' => 'Security action blocked during impersonation',

    // Error Messages
    'cannot_impersonate_self' => 'You cannot impersonate yourself.',
    'cannot_impersonate_admin' => 'You cannot impersonate another Super Admin.',
    'unauthorized_action' => 'You are not authorized to perform this action.',

    // Confirmation Messages
    'confirm_stop' => 'Are you sure you want to stop impersonating this user?',
    'impersonation_stopped' => 'Impersonation has been stopped. You are now logged in as yourself.',
];
