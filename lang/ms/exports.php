<?php

declare(strict_types=1);

return [
    // Page
    'title' => 'Pusat Eksport Data',
    'label' => 'Pusat Eksport Data',

    // Export formats
    'formats' => [
        'csv' => 'CSV — Nilai Dipisahkan Koma (CSV)',
        'excel' => 'Excel — Hamparan Microsoft Excel (XLSX)',
        'pdf' => 'PDF — Format Dokumen Mudah Alih (PDF)',
    ],

    // Fields
    'fields' => [
        'data_type' => 'Jenis Data',
        'format' => 'Format',
        'date_from' => 'Tarikh Dari',
        'date_to' => 'Tarikh Hingga',
        'compress' => 'Mampat Fail',
        'compress_helper' => 'Fail akan dimuat turun sebagai .zip jika melebihi 10MB',
    ],

    // Actions
    'actions' => [
        'export' => 'Eksport',
        'quick_export' => 'Eksport Pantas',
        'quick_export_helper' => 'Guna tetapan lalai (bulan semasa + CSV)',
        'download' => 'Muat Turun',
        'cancel' => 'Batal',
        'retry' => 'Cuba Lagi',
    ],

    // History
    'history' => [
        'title' => 'Eksport Terkini',
        'empty' => 'Tiada sejarah eksport lagi',
        'columns' => [
            'file_name' => 'Nama Fail',
            'data_type' => 'Jenis Data',
            'format' => 'Format',
            'file_size' => 'Saiz Fail',
            'status' => 'Status',
            'created_at' => 'Tarikh Dicipta',
            'actions' => 'Tindakan',
        ],
    ],

    // Status
    'status' => [
        'queued' => 'Dalam Giliran',
        'processing' => 'Sedang Diproses',
        'completed' => 'Selesai',
        'failed' => 'Gagal',
    ],

    // Messages
    'messages' => [
        'export_started' => 'Eksport Dimulakan',
        'export_started_body' => 'Eksport anda sedang diproses. Anda akan dimaklumkan apabila selesai.',
        'export_success' => 'Eksport Berjaya',
        'export_success_body' => 'Fail :filename telah dijana dan sedia untuk dimuat turun.',
        'export_failed' => 'Eksport Gagal',
        'export_failed_body' => 'Ralat berlaku semasa menjana eksport. Sila cuba lagi.',
        'download_expired' => 'Muat Turun Tamat Tempoh',
        'download_expired_body' => 'Pautan muat turun telah tamat tempoh. Sila jana eksport baharu.',
    ],

    // Validation
    'validation' => [
        'date_range_invalid' => 'Tarikh tamat mesti selepas atau sama dengan tarikh mula.',
        'range_too_large' => 'Julat tarikh melebihi 365 hari. Sila pilih julat yang lebih kecil.',
        'format_required' => 'Sila pilih format eksport.',
        'data_type_required' => 'Sila pilih jenis data untuk dieksport.',
    ],

    // Stats
    'stats' => [
        'total_exports' => 'Jumlah Eksport',
        'this_month' => 'Bulan Ini',
        'total_size' => 'Jumlah Saiz',
        'avg_size' => 'Saiz Purata',
    ],

    // Data types
    'data_types' => [
        'helpdesk_tickets' => 'Tiket Helpdesk',
        'loan_applications' => 'Permohonan Pinjaman',
        'assets' => 'Aset',
        'users' => 'Pengguna',
        'audit_logs' => 'Log Audit',
    ],
];
