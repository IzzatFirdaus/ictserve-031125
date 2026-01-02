<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md

declare(strict_types=1);

return [
    'email_templates' => [
        'title' => 'Pengurusan Template Email',
        'label' => 'Template Email',
        'group' => 'Pengurusan Email',
    ],

    'notification_center' => [
        'title' => 'Pusat Pemberitahuan',
        'label' => 'Pusat Pemberitahuan',
        'group' => 'Sistem',
        'kpi' => [
            'total' => 'Jumlah Pemberitahuan',
            'unread' => 'Belum Dibaca',
            'today' => 'Hari Ini',
            'this_week' => 'Minggu Ini',
        ],
        'tabs' => [
            'all' => 'Semua Pemberitahuan',
            'unread' => 'Belum Dibaca',
            'read' => 'Dibaca',
        ],
        'empty' => [
            'title' => 'Tiada pemberitahuan',
            'unread_title' => 'Tiada pemberitahuan belum dibaca',
            'read_title' => 'Tiada pemberitahuan yang telah dibaca',
            'description' => 'Anda belum mempunyai sebarang pemberitahuan.',
            'guidance' => 'Pemberitahuan akan muncul apabila terdapat kemas kini tiket, kelulusan, atau amaran sistem.',
        ],
        'actions' => [
            'view_details' => 'Lihat Butiran',
            'mark_read' => 'Tandakan Dibaca',
            'mark_unread' => 'Tandakan Belum Dibaca',
            'delete' => 'Padam',
            'mark_all_read' => 'Tandakan Semua Dibaca',
            'clear_all' => 'Kosongkan Semua',
            'preferences' => 'Keutamaan',
            'refresh' => 'Muat Semula',
            'load_more' => 'Muatkan Lagi Pemberitahuan',
            'confirm' => 'Sahkan',
            'cancel' => 'Batal',
        ],
        'badges' => [
            'high_priority' => 'Keutamaan Tinggi',
            'urgent' => 'Segera',
        ],
        'modals' => [
            'clear_all_heading' => 'Kosongkan Semua Pemberitahuan',
            'clear_all_description' => 'Adakah anda pasti mahu memadam semua pemberitahuan? Tindakan ini tidak boleh dibatalkan.',
            'delete_confirm' => 'Adakah anda pasti mahu memadam pemberitahuan ini?',
        ],
        'status' => [
            'read_at' => 'Dibaca :time',
        ],
    ],

    'notification_preferences' => [
        'title' => 'Keutamaan Pemberitahuan',
        'label' => 'Keutamaan Pemberitahuan',
        'group' => 'Keutamaan Pemberitahuan',
    ],

    'pdpa_dashboard' => [
        'title' => 'Papan Pemuka PDPA',
        'label' => 'Papan Pemuka PDPA',
        'group' => 'Pematuhan',
        'description' => 'Pemantauan pematuhan perlindungan data peribadi dan pelaporan.',
        'access_note' => 'Sesetengah maklumat sensitif hanya boleh diakses oleh Superuser.',
        'records_exceeding_7_years' => 'Rekod Melebihi 7 Tahun',
        'records_need_archival' => 'Rekod perlu diarkib atau dipadam',
        'no_records_need_archival' => 'Tiada rekod memerlukan tindakan',
        'sensitive_access_log' => 'Akses Data Sensitif Terkini',
        'restricted_to_superuser' => 'Terhad kepada Superuser',
        'restricted_description' => 'Log akses data sensitif hanya boleh dilihat oleh pengguna dengan peranan Superuser.',
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
        'title' => 'Pratetap Penapis',
        'label' => 'Pratetap Penapis',
        'group' => 'Sistem',
        'sections' => [
            'select_resource' => 'Pilih Sumber',
            'quick_filters' => 'Penapis Pantas',
            'saved_presets' => 'Preset Tersimpan',
            'usage_tips' => 'Petua Penggunaan',
        ],
        'resources' => [
            'helpdesk_tickets' => 'Tiket Helpdesk',
            'loan_applications' => 'Permohonan Pinjaman',
            'assets' => 'Aset',
            'users' => 'Pengguna',
        ],
        'quick_filters' => [
            'helpdesk' => [
                'open_high_priority' => 'Tiket Keutamaan Tinggi (Masih Dibuka)',
            ],
            'loans' => [
                'pending_approval' => 'Permohonan Menunggu Kelulusan',
            ],
            'assets' => [
                'available' => 'Aset Tersedia',
            ],
            'users' => [
                'active' => 'Pengguna Aktif',
            ],
            'click_to_apply' => 'Klik untuk terapkan',
        ],
        'fields' => [
            'name' => 'Nama Preset',
            'resource' => 'Sumber',
            'is_default' => 'Jadikan sebagai preset lalai',
            'is_default_help' => 'Preset lalai akan digunakan secara automatik apabila anda membuka sumber ini.',
        ],
        'actions' => [
            'create' => 'Cipta Preset Baharu',
            'save' => 'Simpan',
            'cancel' => 'Batal',
            'apply' => 'Guna',
            'set_default' => 'Lalai',
            'delete' => 'Padam',
        ],
        'badges' => [
            'default' => 'Lalai',
        ],
        'empty' => [
            'title' => 'Tiada preset tersimpan',
            'description' => 'Cipta preset baharu untuk menyimpan kombinasi penapis yang kerap digunakan.',
        ],
        'notifications' => [
            'created' => 'Preset berjaya dicipta',
            'deleted' => 'Preset berjaya dipadam',
            'set_default' => 'Preset ditetapkan sebagai lalai',
            'not_found' => 'Preset tidak dijumpai',
        ],
        'tips' => [
            'default_preset' => 'Preset Lalai: Akan digunakan secara automatik apabila anda membuka sumber tersebut',
            'quick_filters' => 'Penapis Pantas: Kombinasi penapis yang kerap digunakan untuk akses pantas',
            'bookmarkable_url' => 'URL Boleh Ditanda: Setiap preset menghasilkan URL yang boleh ditanda untuk akses terus',
            'sharing' => 'Perkongsian: Kongsi URL preset dengan ahli pasukan untuk akses yang konsisten',
        ],
        'meta' => [
            'created_at' => 'Dicipta',
            'filters_count' => 'Penapis: :count kriteria',
        ],
        'confirm' => [
            'delete' => 'Adakah anda pasti mahu memadam preset ini?',
        ],
    ],

    'data_visualization' => [
        'title' => 'Visualisasi Data',
        'label' => 'Visualisasi Data',
        'group' => 'Laporan & Analitik',
    ],

    'report_templates' => [
        'title' => 'Template Laporan',
        'label' => 'Template Laporan',
        'group' => 'Laporan & Analitik',
    ],

    'data_export_center' => [
        'title' => 'Pusat Eksport Data',
        'label' => 'Pusat Eksport Data',
        'group' => 'Laporan & Analitik',
    ],

    'unified_analytics' => [
        'title' => 'Analitik Terpadu',
        'label' => 'Analitik Terpadu',
        'group' => 'Laporan & Analitik',
        'tooltips' => [
            'active_items' => 'Jumlah item aktif termasuk tiket tertunggak dan pinjaman aktif',
            'overdue_tickets' => 'Tiket yang telah melebihi tarikh akhir SLA dan belum diselesaikan',
            'system_health' => 'Skor kesihatan sistem berdasarkan kadar penyelesaian tiket, kelulusan pinjaman, dan ketersediaan aset',
        ],
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
        'hero_title' => 'Apa yang anda cari?',
        'hero_subtitle' => 'Carian segera untuk tiket, pinjaman, aset, dan pengguna.',
        'input_label' => 'Carian global',
        'placeholder' => 'Taip untuk mencari...',
        'clear' => 'Kosongkan',
        'searching' => 'Mencari...',
        'shortcut_hint' => 'Pintasan papan kekunci: Ctrl/⌘K',
        'filters' => [
            'tickets' => 'Cari Tiket',
            'loans' => 'Cari Pinjaman',
            'assets' => 'Cari Aset',
            'users' => 'Cari Pengguna',
        ],
        'sections' => [
            'tickets' => 'Tiket Meja Bantuan',
            'loans' => 'Permohonan Pinjaman',
            'assets' => 'Aset',
            'users' => 'Pengguna',
        ],
        'assets_count_label' => 'aset',
        'found_results' => 'Dijumpai :count keputusan untuk ":query".',
        'no_results_title' => 'Tiada keputusan dijumpai',
        'no_results_message' => 'Tiada padanan untuk ":query". Cuba kata kunci lain.',
        'toggle_filter' => 'Togol penapis :filter',
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

    'bedrock_routing' => [
        'title' => 'Konfigurasi Penghalaan Bedrock',
        'label' => 'Penghalaan Bedrock',
        'group' => 'Ollama AI',
        'sections' => [
            'general' => 'Tetapan Umum',
            'general_desc' => 'Tetapkan sama ada Bedrock diaktifkan dan perlindungan PII.',
            'routing' => 'Logik Penghalaan',
            'routing_desc' => 'Tetapan cache dan had input untuk keputusan penghalaan.',
            'rate_limits' => 'Had Kadar',
            'rate_limits_desc' => 'Kawalan had kadar permintaan Bedrock. Had per-model mengikut konfigurasi aplikasi.',
            'classification' => 'Pengelasan Data',
            'classification_desc' => 'Tetapan asas untuk mengawal pemprosesan cloud mengikut pengelasan data.',
            'budgets' => 'Bajet & Kos',
            'budgets_desc' => 'Tetapan asas bajet bulanan dan kawalan henti keras (hard stop).',
        ],
        'fields' => [
            'enabled' => 'Aktifkan Bedrock',
            'prevent_cloud_pii' => 'Sekat PII ke Cloud',
            'enforce_malaysia_residency' => 'Kuatkuasakan Residensi Malaysia',
            'enforce_malaysia_residency_help' => 'Jika diaktifkan, Bedrock hanya digunakan apabila residensi Malaysia disahkan oleh pemanggil.',
            'cache_ttl_seconds' => 'Tempoh Cache (saat)',
            'simple_faq_max_words' => 'Had Maksimum Perkataan (FAQ ringkas)',
            'max_prompt_chars' => 'Had Maksimum Aksara Prompt',
            'rate_limit_enabled' => 'Aktifkan Had Kadar',
            'max_attempts_per_minute' => 'Had Maksimum Percubaan/Min',
            'require_consent_for_internal' => 'Perlu Persetujuan untuk Data Dalaman',
            'block_restricted' => 'Sekat Data Terhad (Restricted)',
            'budget_enabled' => 'Aktifkan Bajet',
            'monthly_budget_usd' => 'Bajet Bulanan (USD)',
            'budget_hard_stop' => 'Henti Keras Bila Bajet Habis',
        ],
        'actions' => [
            'save' => 'Simpan',
            'reset' => 'Tetapkan Semula',
        ],
        'notifications' => [
            'saved_title' => 'Disimpan',
            'saved_body' => 'Konfigurasi penghalaan Bedrock berjaya dikemas kini.',
            'save_failed_title' => 'Simpan Gagal',
            'save_failed_body' => 'Gagal menyimpan konfigurasi. Sila cuba lagi.',
            'reset_title' => 'Ditentukan Semula',
            'reset_body' => 'Konfigurasi telah ditetapkan semula ke nilai lalai.',
            'reset_failed_title' => 'Tetapkan Semula Gagal',
            'reset_failed_body' => 'Gagal menetapkan semula konfigurasi. Sila cuba lagi.',
        ],
    ],

    'helpdesk_reports' => [
        'title' => 'Laporan & Analitik Meja Bantuan',
        'label' => 'Laporan & Analitik',
        'filters_heading' => 'Penapis Laporan',
        'filters_description' => 'Pilih julat tarikh untuk menjana laporan.',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'generate' => 'Jana Laporan',
        'export' => 'Eksport Data',
        'empty_state' => 'Sila pilih julat tarikh dan klik \'Jana Laporan\'.',
        'no_data' => 'Tiada tiket dijumpai untuk julat tarikh yang dipilih.',
        'no_chart_data' => 'Tiada data untuk dipaparkan.',
        'kpi_total_tickets' => 'Jumlah Tiket',
        'kpi_guest_submissions' => 'Hantaran Tetamu',
        'kpi_avg_resolution_time' => 'Purata Masa Penyelesaian',
        'kpi_sla_compliance' => 'Pematuhan SLA',
        'by_status' => 'Tiket mengikut Status',
        'by_priority' => 'Tiket mengikut Keutamaan',
        'by_category' => 'Tiket mengikut Kategori',
    ],

    'asset_lifecycle_report' => [
        'title' => 'Laporan Kitaran Hayat Aset',
        'label' => 'Kitaran Hayat Aset',
        'description' => 'Laporan komprehensif mengenai kitaran hayat aset termasuk tarikh perolehan, sejarah penyelenggaraan, dan ramalan akhir hayat.',
        'filters' => 'Penapis Laporan',
        'filters_description' => 'Pilih kriteria untuk menjana laporan kitaran hayat aset',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'category' => 'Kategori Aset',
        'status' => 'Status',
        'lifecycle_stage' => 'Peringkat Kitaran Hayat',
        'stage_new' => 'Baharu',
        'stage_active' => 'Aktif',
        'stage_maintenance' => 'Penyelenggaraan',
        'stage_end_of_life' => 'Akhir Hayat',
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'acquisition_date' => 'Tarikh Perolehan',
        'last_maintenance' => 'Penyelenggaraan Terakhir',
        'next_maintenance' => 'Penyelenggaraan Seterusnya',
        'generate_report' => 'Jana Laporan',
        'empty_state' => 'Tiada Aset Dijumpai',
        'empty_state_description' => 'Tiada aset yang sepadan dengan kriteria penapis yang dipilih.',
        'kpi_total' => 'Jumlah Aset',
        'kpi_new' => 'Aset Baharu',
        'kpi_maintenance' => 'Perlu Penyelenggaraan',
        'kpi_end_of_life' => 'Akhir Hayat',
    ],

    'alert_configuration' => [
        'title' => 'Konfigurasi Sistem Amaran',
        'label' => 'Konfigurasi Amaran',
        'group' => 'Sistem',
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
