<?php

declare(strict_types=1);

/**
 * Filament Admin Login Page Translations (Bahasa Melayu)
 *
 * @version 3.6.1
 * @trace D15_LANGUAGE_MS_EN.md v3.6.1 (Bahasa Melayu Exclusive)
 */

return [
    'title' => 'Log Masuk',

    'heading' => 'Log Masuk Pentadbir',

    'actions' => [
        'register' => [
            'before' => 'atau',
            'label' => 'daftar akaun',
        ],

        'request_password_reset' => [
            'label' => 'Lupa kata laluan?',
        ],
    ],

    'form' => [
        'email' => [
            'label' => 'Emel atau Nama Pengguna',
        ],

        'password' => [
            'label' => 'Kata Laluan',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [
            'authenticate' => [
                'label' => 'Log Masuk',
            ],
        ],
    ],

    'multi_factor' => [
        'heading' => 'Sahkan identiti anda',

        'subheading' => 'Untuk meneruskan log masuk, anda perlu mengesahkan identiti anda.',

        'actions' => [
            'authenticate' => [
                'label' => 'Sahkan',
            ],

            'resend_code' => [
                'label' => 'Hantar semula kod',
            ],
        ],

        'form' => [
            'code' => [
                'label' => 'Kod',
            ],
        ],

        'messages' => [
            'invalid_code' => 'Kod tidak sah. Sila cuba lagi.',
        ],

        'notifications' => [
            'invalid_code' => [
                'title' => 'Kod tidak sah',
                'body' => 'Kod yang anda masukkan tidak sah. Sila cuba lagi.',
            ],

            'code_sent' => [
                'title' => 'Kod dihantar',
                'body' => 'Kod baharu telah dihantar ke :email.',
            ],
        ],
    ],

    'messages' => [
        'failed' => 'Kelayakan ini tidak sepadan dengan rekod kami.',

        'throttled' => 'Terlalu banyak percubaan log masuk. Sila cuba lagi dalam :seconds saat.',
    ],

    'notifications' => [
        'throttled' => [
            'title' => 'Terlalu banyak percubaan',
            'body' => 'Terlalu banyak percubaan log masuk. Sila cuba lagi dalam :seconds saat.',
        ],
    ],
];
