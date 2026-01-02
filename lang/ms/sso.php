<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// SSO (Single Sign-On) Translations

declare(strict_types=1);

return [
    // SSO Users
    'users' => [
        'navigation_label' => 'Pengguna SSO',
        'model_label' => 'Pengguna SSO',
        'plural_model_label' => 'Pengguna SSO',
        'empty_state' => [
            'heading' => 'Tiada pengguna SSO',
            'description' => 'Rekod akan wujud selepas pengguna log masuk menggunakan Google SSO.',
            'not_configured' => 'SSO belum dikonfigurasi. Sila konfigurasi Google SSO untuk membolehkan log masuk SSO.',
        ],
        'columns' => [
            'name' => 'Nama',
            'email' => 'E-mel',
            'google_id' => 'ID Google',
            'verified' => 'Disahkan',
            'sso_login_count' => 'Bilangan Log Masuk',
            'last_sso_login' => 'Log Masuk SSO Terakhir',
        ],
    ],

    // SSO Audit Logs
    'audit' => [
        'navigation_label' => 'Log Audit SSO',
        'model_label' => 'Log Audit SSO',
        'plural_model_label' => 'Log Audit SSO',
        'empty_state' => [
            'heading' => 'Tiada log audit SSO',
            'description' => 'Log akan direkodkan apabila percubaan log masuk SSO berlaku. Cuba log masuk melalui SSO untuk menjana rekod ujian.',
        ],
        'columns' => [
            'email' => 'E-mel',
            'user' => 'Pengguna',
            'status' => 'Status',
            'error_type' => 'Jenis Ralat',
            'ip_address' => 'Alamat IP',
            'attempted_at' => 'Dicuba Pada',
        ],
        'status' => [
            'success' => 'Berjaya',
            'failed' => 'Gagal',
        ],
        'tabs' => [
            'all' => 'Semua',
            'success' => 'Berjaya',
            'failed' => 'Gagal',
            'today' => 'Hari Ini',
        ],
    ],
];
