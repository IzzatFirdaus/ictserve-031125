<?php

declare(strict_types=1);

/**
 * Terjemahan Bahasa Melayu untuk Modul Pemindahan Aset
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 59.1, 59.2, 59.3, 59.4, 59.5
 */
return [
    // Navigation & Labels
    'navigation_label' => 'Pemindahan Aset',
    'model_label' => 'Pemindahan Aset',
    'plural_label' => 'Pemindahan Aset',

    // Sections
    'sections' => [
        'transfer_details' => 'Butiran Pemindahan',
        'parties_involved' => 'Pihak Terlibat',
        'location_notes' => 'Lokasi & Catatan',
    ],

    // Form Fields
    'fields' => [
        'asset_id' => 'Aset',
        'transfer_date' => 'Tarikh Pemindahan',
        'status' => 'Status',
        'from_user_id' => 'Daripada Pengguna (jika berkenaan)',
        'to_user_id' => 'Kepada Pengguna',
        'from_location' => 'Lokasi Asal (jika berkenaan)',
        'to_location' => 'Lokasi Baharu (jika berkenaan)',
        'initiated_by' => 'Dimulakan Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'notes' => 'Catatan',
        'cancellation_reason' => 'Sebab Pembatalan',
    ],

    // Status Options
    'status' => [
        'pending' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    // Table Columns
    'columns' => [
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'to_user' => 'Kepada',
        'status' => 'Status',
        'transfer_date' => 'Tarikh',
        'from_user' => 'Daripada',
        'from_location' => 'Lokasi Asal',
        'to_location' => 'Lokasi Baharu',
        'initiated_by' => 'Dimulakan Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'created_at' => 'Dicipta',
    ],

    // Filters
    'filters' => [
        'status' => 'Status',
        'to_user' => 'Kepada Pengguna',
        'date_from' => 'Dari',
        'date_until' => 'Hingga',
    ],

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada rekod pemindahan aset',
        'description' => 'Klik \'Cipta\' untuk merekod pemindahan aset antara bahagian.',
    ],

    // Actions
    'actions' => [
        'create' => 'Cipta Pemindahan',
        'edit' => 'Sunting',
        'view' => 'Lihat',
        'delete' => 'Padam',
        'approve' => 'Luluskan',
        'cancel' => 'Batalkan',
    ],

    // Messages
    'messages' => [
        'created' => 'Pemindahan aset berjaya dicipta.',
        'updated' => 'Pemindahan aset berjaya dikemaskini.',
        'deleted' => 'Pemindahan aset berjaya dipadam.',
        'approved' => 'Pemindahan aset berjaya diluluskan.',
        'cancelled' => 'Pemindahan aset berjaya dibatalkan.',
    ],
];
