<?php

declare(strict_types=1);

return [
    'accessibility_compliance' => [
        'label' => 'Pematuhan Kebolehcapaian',
        'group' => 'Pengurusan Sistem',
    ],

    'alert_configuration' => [
        'label' => 'Konfigurasi Amaran',
        'title' => 'Konfigurasi Amaran',
        'group' => 'Pengurusan Sistem',
        'sections' => [
            'tickets' => 'Amaran Tiket',
            'tickets_desc' => 'Tetapkan pemberitahuan tiket lewat dan ambang.',
            'loans' => 'Amaran Pinjaman',
            'loans_desc' => 'Urus kelulusan tertangguh dan amaran pinjaman lewat.',
            'assets' => 'Amaran Aset',
            'assets_desc' => 'Pantau kekurangan aset dan tetapkan ambang kritikal.',
            'system' => 'Kesihatan Sistem',
            'system_desc' => 'Tetapkan ambang masa tindak balas dan kesihatan sistem.',
            'delivery' => 'Saluran Penyampaian',
            'delivery_desc' => 'Pilih cara amaran dihantar kepada pentadbir dan staf.',
        ],
        'fields' => [
            'overdue_tickets_enabled' => 'Aktifkan amaran tiket lewat',
            'overdue_tickets_threshold' => 'Ambang tiket lewat (hari)',
            'overdue_tickets_threshold_help' => 'Bilangan hari selepas tarikh tamat sebelum amaran dicetuskan.',
            'overdue_loans_enabled' => 'Aktifkan amaran pinjaman lewat',
            'overdue_loans_threshold' => 'Ambang pinjaman lewat (hari)',
            'overdue_loans_threshold_help' => 'Bilangan hari selepas tarikh tamat sebelum amaran pinjaman dicetuskan.',
            'approval_delays_enabled' => 'Aktifkan amaran kelewatan kelulusan',
            'approval_delay_hours' => 'Ambang kelewatan kelulusan (jam)',
            'approval_delay_hours_help' => 'Jam sebelum eskalasi apabila kelulusan tertunggak.',
            'asset_shortages_enabled' => 'Aktifkan amaran kekurangan aset',
            'critical_asset_shortage_percentage' => 'Ambang kekurangan aset kritikal (%)',
            'critical_asset_shortage_percentage_help' => 'Peratus aset tersedia sebelum amaran dicetuskan.',
            'system_health_enabled' => 'Aktifkan amaran kesihatan sistem',
            'system_health_threshold' => 'Ambang kesihatan sistem (%)',
            'system_health_threshold_help' => 'Skor kesihatan minimum sebelum amaran dicetuskan.',
            'response_time_threshold' => 'Ambang masa tindak balas (ms)',
            'response_time_threshold_help' => 'Masa tindak balas maksimum sebelum amaran dicetuskan.',
            'email_notifications_enabled' => 'Hantar pemberitahuan e-mel',
            'admin_panel_notifications_enabled' => 'Tunjukkan amaran dalam panel admin',
            'alert_frequency' => 'Kekerapan amaran',
        ],
        'frequency' => [
            'immediate' => 'Serta-merta',
            'hourly' => 'Setiap jam',
            'daily' => 'Harian',
        ],
        'actions' => [
            'save' => 'Simpan Tetapan',
            'test' => 'Hantar Amaran Ujian',
            'reset' => 'Tetapkan Semula ke Lalai',
        ],
        'modals' => [
            'reset_heading' => 'Tetapkan Semula Konfigurasi Amaran',
            'reset_description' => 'Tetapkan semua tetapan amaran kepada konfigurasi lalai?',
            'reset_submit' => 'Tetapkan Semula Tetapan',
            'test_heading' => 'Hantar Amaran Ujian',
            'test_description' => 'Hantar amaran contoh menggunakan konfigurasi semasa.',
            'test_submit' => 'Hantar Ujian',
        ],
        'notifications' => [
            'saved_title' => 'Konfigurasi amaran disimpan',
            'saved_body' => 'Keutamaan amaran berjaya dikemas kini.',
            'save_failed_title' => 'Gagal menyimpan tetapan',
            'save_failed_body' => 'Sila semak borang dan cuba lagi.',
            'reset_title' => 'Konfigurasi amaran ditetapkan semula',
            'reset_body' => 'Tetapan telah dipulihkan ke lalai.',
            'reset_failed_title' => 'Tetapan semula gagal',
            'reset_failed_body' => 'Tidak dapat menetapkan semula tetapan. Sila cuba lagi.',
            'test_sent_title' => 'Amaran ujian dihantar',
            'test_sent_body' => 'Amaran ujian dihantar menggunakan konfigurasi semasa.',
            'test_failed_title' => 'Amaran ujian gagal',
            'test_failed_body' => 'Tidak dapat menghantar amaran ujian. Semak konfigurasi.',
        ],
    ],

    'approval_matrix' => [
        'label' => 'Matriks Kelulusan',
        'title' => 'Konfigurasi Matriks Kelulusan',
        'group' => 'Pengurusan Sistem',
    ],

    'bilingual_management' => [
        'label' => 'Pengurusan Dwibahasa',
        'group' => 'Pengurusan Sistem',
        'fields' => [
            'export_format' => 'Format eksport',
            'import_file' => 'Fail import',
        ],
        'actions' => [
            'validate' => 'Sahkan Terjemahan',
            'export' => 'Eksport Terjemahan',
            'import' => 'Import Terjemahan',
        ],
        'notifications' => [
            'validation_complete_title' => 'Pengesahan selesai',
            'validation_complete_body' => 'Tiada terjemahan hilang atau kosong ditemui.',
            'validation_issues_title' => 'Isu terjemahan dikesan',
            'validation_issues_body' => ':missing hilang, :empty terjemahan kosong ditemui.',
            'export_complete_title' => 'Eksport sedia',
            'export_complete_body' => 'Terjemahan dieksport ke :filename.',
            'import_complete_title' => 'Import berjaya',
            'import_complete_body' => 'Terjemahan berjaya diimport.',
            'import_failed_title' => 'Import gagal',
            'import_failed_body' => 'Tidak dapat mengimport terjemahan. Sila semak fail.',
            'no_file_title' => 'Tiada fail dipilih',
            'no_file_body' => 'Sila muat naik fail terjemahan sebelum mengimport.',
            'language_changed_title' => 'Bahasa ditukar',
            'language_changed_body' => 'Bahasa antaramuka ditukar kepada :language.',
        ],
    ],

    'email_queue' => [
        'label' => 'Pemantauan Barisan E-mel',
        'group' => 'Pengurusan Sistem',
    ],

    'email_templates' => [
        'label' => 'Pengurusan Templat E-mel',
        'group' => 'Pengurusan Sistem',
    ],

    'filter_presets' => [
        'label' => 'Pratetap Penapis',
        'title' => 'Pratetap Penapis Disimpan',
        'group' => 'Laporan',
    ],

    'notification_center' => [
        'label' => 'Pusat Notifikasi',
        'title' => 'Pusat Notifikasi',
        'group' => 'Pengurusan Sistem',
    ],

    'notification_preferences' => [
        'label' => 'Keutamaan Notifikasi',
        'title' => 'Keutamaan Notifikasi',
        'group' => 'Pengurusan Sistem',
    ],

    'pdpa_dashboard' => [
        'label' => 'Papan Pemuka PDPA',
    ],

    'performance_monitoring' => [
        'label' => 'Pemantauan Prestasi',
        'group' => 'Pengurusan Sistem',
    ],

    'report_builder' => [
        'label' => 'Pembina Laporan',
        'title' => 'Pembina Laporan',
        'group' => 'Laporan',
    ],

    'two_factor_auth' => [
        'label' => 'Pengesahan Dua Faktor',
        'group' => 'Pengurusan Sistem',
    ],

    'unified_search' => [
        'label' => 'Carian Bersepadu',
        'title' => 'Carian Bersepadu',
        'group' => 'Pengurusan Sistem',
    ],

    'workflow_automation' => [
        'label' => 'Automasi Aliran Kerja',
        'group' => 'Pengurusan Sistem',
    ],
];
