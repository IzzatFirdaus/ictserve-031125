<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// API Token Management Translations

declare(strict_types=1);

return [
    // Navigation & Labels
    'navigation_label' => 'Token API',
    'model_label' => 'Token API',
    'plural_model_label' => 'Token API',

    // Token Creation
    'token_created_title' => 'Token berjaya dijana',
    'token_created_warning' => 'Salin token ini sekarang. Token tidak akan dipaparkan lagi.',
    'copy_button' => 'Salin',
    'close_button' => 'Tutup',
    'copied_notification' => 'Token telah disalin ke papan keratan.',

    // Scope Labels (Technical → Malay)
    'scopes' => [
        'read:tickets' => 'Baca Tiket',
        'write:tickets' => 'Tulis Tiket',
        'read:loans' => 'Baca Pinjaman',
        'write:loans' => 'Tulis Pinjaman',
        'read:assets' => 'Baca Aset',
        'write:assets' => 'Tulis Aset',
        'admin:all' => 'Pentadbir Penuh',
    ],

    // Form Fields
    'fields' => [
        'name' => 'Nama Token',
        'name_placeholder' => 'cth: Token Integrasi Sistem',
        'name_help' => 'Nama deskriptif untuk mengenal pasti token ini.',
        'abilities' => 'Skop/Kebenaran',
        'abilities_placeholder' => 'Pilih atau taip skop...',
        'abilities_help' => 'Skop menentukan akses API yang dibenarkan untuk token ini.',
        'expires_at' => 'Tarikh Tamat Tempoh',
        'expires_help' => 'Lalai: 6 bulan. Kosongkan untuk token kekal (tidak disyorkan).',
    ],

    // Table Columns
    'columns' => [
        'name' => 'Nama Token',
        'user' => 'Pengguna',
        'abilities' => 'Skop',
        'last_used_at' => 'Terakhir Digunakan',
        'expires_at' => 'Tamat Tempoh',
        'created_at' => 'Dicipta Pada',
    ],

    // Status Labels
    'never_used' => 'Belum digunakan',
    'never_expires' => 'Kekal',
    'expired' => 'Tamat tempoh',
    'active' => 'Aktif',

    // Filters
    'filters' => [
        'show_expired' => 'Tunjukkan Token Tamat Tempoh',
        'my_tokens_only' => 'Token Saya Sahaja',
    ],

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada Token API',
        'description' => 'Klik \'Cipta Token Baharu\' untuk jana token API.',
    ],

    // Actions
    'actions' => [
        'create' => 'Cipta Token Baharu',
        'view' => 'Lihat',
        'edit' => 'Sunting',
        'delete' => 'Padam',
        'revoke' => 'Batalkan Token',
    ],

    // Notifications
    'notifications' => [
        'created_title' => 'Token Berjaya Dicipta',
        'created_body' => 'Token API baharu telah berjaya dicipta.',
        'deleted_title' => 'Token Dipadam',
        'deleted_body' => 'Token API telah berjaya dipadam.',
        'revoked_title' => 'Token Dibatalkan',
        'revoked_body' => 'Token API telah dibatalkan dan tidak boleh digunakan lagi.',
    ],
];
