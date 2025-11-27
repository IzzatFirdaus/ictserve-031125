<?php

declare(strict_types=1);

/**
 * Profile Page Translations (Bahasa Melayu)
 *
 * Implements Task 4.1.2: Profile management interface translations
 *
 * @author Pasukan BPM MOTAC
 * @trace R10 (Authenticated Portal), R13 (Bilingual Support)
 * @version 1.0.0
 * @task 4.1.2
 */

return [
	// Page Header
	'title' => 'Profil Saya',
	'subtitle' => 'Urus tetapan akaun dan keutamaan anda',
	'completeness' => 'Kelengkapan Profil',

	// System Information Section
	'system_information' => 'Maklumat Sistem',
	'system_information_desc' => 'Medan ini diuruskan oleh pentadbir sistem dan tidak boleh diedit secara langsung.',
	'read_only' => 'Baca Sahaja',
	'request_correction' => 'Minta Pembetulan',

	// Fields
	'email' => 'Alamat E-mel',
	'staff_id' => 'No. Kakitangan',
	'grade' => 'Gred',
	'department' => 'Bahagian/Jabatan',
	'name' => 'Nama Penuh',
	'phone' => 'Telefon Pejabat',
	'mobile' => 'Telefon Bimbit',
	'bio' => 'Bio',
	'bio_placeholder' => 'Ceritakan sedikit tentang diri anda...',
	'characters' => 'aksara',

	// Personal Information Section
	'personal_information' => 'Maklumat Peribadi',
	'personal_information_desc' => 'Kemaskini maklumat hubungan peribadi anda.',

	// Password Section
	'change_password' => 'Tukar Kata Laluan',
	'change_password_desc' => 'Pastikan akaun anda menggunakan kata laluan yang kukuh untuk keselamatan.',
	'current_password' => 'Kata Laluan Semasa',
	'new_password' => 'Kata Laluan Baharu',
	'confirm_password' => 'Sahkan Kata Laluan Baharu',
	'update_password' => 'Kemaskini Kata Laluan',

	// Language Section
	'language_preference' => 'Keutamaan Bahasa',
	'language_preference_desc' => 'Pilih bahasa pilihan anda untuk antara muka portal.',
	'save_language' => 'Simpan Bahasa',

	// Notification Preferences Section
	'notification_preferences' => 'Keutamaan Pemberitahuan',
	'notification_preferences_desc' => 'Pilih pemberitahuan yang anda ingin terima.',
	'notif_ticket_updates' => 'Kemaskini Status Tiket',
	'notif_ticket_assignments' => 'Tugasan Tiket',
	'notif_ticket_comments' => 'Komen Baharu pada Tiket',
	'notif_sla_alerts' => 'Amaran Pelanggaran SLA',
	'notif_loan_updates' => 'Kemaskini Permohonan Pinjaman',
	'notif_loan_approvals' => 'Permintaan Kelulusan Pinjaman',
	'notif_loan_reminders' => 'Peringatan Pemulangan Pinjaman',
	'notif_system_announcements' => 'Pengumuman Sistem',
	'save_preferences' => 'Simpan Keutamaan',

	// Actions
	'save_changes' => 'Simpan Perubahan',
	'saving' => 'Menyimpan...',
	'updating' => 'Mengemaskini...',

	// Success Messages
	'updated_successfully' => 'Profil berjaya dikemaskini.',
	'password_updated' => 'Kata laluan berjaya dikemaskini.',
	'preferences_updated' => 'Keutamaan pemberitahuan berjaya dikemaskini.',
	'language_updated' => 'Keutamaan bahasa berjaya dikemaskini.',

	// Correction Request
	'correction_request_title' => 'Permintaan Pembetulan Data Profil - :field',
	'correction_request_desc' => 'Saya ingin memohon pembetulan untuk :field saya. Nilai semasa: :current_value',
	'correction_ticket_created' => 'Permintaan pembetulan telah dihantar. ID Tiket: :ticket_id',
];
