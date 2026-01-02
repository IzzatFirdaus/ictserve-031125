<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md

declare(strict_types=1);

/**
 * ICTServe v3.6.1 - Bahasa Melayu (Primary Language)
 * Filament Custom Translations
 *
 * @version 3.6.1
 *
 * @updated 2025-12-23
 *
 * @language Bahasa Melayu (Primary)
 */

return [
    // Navigation - v3.6.1 (used in AdminPanelProvider)
    'navigation' => [
        'brand_name' => 'ICTServe MOTAC',
        'dashboard' => 'Papan Pemuka',
        'operations' => 'Operasi',
        'management' => 'Pengurusan',
        'system' => 'Sistem',
        'reports' => 'Laporan',
        'helpdesk_management' => 'Pengurusan Helpdesk',
        'inventory' => 'Inventori',
        'loan_management' => 'Pengurusan Pinjaman',
    ],

    // Resources - Model labels for Filament resources
    'resources' => [
        'asset' => [
            'singular' => 'Aset',
            'plural' => 'Aset',
            'navigation' => 'Aset',
        ],
        'asset_category' => [
            'singular' => 'Kategori Aset',
            'plural' => 'Kategori Aset',
            'navigation' => 'Kategori Aset',
        ],
        'helpdesk_ticket' => [
            'singular' => 'Tiket Meja Bantuan',
            'plural' => 'Tiket Meja Bantuan',
            'navigation' => 'Tiket Meja Bantuan',
        ],
        'ticket_category' => [
            'singular' => 'Kategori Tiket',
            'plural' => 'Kategori Tiket',
            'navigation' => 'Kategori Tiket',
        ],
        'loan_application' => [
            'singular' => 'Permohonan Pinjaman',
            'plural' => 'Permohonan Pinjaman',
            'navigation' => 'Permohonan Pinjaman',
        ],
        'division' => [
            'singular' => 'Bahagian',
            'plural' => 'Bahagian',
            'navigation' => 'Bahagian',
        ],
        'grade' => [
            'singular' => 'Gred',
            'plural' => 'Gred',
            'navigation' => 'Gred',
        ],
        'user' => [
            'singular' => 'Pengguna',
            'plural' => 'Pengguna',
            'navigation' => 'Pengguna',
        ],
    ],

    // Labels - Table/Form columns (used in Resources/Tables)
    'labels' => [
        'name' => 'Nama',
        'category' => 'Kategori',
        'status' => 'Status',
        'condition' => 'Keadaan',
        'application_number' => 'No. Permohonan',
        'form_reference' => 'Rujukan Borang',
        'applicant' => 'Pemohon',
        'division' => 'Bahagian',
        'priority' => 'Keutamaan',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'loan_date' => 'Tarikh Pinjaman',
        'return_date' => 'Tarikh Pulang',
        'purpose' => 'Tujuan',
        'location' => 'Lokasi',
        'responsible_officer' => 'Pegawai Bertanggungjawab',
        'created_at' => 'Dicipta Pada',
        'updated_at' => 'Dikemaskini Pada',
        'actions' => 'Tindakan',
        'view' => 'Lihat',
        'edit' => 'Kemaskini',
        'delete' => 'Padam',
        'create' => 'Cipta',
        'save' => 'Simpan',
        'cancel' => 'Batal',
        'submit' => 'Hantar',
        'approve' => 'Lulus',
        'reject' => 'Tolak',
        'issue' => 'Keluarkan',
        'return' => 'Pulangkan',
        'ticket_number' => 'No. Tiket',
        'subject' => 'Subjek',
        'description' => 'Keterangan',
        'requester' => 'Pemohon',
        'assigned_to' => 'Ditugaskan Kepada',
        'resolution' => 'Penyelesaian',
        'asset' => 'Aset',
        'asset_tag' => 'Tag Aset',
        'serial_number' => 'No. Siri',
        'brand' => 'Jenama',
        'model' => 'Model',
        'quantity' => 'Kuantiti',
        'unit' => 'Unit',
        'price' => 'Harga',
        'value' => 'Nilai',
        'notes' => 'Nota',
        'remarks' => 'Catatan',
        'attachments' => 'Lampiran',
    ],

    // Reference Data - used in Reference Resources
    'reference' => [
        'code' => 'Kod',
        'name_ms' => 'Nama (BM)',
        'name_en' => 'Nama (EN)',
        'level' => 'Tahap',
        'parent' => 'Induk',
        'parent_division' => 'Bahagian Induk',
        'active' => 'Aktif',
        'status' => 'Status',
        'can_approve' => 'Boleh Melulus',
        'can_approve_loans' => 'Boleh Melulus Pinjaman',
        'description_ms' => 'Keterangan (BM)',
        'description_en' => 'Keterangan (EN)',
    ],

    // Widget Labels - Dashboard widgets
    'widget' => [
        'time' => 'Masa',
        'user' => 'Pengguna',
        'action' => 'Tindakan',
        'data_type' => 'Jenis Data',
        'ip_address' => 'Alamat IP',
        'total' => 'Jumlah',
        'pending' => 'Menunggu',
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'system_health' => 'Kesihatan Sistem',
        'quick_actions' => 'Tindakan Pantas',
        'helpdesk_stats' => 'Statistik Helpdesk',
        'asset_stats' => 'Statistik Aset',
        'loading' => 'Memuatkan...',
    ],

    // Theme System - v3.6.1 (MyDS Design System)
    'theme' => [
        'light' => 'Mod Cerah',
        'dark' => 'Mod Gelap',
        'system' => 'Ikut Sistem',
        'toggle' => 'Tukar Tema',
        'preference_saved' => 'Pilihan tema telah disimpan.',
        'preference_failed' => 'Gagal menyimpan pilihan tema.',
        'high_contrast' => 'Kontras Tinggi',
        'high_contrast_enabled' => 'Mod kontras tinggi telah diaktifkan.',
        'high_contrast_disabled' => 'Mod kontras tinggi telah dimatikan.',
    ],

    // Accessibility - WCAG 2.2 AA
    'accessibility' => [
        'skip_to_content' => 'Langkau ke kandungan utama',
        'main_navigation' => 'Navigasi Utama',
        'sidebar_navigation' => 'Navigasi Sisi',
        'user_menu' => 'Menu Pengguna',
        'notifications' => 'Pemberitahuan',
        'search' => 'Carian',
        'close_menu' => 'Tutup Menu',
        'open_menu' => 'Buka Menu',
        'expand' => 'Kembangkan',
        'collapse' => 'Kuncupkan',
        'loading' => 'Memuatkan',
        'required_field' => 'Medan wajib',
        'error' => 'Ralat',
        'success' => 'Berjaya',
        'warning' => 'Amaran',
        'info' => 'Maklumat',
    ],

    // Asset Form - existing translations
    'asset_form' => [
        'asset_info' => 'Maklumat Aset',
        'financial_info' => 'Maklumat Kewangan',
        'maintenance_attachments' => 'Lampiran Penyelenggaraan',
        'asset_tag' => 'Tag Aset',
    ],

    // Resources - Resource labels for navigation and model names
    'resources' => [
        'asset' => [
            'navigation' => 'Aset',
            'singular' => 'Aset',
            'plural' => 'Aset',
        ],
        'asset_category' => [
            'navigation' => 'Kategori Aset',
            'singular' => 'Kategori Aset',
            'plural' => 'Kategori Aset',
        ],
    ],

    // Actions - Export and other actions
    'actions' => [
        'export' => 'Eksport',
        'export_data' => 'Eksport Data',
        'export_excel' => 'Eksport Excel',
        'export_pdf' => 'Eksport PDF',
        'export_csv' => 'Eksport CSV',
        'export_report' => 'Eksport Laporan',
        'export_report_description' => 'Ini akan menjana laporan PDF dengan statistik untuk semua rekod.',
        'export_selected' => 'Eksport Dipilih',
        'import' => 'Import',
        'create' => 'Cipta',
        'create_another' => 'Simpan & Tambah Lagi',
        'edit' => 'Kemaskini',
        'view' => 'Lihat',
        'delete' => 'Padam',
        'save' => 'Simpan',
        'cancel' => 'Batal',
        'submit' => 'Hantar',
        'approve' => 'Lulus',
        'reject' => 'Tolak',
    ],

    // Boolean values for accessibility
    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
    ],

    // System - System-related translations
    'system' => [
        'email_logs' => 'Log E-mel',
        'audit_logs' => 'Log Audit',
        'failed_jobs' => 'Tugas Gagal',
        'api_tokens' => 'Token API',
    ],

    // User Module - v3.6.1
    'users' => [
        'basic_info' => 'Maklumat Asas',
        'full_name' => 'Nama Penuh',
        'email_address' => 'Alamat E-mel',
        'password' => 'Kata Laluan',
        'password_help' => 'Biarkan kosong untuk kekalkan kata laluan semasa (mod kemaskini)',
        'role' => 'Peranan',
        'role_staff' => 'Staf',
        'role_approver' => 'Pelulus (Gred 41+)',
        'role_admin' => 'Admin',
        'role_superuser' => 'Superuser',
        'role_help' => 'Hanya superuser boleh menukar peranan',
        'active_status' => 'Status Aktif',
        'org_info' => 'Maklumat Organisasi',
        'staff_id' => 'ID Staf',
        'division' => 'Bahagian',
        'grade' => 'Gred',
        'position' => 'Jawatan',
        'contact_info' => 'Maklumat Perhubungan',
        'office_phone' => 'Telefon Pejabat',
        'mobile_phone' => 'Telefon Bimbit',
    ],
];
