<?php

declare(strict_types=1);

/**
 * Bahasa Melayu Asset Loan Module Translations
 *
 * Rentetan untuk permohonan pinjaman aset, kelulusan dan pengurusan.
 */

return [
    // Tajuk Halaman
    'submit_application' => 'Hantar Permohonan Pinjaman Aset',
    'application_details' => 'Butiran Permohonan',
    'my_applications' => 'Permohonan Saya',
    'application_list' => 'Senarai Permohonan',
    'asset_catalog' => 'Katalog Aset',

    // Label Borang
    'applicant_name' => 'Nama Pemohon',
    'applicant_email' => 'Alamat E-mel',
    'applicant_phone' => 'Nombor Telefon',
    'staff_id' => 'ID Kakitangan (Pilihan)',
    'grade' => 'Gred',
    'division' => 'Bahagian',
    'asset_selection' => 'Pemilihan Aset',
    'loan_purpose' => 'Tujuan Pinjaman',
    'start_date' => 'Tarikh Mula',
    'end_date' => 'Tarikh Tamat',
    'loan_period' => 'Tempoh Pinjaman',

    // Teks Bantuan
    'name_help' => 'Masukkan nama penuh anda seperti dalam rekod MOTAC',
    'email_help' => 'Pemberitahuan kelulusan akan dihantar ke e-mel ini',
    'phone_help' => 'Nombor hubungan untuk penyelarasan',
    'staff_id_help' => 'ID kakitangan MOTAC anda jika berkenaan',
    'grade_help' => 'Tahap gred semasa anda',
    'division_help' => 'Bahagian anda dalam MOTAC',
    'asset_help' => 'Pilih aset yang ingin dipinjam',
    'purpose_help' => 'Jelaskan tujuan meminjam aset ini (minimum 10 aksara)',
    'start_date_help' => 'Bila anda memerlukan aset?',
    'end_date_help' => 'Bila anda akan memulangkan aset?',

    // Mesej
    'application_submitted' => 'Permohonan pinjaman anda telah berjaya dihantar',
    'application_number' => 'Nombor Permohonan',
    'approval_pending' => 'Permohonan anda menunggu kelulusan',
    'approval_email_sent' => 'Permintaan kelulusan telah dihantar kepada pegawai pelulus',
    'updates_via_email' => 'Anda akan menerima kemas kini melalui e-mel',

    // Status Permohonan
    'application_status' => 'Status Permohonan',
    'status_pending_approval' => 'Menunggu Kelulusan',
    'status_approved' => 'Diluluskan',
    'status_rejected' => 'Ditolak',
    'status_active' => 'Pinjaman Aktif',
    'status_returned' => 'Dipulangkan',
    'status_overdue' => 'Lewat Tempoh',

    // Maklumat Aset
    'asset_name' => 'Nama Aset',
    'asset_category' => 'Kategori',
    'asset_model' => 'Model',
    'asset_serial' => 'Nombor Siri',
    'asset_condition' => 'Keadaan',
    'condition_before' => 'Keadaan Sebelum Pinjaman',
    'condition_after' => 'Keadaan Selepas Pinjaman',
    'asset_availability' => 'Ketersediaan',
    'available' => 'Tersedia',
    'unavailable' => 'Tidak Tersedia',
    'booked' => 'Ditempah',

    // Kelulusan
    'approver' => 'Pegawai Pelulus',
    'approval_method' => 'Kaedah Kelulusan',
    'approval_email' => 'Kelulusan Berasaskan E-mel',
    'approval_portal' => 'Kelulusan Berasaskan Portal',
    'approval_remarks' => 'Catatan Kelulusan',
    'approved_by' => 'Diluluskan Oleh',
    'approved_at' => 'Diluluskan Pada',
    'rejected_by' => 'Ditolak Oleh',
    'rejected_at' => 'Ditolak Pada',

    // Tindakan
    'submit_application_button' => 'Hantar Permohonan',
    'clear_form' => 'Kosongkan Borang',
    'view_application' => 'Lihat Permohonan',
    'check_availability' => 'Semak Ketersediaan',
    'browse_assets' => 'Layari Aset',

    // Pengesahan
    'name_required' => 'Nama pemohon diperlukan',
    'email_required' => 'Alamat e-mel diperlukan',
    'email_invalid' => 'Sila masukkan alamat e-mel yang sah',
    'phone_required' => 'Nombor telefon diperlukan',
    'grade_required' => 'Gred diperlukan',
    'division_required' => 'Bahagian diperlukan',
    'asset_required' => 'Sila pilih aset',
    'purpose_required' => 'Tujuan pinjaman diperlukan',
    'purpose_min' => 'Tujuan mestilah sekurang-kurangnya 10 aksara',
    'purpose_max' => 'Tujuan tidak boleh melebihi 1000 aksara',
    'start_date_required' => 'Tarikh mula diperlukan',
    'start_date_future' => 'Tarikh mula mestilah pada masa hadapan',
    'end_date_required' => 'Tarikh tamat diperlukan',
    'end_date_after_start' => 'Tarikh tamat mestilah selepas tarikh mula',
    'asset_unavailable' => 'Aset yang dipilih tidak tersedia untuk tempoh yang diminta',

    // Butiran Aset (untuk e-mel & paparan)
    'asset' => [
        'name' => 'Nama Aset',
        'asset_tag' => 'Tag Aset',
        'condition' => 'Keadaan Aset',
    ],

    // Butiran Pinjaman (untuk e-mel & paparan)
    'loan' => [
        'application_number' => 'Nombor Permohonan',
        'returned_by' => 'Dikembalikan Oleh',
    ],

    'categories' => [
        'computer' => 'Komputer',
        'laptop' => 'Komputer riba',
        'projector' => 'Projektor',
        'camera' => 'Kamera',
        'printer' => 'Pencetak',
        'other' => 'Lain-lain',
    ],

    // Label Medan
    'fields' => [
        'application_number' => 'Nombor Permohonan',
        'applicant_name' => 'Nama Pemohon',
        'staff_id' => 'ID Kakitangan',
        'grade' => 'Gred',
        'loan_period' => 'Tempoh Pinjaman',
        'purpose' => 'Tujuan',
        'requested_items' => 'Item Dipohon',
        'total_value' => 'Jumlah Nilai',
    ],

    // Aliran Kerja Kelulusan E-mel
    'approval' => [
        'approve_title' => 'Luluskan Permohonan Pinjaman',
        'decline_title' => 'Tolak Permohonan Pinjaman',
        'form_description' => 'Sila semak butiran permohonan di bawah dan berikan keputusan anda.',
        'application_details' => 'Butiran Permohonan',
        'confirm_approval' => 'Sahkan Kelulusan',
        'confirm_decline' => 'Sahkan Penolakan',
        'comments_label' => 'Komen (Pilihan)',
        'reason_label' => 'Sebab Penolakan',
        'comments_placeholder' => 'Tambah sebarang komen atau syarat untuk kelulusan ini...',
        'reason_placeholder' => 'Sila berikan sebab untuk menolak permohonan ini...',
        'comments_help' => 'Pilihan: Tambah sebarang komen atau syarat untuk kelulusan ini.',
        'reason_help' => 'Wajib: Jelaskan mengapa permohonan ini ditolak.',
        'confirm_approve_button' => 'Sahkan Kelulusan',
        'confirm_decline_button' => 'Sahkan Penolakan',
        'security_notice_title' => 'Notis Keselamatan',
        'security_notice_text' => 'Pautan kelulusan ini sah selama 7 hari dan hanya boleh digunakan sekali. Keputusan anda akan direkodkan dalam jejak audit sistem.',
        'help_text' => 'Jika anda mempunyai sebarang pertanyaan mengenai permohonan ini, sila hubungi pasukan Sokongan ICT.',
        'token_invalid' => 'Pautan kelulusan tidak sah. Pautan mungkin telah digunakan atau tidak betul.',
        'token_expired' => 'Pautan kelulusan ini telah tamat tempoh. Sila hubungi pemohon untuk permintaan kelulusan baharu.',
        'approved_success' => 'Permohonan :application_number telah diluluskan dengan jayanya. Pemohon akan dimaklumkan melalui e-mel.',
        'declined_success' => 'Permohonan :application_number telah ditolak. Pemohon akan dimaklumkan melalui e-mel.',
        'approval_failed' => 'Gagal memproses kelulusan. Sila cuba lagi atau hubungi sokongan.',
        'decline_failed' => 'Gagal memproses penolakan. Sila cuba lagi atau hubungi sokongan.',
    ],

    // Templat E-mel
    'email' => [
        'application_submitted_subject' => 'Permohonan Pinjaman Dihantar - :application_number',
        'approval_request_subject' => 'Kelulusan Permohonan Pinjaman Diperlukan - :application_number',
        'approval_confirmed_subject' => 'Kelulusan Direkodkan - :application_number',
        'decline_confirmed_subject' => 'Keputusan (Ditolak) Direkodkan - :application_number',
        'application_approved_subject' => 'Permohonan Pinjaman Diluluskan - :application_number',
        'application_declined_subject' => 'Permohonan Pinjaman Ditolak - :application_number',
        'status_update_subject' => 'Kemas Kini Permohonan Pinjaman - :application_number',
        'due_today_subject' => 'Pemulangan Aset Hari Ini - :application_number',
        'return_reminder_subject' => 'Peringatan Pemulangan Aset - :application_number',
        'overdue_notification_subject' => 'Notis Lewat Pemulangan Aset - :application_number',
        'asset_preparation_subject' => 'Sediakan Aset untuk Pinjaman - :application_number',
        'application_decision_subject' => 'Keputusan Permohonan Pinjaman - :application_number',
    ],
];
