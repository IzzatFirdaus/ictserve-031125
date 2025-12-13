<?php

return [
    'retention' => [
        'title' => 'Polisi Penyimpanan Data',
        'description' => 'Sistem ini menyimpan rekod audit untuk memenuhi keperluan undang-undang dan pematuhan. Rekod audit disimpan selama 7 tahun secara lalai. Selepas tempoh penyimpanan, rekod akan diarkibkan atau dianonimkan mengikut polisi organisasi dan keperluan PDPA/privasi.',
        'docs' => 'Untuk maklumat lanjut, rujuk :requirements dan dokumentasi :design.',
        'docs_requirements' => 'Keperluan Perisian',
        'docs_design' => 'Reka Bentuk Teknikal',
        'note' => 'Hanya pengguna dengan peranan pentadbir atau pematuhan yang sesuai boleh mengakses atau mengeksport rekod audit. Semak jadual penyimpanan organisasi anda untuk had tambahan.',
        'actions' => [
            'export_all' => 'Eksport Semua',
            'retention_policy' => 'Polisi Penahanan',
            'security_summary' => 'Ringkasan Keselamatan',
        ],
        'modals' => [
            'export' => [
                'heading' => 'Eksport Log Audit',
                'description' => 'Eksport log audit mengikut kriteria yang dipilih. Eksport besar mungkin mengambil masa beberapa minit.',
            ],
        ],
    ],
    
    // PDF Export translations
    'pdf' => [
        'title' => 'Laporan Log Audit Bersepadu',
        'ministry' => 'Kementerian Pelancongan, Seni dan Budaya Malaysia',
        'department' => 'Bahagian Pengurusan Maklumat',
        'generated_at' => 'Dijana Pada',
        'generated_by' => 'Dijana Oleh',
        'filter' => 'Penapis',
        'date_from' => 'Tarikh Dari',
        'date_to' => 'Tarikh Hingga',
        'total_records' => 'Jumlah Rekod',
        'timestamp' => 'Cap Masa',
        'source' => 'Sumber',
        'user' => 'Pengguna',
        'action' => 'Tindakan',
        'entity' => 'Entiti',
        'description_changes' => 'Penerangan/Perubahan',
        'no_records' => 'Tiada rekod dijumpai.',
        'footer_title' => 'ICTServe v3.5.0 - Laporan Log Audit Bersepadu',
        'confidential' => 'SULIT - Untuk Kegunaan Dalaman Sahaja',
        'page' => 'Halaman',
    ],
];
