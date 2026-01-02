<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md v3.6.1
// Filament Panels Translation - WCAG 2.2 AA Compliant

declare(strict_types=1);

return [
    'actions' => [
        'open_actions_menu' => 'Buka menu tindakan',
        'toggle_sidebar' => 'Togol bar sisi',
        'toggle_theme' => 'Tukar tema',
        'logout' => 'Log keluar',
        'open_user_menu' => 'Buka menu pengguna',
        'open_tenant_menu' => 'Buka menu penyewa',
        'toggle_navigation' => 'Togol navigasi',
    ],

    'avatar' => [
        'alt' => 'Avatar :name',
    ],

    'breadcrumbs' => [
        'separator' => '/',
    ],

    'global_search' => [
        'placeholder' => 'Carian global...',
        'no_results' => 'Tiada hasil ditemui.',
        'results_count' => '{0} Tiada hasil|{1} 1 hasil|[2,*] :count hasil',
    ],

    'layout' => [
        'sidebar' => [
            'expand' => 'Kembangkan bar sisi',
            'collapse' => 'Runtuhkan bar sisi',
        ],
    ],

    'navigation' => [
        'label' => 'Navigasi',
        'groups' => [
            'operations' => 'Operasi',
            'management' => 'Pengurusan',
            'system' => 'Sistem',
            'reports' => 'Laporan & Analitik',
        ],
    ],

    'pages' => [
        'dashboard' => [
            'title' => 'Papan Pemuka',
            'navigation_label' => 'Papan Pemuka',
        ],
        'auth' => [
            'login' => [
                'title' => 'Log Masuk',
                'heading' => 'Log Masuk Pentadbir',
                'form' => [
                    'email' => [
                        'label' => 'Emel',
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
                'messages' => [
                    'failed' => 'Kelayakan ini tidak sepadan dengan rekod kami.',
                    'throttled' => 'Terlalu banyak percubaan log masuk. Sila cuba lagi dalam :seconds saat.',
                ],
            ],
        ],
    ],

    'resources' => [
        'label' => 'Sumber',
        'plural_label' => 'Sumber',
        'navigation_label' => 'Sumber',
        'navigation_group' => 'Sumber',
        'pages' => [
            'create' => [
                'title' => 'Cipta :label',
                'navigation_label' => 'Cipta',
                'breadcrumb' => 'Cipta',
                'form' => [
                    'actions' => [
                        'create' => [
                            'label' => 'Cipta',
                        ],
                        'create_another' => [
                            'label' => 'Simpan & Tambah Lagi',
                        ],
                        'cancel' => [
                            'label' => 'Batal',
                        ],
                    ],
                ],
                'notifications' => [
                    'created' => [
                        'title' => 'Dicipta',
                        'body' => ':label berjaya dicipta.',
                    ],
                ],
            ],
            'edit' => [
                'title' => 'Sunting :label',
                'navigation_label' => 'Sunting',
                'breadcrumb' => 'Sunting',
                'form' => [
                    'actions' => [
                        'save' => [
                            'label' => 'Simpan perubahan',
                        ],
                        'cancel' => [
                            'label' => 'Batal',
                        ],
                    ],
                ],
                'notifications' => [
                    'saved' => [
                        'title' => 'Disimpan',
                        'body' => ':label berjaya disimpan.',
                    ],
                ],
            ],
            'index' => [
                'title' => ':label',
                'navigation_label' => ':label',
                'breadcrumb' => ':label',
            ],
            'view' => [
                'title' => 'Lihat :label',
                'navigation_label' => 'Lihat',
                'breadcrumb' => 'Lihat',
            ],
        ],
    ],

    'tenant' => [
        'actions' => [
            'register' => [
                'label' => 'Daftar penyewa',
            ],
        ],
        'registration' => [
            'title' => 'Daftar penyewa',
            'form' => [
                'actions' => [
                    'register' => [
                        'label' => 'Daftar',
                    ],
                ],
            ],
        ],
    ],

    'widgets' => [
        'account' => [
            'label' => 'Akaun',
        ],
        'filament_info' => [
            'label' => 'Maklumat Filament',
        ],
    ],

    // Accessibility labels (WCAG 2.2 AA)
    'accessibility' => [
        'skip_to_content' => 'Langkau ke kandungan utama',
        'main_navigation' => 'Navigasi utama',
        'user_menu' => 'Menu pengguna',
        'search' => 'Carian',
        'close' => 'Tutup',
        'open' => 'Buka',
        'loading' => 'Memuatkan...',
        'error' => 'Ralat',
        'success' => 'Berjaya',
        'warning' => 'Amaran',
        'info' => 'Maklumat',
    ],

    // Status messages
    'status' => [
        'loading' => 'Sedang memuatkan...',
        'saving' => 'Sedang menyimpan...',
        'saved' => 'Disimpan',
        'creating' => 'Sedang mencipta...',
        'created' => 'Dicipta',
        'updating' => 'Sedang mengemaskini...',
        'updated' => 'Dikemaskini',
        'deleting' => 'Sedang memadam...',
        'deleted' => 'Dipadam',
        'error' => 'Ralat berlaku',
        'success' => 'Berjaya',
    ],
];
