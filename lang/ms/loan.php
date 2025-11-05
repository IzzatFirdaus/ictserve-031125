<?php

/**
 * Translation file: Loan Module (Bahasa Melayu)
 * Description: Malay language translations for loan application module
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D15 (Language Support)
 *
 * @version 2.0.0
 *
 * @created 2025-11-04
 */

return [
    'form' => [
        'title' => 'Borang Permohonan Pinjaman Peralatan ICT',
        'subtitle' => 'Untuk Kegunaan Rasmi Kementerian Pelancongan, Seni dan Budaya',
        'section_label' => 'BORANG',
        'of_4_pages' => 'daripada 4 muka surat',
        'required_fields_note' => 'Tanda * adalah WAJIB diisi.',

        // Step labels
        'step_1_label' => 'Maklumat Pemohon',
        'step_2_label' => 'Pegawai Bertanggungjawab',
        'step_3_label' => 'Senarai Peralatan',
        'step_4_label' => 'Pengesahan',

        // Section headers
        'section_1_applicant' => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
        'section_2_responsible_officer' => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
        'section_3_equipment_list' => 'BAHAGIAN 3 | MAKLUMAT PERALATAN',
        'section_4_applicant_confirmation' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)',
        'section_5_approval' => 'BAHAGIAN 5 | PENGESAHAN BAHAGIAN / UNIT / SEKSYEN',

        // Notes and descriptions
        'select_equipment_note' => 'Sila pilih peralatan yang diperlukan dan nyatakan kuantiti.',
        'confirmation_statement' => 'Saya dengan ini mengesahkan dan memperakui bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.',
        'approval_note' => 'Permohonan yang lengkap diisi oleh pemohon hendaklah DISOKONG OLEH PEGAWAI SEKURANG-KURANGNYA GRED 41 DAN KE ATAS.',
        'approval_process_title' => 'Proses Kelulusan',
        'approval_process_description' => 'Permohonan anda akan dihantar kepada pegawai yang berkenaan untuk kelulusan. Anda akan menerima notifikasi melalui e-mel apabila permohonan anda telah diproses.',
        'review_summary' => 'Ringkasan Permohonan',
        'your_information' => 'Maklumat Anda',
    ],

    'fields' => [
        'applicant_name' => 'Nama Penuh',
        'position_grade' => 'Jawatan & Gred',
        'phone' => 'No. Telefon',
        'division_unit' => 'Bahagian/Unit',
        'purpose' => 'Tujuan Permohonan',
        'location' => 'Lokasi',
        'loan_start_date' => 'Tarikh Pinjaman',
        'loan_end_date' => 'Tarikh Dijangka Pulang',
        'is_responsible_officer' => 'Sila tandakan ✓ jika Pemohon adalah Pegawai Bertanggungjawab. Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.',
        'responsible_officer_name' => 'Nama Penuh',
        'date' => 'Tarikh',
        'signature' => 'Tandatangan & Cop',
        'approval_status' => 'Status Kelulusan',
        'submission_date' => 'Tarikh Permohonan',
        'accept_terms' => 'Saya bersetuju dengan terma dan syarat yang ditetapkan',
        'loan_period' => 'Tempoh Pinjaman',
        'total_equipment' => 'Jumlah Peralatan',
    ],

    'placeholders' => [
        'applicant_name' => 'Masukkan nama penuh anda',
        'position' => 'Contoh: Pegawai Tadbir N41',
        'phone' => 'Contoh: 03-12345678',
        'select_division' => 'Pilih bahagian/unit',
        'purpose' => 'Nyatakan tujuan pinjaman peralatan',
        'location' => 'Nyatakan lokasi penggunaan',
        'responsible_officer_name' => 'Masukkan nama pegawai bertanggungjawab',
        'select_equipment' => 'Pilih jenis peralatan',
        'quantity' => '1',
        'notes' => 'Catatan tambahan (jika ada)',
        'signature' => 'Nama penuh',
    ],

    'table' => [
        'no' => 'Bil.',
        'equipment_type' => 'Jenis Peralatan',
        'quantity' => 'Kuantiti',
        'notes' => 'Catatan',
    ],

    'actions' => [
        'previous' => 'Kembali',
        'next' => 'Seterusnya',
        'submit_application' => 'Hantar Permohonan',
        'add_equipment' => 'Tambah Peralatan',
        'remove_equipment' => 'Buang Peralatan',
    ],

    'status' => [
        'pending_approval' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'in_progress' => 'Dalam Proses',
    ],

    'help' => [
        'need_assistance' => 'Perlukan Bantuan?',
        'contact_info' => 'Sebarang pertanyaan sila hubungi:',
        'email' => 'bpm@motac.gov.my',
        'phone' => '03-2161 2345',
        'if_applicable' => 'jika ada',
        'is_responsible_officer' => 'Tandakan jika anda adalah pegawai yang bertanggungjawab untuk peralatan ini',
    ],

    'units' => [
        'items' => 'item',
    ],

    'messages' => [
        'application_submitted' => 'Permohonan anda telah berjaya dihantar. Nombor permohonan: :application_number',
        'submission_failed' => 'Permohonan gagal dihantar. Sila cuba lagi.',
        'not_provided' => 'Tidak dinyatakan',
        'info_from_profile' => 'Maklumat ini diambil dari profil pengguna anda.',
    ],
];
