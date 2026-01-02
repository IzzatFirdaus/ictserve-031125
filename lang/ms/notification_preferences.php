<?php

declare(strict_types=1);

return [
    // Page heading and description
    'page_heading' => 'Keutamaan Pemberitahuan',
    'page_description' => 'Konfigurasikan cara dan bila anda menerima pemberitahuan daripada sistem.',

    // Summary section
    'current_settings_summary' => 'Ringkasan Tetapan Semasa',
    'summary' => [
        'delivery_methods' => 'Kaedah Penghantaran',
        'active_categories' => 'Kategori Aktif',
        'timing_settings' => 'Tetapan Masa',
        'priority_settings' => 'Tetapan Keutamaan',
        'email' => 'E-mel',
        'in_app' => 'Dalam Aplikasi',
        'sms' => 'SMS',
        'desktop' => 'Desktop',
        'helpdesk' => 'Meja Bantuan',
        'asset_loans' => 'Pinjaman Aset',
        'security' => 'Keselamatan',
        'system' => 'Sistem',
        'digest' => 'Ringkasan (Digest)',
        'quiet_hours' => 'Waktu Senyap',
        'weekends' => 'Hujung Minggu',
        'urgent_only' => 'Urgent Sahaja',
        'min_priority' => 'Keutamaan Minimum',
    ],

    // Status labels
    'enabled' => 'Diaktifkan',
    'disabled' => 'Dinyahaktifkan',
    'yes' => 'Ya',
    'no' => 'Tidak',

    // Digest frequency
    'digest_immediate' => 'Segera',
    'digest_hourly' => 'Setiap Jam',
    'digest_daily' => 'Harian',
    'digest_weekly' => 'Mingguan',

    // Priority levels
    'priority_low' => 'Rendah',
    'priority_medium' => 'Sederhana',
    'priority_high' => 'Tinggi',
    'priority_urgent' => 'Urgent',

    // Help section
    'help' => [
        'title' => 'Bantuan Pemberitahuan',
        'delivery_methods_title' => 'Kaedah Penghantaran',
        'email_desc' => 'Pemberitahuan dihantar ke alamat e-mel anda.',
        'in_app_desc' => 'Pemberitahuan dipaparkan dalam aplikasi.',
        'sms_desc' => 'Pemberitahuan dihantar melalui SMS (jika dikonfigurasi).',
        'desktop_desc' => 'Pemberitahuan desktop muncul di komputer anda.',
        'priority_levels_title' => 'Tahap Keutamaan',
        'priority_low_desc' => 'Maklumat umum dan kemas kini rutin.',
        'priority_medium_desc' => 'Perkara yang memerlukan perhatian dalam masa terdekat.',
        'priority_high_desc' => 'Perkara penting yang memerlukan tindakan segera.',
        'priority_urgent_desc' => 'Kritikal - memerlukan tindakan serta-merta.',
        'critical_always_delivered' => 'Pemberitahuan kritikal akan sentiasa dihantar tanpa mengira tetapan anda.',
    ],
    'note' => 'Nota',

    // Form sections
    'delivery_methods' => 'Kaedah Penghantaran',
    'choose_how_receive' => 'Pilih cara anda menerima pemberitahuan',
    'email_notifications' => 'Pemberitahuan E-mel',
    'receive_via_email' => 'Terima pemberitahuan melalui e-mel',
    'in_app_notifications' => 'Pemberitahuan Dalam Aplikasi',
    'show_in_admin_panel' => 'Paparkan pemberitahuan dalam panel admin',
    'sms_notifications' => 'Pemberitahuan SMS',
    'receive_via_sms' => 'Terima pemberitahuan melalui SMS',
    'desktop_notifications' => 'Pemberitahuan Desktop',
    'show_desktop_notifications' => 'Paparkan pemberitahuan desktop',

    // Notification type sections
    'helpdesk_section' => 'Pemberitahuan Meja Bantuan',
    'helpdesk_desc' => 'Pemberitahuan berkaitan tiket dan sokongan',
    'loan_section' => 'Pemberitahuan Pinjaman Aset',
    'loan_desc' => 'Pemberitahuan berkaitan permohonan dan pinjaman aset',
    'security_section' => 'Pemberitahuan Keselamatan',
    'security_desc' => 'Pemberitahuan berkaitan keselamatan sistem',
    'system_section' => 'Pemberitahuan Sistem',
    'system_desc' => 'Pemberitahuan berkaitan penyelenggaraan dan prestasi sistem',
    'notification_types' => 'Jenis Pemberitahuan',

    // Helpdesk notification types
    'ticket_assigned' => 'Tiket Ditugaskan',
    'ticket_status_changed' => 'Status Tiket Berubah',
    'sla_breach' => 'Pelanggaran SLA',
    'overdue_tickets' => 'Tiket Tertunggak',
    'new_comments' => 'Komen Baharu',
    'escalation_alerts' => 'Amaran Eskalasi',

    // Loan notification types
    'new_loan_applications' => 'Permohonan Pinjaman Baharu',
    'application_approved' => 'Permohonan Diluluskan',
    'application_rejected' => 'Permohonan Ditolak',
    'asset_overdue' => 'Aset Tertunggak',
    'return_reminder' => 'Peringatan Pemulangan',
    'damage_reports' => 'Laporan Kerosakan',

    // Security notification types
    'security_incidents' => 'Insiden Keselamatan',
    'failed_logins' => 'Log Masuk Gagal',
    'role_changes' => 'Perubahan Peranan',
    'config_changes' => 'Perubahan Konfigurasi',
    'suspicious_activity' => 'Aktiviti Mencurigakan',
    'audit_alerts' => 'Amaran Audit',

    // System notification types
    'maintenance_alerts' => 'Amaran Penyelenggaraan',
    'performance_alerts' => 'Amaran Prestasi',
    'backup_status' => 'Status Sandaran',
    'update_notifications' => 'Pemberitahuan Kemas Kini',
    'integration_alerts' => 'Amaran Integrasi',
    'queue_alerts' => 'Amaran Baris Gilir',

    // Frequency settings
    'frequency_section' => 'Tetapan Kekerapan',
    'frequency_desc' => 'Konfigurasikan bila dan berapa kerap anda menerima pemberitahuan',
    'digest_frequency' => 'Kekerapan Ringkasan',
    'enable_quiet_hours' => 'Aktifkan Waktu Senyap',
    'quiet_hours_start' => 'Mula Waktu Senyap',
    'quiet_hours_end' => 'Tamat Waktu Senyap',
    'weekend_notifications' => 'Pemberitahuan Hujung Minggu',

    // Priority settings
    'priority_section' => 'Tetapan Keutamaan',
    'priority_desc' => 'Konfigurasikan tahap keutamaan pemberitahuan',
    'urgent_only_mode' => 'Mod Urgent Sahaja',
    'priority_threshold' => 'Had Keutamaan',
    'only_receive_notifications_at_or_above_this_priority_level' => 'Hanya terima pemberitahuan pada atau melebihi tahap keutamaan ini',
    'low_and_above' => 'Rendah dan ke atas',
    'medium_and_above' => 'Sederhana dan ke atas',
    'high_and_above' => 'Tinggi dan ke atas',
    'urgent_only' => 'Urgent sahaja',

    // Actions
    'save_preferences' => 'Simpan Keutamaan',
    'reset_to_defaults' => 'Tetapkan Semula ke Lalai',
    'test_notifications' => 'Uji Pemberitahuan',
    'reset_modal_heading' => 'Tetapkan Semula Keutamaan',
    'reset_modal_desc' => 'Adakah anda pasti mahu menetapkan semula semua keutamaan ke nilai lalai?',

    // Notifications
    'preferences_saved' => 'Keutamaan pemberitahuan berjaya disimpan',
    'preferences_reset' => 'Keutamaan pemberitahuan ditetapkan semula ke lalai',
    'test_notifications_sent' => 'Pemberitahuan ujian telah dihantar',
];
