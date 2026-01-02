<?php

declare(strict_types=1);

return [
    // Page
    'title' => 'Dashboard Visualisasi Data',
    'label' => 'Visualisasi Data',

    // Badges
    'badges' => [
        'realtime' => 'Masa Nyata',
        'interactive' => 'Interaktif',
        'cached' => 'Dikemaskini',
    ],

    // Chart titles
    'charts' => [
        'ticket_trends' => 'Trend Tiket Helpdesk',
        'asset_utilization' => 'Penggunaan Aset mengikut Kategori',
        'sla_compliance' => 'Pematuhan SLA',
        'priority_distribution' => 'Taburan Keutamaan',
        'resolution_time' => 'Trend Masa Penyelesaian',
    ],

    // Series names
    'series' => [
        'tickets_created' => 'Tiket Dicipta',
        'tickets_resolved' => 'Tiket Selesai',
        'loaned' => 'Dipinjam',
        'available' => 'Tersedia',
        'compliant' => 'Mematuhi SLA',
        'non_compliant' => 'Tidak Mematuhi SLA',
        'resolution_time' => 'Masa Penyelesaian (Jam)',
    ],

    // Export formats
    'export_formats' => [
        'png' => 'Imej PNG',
        'pdf' => 'Dokumen PDF',
        'svg' => 'Vektor SVG',
    ],

    // Actions
    'actions' => [
        'export_all' => 'Eksport Semua',
        'export_all_tooltip' => 'Eksport semua carta dan data',
        'export_chart' => 'Eksport Carta',
        'export_chart_tooltip' => 'Eksport carta ini sahaja',
        'download_png' => 'Muat turun PNG',
        'refresh' => 'Muat Semula',
        'export' => 'Eksport',
        'cancel' => 'Batal',
    ],

    // Modal
    'modal' => [
        'export_title' => 'Eksport Carta',
        'select_format' => 'Pilih format eksport',
    ],

    // States
    'states' => [
        'loading' => 'Memuatkan carta...',
        'empty' => 'Tiada data dalam tempoh ini',
        'empty_hint' => 'Cuba laraskan julat tarikh untuk melihat data',
        'error' => 'Ralat memuatkan carta',
        'retry' => 'Muat Semula',
    ],

    // Messages
    'messages' => [
        'export_success' => 'Carta berjaya dieksport',
        'export_failed' => 'Gagal mengeksport carta',
        'refresh_success' => 'Data dikemaskini',
    ],

    // Tooltips
    'tooltips' => [
        'click_to_drilldown' => 'Klik untuk butiran lanjut',
        'hover_for_details' => 'Tuding untuk maklumat lanjut',
    ],
];
