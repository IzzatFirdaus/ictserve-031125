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
	'correction_ticket_created' => 'Correction request submitted. Ticket ID: :ticket_id',
];
