<?php

declare(strict_types=1);

/**
 * Bahasa Melayu Helpdesk Module Translations
 *
 * Penyerahan tiket meja bantuan, pengurusan, dan pemberitahuan
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.2
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 */

return [
    // Tajuk Halaman
    'submit_ticket' => 'Hantar Tiket Meja Bantuan',
    'ticket_details' => 'Butiran Tiket',
    'my_tickets' => 'Tiket Saya',
    'ticket_list' => 'Senarai Tiket',

    // Label Borang
    'full_name' => 'Nama Penuh',
    'email_address' => 'Alamat E-mel',
    'phone_number' => 'Nombor Telefon',
    'staff_id' => 'ID Kakitangan (Pilihan)',
    'division' => 'Bahagian',
    'issue_category' => 'Kategori Isu',
    'subject' => 'Subjek',
    'problem_description' => 'Keterangan Masalah',
    'attachments_optional' => 'Lampiran (Pilihan)',

    // Teks Bantuan
    'name_help' => 'Masukkan nama penuh anda seperti dalam rekod MOTAC',
    'email_help' => 'Kami akan menghantar kemas kini ke alamat e-mel ini',
    'phone_help' => 'Nombor hubungan untuk perkara mendesak',
    'staff_id_help' => 'ID kakitangan MOTAC anda jika berkenaan',
    'category_help' => 'Pilih kategori yang paling sesuai dengan isu anda',
    'description_help' => 'Berikan maklumat terperinci tentang masalah (minimum 10 aksara)',
    'attachments_help' => 'Muat naik tangkapan skrin atau dokumen (maksimum 10MB setiap fail)',

    // Mesej
    'ticket_submitted' => 'Tiket anda telah berjaya dihantar',
    'ticket_number' => 'Nombor Tiket',
    'confirmation_email' => 'Anda akan menerima e-mel pengesahan tidak lama lagi',
    'no_login_required' => 'Tiada log masuk diperlukan',
    'quick_submission' => 'Penyerahan pantas untuk semua kakitangan MOTAC',

    // Status Tiket
    'ticket_status' => 'Status Tiket',
    'status_open' => 'Terbuka',
    'status_assigned' => 'Ditugaskan',
    'status_in_progress' => 'Dalam Proses',
    'status_pending_user' => 'Menunggu Respons Pengguna',
    'status_resolved' => 'Diselesaikan',
    'status_closed' => 'Ditutup',

    // Keutamaan
    'priority_low' => 'Rendah',
    'priority_medium' => 'Sederhana',
    'priority_high' => 'Tinggi',
    'priority_critical' => 'Kritikal',

    // Kategori
    'category_hardware' => 'Isu Perkakasan',
    'category_software' => 'Isu Perisian',
    'category_network' => 'Isu Rangkaian',
    'category_email' => 'Isu E-mel',
    'category_access' => 'Isu Akses/Kebenaran',
    'category_other' => 'Lain-lain',

    // Tindakan
    'submit_ticket_button' => 'Hantar Tiket',
    'clear_form' => 'Kosongkan Borang',
    'view_ticket' => 'Lihat Tiket',
    'add_comment' => 'Tambah Komen',
    'upload_attachment' => 'Muat Naik Lampiran',

    // Pengesahan
    'name_required' => 'Nama penuh diperlukan',
    'email_required' => 'Alamat e-mel diperlukan',
    'email_invalid' => 'Sila masukkan alamat e-mel yang sah',
    'phone_required' => 'Nombor telefon diperlukan',
    'category_required' => 'Sila pilih kategori isu',
    'subject_required' => 'Subjek diperlukan',
    'description_required' => 'Keterangan masalah diperlukan',
    'description_min' => 'Keterangan mestilah sekurang-kurangnya 10 aksara',
    'description_max' => 'Keterangan tidak boleh melebihi 5000 aksara',
    'file_too_large' => 'Saiz fail tidak boleh melebihi 10MB',
    'invalid_file_type' => 'Jenis fail tidak sah. Dibenarkan: imej, PDF, DOC, DOCX',

    // Pemberitahuan E-mel
    'email' => [
        'new_ticket_subject' => 'Tiket Meja Bantuan Baru Dihantar - #:ticket_number',
        'maintenance_ticket_subject' => 'Permintaan Penyelenggaraan Dibuat - #:ticket_number',
        'status_update_subject' => 'Tiket Meja Bantuan Dikemas Kini - #:ticket_number',
        'ticket_claimed_subject' => 'Tiket Dituntut Dengan Berjaya - #:ticket_number',
        'guest_confirmation_subject' => 'Pengesahan Tiket - #:ticket_number',
        'authenticated_confirmation_subject' => 'Tiket Diterima - #:ticket_number',
        'greeting' => 'Halo :name,',
        'new_ticket_created' => 'Tiket meja bantuan baru telah dibuat (penyerahan :submission_type) dengan butiran berikut:',
        'maintenance_ticket_created' => 'Permintaan penyelenggaraan telah dibuat dengan butiran berikut:',
        'maintenance_ticket_description' => 'Aset memerlukan penyelenggaraan:',
        'maintenance_priority_notice' => 'Ini adalah permintaan penyelenggaraan dan mungkin diprioritaskan dengan sewajarnya.',
        'status_updated' => 'Status tiket anda telah dikemas kini. Sila lihat butiran di bawah:',
        'ticket_details' => 'Butiran Tiket',
        'asset_details' => 'Maklumat Aset',
        'loan_details' => 'Maklumat Pinjaman',
        'update_comment' => 'Komentar Kemas Kini',
        'view_ticket' => 'Lihat Tiket',
        'view_ticket_portal' => 'Lihat di Portal',
        'guest_status_info' => 'Sila gunakan nombor tiket anda sebagai rujukan apabila membuat pertanyaan tentang tiket ini.',
        'guest_ticket_received' => 'Kami telah menerima permintaan sokongan anda. Nombor tiket anda ialah **#:ticket_number**.',
        'guest_next_steps' => 'Apa Seterusnya',
        'guest_step_email_updates' => 'Anda akan menerima kemas kini e-mel semasa tiket anda diproses',
        'guest_step_reference_number' => 'Simpan nombor tiket anda sebagai rujukan - anda memerlukannya untuk menuntut akaun anda',
        'guest_step_response_time' => 'Kami bertujuan untuk membalas dalam rangka masa SLA',
        'guest_can_claim' => 'Anda boleh menuntut tiket anda dengan membuat akaun:',
        'claim_ticket' => 'Tuntut Tiket Anda',
        'authenticated_ticket_received' => 'Kami telah menerima permintaan sokongan anda. Nombor tiket anda ialah **#:ticket_number**.',
        'authenticated_features' => 'Sebagai pengguna berdaftar, anda sekarang mempunyai akses ke:',
        'feature_real_time_tracking' => 'Penjejakan tiket masa nyata dan kemas kini status',
        'feature_internal_comments' => 'Ulasan dalaman yang hanya boleh dilihat oleh anda dan kakitangan sokongan',
        'feature_submission_history' => 'Sejarah penyerahan penuh dan penjejakan semakan',
        'feature_instant_notifications' => 'Pemberitahuan serta-merta untuk semua kemas kini tiket',
        'sla_notice' => 'Kami komited untuk membalas dalam Perjanjian Tahap Perkhidmatan (SLA) kami.',
        'ticket_claimed_success' => 'Terima kasih! Anda telah berjaya menuntut tiket **#:ticket_number**.',
        'ticket_claimed_benefits' => 'Sekarang anda berdaftar, anda mempunyai akses ke ciri-ciri yang dipertingkatkan:',
        'benefit_tracking' => 'Jejak kemajuan tiket secara masa nyata',
        'benefit_history' => 'Lihat sejarah tiket lengkap dan semakan',
        'benefit_comments' => 'Tambah ulasan dalaman untuk penyelarasan',
        'benefit_notifications' => 'Terima pemberitahuan serta-merta untuk semua kemas kini',
        'thank_you' => 'Terima kasih kerana menggunakan sistem sokongan kami.',
        'assigned_to' => 'Ditugaskan kepada',
    ],
];
