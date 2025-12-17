<?php

declare(strict_types=1);

/**
 * Ms - System Translations
 *
 * System alerts, notifications, and administrative messages
 * v3.6.0 - Bahasa Melayu sahaja
 */

return [
    'alerts' => [
        'title' => 'Amaran Sistem',
        'types' => [
            'overdue_tickets' => 'Tiket Lewat',
            'overdue_loans' => 'Pinjaman Lewat',
            'approval_delays' => 'Kelewatan Kelulusan',
            'asset_shortages' => 'Kekurangan Aset',
            'system_health' => 'Kesihatan Sistem',
            'system_test' => 'Ujian Sistem',
        ],
        'metrics' => [
            'warning_threshold' => 'Ambang Amaran',
            'pending_approvals' => 'Kelulusan Menunggu',
            'time_threshold' => 'Ambang Masa',
            'asset_availability' => 'Ketersediaan Aset',
            'minimum_threshold' => 'Ambang Minimum',
            'health_score' => 'Skor Kesihatan',
            'current_time' => 'Masa Semasa',
        ],
        'details' => [
            'title' => 'Butiran',
            'ticket_number' => 'Nombor Tiket',
            'subject' => 'Subjek',
            'days_overdue' => 'Hari Lewat',
            'application_number' => 'Nombor Permohonan',
            'applicant_name' => 'Nama Pemohon',
        ],
        'actions' => [
            'title' => 'Tindakan Disyorkan',
            'view_dashboard' => 'Lihat Papan Pemuka',
            'review_tickets' => 'Semak tiket yang lewat dan ambil tindakan segera',
            'review_approvals' => 'Semak kelulusan yang tertangguh',
            'check_assets' => 'Semak ketersediaan aset dan optimumkan penggunaan',
            'view_system_status' => 'Lihat status sistem dan lakukan penyelenggaraan jika perlu',
        ],
        'footer' => [
            'generated_at' => 'Dijana pada',
            'automatic_notice' => 'Ini adalah pemberitahuan automatik daripada sistem ICTServe.',
        ],
    ],
    'reports' => [
        'metrics' => [
            'system_uptime' => 'Masa Aktif Sistem',
        ],
        'details' => [
            'report_name' => 'Laporan Analitik Bersepadu',
        ],
    ],
];
