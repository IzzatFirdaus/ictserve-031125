<?php

// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details

declare(strict_types=1);

/**
 * En - Auth Translations
 *
 * Auto-generated on 2025-11-11 13:02:54
 * Updated: 2025-12-02 - Added registration translations for Task 13.1
 * Updated: 2025-12-02 - Added flexible login translations for Task 14.1
 * Updated: 2025-12-17 - Added technical reference comment (v3.6.1)
 */

return [
    'login_title' => 'Log In',
    'login_subtitle' => 'Access the ICTServe staff portal',
    'email' => 'Email',
    'email_placeholder' => 'name@motac.gov.my',

    // Flexible Login translations (Task 14.1 - Requirements 16.2, 16.3, 16.5)
    'email_or_username' => 'Email or Username',
    'email_or_username_placeholder' => 'name@motac.gov.my or name',
    'flexible_login_hint' => 'Enter your full email or just your username (without @motac.gov.my)',
    'logging_in' => 'Logging in...',
    'password_placeholder' => 'Enter your password',
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot password?',
    'login_button' => 'Log In',
    'need_help' => 'Need help?',
    'contact_support' => 'Contact Support',
    'extend_session' => 'Extend Session',
    'failed' => 'These credentials do not match our records.',
    'insufficient_permissions_portal' => 'Access denied. You do not have permission to access the staff portal. Please contact your administrator.',
    'logged_in' => 'You have been logged in successfully.',
    'logged_out' => 'You have been logged out successfully.',
    'logout' => 'Logout',
    'must_login_portal' => 'You must be logged in to access the staff portal.',
    'password' => 'The provided password is incorrect.',
    'session_expired' => 'Your session has expired. Please login again.',
    'session_expiring_message' => 'Your session will expire due to inactivity. Would you like to extend your session?',
    'session_expiring_title' => 'Session Expiring Soon',
    'session_extended' => 'Your session has been extended successfully.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'time_remaining' => 'Time remaining',
    'email_unverified' => 'Your email address is unverified.',
    'resend_verification' => 'Click here to re-send the verification email.',
    'verification_link_sent' => 'A new verification link has been sent to your email address.',

    // Registration translations (Task 13.1 - Requirements 15.1, 15.2)
    'register_title' => 'Staff Registration',
    'register_subtitle' => 'Create your ICTServe account',
    'register_button' => 'Register',
    'already_registered' => 'Already registered?',
    'name' => 'Full Name',
    'name_placeholder' => 'Enter your full name',
    'confirm_password' => 'Confirm Password',
    'confirm_password_placeholder' => 'Re-enter your password',
    'email_domain_hint' => 'Only @motac.gov.my email addresses are allowed',
    'email_domain_error' => 'Only MOTAC staff with @motac.gov.my email can register.',
    'password_requirements' => 'Password Requirements',
    'password_min_length' => 'At least 8 characters',
    'password_uppercase' => 'At least one uppercase letter',
    'password_lowercase' => 'At least one lowercase letter',
    'password_number' => 'At least one number',
    'password_special' => 'At least one special character',
    'password_strength' => 'Password Strength',
    'password_weak' => 'Weak',
    'password_fair' => 'Fair',
    'password_good' => 'Good',
    'password_strong' => 'Strong',
    'registration_success' => 'Registration successful! Please check your email to verify your account.',
    'registration_failed' => 'Registration failed. Please try again.',

    // Email verification translations (Task 13.2 - Requirements 15.4, 15.5)
    'verify_email_title' => 'Verify Your Email',
    'verify_email_subtitle' => 'Almost there! Please verify your email address.',
    'verify_email_message' => 'Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn\'t receive the email, we will gladly send you another.',
    'verify_email_sent' => 'A new verification link has been sent to the email address you provided during registration.',
    'resend_verification_button' => 'Resend Verification Email',
    'verification_success' => 'Your email has been verified successfully. You can now log in.',
    'verification_failed' => 'The verification link is invalid or has expired.',
    'verification_expired' => 'This verification link has expired. Please request a new one.',
    'verification_already_verified' => 'Your email is already verified.',

    // Google SSO Error Messages (Task 4.1 - Requirements 2.1, 2.2, 2.3)
    'google_sso_failed' => 'Google authentication failed. Please try again or use traditional login.',
    'google_sso_domain_error' => 'Only @motac.gov.my accounts are allowed to login via Google SSO.',
    'google_sso_oauth_error' => 'Security error during Google authentication. Please try again.',
    'google_sso_network_error' => 'Connection problem with Google. Please try again or use traditional login.',
    'google_sso_unavailable' => 'Google SSO service is currently unavailable. Please use traditional login.',
    'google_sso_account_disabled' => 'Your account has been disabled. Please contact system administrator.',
    'google_sso_fallback_hint' => 'You can login using your email and password.',
];
