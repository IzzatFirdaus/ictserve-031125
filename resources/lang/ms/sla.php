<?php

declare(strict_types=1);

return [
    // SLA Status Labels
    'status' => [
        'ok' => 'Dalam Sasaran',
        'warning' => 'Amaran',
        'critical' => 'Kritikal',
        'breached' => 'Melebihi Had',
        'completed' => 'Selesai',
    ],

    // SLA Dashboard
    'dashboard' => [
        'title' => 'Pemantauan SLA',
        'summary' => 'Ringkasan SLA',
        'total_pending' => 'Jumlah Menunggu',
        'on_track' => 'Dalam Sasaran',
        'at_risk' => 'Berisiko',
        'breached' => 'Melebihi Had',
        'compliance_rate' => 'Kadar Pematuhan',
        'hours_elapsed' => 'Jam Berlalu',
        'hours_remaining' => 'Jam Berbaki',
        'no_pending' => 'Tiada permohonan menunggu',
    ],

    // SLA Thresholds
    'thresholds' => [
        'warning' => '24 jam (1 hari bekerja)',
        'critical' => '48 jam (2 hari bekerja)',
        'breach' => '72 jam (3 hari bekerja)',
    ],

    // SLA Alerts
    'alerts' => [
        'warning_title' => 'Amaran SLA',
        'warning_message' => 'Permohonan ini telah menunggu selama :hours jam. Sila semak segera.',
        'critical_title' => 'SLA Kritikal',
        'critical_message' => 'Permohonan ini berisiko melebihi had SLA. Hanya :hours jam berbaki.',
        'breached_title' => 'SLA Melebihi Had',
        'breached_message' => 'Permohonan ini telah melebihi had SLA 72 jam bekerja.',
    ],

    // Email Templates
    'email' => [
        'title' => 'Makluman SLA - Permohonan Pinjaman Menunggu Semakan',
        'subject_warning' => '[Amaran] Permohonan Pinjaman :number Memerlukan Perhatian',
        'subject_critical' => '[Kritikal] Permohonan Pinjaman :number - SLA Berisiko',
        'subject_breached' => '[Segera] Permohonan Pinjaman :number - SLA Melebihi Had',
        'subject_reminder' => 'Peringatan: Permohonan Pinjaman :number Menunggu Semakan',
        'greeting' => 'Yang Dihormati :name,',
        'intro' => 'Permohonan pinjaman yang ditugaskan kepada anda memerlukan perhatian anda.',
        'application_details' => 'Butiran Permohonan',
        'application_number' => 'Nombor Permohonan',
        'applicant' => 'Pemohon',
        'submitted_at' => 'Tarikh Hantar',
        'hours_elapsed' => 'Jam Bekerja Berlalu',
        'hours_remaining' => 'Jam Bekerja Berbaki',
        'hours' => 'jam',
        'review_button' => 'Semak Permohonan',
        'footer' => 'Sila semak dan ambil tindakan terhadap permohonan ini secepat mungkin.',
        'regards' => 'Salam hormat',
        'warning_notice' => 'Permohonan ini telah menunggu lebih 24 jam bekerja.',
        'critical_notice' => 'Permohonan ini berisiko melebihi had SLA. Tindakan segera diperlukan.',
        'breached_notice' => 'Permohonan ini telah melebihi had SLA 72 jam.',
    ],

    // Tooltip/Help Text
    'help' => [
        'sla_indicator' => 'Status SLA menunjukkan berapa lama permohonan telah menunggu kelulusan.',
        'business_hours' => 'Jam bekerja dikira dari 8:00 pagi hingga 6:00 petang, tidak termasuk hujung minggu.',
    ],
];
