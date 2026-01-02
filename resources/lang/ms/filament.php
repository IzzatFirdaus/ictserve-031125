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
        // Note: data_visualization moved to lang/ms/admin_pages.php (canonical location)
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
        // Create form actions - Task 40.1
        'create' => 'Cipta',
        'create_another' => 'Simpan & Tambah Lagi',
        'save' => 'Simpan',
        'cancel' => 'Batal',
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

    // Helpdesk Module - v3.6.1
    'helpdesk' => [
        'ticket_number' => 'Nombor Tiket',
        'subject' => 'Subjek',
        'category' => 'Kategori',
        'priority' => 'Keutamaan',
        'damage_type' => 'Jenis Kerosakan',
        'related_asset' => 'Aset Berkaitan',
        'status' => 'Status',
        'ticket_info' => 'Maklumat Tiket',
        'complainant_info' => 'Maklumat Pengadu',
        'complainant_info_desc' => 'Tiket boleh daripada pengguna berdaftar atau tetamu',
        'registered_user' => 'Pengguna Berdaftar',
        'registered_user_help' => 'Pilih pengguna berdaftar ATAU isi maklumat tetamu di bawah',
        'guest_name' => 'Nama Tetamu',
        'guest_email' => 'Emel Tetamu',
        'guest_phone' => 'Telefon Tetamu',
        'guest_staff_id' => 'ID Staf Tetamu',
        'division' => 'Bahagian',
        'job_grade' => 'Gred Jawatan',
        'declaration' => 'Saya dengan ini mengakui bahawa maklumat yang diberikan adalah benar dan tepat.',
        'declaration_error' => 'Anda mesti menerima pengakuan ini untuk meneruskan.',
        'description' => 'Perincian Aduan',
        'assignment_sla' => 'Tugasan & SLA',
        'assigned_division' => 'Ditugaskan kepada Bahagian',
        'external_agency' => 'Agensi Luar',
        'assigned_officer' => 'Pegawai Bertugas',
        'sla_response' => 'SLA Respons',
        'sla_resolution' => 'SLA Resolusi',
        'response_date' => 'Tarikh Respons',
        'resolved_date' => 'Tarikh Selesai',
        'closed_date' => 'Tarikh Tutup',
        'notes' => 'Nota',
        'admin_notes' => 'Nota Pentadbir',
        'internal_notes' => 'Nota Dalaman',
        'resolution_notes' => 'Nota Penyelesaian',
        // Status options
        'status_open' => 'Dibuka',
        'status_assigned' => 'Ditugaskan',
        'status_in_progress' => 'Dalam Tindakan',
        'status_pending_user' => 'Menunggu Pengadu',
        'status_resolved' => 'Selesai',
        'status_closed' => 'Ditutup',
        // Priority options
        'priority_low' => 'Rendah',
        'priority_normal' => 'Biasa',
        'priority_high' => 'Tinggi',
        'priority_urgent' => 'Segera',
    ],

    // Asset Module - v3.6.1
    'asset_form' => [
        'asset_info' => 'Maklumat Aset',
        'asset_tag' => 'Tag Aset',
        'serial_number' => 'Nombor Siri',
        'financial_info' => 'Maklumat Kewangan',
        'purchase_date' => 'Tarikh Pembelian',
        'purchase_value' => 'Nilai Pembelian',
        'current_value' => 'Nilai Semasa',
        'warranty_expiry' => 'Tamat Waranti',
        'maintenance_attachments' => 'Penyelenggaraan & Lampiran',
        'last_maintenance' => 'Penyelenggaraan Terakhir',
        'next_maintenance' => 'Penyelenggaraan Seterusnya',
        'specifications' => 'Spesifikasi',
        'parameter' => 'Parameter',
        'details' => 'Butiran',
        'accessories' => 'Aksesori',
        'accessory' => 'Aksesori',
        'quantity_notes' => 'Kuantiti/Nota',
        'additional_notes' => 'Nota Tambahan',
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

    // Loan Module - v3.6.1
    'loans' => [
        'application_info' => 'Maklumat Permohonan',
        'applicant_info' => 'Maklumat Pemohon',
        'loan_period' => 'Tempoh Pinjaman',
        'purpose' => 'Tujuan Pinjaman',
        'items' => 'Item Pinjaman',
        'approval_info' => 'Maklumat Kelulusan',
        'first_approver' => 'Pelulus Pertama',
        'second_approver' => 'Pelulus Kedua',
        'approval_date' => 'Tarikh Kelulusan',
        'remarks' => 'Catatan',
    ],

    // Global Search Results - v3.6.1
    'search' => [
        'subject' => 'Subjek',
        'status' => 'Status',
        'priority' => 'Keutamaan',
        'category' => 'Kategori',
    ],

    // Job Grades - v3.6.1
    'grades' => [
        'grade_11' => 'Gred 11',
        'grade_17' => 'Gred 17',
        'grade_19' => 'Gred 19',
        'grade_22' => 'Gred 22',
        'grade_26' => 'Gred 26',
        'grade_27' => 'Gred 27',
        'grade_29' => 'Gred 29',
        'grade_32' => 'Gred 32',
        'grade_36' => 'Gred 36',
        'grade_38' => 'Gred 38',
        'grade_41' => 'Gred 41',
        'grade_42' => 'Gred 42',
        'grade_44' => 'Gred 44',
        'grade_45' => 'Gred 45',
        'grade_48' => 'Gred 48',
        'grade_52' => 'Gred 52',
        'grade_54' => 'Gred 54',
        'grade_56' => 'Gred 56',
        'jusa_a' => 'JUSA A',
        'jusa_b' => 'JUSA B',
        'jusa_c' => 'JUSA C',
    ],

    // System Module - v3.6.1
    'system' => [
        'email_logs' => 'Log E-mel',
        'email_log' => 'Log E-mel',
        'failed_jobs' => 'Tugas Gagal',
        'failed_job' => 'Tugas Gagal',
        'audit_trail' => 'Jejak Audit',
        'api_tokens' => 'Token API',
        'report_schedules' => 'Jadual Laporan',
    ],

    // Asset Maintenance Module - v3.6.1
    'asset_maintenance' => [
        'navigation_label' => 'Penyelenggaraan Aset',
        'model_label' => 'Penyelenggaraan Aset',
        'plural_label' => 'Penyelenggaraan Aset',
        'asset' => 'Aset',
        'maintenance_type' => 'Jenis Penyelenggaraan',
        'maintenance_date' => 'Tarikh Penyelenggaraan',
        'performed_by' => 'Dilakukan Oleh',
        'cost' => 'Kos',
        'notes' => 'Nota',
        'status' => 'Status',
        'next_maintenance' => 'Penyelenggaraan Seterusnya',
        'type_preventive' => 'Pencegahan',
        'type_corrective' => 'Pembetulan',
        'type_emergency' => 'Kecemasan',
        'status_scheduled' => 'Dijadualkan',
        'status_in_progress' => 'Dalam Proses',
        'status_completed' => 'Selesai',
        'status_cancelled' => 'Dibatalkan',
    ],

    // Asset Transfer Module - v3.6.1
    'asset_transfer' => [
        'navigation_label' => 'Pemindahan Aset',
        'model_label' => 'Pemindahan Aset',
        'plural_label' => 'Pemindahan Aset',
        'asset' => 'Aset',
        'from_division' => 'Dari Bahagian',
        'to_division' => 'Ke Bahagian',
        'transfer_date' => 'Tarikh Pemindahan',
        'transferred_by' => 'Dipindahkan Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'reason' => 'Sebab',
        'notes' => 'Nota',
        'status' => 'Status',
        'status_pending' => 'Menunggu',
        'status_approved' => 'Diluluskan',
        'status_rejected' => 'Ditolak',
        'status_completed' => 'Selesai',
    ],
];
