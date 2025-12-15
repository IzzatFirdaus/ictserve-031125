<?php

declare(strict_types=1);

/**
 * En - Auth Translations
 *
 * Auto-generated on 2025-11-11 13:02:54
 */

return [
    'login_title' => 'Login',
    'login_subtitle' => 'Access ICTServe staff portal',
    'email' => 'Email',
    'email_placeholder' => 'name@motac.gov.my',
    'password_placeholder' => 'Enter your password',
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot password?',
    'login_button' => 'Login',
    'login' => 'Login',
    'logging_in' => 'Logging in...',
    'need_help' => 'Need help?',
    'contact_support' => 'Contact Support',
    'extend_session' => 'Extend Session',
    'failed' => 'These credentials do not match our records.',
    'insufficient_permissions_portal' => 'Access denied. You do not have permission to access the staff portal. Please contact your administrator.',
    'logged_in' => 'You have been logged in successfully.',
    'logged_out' => 'You have been logged out successfully.',
    'logout' => 'Logout',
    'must_login_portal' => 'You must be logged in to access the staff portal.',
    'password' => 'Password',
    'session_expired' => 'Your session has expired. Please login again.',
    'session_expiring_message' => 'Your session will expire due to inactivity. Would you like to extend your session?',
    'session_expiring_title' => 'Session Expiring Soon',
    'session_extended' => 'Your session has been extended successfully.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'time_remaining' => 'Time remaining',
    'email_unverified' => 'Your email address is unverified.',
    'resend_verification' => 'Click here to re-send the verification email.',
    'verification_link_sent' => 'A new verification link has been sent to your email address.',

    // Task 14.1: Flexible Login
    'email_or_username' => 'Email or Username',
    'email_or_username_placeholder' => 'name@motac.gov.my or username',
    'flexible_login_hint' => 'Enter your full email or just your username',

    // Registration
    'register_title' => 'Register',
    'register_subtitle' => 'Create your ICTServe account',
    'register_button' => 'Register',
    'already_registered' => 'Already registered?',
    'name' => 'Name',
    'name_placeholder' => 'Enter your full name',
    'confirm_password' => 'Confirm Password',
    'confirm_password_placeholder' => 'Re-enter your password',

    // Email Domain Validation
    'email_domain_hint' => 'Must use @motac.gov.my email',
    'email_domain_error' => 'Email must end with @motac.gov.my',
    'registration_failed' => 'Registration failed. Please try again.',

    // Password Strength
    'password_strength' => 'Password Strength',
    'password_weak' => 'Weak',
    'password_fair' => 'Fair',
    'password_good' => 'Good',
    'password_strong' => 'Strong',
    'password_requirements' => 'Password Requirements',
    'password_min_length' => 'At least 8 characters',
    'password_uppercase' => 'One uppercase letter',
    'password_lowercase' => 'One lowercase letter',
    'password_number' => 'One number',
    'password_special' => 'One special character',

    // Google SSO (v3.5.0)
    'google_sign_in' => 'Sign in with Google',
    'google_sign_in_description' => 'Use your @motac.gov.my Google account',
    'or_separator' => 'or',
    'google_sso_failed' => 'Google authentication failed. Please try again.',

    // Google SSO Enhanced Error Messages (v3.6.0)
    'sso_domain_error' => 'Only @motac.gov.my accounts are allowed to login.',
    'sso_domain_error_help' => 'Please ensure you are using your official MOTAC Google account.',
    'sso_oauth_error' => 'Error during Google authentication. Please try again.',
    'sso_oauth_state_error' => 'Security error during authentication. Please try again from the login page.',
    'sso_network_error' => 'Connection problem with Google. Please try again or use regular login.',
    'sso_general_error' => 'Google authentication failed. Please try again or use regular login.',
    'sso_user_cancelled' => 'Authentication cancelled. Please try again if you want to sign in with Google.',
    'sso_account_disabled' => 'Your account has been disabled. Please contact the system administrator.',
    'sso_rate_limited' => 'Too many login attempts. Please wait a moment before trying again.',
    'sso_service_unavailable' => 'Google SSO service is currently unavailable. Please use regular login.',
    'sso_fallback_available' => 'You can login using email and password.',
    'sso_try_again' => 'Try Again',
    'sso_use_password_login' => 'Use Regular Login',
    'sso_loading' => 'Connecting to Google...',
    'sso_redirecting' => 'Redirecting to Google...',
    'sso_processing' => 'Processing authentication...',
    'sso_success' => 'Login successful!',
    'sso_linking_account' => 'Linking your Google account...',
    'sso_account_linked' => 'Your Google account has been linked successfully.',
    'sso_account_already_linked' => 'This Google account is already linked to another account.',
    'sso_unlink_success' => 'Your Google account has been unlinked successfully.',
    'sso_unlink_failed' => 'Failed to unlink Google account. Please try again.',

    // Error Type Labels (v3.6.0)
    'error_types' => [
        'domain_error' => 'Domain Error',
        'oauth_error' => 'OAuth Error',
        'oauth_state_error' => 'OAuth State Error',
        'network_error' => 'Network Error',
        'general_error' => 'General Error',
    ],
];
