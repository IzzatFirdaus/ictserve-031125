<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md v3.6.1
// Filament Actions Translation - WCAG 2.2 AA Compliant

declare(strict_types=1);

return [
    'attach' => [
        'single' => [
            'label' => 'Lampirkan',
            'modal' => [
                'heading' => 'Lampirkan :label',
                'fields' => [
                    'record_id' => [
                        'label' => 'Rekod',
                    ],
                ],
                'actions' => [
                    'attach' => [
                        'label' => 'Lampirkan',
                    ],
                    'attach_another' => [
                        'label' => 'Lampirkan & lampirkan lagi',
                    ],
                ],
            ],
            'notifications' => [
                'attached' => [
                    'title' => 'Dilampirkan',
                ],
            ],
        ],
    ],

    'associate' => [
        'single' => [
            'label' => 'Kaitkan',
            'modal' => [
                'heading' => 'Kaitkan :label',
                'fields' => [
                    'record_id' => [
                        'label' => 'Rekod',
                    ],
                ],
                'actions' => [
                    'associate' => [
                        'label' => 'Kaitkan',
                    ],
                    'associate_another' => [
                        'label' => 'Kaitkan & kaitkan lagi',
                    ],
                ],
            ],
            'notifications' => [
                'associated' => [
                    'title' => 'Dikaitkan',
                ],
            ],
        ],
    ],

    'clone' => [
        'single' => [
            'label' => 'Klon',
            'modal' => [
                'heading' => 'Klon :label',
                'actions' => [
                    'clone' => [
                        'label' => 'Klon',
                    ],
                ],
            ],
            'notifications' => [
                'cloned' => [
                    'title' => 'Diklon',
                ],
            ],
        ],
    ],

    'create' => [
        'single' => [
            'label' => 'Cipta :label',
            'modal' => [
                'heading' => 'Cipta :label',
                'actions' => [
                    'create' => [
                        'label' => 'Cipta',
                    ],
                    'create_another' => [
                        'label' => 'Cipta & cipta lagi',
                    ],
                ],
            ],
            'notifications' => [
                'created' => [
                    'title' => 'Dicipta',
                ],
            ],
        ],
    ],

    'delete' => [
        'single' => [
            'label' => 'Padam',
            'modal' => [
                'heading' => 'Padam :label',
                'description' => 'Adakah anda pasti mahu memadam rekod ini? Tindakan ini tidak boleh dibuat asal.',
                'actions' => [
                    'delete' => [
                        'label' => 'Padam',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Dipadam',
                ],
            ],
        ],

        'multiple' => [
            'label' => 'Padam yang dipilih',
            'modal' => [
                'heading' => 'Padam :label yang dipilih',
                'description' => 'Adakah anda pasti mahu memadam rekod yang dipilih? Tindakan ini tidak boleh dibuat asal.',
                'actions' => [
                    'delete' => [
                        'label' => 'Padam',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Dipadam',
                ],
            ],
        ],
    ],

    'detach' => [
        'single' => [
            'label' => 'Nyahlampir',
            'modal' => [
                'heading' => 'Nyahlampir :label',
                'description' => 'Adakah anda pasti mahu menyahlampir rekod ini?',
                'actions' => [
                    'detach' => [
                        'label' => 'Nyahlampir',
                    ],
                ],
            ],
            'notifications' => [
                'detached' => [
                    'title' => 'Dinyahlampir',
                ],
            ],
        ],

        'multiple' => [
            'label' => 'Nyahlampir yang dipilih',
            'modal' => [
                'heading' => 'Nyahlampir :label yang dipilih',
                'description' => 'Adakah anda pasti mahu menyahlampir rekod yang dipilih?',
                'actions' => [
                    'detach' => [
                        'label' => 'Nyahlampir',
                    ],
                ],
            ],
            'notifications' => [
                'detached' => [
                    'title' => 'Dinyahlampir',
                ],
            ],
        ],
    ],

    'dissociate' => [
        'single' => [
            'label' => 'Nyahkait',
            'modal' => [
                'heading' => 'Nyahkait :label',
                'description' => 'Adakah anda pasti mahu menyahkait rekod ini?',
                'actions' => [
                    'dissociate' => [
                        'label' => 'Nyahkait',
                    ],
                ],
            ],
            'notifications' => [
                'dissociated' => [
                    'title' => 'Dinyahkait',
                ],
            ],
        ],

        'multiple' => [
            'label' => 'Nyahkait yang dipilih',
            'modal' => [
                'heading' => 'Nyahkait :label yang dipilih',
                'description' => 'Adakah anda pasti mahu menyahkait rekod yang dipilih?',
                'actions' => [
                    'dissociate' => [
                        'label' => 'Nyahkait',
                    ],
                ],
            ],
            'notifications' => [
                'dissociated' => [
                    'title' => 'Dinyahkait',
                ],
            ],
        ],
    ],

    'edit' => [
        'single' => [
            'label' => 'Sunting',
            'modal' => [
                'heading' => 'Sunting :label',
                'actions' => [
                    'save' => [
                        'label' => 'Simpan perubahan',
                    ],
                ],
            ],
            'notifications' => [
                'saved' => [
                    'title' => 'Disimpan',
                ],
            ],
        ],
    ],

    'export' => [
        'single' => [
            'label' => 'Eksport',
            'modal' => [
                'heading' => 'Eksport :label',
                'form' => [
                    'type' => [
                        'label' => 'Jenis',
                        'options' => [
                            'csv' => 'CSV',
                            'xlsx' => 'XLSX',
                            'pdf' => 'PDF',
                        ],
                    ],
                ],
                'actions' => [
                    'export' => [
                        'label' => 'Eksport',
                    ],
                ],
            ],
            'notifications' => [
                'completed' => [
                    'title' => 'Eksport selesai',
                    'body' => 'Eksport anda sudah siap dan sedang dimuat turun.',
                ],
                'failed' => [
                    'title' => 'Eksport gagal',
                    'body' => 'Eksport gagal disebabkan ralat yang tidak diketahui.',
                ],
            ],
        ],
    ],

    'force_delete' => [
        'single' => [
            'label' => 'Padam kekal',
            'modal' => [
                'heading' => 'Padam kekal :label',
                'description' => 'Adakah anda pasti mahu memadam kekal rekod ini? Tindakan ini tidak boleh dibuat asal.',
                'actions' => [
                    'delete' => [
                        'label' => 'Padam kekal',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Dipadam kekal',
                ],
            ],
        ],

        'multiple' => [
            'label' => 'Padam kekal yang dipilih',
            'modal' => [
                'heading' => 'Padam kekal :label yang dipilih',
                'description' => 'Adakah anda pasti mahu memadam kekal rekod yang dipilih? Tindakan ini tidak boleh dibuat asal.',
                'actions' => [
                    'delete' => [
                        'label' => 'Padam kekal',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Dipadam kekal',
                ],
            ],
        ],
    ],

    'import' => [
        'single' => [
            'label' => 'Import :label',
            'modal' => [
                'heading' => 'Import :label',
                'form' => [
                    'file' => [
                        'label' => 'Fail',
                        'placeholder' => 'Muat naik fail CSV',
                    ],
                ],
                'actions' => [
                    'import' => [
                        'label' => 'Import',
                    ],
                ],
            ],
            'notifications' => [
                'completed' => [
                    'title' => 'Import selesai',
                    'body' => 'Import anda telah selesai dan data telah diproses.',
                ],
                'processing' => [
                    'title' => 'Memproses import',
                    'body' => 'Import anda sedang diproses dan anda akan dimaklumkan apabila selesai.',
                ],
                'failed' => [
                    'title' => 'Import gagal',
                    'body' => 'Import gagal disebabkan ralat yang tidak diketahui.',
                ],
            ],
        ],
    ],

    'replicate' => [
        'single' => [
            'label' => 'Replikasi',
            'modal' => [
                'heading' => 'Replikasi :label',
                'actions' => [
                    'replicate' => [
                        'label' => 'Replikasi',
                    ],
                ],
            ],
            'notifications' => [
                'replicated' => [
                    'title' => 'Direplikasi',
                ],
            ],
        ],
    ],

    'restore' => [
        'single' => [
            'label' => 'Pulihkan',
            'modal' => [
                'heading' => 'Pulihkan :label',
                'description' => 'Adakah anda pasti mahu memulihkan rekod ini?',
                'actions' => [
                    'restore' => [
                        'label' => 'Pulihkan',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Dipulihkan',
                ],
            ],
        ],

        'multiple' => [
            'label' => 'Pulihkan yang dipilih',
            'modal' => [
                'heading' => 'Pulihkan :label yang dipilih',
                'description' => 'Adakah anda pasti mahu memulihkan rekod yang dipilih?',
                'actions' => [
                    'restore' => [
                        'label' => 'Pulihkan',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Dipulihkan',
                ],
            ],
        ],
    ],

    'view' => [
        'single' => [
            'label' => 'Lihat',
        ],
    ],

    // Common action labels
    'common' => [
        'save' => 'Simpan',
        'cancel' => 'Batal',
        'close' => 'Tutup',
        'confirm' => 'Sahkan',
        'submit' => 'Hantar',
        'reset' => 'Tetapkan semula',
        'clear' => 'Kosongkan',
        'refresh' => 'Muat semula',
        'back' => 'Kembali',
        'next' => 'Seterusnya',
        'previous' => 'Sebelumnya',
        'continue' => 'Teruskan',
        'finish' => 'Selesai',
        'skip' => 'Langkau',
        'retry' => 'Cuba lagi',
        'download' => 'Muat turun',
        'upload' => 'Muat naik',
        'browse' => 'Layari',
        'select' => 'Pilih',
        'deselect' => 'Nyahpilih',
        'toggle' => 'Togol',
        'expand' => 'Kembangkan',
        'collapse' => 'Runtuhkan',
        'show' => 'Tunjukkan',
        'hide' => 'Sembunyikan',
        'enable' => 'Aktifkan',
        'disable' => 'Nyahaktif',
        'approve' => 'Luluskan',
        'reject' => 'Tolak',
        'accept' => 'Terima',
        'decline' => 'Tolak',
        'send' => 'Hantar',
        'receive' => 'Terima',
        'copy' => 'Salin',
        'paste' => 'Tampal',
        'cut' => 'Potong',
        'undo' => 'Buat asal',
        'redo' => 'Buat semula',
        'search' => 'Cari',
        'filter' => 'Tapis',
        'sort' => 'Susun',
        'group' => 'Kumpul',
        'print' => 'Cetak',
        'share' => 'Kongsi',
        'bookmark' => 'Tandabuku',
        'favorite' => 'Kegemaran',
        'like' => 'Suka',
        'dislike' => 'Tidak suka',
        'follow' => 'Ikut',
        'unfollow' => 'Nyahikut',
        'subscribe' => 'Langgan',
        'unsubscribe' => 'Nyahlanggan',
        'login' => 'Log masuk',
        'logout' => 'Log keluar',
        'register' => 'Daftar',
        'profile' => 'Profil',
        'settings' => 'Tetapan',
        'preferences' => 'Keutamaan',
        'help' => 'Bantuan',
        'support' => 'Sokongan',
        'contact' => 'Hubungi',
        'about' => 'Tentang',
        'terms' => 'Terma',
        'privacy' => 'Privasi',
        'legal' => 'Undang-undang',
    ],

    // Accessibility labels (WCAG 2.2 AA)
    'accessibility' => [
        'action_button' => 'Butang tindakan',
        'primary_action' => 'Tindakan utama',
        'secondary_action' => 'Tindakan sekunder',
        'destructive_action' => 'Tindakan merosakkan',
        'bulk_action' => 'Tindakan pukal',
        'dropdown_menu' => 'Menu dropdown',
        'modal_dialog' => 'Dialog modal',
        'confirmation_dialog' => 'Dialog pengesahan',
        'loading_action' => 'Tindakan sedang diproses',
        'disabled_action' => 'Tindakan dinyahaktif',
        'keyboard_shortcut' => 'Pintasan papan kekunci',
        'tooltip' => 'Petua alat',
        'action_menu' => 'Menu tindakan',
        'context_menu' => 'Menu konteks',
    ],
];
