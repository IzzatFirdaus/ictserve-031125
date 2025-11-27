<?php

declare(strict_types=1);

/**
 * Profile Page Translations (English)
 *
 * Implements Task 4.1.2: Profile management interface translations
 *
 * @author Pasukan BPM MOTAC
 * @trace R10 (Authenticated Portal), R13 (Bilingual Support)
 * @version 1.0.0
 * @task 4.1.2
 */

return [
	// Page Header
	'title' => 'My Profile',
	'subtitle' => 'Manage your account settings and preferences',
	'completeness' => 'Profile Completeness',

	// System Information Section
	'system_information' => 'System Information',
	'system_information_desc' => 'These fields are managed by the system administrator and cannot be edited directly.',
	'read_only' => 'Read Only',
	'request_correction' => 'Request Correction',

	// Fields
	'email' => 'Email Address',
	'staff_id' => 'Staff ID',
	'grade' => 'Grade',
	'department' => 'Department/Division',
	'name' => 'Full Name',
	'phone' => 'Office Phone',
	'mobile' => 'Mobile Phone',
	'bio' => 'Bio',
	'bio_placeholder' => 'Tell us a little about yourself...',
	'characters' => 'characters',

	// Personal Information Section
	'personal_information' => 'Personal Information',
	'personal_information_desc' => 'Update your personal contact information.',

	// Password Section
	'change_password' => 'Change Password',
	'change_password_desc' => 'Ensure your account is using a strong password for security.',
	'current_password' => 'Current Password',
	'new_password' => 'New Password',
	'confirm_password' => 'Confirm New Password',
	'update_password' => 'Update Password',

	// Language Section
	'language_preference' => 'Language Preference',
	'language_preference_desc' => 'Select your preferred language for the portal interface.',
	'save_language' => 'Save Language',

	// Notification Preferences Section
	'notification_preferences' => 'Notification Preferences',
	'notification_preferences_desc' => 'Choose which notifications you would like to receive.',
	'notif_ticket_updates' => 'Ticket Status Updates',
	'notif_ticket_assignments' => 'Ticket Assignments',
	'notif_ticket_comments' => 'New Comments on Tickets',
	'notif_sla_alerts' => 'SLA Breach Alerts',
	'notif_loan_updates' => 'Loan Application Updates',
	'notif_loan_approvals' => 'Loan Approval Requests',
	'notif_loan_reminders' => 'Loan Return Reminders',
	'notif_system_announcements' => 'System Announcements',
	'save_preferences' => 'Save Preferences',

	// Actions
	'save_changes' => 'Save Changes',
	'saving' => 'Saving...',
	'updating' => 'Updating...',

	// Success Messages
	'updated_successfully' => 'Profile updated successfully.',
	'password_updated' => 'Password updated successfully.',
	'preferences_updated' => 'Notification preferences updated successfully.',
	'language_updated' => 'Language preference updated successfully.',

	// Correction Request
	'correction_request_title' => 'Profile Data Correction Request - :field',
	'correction_request_desc' => 'I would like to request a correction for my :field. Current value: :current_value',
	'correction_ticket_created' => 'Correction request submitted successfully. Ticket ID: :ticket_id',

	// Profile Page Additional
	'title' => 'My Profile',
	'description' => 'Manage your account settings and preferences',
	'information_title' => 'Profile Information',
	'information_description' => 'Update your personal information and contact details.',
	'notifications_title' => 'Notification Preferences',
	'preferences_description' => 'Choose which notifications you would like to receive.',
	'password_title' => 'Change Password',
	'security_description' => 'Ensure your account is using a strong password for security.',
	'update_success' => 'Profile updated successfully.',
	'update_error' => 'An error occurred while updating your profile. Please try again.',
	'password_error' => 'An error occurred while updating your password. Please try again.',
	'name_placeholder' => 'Enter your full name',
	'phone_placeholder' => 'Enter your phone number',
	'current_password_placeholder' => 'Enter your current password',
	'new_password_placeholder' => 'Enter your new password',
	'confirm_password_placeholder' => 'Confirm your new password',
	'password_requirements' => 'Password must be at least 8 characters with uppercase, lowercase, numbers, and symbols.',
	'updating_password' => 'Updating password...',
	'helpdesk_notifications' => 'Helpdesk Notifications',
	'ticket_updates' => 'Ticket Status Updates',
	'ticket_updates_desc' => 'Receive notifications when your ticket status changes.',
	'ticket_assignments' => 'Ticket Assignments',
	'ticket_assignments_desc' => 'Receive notifications when tickets are assigned to you.',
	'ticket_comments' => 'New Comments',
	'ticket_comments_desc' => 'Receive notifications when new comments are added to your tickets.',
	'sla_alerts' => 'SLA Alerts',
	'sla_alerts_desc' => 'Receive alerts when tickets are approaching or breaching SLA.',
	'loan_notifications' => 'Asset Loan Notifications',
	'loan_updates' => 'Loan Application Updates',
	'loan_updates_desc' => 'Receive notifications when your loan application status changes.',
	'loan_approvals' => 'Loan Approval Requests',
	'loan_approvals_desc' => 'Receive notifications when you have pending loan approvals.',
	'loan_reminders' => 'Loan Return Reminders',
	'loan_reminders_desc' => 'Receive reminders when your borrowed assets are due for return.',
	'system_notifications' => 'System Notifications',
	'system_announcements' => 'System Announcements',
	'system_announcements_desc' => 'Receive important system announcements and updates.',
	'saving_preferences' => 'Saving preferences...',
	'preferences_auto_save' => 'Preferences are saved automatically.',
	'position' => 'Position',
	'division' => 'Division',
];
