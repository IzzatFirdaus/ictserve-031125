<?php

declare(strict_types=1);

/**
 * System Translations (Malay)
 * 
 * Amaran sistem, laporan automatik, dan notifikasi pentadbiran
 * 
 * @author Pasukan Penyetempatan ICTServe
 * @created 2025-11-11
 */

return [
    // Amaran Sistem
    'alerts' => [
        'title' => 'Amaran Sistem ICTServe',
        'severity' => [
            'critical' => 'Kritikal',
            'high' => 'Tinggi',
            'medium' => 'Sederhana',
            'low' => 'Rendah',
        ],
        'types' => [
            'overdue_tickets' => 'Tiket Tertunggak',
            'overdue_loans' => 'Pinjaman Tertunggak',
            'approval_delays' => 'Kelulusan Tertunda',
            'low_asset_availability' => 'Ketersediaan Aset Rendah',
            'system_health' => 'Kesihatan Sistem',
        ],
        'metrics' => [
            'overdue_tickets' => 'Tiket Tertunggak',
            'overdue_loans' => 'Pinjaman Tertunggak',
            'pending_approvals' => 'Permohonan Tertunda',
            'warning_threshold' => 'Had Amaran',
            'time_threshold' => 'Had Masa',
            'asset_availability' => 'Ketersediaan Aset',
            'minimum_threshold' => 'Had Minimum',
            'current_time' => 'Masa Semasa',
            'health_score' => 'Skor Kesihatan',
        ],
        'details' => [
            'title' => 'Butiran Terperinci',
            'ticket_number' => 'No. Tiket',
            'application_number' => 'No. Permohonan',
            'subject' => 'Subjek',
            'applicant_name' => 'Nama Pemohon',
            'days_overdue' => 'Hari Tertunggak',
            'pending_since' => 'Tertunda Sejak',
            'asset_name' => 'Nama Aset',
            'current_availability' => 'Ketersediaan Semasa',
        ],
        'actions' => [
            'title' => 'Cadangan Tindakan',
            'view_dashboard' => 'Lihat Dashboard Analitik',
            'review_tickets' => 'Semak Tiket Tertunggak',
            'review_approvals' => 'Semak Kelulusan Tertunda',
            'check_assets' => 'Semak Inventori Aset',
            'view_system_status' => 'Lihat Status Sistem',
        ],
        'footer' => [
            'generated_at' => 'Amaran dijana pada',
            'automatic_notice' => 'Amaran ini dijana secara automatik oleh Sistem ICTServe',
        ],
    ],

    // Laporan Automatik
    'reports' => [
        'greeting' => 'Assalamualaikum dan Salam Sejahtera,',
        'title' => 'Laporan Sistem Automatik',
        'overall_health' => 'Kesihatan Sistem Keseluruhan',
        'key_metrics' => 'Metrik Utama',
        'metrics' => [
            'total_tickets' => 'Jumlah Tiket',
            'resolution_rate' => 'Kadar Penyelesaian',
            'approval_rate' => 'Kadar Kelulusan',
            'loan_applications' => 'Permohonan Pinjaman',
            'average_response_time' => 'Masa Respons Purata',
            'asset_utilization' => 'Penggunaan Aset',
            'system_uptime' => 'Masa Aktif Sistem',
            'active_users' => 'Pengguna Aktif',
        ],
        'details' => [
            'title' => 'Butiran Terperinci',
            'report_info' => 'Maklumat Laporan',
            'report_name' => 'Nama Laporan',
            'module' => 'Modul',
            'frequency' => 'Kekerapan',
            'description' => 'Keterangan',
            'format' => 'Format',
            'generated_on' => 'Dijana Pada',
        ],
        'issues' => [
            'title' => '⚠️ Isu Yang Memerlukan Perhatian',
            'none' => 'Tiada isu dikesan - Sistem beroperasi dengan normal',
        ],
        'suggestions' => [
            'title' => '💡 Cadangan Penambahbaikan',
        ],
        'formats' => [
            'csv' => 'Data CSV - Format data mentah',
            'excel' => 'Data Excel - Untuk analisis lanjut',
            'pdf' => 'Laporan PDF - Sedia untuk dicetak',
        ],
        'footer' => [
            'thank_you' => 'Terima kasih kerana menggunakan ICTServe.',
            'regards' => 'Sekian, terima kasih.',
            'team' => 'Pasukan BPM MOTAC',
        ],
    ],

    // Notifikasi Eksport
    'export' => [
        'ready' => [
            'title' => 'Eksport Anda Telah Siap',
            'greeting' => 'Salam',
            'intro' => 'Eksport data yang anda minta telah selesai dan sedia untuk dimuat turun.',
            'details' => 'Butiran Eksport',
            'export_type' => 'Jenis Eksport',
            'records_count' => 'Bilangan Rekod',
            'file_size' => 'Saiz Fail',
            'format' => 'Format',
            'expires_at' => 'Pautan Luput Pada',
            'download_button' => 'Muat Turun Eksport',
            'download_instructions' => 'Klik butang di atas untuk memuat turun fail eksport anda. Pautan muat turun akan luput selepas 24 jam.',
            'footer_note' => 'Ini adalah notifikasi automatik daripada Sistem Eksport ICTServe.',
        ],
    ],

    // Insiden Keselamatan
    'security' => [
        'incident' => [
            'title' => 'Amaran Insiden Keselamatan',
            'greeting' => 'Amaran Keselamatan',
            'intro' => 'Insiden keselamatan telah dikesan dalam sistem ICTServe dan memerlukan perhatian segera.',
            'details' => 'Butiran Insiden',
            'incident_type' => 'Jenis Insiden',
            'severity' => 'Tahap Keterukan',
            'detection_time' => 'Masa Pengesanan',
            'affected_area' => 'Kawasan Terjejas',
            'description' => 'Keterangan',
            'actions_taken' => 'Tindakan Diambil',
            'recommended_actions' => 'Tindakan Dicadangkan',
            'view_details' => 'Lihat Butiran Penuh',
            'contact_support' => 'Hubungi Pasukan Keselamatan',
            'footer_note' => 'Ini adalah notifikasi keselamatan kritikal. Sila ambil tindakan segera.',
        ],
    ],

    // E-mel Selamat Datang
    'welcome' => [
        'title' => 'Selamat Datang ke ICTServe',
        'greeting' => 'Selamat Datang',
        'intro' => 'Akaun anda telah berjaya dicipta dalam sistem ICTServe.',
        'getting_started' => 'Memulakan',
        'features' => [
            'title' => 'Ciri-ciri Utama',
            'asset_loans' => 'Mohon pinjaman aset secara dalam talian',
            'helpdesk' => 'Hantar dan jejaki tiket helpdesk',
            'reports' => 'Akses laporan dan analitik sistem',
            'profile' => 'Urus profil dan keutamaan anda',
        ],
        'login_info' => 'Maklumat Log Masuk',
        'username' => 'Nama Pengguna',
        'portal_url' => 'URL Portal',
        'support' => 'Perlukan Bantuan?',
        'support_text' => 'Hubungi pasukan sokongan kami jika anda mempunyai sebarang soalan atau memerlukan bantuan.',
        'login_button' => 'Log Masuk ke ICTServe',
    ],

    // Penyerahan Dituntut
    'submission_claimed' => [
        'title' => 'Penyerahan Anda Telah Dituntut',
        'greeting' => 'Yang Dihormati',
        'intro' => 'Penyerahan tetamu anda telah dituntut dan anda kini mempunyai akses untuk menjejaki kemajuannya.',
        'submission_details' => 'Butiran Penyerahan',
        'submission_number' => 'Nombor Penyerahan',
        'claimed_at' => 'Dituntut Pada',
        'next_steps' => 'Langkah Seterusnya',
        'access_portal' => 'Anda kini boleh mengakses portal untuk:',
        'features' => [
            'track_progress' => 'Jejaki kemajuan penyerahan secara masa nyata',
            'receive_updates' => 'Terima kemas kini status melalui e-mel',
            'view_history' => 'Lihat sejarah penyerahan lengkap',
            'submit_new' => 'Hantar permintaan tambahan',
        ],
        'access_button' => 'Akses Portal Anda',
        'footer_note' => 'Selamat datang ke ICTServe! Kami di sini untuk melayani anda dengan lebih baik.',
    ],
];
