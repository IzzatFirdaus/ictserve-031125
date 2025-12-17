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
        'ollama_ai' => 'Ollama AI',
        'asset_management' => 'Pengurusan Aset',
        'helpdesk_management' => 'Meja Bantuan',
        'loan_management' => 'Pinjaman Aset',
        'reference_data' => 'Data Rujukan',
        'system_management' => 'Pengurusan Sistem',
        'user_management' => 'Pengurusan Pengguna',
        'reports' => 'Laporan & Analitik',
        'go_to_portal' => 'Pergi ke Portal',
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
    ],

    'tooltips' => [
        'approval_approved' => 'Kelulusan 1: :name1 | Kelulusan 2: :name2',
        'approval_rejected' => 'Ditolak oleh: :name',
        'approval_pending' => 'Menunggu kelulusan',
        'approval_not_submitted' => 'Belum dihantar untuk kelulusan',
        'applicant_responsible' => 'Pemohon bertanggungjawab',
        'different_responsible_officer' => 'Pegawai lain bertanggungjawab',
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
    ],
];
