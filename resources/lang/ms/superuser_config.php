<?php

declare(strict_types=1);

return [
    'stats' => [
        'sla_categories' => 'Kategori SLA',
        'approval_rules' => 'Peraturan Kelulusan',
        'email_templates' => 'Templat E-mel',
        'expired_tokens' => 'Token Tamat Tempoh',
    ],

    'sections' => [
        'sla' => [
            'title' => 'Konfigurasi SLA',
            'manage' => 'Urus SLA',
            'description' => 'Lihat masa respons dan penyelesaian semasa untuk semua keutamaan.',
            'minutes' => 'minit',
            'hours' => 'jam',
        ],
        'approval' => [
            'title' => 'Aliran Kerja Kelulusan',
            'manage' => 'Urus Matriks Kelulusan',
            'description' => 'Semak peraturan kelulusan, tempoh sah token, dan kelulusan tertunggak.',
            'pending_approvals' => 'Kelulusan tertunggak',
            'active_rules' => 'Peraturan aktif',
            'token_validity' => 'Tempoh sah token',
            'hours' => 'jam',
        ],
    ],

    'token_regeneration' => [
        'title' => 'Jana Semula Token Kelulusan',
        'description' => 'Jana semula token kelulusan untuk pinjaman yang tersekat kerana token tamat tempoh.',
        'loan_reference' => 'Rujukan pinjaman',
        'helper' => 'Pilih pinjaman dengan token kelulusan yang tamat tempoh atau tiada.',
        'reason' => 'Sebab penjanaan semula',
        'reason_helper' => 'Berikan justifikasi (contoh: pelulus meminta pautan baharu).',
        'regenerate_button' => 'Jana semula token',
        'regenerating' => 'Menjana semula...',
        'note' => 'Token sah selama 72 jam. Penjanaan semula direkodkan dalam audit.',
        'expired_at' => 'Tamat pada :date',
        'no_token' => 'Tiada token',
    ],

    'notifications' => [
        'select_loan' => 'Sila pilih pinjaman untuk menjana semula token.',
        'reason_required' => 'Sila berikan sebab penjanaan semula.',
        'token_regenerated' => 'Token kelulusan berjaya dijana semula.',
        'token_regenerated_body' => 'Pinjaman :reference kini mempunyai token sah sehingga :expires_at.',
        'loan_not_found' => 'Pinjaman tidak dijumpai. Sila muat semula senarai.',
        'token_error' => 'Gagal menjana semula token.',
    ],

    'actions' => [
        'manage_sla' => 'Urus Ambang SLA',
        'manage_email' => 'Urus Templat E-mel',
        'manage_approval' => 'Urus Matriks Kelulusan',
        'view_audit' => 'Lihat Log Audit',
    ],

    'recent_changes' => [
        'title' => 'Perubahan Konfigurasi Terkini',
        'view_all' => 'Lihat sejarah audit',
        'no_changes' => 'Tiada perubahan konfigurasi terkini.',
        'system' => 'Sistem',
    ],

    'guidelines' => [
        'title' => 'Garis Panduan Konfigurasi',
        'sla' => [
            'title' => 'Ambang SLA',
            'description' => 'Pastikan masa respons dan penyelesaian selaras dengan SLA kementerian. Kemas kini eskalasi dan pemberitahuan apabila ambang berubah.',
        ],
        'approval' => [
            'title' => 'Matriks Kelulusan',
            'description' => 'Pastikan peranan pelulus sentiasa terkini. Tempoh sah token lalai ialah 72 jam untuk kelulusan penyelia.',
        ],
        'token' => [
            'title' => 'Penjanaan Semula Token',
            'description' => 'Hanya jana semula token jika benar-benar diperlukan. Setiap tindakan direkodkan untuk audit.',
        ],
        'audit' => [
            'title' => 'Audit & Log',
            'description' => 'Semak Log Audit Bersepadu selepas kemas kini konfigurasi besar untuk mengesahkan tindakan yang direkodkan.',
        ],
    ],

    'navigation' => [
        'label' => 'Konfigurasi Superuser',
        'group' => 'Konfigurasi',
        'title' => 'Konfigurasi Superuser',
    ],
];
