<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md


declare(strict_types=1);

/**
 * Status Checker Translations (Bahasa Melayu)
 *
 * Terjemahan untuk komponen Livewire StatusChecker.
 * Menyokong semakan status berasaskan token untuk tiket dan pinjaman.
 *
 * @see App\Livewire\Status\StatusChecker
 *
 * @requirements 2.1, 2.2
 */

return [
    // Tajuk halaman
    'page_title' => 'Semak Status',
    'page_tagline' => 'Status Semasa',
    'title' => 'Semak Status Permohonan Anda',
    'subtitle' => 'Masukkan token status anda untuk melihat status terkini tiket helpdesk atau permohonan pinjaman anda.',

    // Label borang
    'form_label' => 'Borang semakan status',
    'form_helper' => 'Masukkan token untuk menyemak status permohonan anda.',
    'token_label' => 'Token Status',
    'token_placeholder' => 'Masukkan token status anda (cth., abc123def456...)',
    'token_help' => 'Token status telah dihantar ke emel anda semasa anda menghantar permohonan.',
    'type_label' => 'Jenis Permohonan (Pilihan)',
    'type_auto' => 'Kesan automatik',
    'type_ticket' => 'Tiket Helpdesk',
    'type_loan' => 'Permohonan Pinjaman',
    'type_help' => 'Biarkan sebagai kesan automatik jika anda tidak pasti.',

    // Butang
    'check_button' => 'Semak Status',
    'checking' => 'Menyemak...',
    'clear' => 'Kosongkan',

    // Mesej status
    'last_updated' => 'Dikemaskini pada:',
    'current_status' => 'Status Semasa',

    // Mesej ralat (format dwibahasa)
    'token_invalid' => 'Token tidak sah atau telah tamat tempoh. Sila semak dan cuba lagi.',
    'not_found_title' => 'Permohonan Tidak Dijumpai / Submission Not Found',
    'not_found_message' => 'Kami tidak dapat mencari permohonan yang sepadan dengan token anda. Sila sahkan perkara berikut:',
    'not_found_hint_1' => 'Pastikan token disalin dengan betul dari emel anda',
    'not_found_hint_2' => 'Semak bahawa anda menggunakan jenis permohonan yang betul',
    'not_found_hint_3' => 'Hubungi sokongan BPM jika masalah berterusan',

    // Bahagian keputusan
    'ticket_number' => 'Nombor Tiket',
    'loan_reference' => 'Permohonan Pinjaman :ref',
    'submitted_on' => 'Dihantar pada :date',
    'category' => 'Kategori',
    'priority' => 'Keutamaan',
    'division' => 'Bahagian',
    'applicant' => 'Pemohon',
    'loan_period' => 'Tempoh Pinjaman',
    'location' => 'Lokasi',
    'not_specified' => 'Tidak dinyatakan',

    // Garis masa
    'timeline_title' => 'Garis Masa Status',
    'no_timeline' => 'Tiada maklumat garis masa tersedia pada masa ini.',

    // Komen
    'comments_title' => 'Kemaskini & Komen',
    'system' => 'Sistem',

    // Item pinjaman
    'loan_items_title' => 'Item Dimohon',
    'unknown_item' => 'Item Tidak Diketahui',

    // Penerangan
    'description_title' => 'Butiran Permohonan',
    'resolution_notes' => 'Nota Penyelesaian',

    // Bahagian bantuan
    'help_text' => 'Perlukan bantuan? Tidak dapat mencari permohonan anda?',
    'contact_support' => 'Hubungi Sokongan BPM',

    // Bahagian bantuan pantas (Quick Help sidebar)
    'quick_help_title' => 'Bantuan Pantas',
    'quick_help_email' => 'Emel sokongan BPM',
    'quick_help_phone' => 'Talian bantuan helpdesk',
    'quick_help_ticket' => 'Hantar tiket baharu',
    'quick_help_ticket_cta' => 'Pergi ke borang helpdesk',
];
