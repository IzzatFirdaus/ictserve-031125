<?php

declare(strict_types=1);

return [
    'navigation' => [
        'title' => 'Pengurusan Ambang SLA',
        'label' => 'Ambang SLA',
        'group' => 'Konfigurasi Sistem',
    ],

    'form' => [
        'categories' => [
            'label' => 'Kategori SLA',
            'fieldset' => 'Maklumat Kategori',
            'name' => 'Nama Kategori',
            'description' => 'Keterangan',
            'response_fieldset' => 'Masa Respons (Jam)',
            'resolution_fieldset' => 'Masa Penyelesaian (Jam)',
            'new_label' => 'Kategori Baharu',
            'add_action' => 'Tambah Kategori',
            'suffix' => [
                'hours' => 'jam',
                'minutes' => 'minit',
            ],
            'levels' => [
                'low' => 'Rendah',
                'normal' => 'Biasa',
                'high' => 'Tinggi',
                'urgent' => 'Segera',
            ],
        ],

        'escalation' => [
            'fieldset' => 'Konfigurasi Eskalasi',
            'enabled' => 'Aktifkan Eskalasi Automatik',
            'threshold_percent' => 'Ambang Eskalasi (%)',
            'helper' => 'Eskalasi akan berlaku apabila baki masa kurang daripada peratusan ini',
            'roles' => [
                'label' => 'Peranan untuk Eskalasi',
                'options' => [
                    'admin' => 'Pentadbir',
                    'superuser' => 'Superuser',
                ],
            ],
            'auto_assign' => 'Tugaskan Automatik kepada Pelulus',
        ],

        'notifications' => [
            'fieldset' => 'Konfigurasi Notifikasi',
            'enabled' => 'Aktifkan Notifikasi SLA',
            'warning' => 'Amaran (Minit sebelum breach)',
            'critical' => 'Kritikal (Minit sebelum breach)',
            'overdue' => 'Tertunggak (Selang notifikasi)',
            'recipients' => [
                'assignee' => 'Notifikasi kepada Penerima Tugasan',
                'supervisor' => 'Notifikasi kepada Penyelia',
                'admin' => 'Notifikasi kepada Pentadbir',
            ],
        ],

        'business_hours' => [
            'fieldset' => 'Waktu Perniagaan',
            'enabled' => 'Aktifkan Waktu Perniagaan',
            'timezone' => 'Zon Masa',
            'start' => 'Masa Mula',
            'end' => 'Masa Tamat',
            'working_days' => 'Hari Bekerja',
            'days' => [
                1 => 'Isnin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Khamis',
                5 => 'Jumaat',
                6 => 'Sabtu',
                7 => 'Ahad',
            ],
            'exclude_weekends' => 'Kecualikan Hujung Minggu',
            'exclude_holidays' => 'Kecualikan Cuti Umum',
            'timezones' => [
                'Asia/Kuala_Lumpur' => 'Asia/Kuala Lumpur (MYT)',
                'Asia/Singapore' => 'Asia/Singapore (SGT)',
                'UTC' => 'UTC',
            ],
        ],
    ],

    'actions' => [
        'save' => 'Simpan Konfigurasi',
        'test' => 'Uji SLA',
        'reset' => 'Reset ke Lalai',
        'export' => 'Eksport',
        'import' => 'Import',
    ],

    'modals' => [
        'reset' => [
            'heading' => 'Reset Ambang SLA',
            'description' => 'Adakah anda pasti mahu reset ambang SLA ke konfigurasi lalai? Semua perubahan akan hilang.',
        ],
    ],

    'notifications' => [
        'save_success' => 'Ambang SLA berjaya dikemaskini',
        'save_error' => 'Ralat menyimpan konfigurasi',
        'reset_success' => 'Ambang SLA telah direset',
        'import_success' => 'Ambang SLA berjaya diimport',
        'import_error' => 'Ralat mengimport ambang SLA',
        'test_title' => 'Ujian SLA selesai',
        'test_body' => 'Semua :count kes ujian telah dijalankan',
    ],

    'upload' => [
        'label' => 'Fail JSON',
        'invalid' => 'Fail JSON tidak sah',
    ],
];
