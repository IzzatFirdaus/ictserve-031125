<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md v3.6.1
// Filament Tables Translation - WCAG 2.2 AA Compliant

declare(strict_types=1);

return [
    'columns' => [
        'select_all' => [
            'label' => 'Pilih semua',
        ],
    ],

    'actions' => [
        'attach' => [
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

        'associate' => [
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

        'bulk_delete' => [
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

        'create' => [
            'label' => 'Cipta :label',
        ],

        'delete' => [
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

        'detach' => [
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

        'dissociate' => [
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

        'edit' => [
            'label' => 'Sunting',
        ],

        'export' => [
            'label' => 'Eksport',
            'modal' => [
                'heading' => 'Eksport :label',
                'form' => [
                    'type' => [
                        'label' => 'Jenis',
                        'options' => [
                            'csv' => 'CSV',
                            'xlsx' => 'XLSX',
                        ],
                    ],
                    'columns' => [
                        'label' => 'Lajur',
                        'form' => [
                            'is_enabled' => [
                                'label' => ':column diaktifkan',
                            ],
                            'label' => [
                                'label' => 'Label :column',
                            ],
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
                'max_rows' => [
                    'title' => 'Eksport terlalu besar',
                    'body' => 'Tidak boleh mengeksport lebih daripada 1 baris pada satu masa.|Tidak boleh mengeksport lebih daripada :count baris pada satu masa.',
                ],
                'failed' => [
                    'title' => 'Eksport gagal',
                    'body' => 'Eksport gagal disebabkan ralat yang tidak diketahui.',
                ],
            ],
        ],

        'filter' => [
            'label' => 'Tapis',
        ],

        'group' => [
            'label' => 'Kumpul',
        ],

        'import' => [
            'label' => 'Import',
            'modal' => [
                'heading' => 'Import :label',
                'form' => [
                    'file' => [
                        'label' => 'Fail',
                        'placeholder' => 'Muat naik fail CSV',
                        'rules' => [
                            'duplicate_columns' => '{0} Fail tidak boleh mengandungi lebih daripada satu lajur kosong.|{1,*} Fail tidak boleh mengandungi lajur pendua: :columns.',
                        ],
                    ],
                    'columns' => [
                        'label' => 'Lajur',
                        'placeholder' => 'Pilih lajur',
                    ],
                ],
                'actions' => [
                    'download_example' => [
                        'label' => 'Muat turun fail contoh',
                    ],
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
            'example_csv' => [
                'file_name' => ':importer-contoh',
            ],
        ],

        'open' => [
            'label' => 'Buka',
        ],

        'replicate' => [
            'label' => 'Replikasi',
        ],

        'view' => [
            'label' => 'Lihat',
        ],
    ],

    'empty' => [
        'heading' => 'Tiada :model dijumpai',
        'description' => 'Cipta :model untuk bermula.',
    ],

    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Gunakan penapis',
            ],
            'remove' => [
                'label' => 'Buang penapis',
            ],
            'remove_all' => [
                'label' => 'Buang semua penapis',
            ],
            'reset' => [
                'label' => 'Tetapkan semula',
            ],
        ],

        'heading' => 'Penapis',

        'indicator' => 'Penapis aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
        ],

        'trashed' => [
            'label' => 'Rekod yang dipadam',
            'only_trashed' => 'Hanya rekod yang dipadam',
            'with_trashed' => 'Dengan rekod yang dipadam',
            'without_trashed' => 'Tanpa rekod yang dipadam',
        ],
    ],

    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'Kumpul mengikut',
                'placeholder' => 'Kumpul mengikut',
            ],
            'direction' => [
                'label' => 'Arah kumpulan',
                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],
            ],
        ],
    ],

    'reorder_indicator' => 'Seret dan lepas rekod ke dalam susunan.',

    'selection_indicator' => [
        'selected_count' => '1 rekod dipilih|:count rekod dipilih',
        'actions' => [
            'select_all' => [
                'label' => 'Pilih semua :count',
            ],
            'deselect_all' => [
                'label' => 'Nyahpilih semua',
            ],
        ],
    ],

    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Susun mengikut',
            ],
            'direction' => [
                'label' => 'Arah susunan',
                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],
            ],
        ],
    ],

    'search' => [
        'label' => 'Cari',
        'placeholder' => 'Cari',
        'indicator' => 'Cari',
    ],

    'pagination' => [
        'label' => 'Navigasi halaman',
        'overview' => '{1} Menunjukkan 1 hasil|[2,*] Menunjukkan :first hingga :last daripada :total hasil',
        'fields' => [
            'records_per_page' => [
                'label' => 'setiap halaman',
                'options' => [
                    'all' => 'Semua',
                ],
            ],
        ],
        'actions' => [
            'first' => [
                'label' => 'Pertama',
            ],
            'go_to_page' => [
                'label' => 'Pergi ke halaman :page',
            ],
            'last' => [
                'label' => 'Terakhir',
            ],
            'next' => [
                'label' => 'Seterusnya',
            ],
            'previous' => [
                'label' => 'Sebelumnya',
            ],
        ],
    ],

    // Accessibility labels (WCAG 2.2 AA)
    'accessibility' => [
        'table' => 'Jadual data',
        'sortable_column' => 'Lajur boleh disusun',
        'sort_ascending' => 'Susun menaik',
        'sort_descending' => 'Susun menurun',
        'row_actions' => 'Tindakan baris',
        'select_row' => 'Pilih baris',
        'deselect_row' => 'Nyahpilih baris',
        'loading_data' => 'Sedang memuatkan data',
        'no_data' => 'Tiada data tersedia',
        'page_navigation' => 'Navigasi halaman',
        'current_page' => 'Halaman semasa',
        'filter_panel' => 'Panel penapis',
        'search_field' => 'Medan carian',
        'bulk_actions' => 'Tindakan pukal',
    ],
];
