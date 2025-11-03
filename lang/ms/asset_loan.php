<?php

declare(strict_types=1);

/**
 * Bahasa Melayu Asset Loan Module Translations
 *
 * Permohonan pinjaman aset, kelulusan, dan pengurusan
 *
 * @requirements 1.4, 1.5, 12.1-12.5, 15.2
 * @wcag-level AA
 * @version 1.0.0
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
];
