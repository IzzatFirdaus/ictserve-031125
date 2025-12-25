<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md

declare(strict_types=1);

return [
    'navigation' => [
        'operations' => 'Operasi',
        'inventory' => 'Inventori',
        'management' => 'Pengurusan',
        'system' => 'Sistem',
        'reports' => 'Laporan & Analitik',
        'ollama_ai' => 'Ollama AI',
        'asset_management' => 'Pengurusan Aset',
        'helpdesk_management' => 'Meja Bantuan',
        'loan_management' => 'Pinjaman Aset',
        'reference_data' => 'Data Rujukan',
        'system_management' => 'Pengurusan Sistem',
        'user_management' => 'Pengurusan Pengguna',
        'go_to_portal' => 'Pergi ke Portal',
        'brand_name' => 'ICTServe Admin',
        'dashboard' => 'Papan Pemuka',
        'home' => 'Laman Utama',
        // Additional navigation groups
        'system_configuration' => 'Konfigurasi Sistem',
        'email_management' => 'Pengurusan Email',
        'security' => 'Keselamatan',
        'compliance' => 'Pematuhan',
        'user_settings' => 'Tetapan Pengguna',
        'notification_center' => 'Pusat Pemberitahuan',
        'notification_preferences' => 'Keutamaan Pemberitahuan',
        'filter_presets' => 'Pratetap Penapis',
    ],

    // Admin Pages Navigation Groups - v3.6.1 Bahasa Melayu sahaja
    'admin_pages' => [
        'filter_presets' => [
            'group' => 'Sistem',
        ],
        'notification_center' => [
            'group' => 'Sistem',
        ],
        'notification_preferences' => [
            'group' => 'Sistem',
        ],
        'email_templates' => [
            'group' => 'Pengurusan Email',
        ],
        'email_queue' => [
            'group' => 'Pengurusan Email',
        ],
        'security_monitoring' => [
            'group' => 'Keselamatan',
        ],
        'two_factor_auth' => [
            'group' => 'Keselamatan',
        ],
        'accessibility_compliance' => [
            'group' => 'Pematuhan',
        ],
        'pdpa_dashboard' => [
            'group' => 'Pematuhan',
        ],
        'performance_monitoring' => [
            'group' => 'Sistem',
        ],
        'unified_search' => [
            'group' => 'Sistem',
        ],
        'report_builder' => [
            'group' => 'Laporan & Analitik',
        ],
        'data_visualization' => [
            'group' => 'Laporan & Analitik',
        ],
        'unified_analytics' => [
            'group' => 'Laporan & Analitik',
        ],
        'workflow_automation' => [
            'group' => 'Konfigurasi Sistem',
        ],
        'approval_matrix' => [
            'group' => 'Konfigurasi Sistem',
        ],
        'alert_configuration' => [
            'group' => 'Sistem',
        ],
        'bilingual_management' => [
            'group' => 'Konfigurasi Sistem',
        ],
        'bedrock_routing' => [
            'group' => 'Ollama AI',
        ],
    ],

    'labels' => [
        'application_number' => 'No. Permohonan',
        'form_reference' => 'Rujukan Borang',
        'applicant' => 'Pemohon',
        'division' => 'Bahagian',
        'status' => 'Status',
        'priority' => 'Keutamaan',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'overdue_status' => 'Status Lewat',
        'total_value' => 'Jumlah Nilai',
        'maintenance_required' => 'Penyelenggaraan Diperlukan',
        'approval_status' => 'Status Kelulusan',
        'submission_type' => 'Jenis Penghantaran',
        'responsible_officer' => 'Pegawai Bertanggungjawab',
        'created_from' => 'Dicipta Dari',
        'created_until' => 'Dicipta Hingga',
        'asset_type' => 'Jenis Aset',
        'category' => 'Kategori',
        'approval_method' => 'Kaedah Kelulusan',
        'submission_type_filter' => 'Jenis Penghantaran',
        // Asset-specific labels
        'tag' => 'Tag Aset',
        'name' => 'Nama',
        'brand' => 'Jenama',
        'model' => 'Model',
        'serial_number' => 'No. Siri',
        'condition' => 'Keadaan',
        'location' => 'Lokasi',
        'purchase_date' => 'Tarikh Pembelian',
        'current_value' => 'Nilai Semasa',
        'next_maintenance_date' => 'Tarikh Penyelenggaraan Seterusnya',
        'warranty_expiry' => 'Tamat Tempoh Waranti',
        'age' => 'Umur',
    ],

    'status' => [
        'overdue_days' => ':count hari lewat',
        'due_soon' => 'Hampir tamat',
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'pending' => 'Menunggu',
        'not_submitted' => 'Belum Dihantar',
        'applicant_is_responsible' => 'Pemohon Bertanggungjawab',
        'different_officer' => 'Pegawai Lain: :name',
        // Maintenance status
        'no_maintenance_schedule' => 'Tiada jadual penyelenggaraan',
        'overdue_maintenance' => 'Penyelenggaraan lewat :days hari',
        'due_today' => 'Dijadualkan hari ini',
        'due_in_days' => 'Dijadualkan dalam :days hari',
        // Warranty status
        'no_warranty' => 'Tiada waranti',
        'warranty_expired' => 'Waranti telah tamat',
        'warranty_expires_in' => 'Waranti tamat :time',
    ],

    'tooltips' => [
        'approval_approved' => 'Kelulusan 1: :name1 | Kelulusan 2: :name2',
        'approval_rejected' => 'Ditolak oleh: :name',
        'approval_pending' => 'Menunggu kelulusan',
        'approval_not_submitted' => 'Belum dihantar untuk kelulusan',
        'applicant_responsible' => 'Pemohon bertanggungjawab',
        'different_responsible_officer' => 'Pegawai lain bertanggungjawab',
        // Maintenance tooltips
        'maintenance_next' => 'Penyelenggaraan seterusnya: :date (:status)',
        // Warranty tooltips
        'warranty_expired_on' => 'Waranti tamat pada :date',
        'warranty_expires_on' => 'Waranti tamat pada :date (:time)',
    ],

    'date_filters' => [
        'select_start_date' => 'Pilih tarikh mula',
        'select_end_date' => 'Pilih tarikh tamat',
        'from_date' => 'Dari Tarikh',
        'until_date' => 'Hingga Tarikh',
        'category_filter' => 'Tapis mengikut kategori',
    ],

    'asset_categories' => [
        'computer' => 'Komputer',
        'laptop' => 'Komputer Riba',
        'printer' => 'Pencetak',
        'projector' => 'Projektor',
        'camera' => 'Kamera',
        'other' => 'Lain-lain',
    ],

    'filters' => [
        'pending_approval' => 'Menunggu Kelulusan',
        'approval_indicator' => 'Penunjuk Kelulusan',
        'approved' => 'Diluluskan',
        'overdue' => 'Lewat',
        'overdue_indicator' => 'Penunjuk Lewat',
        'guest_submission' => 'Tetamu',
        'authenticated_submission' => 'Pengguna Berdaftar',
        'email_approval' => 'E-mel',
        'portal_approval' => 'Portal',
        // Asset-specific filters
        'needs_maintenance' => 'Perlu Penyelenggaraan',
        'maintenance_indicator' => 'Penyelenggaraan Diperlukan',
        'available' => 'Tersedia',
        'in_use' => 'Sedang Digunakan',
        'warranty_expiring' => 'Waranti Hampir Tamat',
    ],

    'actions' => [
        'send_for_approval' => 'Hantar untuk Kelulusan',
        'approve' => 'Luluskan',
        'approval_remarks' => 'Catatan Kelulusan',
        'decline' => 'Tolak',
        'rejection_reason' => 'Sebab Penolakan',
        'extend' => 'Lanjutkan',
        'new_date' => 'Tarikh Baru',
        'instructions' => 'Arahan',
        'export_pdf' => 'Eksport PDF',
        'export_excel' => 'Eksport Excel',
        'export_report' => 'Eksport Laporan',
        'reason' => 'Sebab',
        // Asset-specific actions
        'mark_maintenance' => 'Tandai Penyelenggaraan',
        'update_status' => 'Kemaskini Status',
        'update_condition' => 'Kemaskini Keadaan',
        'update_location' => 'Kemaskini Lokasi',
        'new_location' => 'Lokasi Baru',
        'export' => 'Eksport',
    ],

    'notifications' => [
        'status_updated' => 'Status Dikemaskini',
        'condition_updated' => 'Keadaan Dikemaskini',
        'location_updated' => 'Lokasi Dikemaskini',
        'assets_updated' => ':count aset telah dikemaskini',
        'assets_updated_simple' => 'Lokasi aset telah dikemaskini',
    ],

    'announcements' => [
        'dark_mode_enabled' => 'Mod gelap diaktifkan',
        'light_mode_enabled' => 'Mod cerah diaktifkan',
    ],

    'reference' => [
        'code' => 'Kod',
        'name_ms' => 'Nama (BM)',
        'parent' => 'Induk',
        'active' => 'Aktif',
        'status' => 'Status',
        'level' => 'Tahap',
        'can_approve' => 'Boleh Lulus',
        'parent_division' => 'Bahagian Induk',
        'description_ms' => 'Deskripsi (BM)',
        'can_approve_loans' => 'Boleh Lulus Pinjaman (G41+)',
    ],

    'issuance' => [
        'otp_code' => 'Kod OTP',
        'applicant' => 'Pemohon',
        'application_number' => 'No. Permohonan',
        'issuance_datetime' => 'Tarikh & Masa Pengeluaran',
        'issued_by' => 'Dikeluarkan Oleh',
        'asset_condition' => 'Keadaan Aset',
        'asset' => 'Aset',
        'condition' => 'Keadaan',
        'condition_notes' => 'Catatan Keadaan',
        'standard_accessories' => 'Aksesori Standard / Standard Accessories',
        'accessory_type' => 'Jenis Aksesori',
        'accessory_name' => 'Nama Aksesori (untuk Lain-lain)',
        'included' => 'Disertakan / Included',
        'accessory_condition_notes' => 'Catatan Keadaan / Condition Notes',
        'special_instructions' => 'Arahan Khas',
        'confirmation' => 'Saya mengesahkan bahawa semua butiran adalah tepat dan aset telah diserahkan kepada pemohon',
    ],

    'return' => [
        'borrower' => 'Peminjam',
        'application_number' => 'No. Permohonan',
        'return_datetime' => 'Tarikh & Masa Pemulangan',
    ],

    'widget' => [
        'time' => 'Masa',
        'user' => 'Pengguna',
        'action' => 'Tindakan',
        'data_type' => 'Jenis Data',
        'ip_address' => 'Alamat IP',
    ],
];
