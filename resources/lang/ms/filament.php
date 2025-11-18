<?php

return [
    // Navigation Groups
    'navigation' => [
        'asset_management' => 'Pengurusan Aset',
        'helpdesk_management' => 'Pengurusan Helpdesk',
        'loan_management' => 'Pengurusan Pinjaman',
        'user_management' => 'Pengurusan Pengguna',
        'system_management' => 'Pengurusan Sistem',
        'reports' => 'Laporan',
        'reference_data' => 'Data Rujukan',
    ],

    // Common Labels
    'labels' => [
        'tag' => 'Tag',
        'name' => 'Nama',
        'brand' => 'Jenama',
        'model' => 'Model',
        'serial_number' => 'No. Siri',
        'category' => 'Kategori',
        'status' => 'Status',
        'condition' => 'Keadaan',
        'location' => 'Lokasi',
        'purchase_date' => 'Perolehan',
        'current_value' => 'Nilai Semasa',
        'next_maintenance_date' => 'Penyelenggaraan Seterusnya',
        'warranty_expiry' => 'Waranti',
        'age' => 'Umur',
        'priority' => 'Keutamaan',
        'division' => 'Bahagian',
        'applicant' => 'Pemohon',
        'application_number' => 'No Permohonan',
        'start_date' => 'Mula',
        'end_date' => 'Tamat',
        'overdue_status' => 'Status Lewat',
        'total_value' => 'Nilai (RM)',
        'maintenance_required' => 'Penyelenggaraan',
        'approval_status' => 'Status Kelulusan',
        'submission_type' => 'Jenis',
        'approval_method' => 'Kaedah Kelulusan',
        'created_from' => 'Dari Tarikh',
        'created_until' => 'Hingga Tarikh',
        'asset_type' => 'Jenis Aset',
        'submission_type_filter' => 'Jenis Penghantaran',
    ],

    // Asset Form Sections
    'asset_form' => [
        'asset_info' => 'Maklumat Aset',
        'financial_info' => 'Maklumat Kewangan',
        'maintenance_attachments' => 'Penyenggaraan & Lampiran',
        'asset_tag' => 'Tag Aset',
        'purchase_value' => 'Nilai Perolehan (RM)',
        'current_value' => 'Nilai Semasa (RM)',
        'warranty_expiry' => 'Waranti Tamat',
        'last_maintenance' => 'Penyenggaraan Terakhir',
        'next_maintenance' => 'Penyenggaraan Seterusnya',
        'specifications' => 'Spesifikasi',
        'accessories' => 'Aksesori',
        'parameter' => 'Parameter',
        'details' => 'Butiran',
        'accessory' => 'Aksesori',
        'quantity_notes' => 'Kuantiti / Nota',
        'additional_notes' => 'Nota Tambahan',
        'serial_number' => 'Nombor Siri',
        'purchase_date' => 'Tarikh Perolehan',
    ],

    // Actions
    'actions' => [
        'mark_maintenance' => 'Tanda Penyelenggaraan',
        'update_status' => 'Kemaskini Status',
        'update_condition' => 'Kemaskini Keadaan',
        'update_location' => 'Kemaskini Lokasi',
        'new_location' => 'Lokasi Baharu',
        'export' => 'Eksport',
        'send_for_approval' => 'Hantar untuk Kelulusan',
        'approve' => 'Luluskan',
        'decline' => 'Tolak',
        'extend' => 'Lanjutkan',
        'approval_remarks' => 'Catatan Kelulusan',
        'rejection_reason' => 'Sebab Penolakan',
        'new_date' => 'Tarikh Baru',
        'instructions' => 'Arahan',
        'reason' => 'Sebab',
    ],

    // Filters
    'filters' => [
        'needs_maintenance' => 'Perlu Penyelenggaraan',
        'available' => 'Tersedia',
        'in_use' => 'Sedang Digunakan',
        'warranty_expiring' => 'Waranti Hampir Tamat',
        'pending_approval' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'overdue' => 'Lewat',
        'guest_submission' => 'Tetamu',
        'authenticated_submission' => 'Disahkan',
        'email_approval' => 'E-mel',
        'portal_approval' => 'Portal',
        'maintenance_indicator' => 'Penyelenggaraan',
        'approval_indicator' => 'Kelulusan',
        'overdue_indicator' => 'Lewat',
    ],

    // Status Messages
    'status' => [
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'pending' => 'Menunggu',
        'not_submitted' => 'Belum Dihantar',
        'overdue_days' => 'Lewat :days hari',
        'due_soon' => 'Hampir tamat (:days hari)',
        'no_maintenance_schedule' => 'Tiada jadual penyelenggaraan',
        'overdue_maintenance' => 'Lewat :days hari',
        'due_today' => 'Hari ini',
        'due_in_days' => 'Dalam :days hari',
        'no_warranty' => 'Tiada waranti',
        'warranty_expired' => 'Waranti tamat',
        'warranty_expires_in' => 'Tamat dalam :time',
        'purchased_on' => 'Dibeli: :date',
    ],

    // Tooltips
    'tooltips' => [
        'approval_approved' => 'Diluluskan: :date\nOleh: :approver\nKaedah: :method',
        'approval_rejected' => 'Ditolak: :reason',
        'approval_pending' => 'Token dihantar ke: :email\nTamat: :expires',
        'approval_not_submitted' => 'Belum dihantar untuk kelulusan',
        'warranty_expired_on' => 'Waranti tamat pada :date',
        'warranty_expires_on' => 'Waranti tamat pada :date (:time)',
        'maintenance_next' => 'Penyelenggaraan seterusnya :date - :status',
    ],

    // Notifications
    'notifications' => [
        'status_updated' => 'Status Dikemaskini',
        'condition_updated' => 'Keadaan Dikemaskini',
        'location_updated' => 'Lokasi Dikemaskini',
        'assets_updated' => ':count aset dikemaskini.',
        'assets_updated_simple' => 'Aset dikemaskini.',
    ],

    // Asset Categories
    'asset_categories' => [
        'computer' => 'Komputer',
        'laptop' => 'Komputer Riba',
        'printer' => 'Pencetak',
        'projector' => 'Projektor',
        'camera' => 'Kamera',
        'other' => 'Lain-lain',
    ],

    // Date Filters
    'date_filters' => [
        'from_date' => 'Dari: :date',
        'until_date' => 'Hingga: :date',
        'category_filter' => 'Kategori: :categories',
        'select_start_date' => 'Pilih tarikh mula',
        'select_end_date' => 'Pilih tarikh akhir',
    ],
];
