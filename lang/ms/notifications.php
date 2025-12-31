<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md

declare(strict_types=1);

return [
    'bell_aria' => 'Notifikasi, :count belum dibaca',
    'unread_count' => 'Anda mempunyai :count notifikasi belum dibaca',
    'title' => 'Notifikasi',
    'mark_all_read' => 'Tandakan semua sebagai dibaca',
    'mark_read' => 'Tandakan sebagai dibaca',
    'view_details' => 'Lihat butiran',
    'no_new' => 'Tiada notifikasi baharu',
    'view_all' => 'Lihat semua notifikasi',
    'untitled' => 'Notifikasi',

    // Notification types
    'ticket_assigned' => 'Tiket Ditugaskan',
    'ticket_resolved' => 'Tiket Diselesaikan',
    'loan_approved' => 'Pinjaman Diluluskan',
    'loan_rejected' => 'Pinjaman Ditolak',
    'asset_overdue' => 'Aset Lewat Tempoh',
    'sla_breach' => 'Amaran Pelanggaran SLA',

    // Notification actions
    'mark_as_read' => 'Tandakan sebagai dibaca',
    'mark_all_as_read' => 'Tandakan semua sebagai dibaca',
    'unread' => 'Notifikasi belum dibaca',
    'no_notifications' => 'Tiada notifikasi untuk dipaparkan.',
    'loading' => 'Memuatkan notifikasi...',

    // Notification center
    'filter_all' => 'Semua',
    'filter_unread' => 'Belum Dibaca',
    'filter_read' => 'Dibaca',
    'filter_by_type' => 'Tapis mengikut jenis',
    'selected_marked_read' => 'Notifikasi terpilih ditandakan sebagai dibaca',
    'selected_deleted' => 'Notifikasi terpilih dipadam',
    'all_marked_read' => 'Semua notifikasi ditandakan sebagai dibaca',

    // Enhanced notification center (v3.6.1)
    'total_count' => ':total notifikasi (:unread belum dibaca)',
    'search_placeholder' => 'Cari notifikasi...',
    'search_help' => 'Cari mengikut tajuk atau kandungan mesej notifikasi',
    'filters' => 'Penapis',
    'filter_status' => 'Status',
    'filter_type' => 'Jenis',
    'filter_date_from' => 'Dari Tarikh',
    'filter_date_to' => 'Hingga Tarikh',
    'unread_only' => 'Tunjukkan belum dibaca sahaja',
    'all_types' => 'Semua jenis',
    'clear_filters' => 'Kosongkan penapis',
    'sort_by' => 'Susun mengikut',
    'sort_asc' => 'Susun menaik',
    'sort_desc' => 'Susun menurun',

    // Bulk actions
    'bulk_actions' => 'Tindakan pukal',
    'selected_count' => ':count dipilih',
    'select_all' => 'Pilih semua',
    'select_all_visible' => 'Pilih semua yang kelihatan',
    'deselect_all' => 'Nyahpilih semua',
    'select_notification' => 'Pilih notifikasi: :title',
    'bulk_marked_read' => ':count notifikasi ditandakan sebagai dibaca',
    'bulk_deleted' => ':count notifikasi dipadam',
    'deselected_all' => 'Semua notifikasi dinyahpilih',

    // Export
    'export' => 'Eksport',
    'export_title' => 'Eksport Notifikasi',
    'export_format' => 'Format Eksport',
    'export_csv_desc' => 'Serasi dengan hamparan',
    'export_json_desc' => 'Mesra pembangun',
    'export_description' => 'Eksport :count notifikasi dengan penapis semasa',
    'export_download' => 'Muat Turun',
    'cancel' => 'Batal',

    // Empty states
    'empty_title' => 'Tiada notifikasi',
    'empty_message' => 'Anda sudah dikemaskini! Semak semula kemudian untuk notifikasi baharu.',
    'no_results' => 'Tiada notifikasi dijumpai',
    'try_different_filters' => 'Cuba laraskan carian atau penapis anda',
    'list' => 'Senarai notifikasi',

    // Actions
    'view' => 'Lihat',
    'delete' => 'Padam',
    'deleted' => 'Notifikasi dipadam',
    'marked_read' => 'Notifikasi ditandakan sebagai dibaca',
    'all_marked_read_count' => ':count notifikasi ditandakan sebagai dibaca',
    'new_notification_received' => 'Notifikasi baharu diterima',

    // Confirmations
    'confirm_mark_all_read' => 'Adakah anda pasti mahu menandakan semua notifikasi sebagai dibaca?',
    'confirm_delete' => 'Adakah anda pasti mahu memadam notifikasi ini?',
    'confirm_delete_selected' => 'Adakah anda pasti mahu memadam notifikasi yang dipilih?',
    'confirm_clear_all' => 'Adakah anda pasti mahu mengosongkan semua notifikasi?',

    // Real-time notification messages (Phase 9)
    'ticket_status_changed' => 'Tiket :ticket telah dikemaskini kepada :status',
    'loan_status_changed' => 'Permohonan pinjaman :application telah dikemaskini kepada :status',
    'ticket_updated' => 'Tiket anda telah dikemaskini',
    'loan_updated' => 'Permohonan pinjaman anda telah dikemaskini',
    'status_updated' => ':type telah dikemaskini kepada :status',
    'new_notification' => 'Notifikasi baharu diterima',

    // Category labels
    'category' => [
        'all' => 'Semua',
        'tickets' => 'Tiket',
        'loans' => 'Pinjaman',
        'system' => 'Sistem',
        'alerts' => 'Amaran',
    ],
    'category_filter' => 'Tapis mengikut kategori',
    'new_count_announcement' => 'Anda mempunyai :count notifikasi baharu',

    // WP-08 Event notifications
    'email_verified' => 'E-mel anda telah disahkan',
    'submissions_linked' => '%d penghantaran telah dipautkan ke akaun anda',
    'api_token_created' => 'Token API "%s" telah dicipta',
    'google_sso_linked' => 'Akaun Google %s telah dipautkan',
    'google_account' => 'Akaun Google',

    // Analytics
    'analytics' => [
        'delivery_rate' => 'Kadar Penghantaran',
        'bounce_rate' => 'Kadar Lantunan',
        'queue_health' => 'Kesihatan Giliran',
        'total_sent' => 'Jumlah Dihantar',
        'last_30_days' => '30 hari lepas',
        'alert_threshold_exceeded' => 'Amaran: Ambang melebihi',
        'within_threshold' => 'Dalam ambang yang boleh diterima',
        'throughput_per_minute' => ':0 e-mel/min throughput',
        'delivered_count' => ':0 dihantar',
        'stuck_emails' => ':count tersekat dalam giliran',
        'pending_retries' => ':count menunggu percubaan semula',
    ],

    // Comment notifications (Phase 9)
    'comment_posted' => ':author telah menambah komen',
];
