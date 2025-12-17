<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md


declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Konfigurasi Sistem',
        'group' => 'Sistem',
        'title' => 'Konfigurasi Superuser',
    ],

    'actions' => [
        'manage_sla' => 'Urus SLA',
        'manage_email' => 'Templat E-mel',
        'manage_approval' => 'Matriks Kelulusan',
        'view_audit' => 'Log Audit',
    ],

    'stats' => [
        'sla_categories' => 'Kategori SLA',
        'approval_rules' => 'Peraturan Kelulusan',
        'email_templates' => 'Templat E-mel',
        'expired_tokens' => 'Token Tamat Tempoh',
    ],

    'sections' => [
        'sla' => [
            'title' => 'Konfigurasi SLA',
            'description' => 'Ambang masa respons dan penyelesaian untuk tiket helpdesk.',
            'manage' => 'Urus SLA',
            'minutes' => 'min',
            'hours' => 'jam',
        ],
        'approval' => [
            'title' => 'Aliran Kerja Kelulusan',
            'description' => 'Matriks kelulusan permohonan pinjaman dan peraturan penghalaan.',
            'manage' => 'Urus Kelulusan',
            'pending_approvals' => 'Kelulusan Tertunda',
            'active_rules' => 'Peraturan Aktif',
            'token_validity' => 'Kesahan Token',
            'hours' => 'jam',
        ],
    ],

    'token_regeneration' => [
        'title' => 'Penjanaan Semula Token',
        'description' => 'Jana semula token kelulusan yang tamat tempoh untuk permohonan pinjaman yang menunggu kelulusan penyelia.',
        'loan_reference' => 'Pilih Permohonan Pinjaman',
        'helper' => 'Hanya permohonan pinjaman dengan token kelulusan yang tamat tempoh atau tiada token ditunjukkan.',
        'reason' => 'Sebab Penjanaan Semula',
        'reason_helper' => 'Berikan sebab untuk menjana semula token. Ini akan direkodkan untuk tujuan audit.',
        'regenerate_button' => 'Jana Semula Token',
        'regenerating' => 'Menjana semula...',
        'note' => 'Token kelulusan baharu selama 72 jam akan dijana dan pelulus akan dimaklumkan.',
        'expired_at' => 'Tamat: :date',
        'no_token' => 'Tiada token dijana',
    ],

    'recent_changes' => [
        'title' => 'Perubahan Konfigurasi Terkini',
        'view_all' => 'Lihat Semua',
        'no_changes' => 'Tiada perubahan konfigurasi terkini.',
        'system' => 'Sistem',
    ],

    'guidelines' => [
        'title' => 'Panduan Konfigurasi',
        'sla' => [
            'title' => 'Ambang SLA:',
            'description' => 'Konfigurasikan masa respons dan penyelesaian untuk setiap tahap keutamaan. Pengiraan SLA mengambil kira waktu perniagaan jika diaktifkan. Eskalasi dicetuskan apabila baki masa jatuh di bawah peratusan ambang.',
        ],
        'approval' => [
            'title' => 'Aliran Kerja Kelulusan:',
            'description' => 'Tentukan peraturan kelulusan berdasarkan nilai aset, gred pemohon, dan tempoh pinjaman. Peraturan dinilai mengikut susunan keutamaan. Kelulusan automatik boleh diaktifkan untuk pinjaman bernilai rendah.',
        ],
        'token' => [
            'title' => 'Penjanaan Semula Token:',
            'description' => 'Token kelulusan tamat tempoh selepas 72 jam. Gunakan ciri ini untuk menjana semula token bagi permohonan yang tertunda. Semua tindakan penjanaan semula direkodkan untuk pematuhan audit.',
        ],
        'audit' => [
            'title' => 'Jejak Audit:',
            'description' => 'Semua perubahan konfigurasi direkodkan menggunakan sistem audit dwi. Semak log audit bersepadu untuk pelaporan pematuhan dan penjejakan perubahan.',
        ],
    ],

    'notifications' => [
        'select_loan' => 'Sila pilih permohonan pinjaman.',
        'reason_required' => 'Sila berikan sebab untuk penjanaan semula token.',
        'loan_not_found' => 'Permohonan pinjaman tidak dijumpai.',
        'token_regenerated' => 'Token Berjaya Dijana Semula',
        'token_regenerated_body' => 'Token kelulusan baharu telah dijana untuk :reference. Token akan tamat tempoh pada :expires_at.',
        'token_error' => 'Ralat Menjana Semula Token',
    ],
];
