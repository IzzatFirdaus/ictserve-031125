# Pemetaan Borang Pinjaman Aset ICT → Model (Loan Form → Model Mapping)

Dokumen ini memetakan medan borang permohonan pinjaman aset ICT kepada atribut model, struktur pangkalan data, peraturan validasi, dan lokasi kod. Disediakan dalam Bahasa Melayu dengan istilah teknikal Inggeris bagi kejelasan.

Nota Penting: Sistem ini adalah untuk kegunaan dalaman MOTAC sahaja (internal use only). Tidak untuk kegunaan orang awam.

## Maklumat Dokumen (Document Information)

- Versi: 2.1.1 (SemVer)
- Tarikh Kemaskini: 31 Oktober 2025
- Status: Aktif — Diseragamkan mengikut D00–D15
- Klasifikasi: Terhad — Dalaman MOTAC
- Bahasa: Bahasa Melayu (utama), English (teknikal)
- Rujukan D-Docs: D03 (SRS), D04 (Design), D05–D06 (Data Migration), D09 (Database), D10 (Source Code Docs), D11 (Technical Design)

Teknologi: Laravel 12, PHP 8.2, Livewire v3, Filament v4, Tailwind CSS, Vite. Autentikasi: Laravel Breeze/Jetstream.

## Ringkasan Lokasi (Code/Views Overview)

- Komponen UI (Livewire/Volt): `app/Livewire/Loan/LoanApplicationForm.php`, `resources/views/livewire/loan/loan-application-form.blade.php`
- Model: `app/Models/LoanApplication.php` (contoh jadual: `loans`)
- Perkhidmatan: `app/Services/LoanManagement/LoanApplicationService.php`
- Notifikasi E-mel: `app/Notifications/Loans/*`, `resources/views/emails/loans/*`

## Pemetaan Medan (Field Mappings)

| Medan Borang (Malay/English) | Sifat Livewire | Lajur DB / Atribut Model | Peraturan Validasi (Validation) | Nota & Rujukan |
| --- | --- | --- | --- | --- |
| Pemohon (Applicant) | `$applicant_id` (Auth) atau `$applicant_name` | `loans.applicant_id` atau `loans.applicant_name` | `required_without:applicant_id|string|max:255` | Gunakan pengguna yang log masuk sebagai keutamaan.
| Jawatan & Gred (Position & Grade) | `$position_grade` | `loans.position_grade` | `nullable|string|max:50` | Disyorkan simpan di profil pengguna.
| Bahagian/Unit (Division) | `$division_id` | `loans.division_id` (FK) | `required|exists:divisions,id` | Digunakan bagi rantaian kelulusan.
| Tujuan Permohonan (Purpose) | `$purpose` | `loans.purpose` (text) | `required|string|min:50|max:2000` | Ikut polisi organisasi.
| No. Telefon (Phone) | `$phone` | `loans.phone` | `nullable|string|max:50` | Normalisasi format di service layer.
| Lokasi (Location) | `$location` | `loans.location` | `nullable|string|max:255` | Opsyenal.
| Tarikh Mula Pinjaman (Start) | `$loan_start_date` | `loans.loan_start_date` (date) | `required|date|after_or_equal:today` | Untuk semakan ketersediaan.
| Tarikh Tamat Pinjaman (End) | `$loan_end_date` | `loans.loan_end_date` (date) | `required|date|after:loan_start_date` | Untuk kira tempoh & kelulusan.
| Pegawai Bertanggungjawab (Responsible Officer) | `$responsible_officer_id` / `$responsible_officer_name` | `loans.responsible_officer_id` / `loans.responsible_officer_name` | `nullable|exists:users,id` atau `string|max:255` | Wajib jika polisi memerlukan pengawasan.
| Senarai Peralatan (Equipment List) | `$equipment_list` (array) | `loans.equipment_list` (JSON cast) | `array|min:1` + kawal skema item | Skema item: `{type, brand, model, serial, accessories: []}`.
| Aksesori (Accessories checklist) | sebahagian `equipment_list` | lihat atas | `array` (keys yang dibenarkan) | Contoh: `adapter`, `bag`, `mouse`, `cables`.
| Pengeluaran (Issuance) | `$issued_by_user_id`, `$issued_at`, `$issued_notes` | `loans.issued_by_user_id`, `loans.issued_at`, `loans.issued_notes` | `nullable` | Ditetapkan semasa pengeluaran aset.
| Pemulangan (Return) | `$returned_by_user_id`, `$returned_at`, `$return_notes` | `loans.returned_by_user_id`, `loans.returned_at`, `loans.return_notes` | `nullable` | Ditetapkan semasa pemulangan aset.
| Terma & Syarat (Terms Accepted) | `$terms_accepted` (boolean) | `loans.terms_accepted` | `accepted` | Papar salinan terma di UI dan e‑mel.

## Aliran Bisnes (Business & Workflow)

- Rantaian Kelulusan: bergantung kepada `division_id`, gred pemohon, nilai aset tinggi (high value), dan tempoh pinjaman (long duration).
- Nombor Permohonan: format diseragamkan, sebagai contoh `LOAN-YYYYMM-####` (rujuk service).
- Konflik Ketersediaan Aset: semak pertindihan julat tarikh; gunakan `lockForUpdate()` semasa pengeluaran (issuance) untuk elak konflik.
- Notifikasi: `LoanApplicationSubmitted`, `LoanApplicationApproved`, `LoanReturnReminder` (queue + jadual peringatan).

## Validasi, Polisi & Akses

- Polisi Akses (Policies): `LoanApplicationPolicy` bagi `create`, `approve`, `issue`, `return`.
- Sanitasi Input: panjang maks, skema JSON peralatan, dan normalisasi telefon.
- Aksesibiliti (WCAG 2.2 AA): label ARIA, navigasi papan kekunci, kontras.
- Audit Trail: jejak aktiviti kritikal dengan user, masa, IP, user-agent, nilai lama/baharu.

## Rujukan RTM (Traceability)

- RTM: `docs/rtm/loan_requirements_rtm.csv`
- Pemetaan contoh: `SRS-LOAN-001` → `LoanApplicationController@store` → `LoanApplicationTest::testCreate`

## Sejarah Revisi (Changelog)

- 2.1.1 (31-10-2025): Terjemah penuh ke BM, tambah istilah Inggeris, kemaskini Laravel 12 + Livewire v3 + Filament v4, pembetulan encoding.
- 1.0.0 (21-10-2025): Draf awal, pemetaan asas borang → model.

