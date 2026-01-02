<?php

declare(strict_types=1);

/**
 * Terjemahan Bahasa Melayu untuk Filament Create Record Page
 *
 * Override untuk label "Create Another" kepada Bahasa Melayu
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 40.1, 40.2, 40.3
 */
return [
    'title' => 'Cipta :label',

    'breadcrumb' => 'Cipta',

    'form' => [
        'actions' => [
            'cancel' => [
                'label' => 'Batal',
            ],

            'create' => [
                'label' => 'Cipta',
            ],

            'create_another' => [
                'label' => 'Simpan & Tambah Lagi',
            ],
        ],
    ],

    'notifications' => [
        'created' => [
            'title' => 'Dicipta',
        ],
    ],
];
