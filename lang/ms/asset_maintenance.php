<?php

declare(strict_types=1);

/**
 * Terjemahan Bahasa Melayu untuk Modul Penyelenggaraan Aset
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 54.4, 55.5, 56.2, 57.1, 57.2, 57.3
 */
return [
    // Navigation and Labels
    'navigation_label' => 'Penyelenggaraan Aset',
    'model_label' => 'Penyelenggaraan',
    'plural_label' => 'Penyelenggaraan Aset',

    // Sections
    'sections' => [
        'maintenance_details' => 'Butiran Penyelenggaraan',
        'performer_info' => 'Maklumat Pelaksana',
        'additional_info' => 'Maklumat Tambahan',
    ],

    // Field Labels
    'fields' => [
        'asset_id' => 'Aset',
        'maintenance_type' => 'Jenis Penyelenggaraan',
        'status' => 'Status',
        'scheduled_date' => 'Tarikh Dijadualkan',
        'completed_date' => 'Tarikh Selesai',
        'cost' => 'Kos (RM)',
        'performer_mode' => 'Pelaksana',
        'performed_by_user_id' => 'Kakitangan Dalaman',
        'performed_by' => 'Vendor Luar',
        'notes' => 'Catatan',
    ],

    // Helper Text
    'helpers' => [
        'asset_id' => 'Pilih aset yang memerlukan penyelenggaraan',
        'maintenance_type' => 'Pilih jenis penyelenggaraan yang akan dilakukan',
        'status' => 'Status semasa penyelenggaraan',
        'scheduled_date' => 'Tarikh penyelenggaraan dijadualkan',
        'completed_date' => 'Tarikh penyelenggaraan selesai',
        'cost' => 'Anggaran atau kos sebenar penyelenggaraan',
        'performer_mode' => 'Pilih sama ada penyelenggaraan dilakukan oleh kakitangan dalaman atau vendor luar',
        'performed_by_user_id' => 'Pilih kakitangan yang melaksanakan penyelenggaraan',
        'performed_by' => 'Masukkan nama vendor atau kontraktor luar',
        'notes' => 'Catatan tambahan mengenai penyelenggaraan',
    ],

    // Placeholders
    'placeholders' => [
        'asset_id' => 'Pilih aset...',
        'maintenance_type' => 'Pilih jenis...',
        'status' => 'Pilih status...',
        'performed_by_user_id' => 'Pilih kakitangan...',
        'performed_by' => 'Nama vendor atau kontraktor...',
        'notes' => 'Masukkan catatan di sini...',
        'cost' => '0.00',
    ],

    // Performer Options
    'performer_options' => [
        'internal' => 'Kakitangan Dalaman',
        'external' => 'Vendor Luar',
    ],

    // Maintenance Types
    'maintenance_types' => [
        'routine' => 'Rutin',
        'repair' => 'Pembaikan',
        'upgrade' => 'Naik Taraf',
        'inspection' => 'Pemeriksaan',
    ],

    // Status Options
    'status_options' => [
        'scheduled' => 'Dijadualkan',
        'in_progress' => 'Sedang Dijalankan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    // Column Labels
    'columns' => [
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'maintenance_type' => 'Jenis',
        'status' => 'Status',
        'scheduled_date' => 'Tarikh Dijadualkan',
        'completed_date' => 'Tarikh Selesai',
        'performed_by_user' => 'Pelaksana (Dalaman)',
        'performed_by' => 'Pelaksana (Luar)',
        'cost' => 'Kos',
        'created_at' => 'Dicipta',
        'updated_at' => 'Dikemaskini',
    ],

    // Filter Labels
    'filters' => [
        'status' => 'Status',
        'maintenance_type' => 'Jenis Penyelenggaraan',
        'date_from' => 'Dari Tarikh',
        'date_until' => 'Hingga Tarikh',
    ],

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada rekod penyelenggaraan',
        'description' => 'Klik "Cipta" untuk merekod penyelenggaraan aset baharu.',
        'action' => 'Cipta Penyelenggaraan',
    ],

    // Actions
    'actions' => [
        'create' => 'Cipta Penyelenggaraan',
        'edit' => 'Edit Penyelenggaraan',
        'view' => 'Lihat Penyelenggaraan',
        'delete' => 'Padam',
        'restore' => 'Pulihkan',
    ],

    // Notifications
    'notifications' => [
        'created' => 'Penyelenggaraan berjaya dicipta',
        'updated' => 'Penyelenggaraan berjaya dikemaskini',
        'deleted' => 'Penyelenggaraan berjaya dipadam',
    ],
];
