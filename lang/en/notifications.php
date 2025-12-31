<?php

// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details

declare(strict_types=1);

/**
 * En - Notifications Translations
 *
 * Updated: 2025-12-17 - Added technical reference comment (v3.6.1)
 */

return [
    'bell_aria' => 'Notifications, :count unread',
    'unread_count' => 'You have :count unread notifications',
    'title' => 'Notifications',
    'mark_all_read' => 'Mark all as read',
    'mark_read' => 'Mark as read',
    'view_details' => 'View details',
    'no_new' => 'No new notifications',
    'view_all' => 'View all notifications',
    'untitled' => 'Notification',

    // Notification types
    'ticket_assigned' => 'Ticket Assigned',
    'ticket_resolved' => 'Ticket Resolved',
    'loan_approved' => 'Loan Approved',
    'loan_rejected' => 'Loan Rejected',
    'asset_overdue' => 'Asset Overdue',
    'sla_breach' => 'SLA Breach Alert',

    // Notification actions
    'mark_as_read' => 'Mark as read',
    'mark_all_as_read' => 'Mark all as read',
    'unread' => 'Unread notification',
    'no_notifications' => 'No notifications to display.',
    'loading' => 'Loading notifications...',

    // Notification center
    'filter_all' => 'All',
    'filter_unread' => 'Unread',
    'filter_read' => 'Read',

    // Enhanced notification center (v3.6.1)
    'total_count' => ':total notifications (:unread unread)',
    'search_placeholder' => 'Search notifications...',
    'search_help' => 'Search by notification title or message content',
    'filters' => 'Filters',
    'filter_status' => 'Status',
    'filter_type' => 'Type',
    'filter_date_from' => 'From Date',
    'filter_date_to' => 'To Date',
    'unread_only' => 'Show unread only',
    'all_types' => 'All types',
    'clear_filters' => 'Clear filters',
    'sort_by' => 'Sort by',
    'sort_asc' => 'Sort ascending',
    'sort_desc' => 'Sort descending',

    // Bulk actions
    'bulk_actions' => 'Bulk actions',
    'selected_count' => ':count selected',
    'select_all' => 'Select all',
    'select_all_visible' => 'Select all visible',
    'deselect_all' => 'Deselect all',
    'select_notification' => 'Select notification: :title',
    'selected_marked_read' => 'Selected notifications marked as read',
    'selected_deleted' => 'Selected notifications deleted',
    'bulk_marked_read' => ':count notifications marked as read',
    'bulk_deleted' => ':count notifications deleted',
    'deselected_all' => 'All notifications deselected',

    // Export
    'export' => 'Export',
    'export_title' => 'Export Notifications',
    'export_format' => 'Export Format',
    'export_csv_desc' => 'Spreadsheet compatible',
    'export_json_desc' => 'Developer friendly',
    'export_description' => 'Export :count notifications with current filters applied',
    'export_download' => 'Download',
    'cancel' => 'Cancel',

    // Empty states
    'empty_title' => 'No notifications',
    'empty_message' => 'You\'re all caught up! Check back later for new notifications.',
    'no_results' => 'No notifications found',
    'try_different_filters' => 'Try adjusting your search or filters',
    'list' => 'Notification list',

    // Actions
    'view' => 'View',
    'delete' => 'Delete',
    'deleted' => 'Notification deleted',
    'marked_read' => 'Notification marked as read',
    'all_marked_read' => 'All notifications marked as read',
    'all_marked_read_count' => ':count notifications marked as read',
    'new_notification_received' => 'New notification received',

    // Confirmations
    'confirm_mark_all_read' => 'Are you sure you want to mark all notifications as read?',
    'confirm_delete' => 'Are you sure you want to delete this notification?',
    'confirm_delete_selected' => 'Are you sure you want to delete the selected notifications?',
    'confirm_clear_all' => 'Are you sure you want to clear all notifications?',

    // Categories
    'category' => [
        'all' => 'All',
        'tickets' => 'Tickets',
        'loans' => 'Loans',
        'system' => 'System',
        'alerts' => 'Alerts',
    ],
    'category_filter' => 'Filter by category',

    // Analytics
    'analytics' => [
        'delivery_rate' => 'Delivery Rate',
        'bounce_rate' => 'Bounce Rate',
        'queue_health' => 'Queue Health',
        'total_sent' => 'Total Sent',
        'last_30_days' => 'Last 30 days',
        'alert_threshold_exceeded' => 'Alert: Threshold exceeded',
        'within_threshold' => 'Within acceptable threshold',
        'throughput_per_minute' => ':0 emails/min throughput',
        'delivered_count' => ':0 delivered',
        'stuck_emails' => ':count stuck in queue',
        'pending_retries' => ':count pending retries',
    ],

    // Real-time
    'new_count_announcement' => 'You have :count new notifications',
    'ticket_updated' => 'Ticket status updated',
    'loan_updated' => 'Loan status updated',
    'status_updated' => ':type status changed to :status',
    'new_notification' => 'New notification',
    'email_verified' => 'Email verified successfully',
    'submissions_linked' => '%d submissions linked to your account',
    'api_token_created' => 'API token "%s" created',
    'google_sso_linked' => 'Google account %s linked',
    'google_account' => 'Google account',
];
