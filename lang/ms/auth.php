<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md

declare(strict_types=1);

/**
 * Ms - Auth Translations
 *
 * Auto-generated on 2025-11-11 13:02:55
 * Dikemaskini: 2025-12-02 - Tambah terjemahan pendaftaran untuk Tugasan 13.1
 * Dikemaskini: 2025-12-02 - Tambah terjemahan log masuk fleksibel untuk Tugasan 14.1
 * Dikemaskini: 2025-12-17 - Tambah header versi (v3.6.1)
 */

return [
    'login_title' => 'Log Masuk',
    'login_subtitle' => 'Akses portal kakitangan ICTServe',
    'email' => 'E-mel',
    'email_placeholder' => 'nama@motac.gov.my',

    // Terjemahan Log Masuk Fleksibel (Tugasan 14.1 - Keperluan 16.2, 16.3, 16.5)
    'email_or_username' => 'E-mel atau Nama Pengguna',
    'email_or_username_placeholder' => 'nama@motac.gov.my atau nama pengguna',
    'flexible_login_hint' => 'Masukkan e-mel penuh atau nama pengguna sahaja',
    'logging_in' => 'Sedang log masuk...',
    'password_placeholder' => 'Masukkan kata laluan anda',
    'remember_me' => 'Ingat saya',
    'forgot_password' => 'Lupa kata laluan?',
    'login_button' => 'Log Masuk',
    'need_help' => 'Perlukan bantuan?',
    'contact_support' => 'Hubungi Sokongan',
    'extend_session' => 'Lanjutkan Sesi',
    'failed' => 'Kelayakan ini tidak sepadan dengan rekod kami.',
    'insufficient_permissions_portal' => 'Akses ditolak. Anda tidak mempunyai kebenaran untuk mengakses portal kakitangan. Sila hubungi pentadbir anda.',
    'logged_in' => 'Anda telah log masuk dengan jayanya.',
    'logged_out' => 'Anda telah log keluar dengan jayanya.',
    'logout' => 'Log Keluar',
    'must_login_portal' => 'Anda mesti log masuk untuk mengakses portal kakitangan.',
    'password' => 'Kata Laluan',
    'session_expired' => 'Sesi anda telah tamat. Sila log masuk semula.',
    'session_expiring_message' => 'Sesi anda akan tamat disebabkan tidak aktif. Adakah anda mahu melanjutkan sesi anda?',
    'session_expiring_title' => 'Sesi Akan Tamat',
    'session_extended' => 'Sesi anda telah dilanjutkan dengan jayanya.',
    'throttle' => 'Terlalu banyak percubaan log masuk. Sila cuba lagi dalam :seconds saat.',
    'time_remaining' => 'Masa berbaki',
    'email_unverified' => 'Alamat e-mel anda belum disahkan.',
    'resend_verification' => 'Klik di sini untuk menghantar semula e-mel pengesahan.',
    'verification_link_sent' => 'Pautan pengesahan baharu telah dihantar ke alamat e-mel anda.',

    // Terjemahan pendaftaran (Tugasan 13.1 - Keperluan 15.1, 15.2)
    'register_title' => 'Pendaftaran Kakitangan',
    'register_subtitle' => 'Cipta akaun ICTServe anda',
    'register_button' => 'Daftar',
    'already_registered' => 'Sudah berdaftar?',
    'name' => 'Nama Penuh',
    'name_placeholder' => 'Masukkan nama penuh anda',
    'confirm_password' => 'Sahkan Kata Laluan',
    'confirm_password_placeholder' => 'Masukkan semula kata laluan anda',
    'email_domain_hint' => 'Hanya alamat e-mel @motac.gov.my dibenarkan',
    'email_domain_error' => 'Hanya kakitangan MOTAC dengan e-mel @motac.gov.my boleh mendaftar.',
    'password_requirements' => 'Keperluan Kata Laluan',
    'password_min_length' => 'Sekurang-kurangnya 8 aksara',
    'password_uppercase' => 'Sekurang-kurangnya satu huruf besar',
    'password_lowercase' => 'Sekurang-kurangnya satu huruf kecil',
    'password_number' => 'Sekurang-kurangnya satu nombor',
    'password_special' => 'Sekurang-kurangnya satu aksara khas',
    'password_strength' => 'Kekuatan Kata Laluan',
    'password_weak' => 'Lemah',
    'password_fair' => 'Sederhana',
    'password_good' => 'Baik',
    'password_strong' => 'Kuat',
    'registration_success' => 'Pendaftaran berjaya! Sila semak e-mel anda untuk mengesahkan akaun anda.',
    'registration_failed' => 'Pendaftaran gagal. Sila cuba lagi.',

    // Terjemahan pengesahan e-mel (Tugasan 13.2 - Keperluan 15.4, 15.5)
    'verify_email_title' => 'Sahkan E-mel Anda',
    'verify_email_subtitle' => 'Hampir selesai! Sila sahkan alamat e-mel anda.',
    'verify_email_message' => 'Terima kasih kerana mendaftar! Sebelum bermula, sila sahkan alamat e-mel anda dengan mengklik pautan yang baru kami hantar kepada anda. Jika anda tidak menerima e-mel tersebut, kami dengan senang hati akan menghantar yang lain.',
    'verify_email_sent' => 'Pautan pengesahan baharu telah dihantar ke alamat e-mel yang anda berikan semasa pendaftaran.',
    'resend_verification_button' => 'Hantar Semula E-mel Pengesahan',
    'verification_success' => 'E-mel anda telah disahkan dengan jayanya. Anda kini boleh log masuk.',
    'verification_failed' => 'Pautan pengesahan tidak sah atau telah tamat tempoh.',
    'verification_expired' => 'Pautan pengesahan ini telah tamat tempoh. Sila minta yang baharu.',
    'verification_already_verified' => 'E-mel anda sudah disahkan.',

    // Terjemahan pengesahan kata laluan
    'confirm_password_message' => 'Ini adalah kawasan selamat aplikasi. Sila sahkan kata laluan anda sebelum meneruskan.',
    'confirm_button' => 'Sahkan',

    // Mesej Ralat Google SSO (Tugasan 4.1 - Keperluan 2.1, 2.2, 2.3)
    'google_sso_failed' => 'Pengesahan Google gagal. Sila cuba lagi atau gunakan log masuk biasa.',
    'google_sso_domain_error' => 'Hanya akaun @motac.gov.my sahaja dibenarkan untuk log masuk melalui Google SSO.',
    'google_sso_oauth_error' => 'Ralat keselamatan semasa pengesahan Google. Sila cuba lagi.',
    'google_sso_network_error' => 'Masalah sambungan ke Google. Sila cuba lagi atau gunakan log masuk biasa.',
    'google_sso_unavailable' => 'Perkhidmatan Google SSO tidak tersedia buat masa ini. Sila gunakan log masuk biasa.',
    'google_sso_account_disabled' => 'Akaun anda telah dinyahaktifkan. Sila hubungi pentadbir sistem.',
    'google_sso_fallback_hint' => 'Anda boleh log masuk menggunakan e-mel dan kata laluan anda.',

    // Status Pengesahan OAuth (Tugasan 1.1 - Keperluan 1.1, 2.5, 4.1)
    'oauth_status' => [
        'verified' => 'Disahkan',
        'pending' => 'Menunggu Pengesahan',
        'testing' => 'Mod Ujian',
        'rejected' => 'Ditolak',
        'unknown' => 'Tidak Diketahui',
    ],
    'test_user_required' => 'Akaun :email perlu ditambah ke senarai pengguna ujian. Aplikasi OAuth sedang dalam :status. Sila hubungi pentadbir sistem.',
    'verification_pending' => 'Aplikasi sedang dalam proses pengesahan Google. Sila hubungi pentadbir sistem.',
    'gmail_quota_exceeded' => 'Had penggunaan Gmail API telah dicapai. E-mel akan dihantar melalui sistem biasa.',
    'gmail_auth_failed' => 'Pengesahan Gmail tidak berjaya. E-mel akan dihantar melalui sistem biasa.',

    // Jenis Ralat SSO
    'error_types' => [
        'domain_error' => 'Ralat Domain',
        'oauth_error' => 'Ralat OAuth',
        'oauth_state_error' => 'Ralat Keadaan OAuth',
        'network_error' => 'Ralat Rangkaian',
        'general_error' => 'Ralat Umum',
        'verification_error' => 'Ralat Pengesahan',
        'quota_error' => 'Ralat Kuota',
        'rate_limit_error' => 'Ralat Had Kadar',
        'authentication_error' => 'Ralat Pengesahan',
        'configuration_error' => 'Ralat Konfigurasi',
        'service_unavailable' => 'Perkhidmatan Tidak Tersedia',
    ],

    // Mesej Ralat Gmail API Tambahan (Tugasan 8.1 - Keperluan 7.1, 7.4)
    'gmail_rate_limit_exceeded' => 'Had kadar Gmail API telah dicapai. Sila cuba lagi dalam beberapa minit.',
    'gmail_service_unavailable' => 'Perkhidmatan Gmail tidak tersedia buat masa ini. E-mel akan dihantar melalui sistem biasa.',
    'gmail_send_failed' => 'Penghantaran e-mel melalui Gmail gagal. E-mel akan dihantar melalui sistem biasa.',

    // Mesej Ralat Google SSO Tambahan (Tugasan 8.1 - Keperluan 7.1, 7.4)
    'google_sso_test_user_required' => 'Akaun anda perlu ditambah ke senarai pengguna ujian. Sila hubungi pentadbir sistem.',
    'google_sso_verification_rejected' => 'Pengesahan aplikasi OAuth telah ditolak. Sila hubungi pentadbir sistem.',
    'google_sso_configuration_error' => 'Konfigurasi Google SSO tidak lengkap. Sila hubungi pentadbir sistem.',

    // Perkhidmatan Google - Mesej Terperinci (Tugasan 8.1 - Keperluan 7.1, 7.2, 7.4, 7.5)
    'google_services' => [
        // Status Pengesahan
        'verification_rejected' => 'Pengesahan aplikasi OAuth telah ditolak oleh Google.',
        'verification_in_progress' => 'Pengesahan aplikasi OAuth sedang dalam proses.',

        // Mesej Gmail
        'gmail_token_expired' => 'Token Gmail telah tamat tempoh. Sila sahkan semula.',
        'gmail_not_configured' => 'Gmail API belum dikonfigurasi. Sila hubungi pentadbir sistem.',
        'gmail_fallback_activated' => 'E-mel akan dihantar melalui sistem SMTP biasa.',

        // Teks Bantuan
        'help' => [
            'domain' => 'Hanya akaun e-mel @motac.gov.my dibenarkan untuk log masuk. Sila gunakan akaun Google Workspace MOTAC anda.',
            'verification' => 'Aplikasi sedang dalam proses pengesahan Google. Sila hubungi pentadbir sistem untuk maklumat lanjut.',
            'verification_pending' => 'Pengesahan aplikasi OAuth sedang diproses oleh Google. Ini mungkin mengambil masa beberapa hari.',
            'verification_rejected' => 'Pengesahan aplikasi OAuth telah ditolak. Pentadbir sistem perlu mengemukakan semula permohonan.',
            'test_user' => 'Akaun anda perlu ditambah ke senarai pengguna ujian oleh pentadbir sistem sebelum boleh menggunakan Google SSO.',
            'quota' => 'Had penggunaan harian Gmail API telah dicapai. Kuota akan ditetapkan semula pada tengah malam.',
            'rate_limit' => 'Terlalu banyak permintaan dalam masa singkat. Sila tunggu beberapa minit sebelum mencuba lagi.',
            'network' => 'Masalah sambungan ke pelayan Google. Sila semak sambungan internet anda dan cuba lagi.',
            'token_expired' => 'Sesi Gmail anda telah tamat. Sila sahkan semula untuk meneruskan.',
            'gmail_setup' => 'Gmail API belum dikonfigurasi. Pentadbir sistem perlu menjalankan arahan "php artisan gmail:authorize".',
            'gmail_auth' => 'Pengesahan Gmail gagal. Sila cuba lagi atau hubungi pentadbir sistem.',
            'fallback' => 'Anda boleh log masuk menggunakan e-mel dan kata laluan anda sebagai alternatif.',
        ],

        // Mesej Kejayaan
        'success' => [
            'sso_linked' => 'Akaun Google anda telah berjaya dipautkan.',
            'sso_unlinked' => 'Akaun Google anda telah berjaya diputuskan.',
            'gmail_authorized' => 'Gmail API telah berjaya disahkan.',
            'test_user_added' => 'Pengguna ujian telah berjaya ditambah.',
            'test_user_removed' => 'Pengguna ujian telah berjaya dikeluarkan.',
        ],

        // Mesej Amaran
        'warnings' => [
            'quota_warning' => 'Penggunaan kuota Gmail API telah mencapai :percentage%. Sila pantau penggunaan.',
            'quota_critical' => 'Penggunaan kuota Gmail API kritikal (:percentage%). E-mel mungkin gagal dihantar.',
            'verification_expiring' => 'Pengesahan OAuth akan tamat tempoh dalam :days hari.',
        ],

        // Label Status
        'status' => [
            'healthy' => 'Sihat',
            'warning' => 'Amaran',
            'critical' => 'Kritikal',
            'unavailable' => 'Tidak Tersedia',
            'authenticated' => 'Disahkan',
            'not_authenticated' => 'Tidak Disahkan',
            'enabled' => 'Diaktifkan',
            'disabled' => 'Dinyahaktifkan',
        ],
    ],
];
