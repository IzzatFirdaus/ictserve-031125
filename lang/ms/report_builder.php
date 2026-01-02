<?php

declare(strict_types=1);

return [
    // Page
    'title' => 'Pembina Laporan',
    'label' => 'Pembina Laporan',

    // Configuration section
    'config' => [
        'heading' => 'Konfigurasi Laporan',
        'module' => 'Modul',
        'date_from' => 'Tarikh Dari',
        'date_to' => 'Tarikh Hingga',
        'status' => 'Status',
        'format' => 'Format Eksport',
    ],

    // Modules
    'modules' => [
        'helpdesk' => 'Tiket Helpdesk',
        'loans' => 'Permohonan Pinjaman',
        'assets' => 'Aset',
    ],

    // Helpdesk statuses
    'statuses' => [
        'helpdesk' => [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'in_progress' => 'Dalam Proses',
            'resolved' => 'Diselesaikan',
            'closed' => 'Ditutup',
        ],
        'loans' => [
            'pending' => 'Menunggu',
            'approved' => 'Diluluskan',
            'in_use' => 'Sedang Digunakan',
            'completed' => 'Selesai',
        ],
        'assets' => [
            'available' => 'Tersedia',
            'on_loan' => 'Dipinjam',
            'maintenance' => 'Penyelenggaraan',
            'retired' => 'Bersara',
        ],
    ],

    // Export formats
    'formats' => [
        'csv' => 'CSV',
        'excel' => 'Excel (XLSX)',
        'pdf' => 'PDF',
    ],

    // Preview section
    'preview' => [
        'heading' => 'Pratonton',
        'module_label' => 'Modul',
        'total_records' => 'Jumlah Rekod',
        'no_preview' => 'Tiada pratonton. Sila jana laporan.',
        'showing_records' => 'Menunjukkan :shown daripada :total rekod',
    ],

    // Actions
    'actions' => [
        'generate' => 'Jana Pratonton',
        'export_csv' => 'Eksport CSV',
        'export_excel' => 'Eksport Excel',
        'export_pdf' => 'Eksport PDF',
        'clear' => 'Kosongkan',
    ],

    // Messages
    'messages' => [
        'generating' => 'Menjana laporan...',
        'module_required' => 'Modul Diperlukan',
        'module_required_body' => 'Sila pilih modul untuk menjana laporan.',
        'report_generated' => 'Laporan Dijana',
        'records_found' => 'Ditemui :count rekod.',
        'export_success' => 'Eksport Berjaya',
        'export_success_body' => 'Laporan :filename telah dijana.',
    ],

    // Validation
    'validation' => [
        'date_range_invalid' => 'Tarikh tamat mesti selepas atau sama dengan tarikh mula.',
        'range_too_large' => 'Julat tarikh melebihi 365 hari. Sila pilih julat yang lebih kecil.',
    ],

    // Guidance
    'guidance' => [
        'title' => 'Panduan Penggunaan',
        'step_1' => '1. Pilih modul yang ingin dijana laporan',
        'step_2' => '2. Tetapkan julat tarikh (pilihan)',
        'step_3' => '3. Pilih status untuk ditapis (pilihan)',
        'step_4' => '4. Pilih format eksport',
        'step_5' => '5. Klik "Jana Pratonton" untuk melihat hasil',
    ],

    // Headers for export
    'headers' => [
        'helpdesk' => [
            'id' => 'ID Tiket',
            'subject' => 'Subjek',
            'status' => 'Status',
            'priority' => 'Keutamaan',
            'created_at' => 'Tarikh Dicipta',
            'resolved_at' => 'Tarikh Diselesaikan',
        ],
        'loans' => [
            'id' => 'ID Permohonan',
            'applicant' => 'Pemohon',
            'asset' => 'Aset',
            'status' => 'Status',
            'loan_date' => 'Tarikh Pinjaman',
            'return_date' => 'Tarikh Pulangan',
        ],
        'assets' => [
            'id' => 'ID Aset',
            'name' => 'Nama',
            'category' => 'Kategori',
            'status' => 'Status',
            'location' => 'Lokasi',
            'acquisition_date' => 'Tarikh Perolehan',
        ],
    ],
];
