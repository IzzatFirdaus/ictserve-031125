<?php

declare(strict_types=1);

return [
    'email_templates' => [
        'title' => 'Pengurusan Template Email',
        'label' => 'Template Email',
        'group' => 'Pengurusan Email',
    ],

    'notification_center' => [
        'title' => 'Pusat Notifikasi',
        'label' => 'Notifikasi',
        'group' => 'Sistem',
    ],

    'notification_preferences' => [
        'title' => 'Keutamaan Notifikasi',
        'label' => 'Keutamaan Notifikasi',
        'group' => 'Tetapan Pengguna',
    ],

    'pdpa_dashboard' => [
        'title' => 'Papan Pemuka PDPA',
        'label' => 'Papan Pemuka PDPA',
        'group' => 'Pematuhan',
    ],

    'performance_monitoring' => [
        'title' => 'Pemantauan Prestasi',
        'label' => 'Pemantauan Prestasi',
        'group' => 'Sistem',
    ],

    'report_builder' => [
        'title' => 'Pembina Laporan',
        'label' => 'Pembina Laporan',
        'group' => 'Laporan',
    ],

    'security_monitoring' => [
        'title' => 'Pemantauan Keselamatan',
        'label' => 'Pemantauan Keselamatan',
        'group' => 'Keselamatan',
    ],

    'filter_presets' => [
        'title' => 'Preset Penapis',
        'label' => 'Preset Penapis',
        'group' => 'Tetapan Pengguna',
    ],

    'email_queue' => [
        'title' => 'Pemantauan Baris Gilir Email',
        'label' => 'Pemantauan Baris Gilir Email',
        'group' => 'Pengurusan Email',
    ],

    'bilingual_management' => [
        'title' => 'Pengurusan Bahasa',
        'label' => 'Pengurusan Bahasa',
        'group' => 'Konfigurasi Sistem',
        'fields' => [
            'export_format' => 'Format Eksport',
            'import_file' => 'Fail Import',
        ],
        'actions' => [
            'validate' => 'Sahkan Terjemahan',
            'export' => 'Eksport Terjemahan',
            'import' => 'Import Terjemahan',
        ],
        'notifications' => [
            'validation_complete_title' => 'Pengesahan Terjemahan Selesai',
            'validation_complete_body' => 'Tiada isu dijumpai. Semua terjemahan lengkap.',
            'validation_issues_title' => 'Isu Terjemahan Dijumpai',
            'validation_issues_body' => 'Terjemahan hilang: :missing, Terjemahan kosong: :empty',
            'export_complete_title' => 'Eksport Selesai',
            'export_complete_body' => 'Terjemahan dieksport sebagai :filename',
            'no_file_title' => 'Tiada Fail Dipilih',
            'no_file_body' => 'Sila pilih fail untuk import',
            'import_complete_title' => 'Import Selesai',
            'import_complete_body' => 'Terjemahan berjaya diimport',
            'import_failed_title' => 'Import Gagal',
            'import_failed_body' => 'Gagal import terjemahan. Sila semak format fail.',
            'language_changed_title' => 'Bahasa Ditukar',
            'language_changed_body' => 'Bahasa antara muka ditukar kepada :language',
        ],
    ],

    'approval_matrix' => [
        'title' => 'Konfigurasi Matriks Kelulusan',
        'label' => 'Matriks Kelulusan',
        'group' => 'Konfigurasi Sistem',
    ],

    'accessibility_compliance' => [
        'title' => 'Pematuhan Kebolehcapaian',
        'label' => 'Pematuhan Kebolehcapaian',
        'group' => 'Pematuhan',
    ],

    'unified_search' => [
        'title' => 'Carian Global',
        'label' => 'Carian Global',
        'group' => 'Sistem',
    ],

    'workflow_automation' => [
        'title' => 'Konfigurasi Automasi Aliran Kerja',
        'label' => 'Automasi Aliran Kerja',
        'group' => 'Konfigurasi Sistem',
    ],

    'two_factor_auth' => [
        'title' => 'Pengurusan 2FA',
        'label' => 'Pengurusan 2FA',
        'group' => 'Keselamatan',
    ],

    'alert_configuration' => [
        'title' => 'Konfigurasi Sistem Amaran',
        'label' => 'Konfigurasi Amaran',
        'group' => 'System',
        'sections' => [
            'tickets' => 'Konfigurasi Amaran Tiket',
            'tickets_desc' => 'Tetapkan had dan konfigurasi untuk amaran tiket helpdesk',
            'loans' => 'Konfigurasi Amaran Pinjaman',
            'loans_desc' => 'Tetapkan had dan konfigurasi untuk amaran pinjaman aset',
            'assets' => 'Konfigurasi Amaran Aset',
            'assets_desc' => 'Tetapkan had dan konfigurasi untuk amaran aset dan inventori',
            'system' => 'Konfigurasi Amaran Sistem',
            'system_desc' => 'Tetapkan had untuk amaran kesihatan sistem keseluruhan',
            'delivery' => 'Konfigurasi Penyampaian',
            'delivery_desc' => 'Tetapkan kaedah dan kekerapan penyampaian amaran',
        ],
        'fields' => [
            'overdue_tickets_enabled' => 'Aktifkan Amaran Tiket Tertunggak',
            'overdue_tickets_threshold' => 'Had Tiket Tertunggak',
            'overdue_tickets_threshold_help' => 'Bilangan tiket tertunggak sebelum amaran dihantar',
            'overdue_loans_enabled' => 'Aktifkan Amaran Pinjaman Tertunggak',
            'overdue_loans_threshold' => 'Had Pinjaman Tertunggak',
            'overdue_loans_threshold_help' => 'Bilangan pinjaman tertunggak sebelum amaran dihantar',
            'approval_delays_enabled' => 'Aktifkan Amaran Kelewatan Kelulusan',
            'approval_delay_hours' => 'Had Kelewatan Kelulusan (Jam)',
            'approval_delay_hours_help' => 'Bilangan jam sebelum amaran kelewatan kelulusan dihantar',
            'asset_shortages_enabled' => 'Aktifkan Amaran Kekurangan Aset',
            'critical_asset_shortage_percentage' => 'Had Kekurangan Aset Kritikal (%)',
            'critical_asset_shortage_percentage_help' => 'Peratusan ketersediaan minimum sebelum amaran dihantar',
            'system_health_enabled' => 'Aktifkan Amaran Kesihatan Sistem',
            'system_health_threshold' => 'Had Skor Kesihatan Sistem (%)',
            'system_health_threshold_help' => 'Skor kesihatan minimum sebelum amaran dihantar',
            'response_time_threshold' => 'Had Masa Respons (Saat)',
            'response_time_threshold_help' => 'Masa respons maksimum sebelum amaran prestasi dihantar',
            'email_notifications_enabled' => 'Aktifkan Notifikasi Email',
            'admin_panel_notifications_enabled' => 'Aktifkan Notifikasi Panel Admin',
            'alert_frequency' => 'Kekerapan Semakan Amaran',
        ],
        'frequency' => [
            'immediate' => 'Segera (Real-time)',
            'hourly' => 'Setiap Jam',
            'daily' => 'Harian',
        ],
        'actions' => [
            'save' => 'Simpan Konfigurasi',
            'test' => 'Uji Amaran',
            'reset' => 'Reset ke Default',
        ],
        'modals' => [
            'test_heading' => 'Uji Sistem Amaran',
            'test_description' => 'Adakah anda pasti untuk menguji sistem amaran? Ini akan menghantar amaran ujian kepada penerima yang dikonfigurasi.',
            'test_submit' => 'Ya, Uji Amaran',
            'reset_heading' => 'Reset Konfigurasi',
            'reset_description' => 'Adakah anda pasti untuk reset semua konfigurasi ke nilai default?',
            'reset_submit' => 'Ya, Reset',
        ],
        'notifications' => [
            'saved_title' => 'Konfigurasi Disimpan',
            'saved_body' => 'Konfigurasi amaran telah berjaya disimpan dan akan berkuat kuasa serta-merta.',
            'save_failed_title' => 'Gagal Menyimpan',
            'save_failed_body' => 'Ralat semasa menyimpan konfigurasi: :error',
            'test_sent_title' => 'Ujian Amaran Dihantar',
            'test_sent_body' => 'Amaran ujian telah dihantar kepada semua penerima yang dikonfigurasi. Semak email dan panel admin untuk mengesahkan penerimaan.',
            'test_failed_title' => 'Ujian Gagal',
            'test_failed_body' => 'Ralat semasa menguji amaran: :error',
            'reset_title' => 'Konfigurasi Direset',
            'reset_body' => 'Semua konfigurasi amaran telah direset ke nilai default.',
            'reset_failed_title' => 'Reset Gagal',
            'reset_failed_body' => 'Ralat semasa mereset konfigurasi: :error',
        ],
    ],
];
