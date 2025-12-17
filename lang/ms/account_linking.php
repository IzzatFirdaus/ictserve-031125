<?php

// ICTServe v3.6.1 - Bahasa Melayu Sahaja
// Rujukan: D15_LANGUAGE_MS_EN.md


declare(strict_types=1);

/**
 * Account Linking Translations (Bahasa Melayu)
 *
 * Translation strings for the Account Linking feature in ICTServe v3.5.0.
 * This feature allows staff to link their historical guest submissions
 * to their newly registered account.
 *
 * @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
 * @see D02 FR-050 Optional account linking
 * @see D15_LANGUAGE_MS_EN.md Bilingual localization
 */

return [
    // Page Header
    'title' => 'Pautkan Penghantaran Terdahulu',
    'description' => 'Pautkan penghantaran tetamu anda sebelum ini (tiket helpdesk dan permohonan pinjaman) ke akaun anda untuk melihatnya dalam sejarah penghantaran anda.',

    // Statistics
    'statistics_title' => 'Statistik Penghantaran Anda',
    'linked_tickets' => 'Tiket Dipautkan',
    'linked_loans' => 'Pinjaman Dipautkan',
    'unlinked_tickets' => 'Tiket Belum Dipautkan',
    'unlinked_loans' => 'Pinjaman Belum Dipautkan',

    // How It Works
    'how_it_works_title' => 'Cara Pautan Akaun Berfungsi',
    'how_it_works_description' => 'Jika anda menghantar tiket helpdesk atau permohonan pinjaman sebagai tetamu sebelum mendaftar, anda boleh memautkan penghantaran tersebut ke akaun anda. Masukkan alamat e-mel yang anda gunakan untuk penghantaran tersebut, pilih yang ingin dipautkan, dan klik butang Pautkan.',

    // Search Form
    'search_title' => 'Cari Penghantaran Belum Dipautkan',
    'email_label' => 'Alamat E-mel',
    'email_placeholder' => 'Masukkan e-mel yang digunakan untuk penghantaran tetamu',
    'email_help' => 'Masukkan alamat e-mel yang anda gunakan semasa menghantar tiket atau permohonan pinjaman sebagai tetamu.',
    'search_button' => 'Cari',

    // Results
    'found_submissions' => ':count penghantaran dijumpai',
    'select_all' => 'Pilih Semua',
    'deselect_all' => 'Nyahpilih Semua',
    'select_submission' => 'Pilih penghantaran :reference',
    'type_ticket' => 'Tiket',
    'type_loan' => 'Pinjaman',
    'submitted_on' => 'Dihantar pada',

    // Selection
    'selected_count' => '{0} Tiada penghantaran dipilih|{1} :count penghantaran dipilih|[2,*] :count penghantaran dipilih',

    // Actions
    'link_button' => 'Pautkan Penghantaran Terpilih',
    'linking' => 'Memautkan...',
    'back_to_dashboard' => 'Kembali ke Papan Pemuka',

    // Messages
    'no_submissions_found' => 'Tiada penghantaran belum dipautkan dijumpai untuk alamat e-mel ini.',
    'no_submissions_selected' => 'Sila pilih sekurang-kurangnya satu penghantaran untuk dipautkan.',
    'submissions_linked_success' => '{1} :count penghantaran telah dipautkan ke akaun anda.|[2,*] :count penghantaran telah dipautkan ke akaun anda.',
    'linking_failed' => 'Gagal memautkan penghantaran. Sila cuba lagi.',
    'linking_error' => 'Ralat berlaku semasa memautkan penghantaran. Sila cuba lagi kemudian.',

    // No Results
    'no_results_title' => 'Tiada Penghantaran Dijumpai',
    'no_results_description' => 'Kami tidak dapat mencari penghantaran belum dipautkan untuk alamat e-mel ini. Penghantaran mungkin telah dipautkan atau alamat e-mel mungkin berbeza.',
    'try_different_email' => 'Cuba alamat e-mel lain',
];
