# Spesifikasi Keperluan Perniagaan (Business Requirements Specification - BRS)

**Sistem ICTServe**  
**Versi:** 3.5.0 (SemVer)  
**Tarikh Kemaskini:** 30 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                       |
| -------------------- | ------------------------------------------------------------------------------------------- |
| **Versi**            | 3.5.0                                                                                       |
| **Tarikh Kemaskini** | 30 November 2025                                                                            |
| **Status**           | Aktif                                                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**         | ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                                   |

> Notis Penggunaan Dalaman: Sistem ini digunakan oleh staf dan pegawai gred MOTAC sahaja; tidak untuk kegunaan awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), multi-channel notifications. Pematuhan Jabatan Digital Negara. | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: FR-001 ubah 'Portal (login pilihan)', tambah FR-050 'Staff boleh akses sebagai tetamu atau pengguna berdaftar'. Penyelarasan dengan D00/D04 v3.4.0.                                                                                                  | Pasukan Pembangunan BPM |
| 3.3.0 | 29 November 2025 | Penjajaran penuh Guest-First: Hapus rujukan 'Portal Intranet (Login)' untuk staf, ganti dengan 'Guest Form'. Penyelarasan dengan D00/D04/D05 v3.3.0.                                                                                                                      | Pasukan Pembangunan BPM |
| 3.2.1 | 29 November 2025 | Penjajaran kepada seni bina "Guest-First": Staf/Pengguna Dalaman menggunakan borang tetamu (tanpa log masuk). Authentication terhad kepada admin/superuser sahaja. Penyelarasan dengan D00 v3.2.1 dan D04 v3.2.1.                                                         | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, PHP 8.2.12, Livewire 3.7.0, Filament 4.1.10, PHPUnit 11.5.44, Larastan 3.8.0, Laravel Pint 1.26.0). Penyelarasan dengan D00-D01 v3.2.0.                                                 | Pasukan Pembangunan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini kepada teknologi semasa: Laravel 12.40.1, Livewire 3.7.0, Filament 4.1.10, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, Laravel Reverb 1.6.2, Laravel Echo 2.2.6, PHPUnit 11.5.44. Pematuhan WCAG 2.2 AA dan MyGOV Digital Service Standards v2.1.0.          | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Penjajaran penuh kepada seni bina dalaman (internal-only): portal staf MOTAC dengan login, keperluan kelulusan berperingkat dalam sistem, dan pematuhan WCAG 2.2 AA.                                                                                                      | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                    | Pasukan BPM             |
| 1.0.0 | September 2025   | Versi awal dokumen keperluan perniagaan                                                                                                                                                                                                                                   | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]**
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]**
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]**
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]**
- **[D05_DATA_MIGRATION_PLAN.md]**
- **[D06_DATA_MIGRATION_SPECIFICATION.md]**
- **[D07_SYSTEM_INTEGRATION_PLAN.md]**
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]**
- **[D09_DATABASE_DOCUMENTATION.md]**
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]**
- **[D12_UI_UX_DESIGN_GUIDE.md]**
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]**
- **[D14_UI_UX_STYLE_GUIDE.md]**
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**
- **docs/frontend/accessibility-guidelines.md**
- **docs/frontend/core-web-vitals-testing-guide.md**
- **docs/performance-optimization-report.md**

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini mentakrifkan keperluan perniagaan bagi sistem ICTServe yang digunakan secara dalaman (internal-only) oleh warga kerja MOTAC. Ia menetapkan matlamat, skop, keperluan fungsional dan bukan fungsional, serta kriteria kejayaan yang memacu pembangunan modul Helpdesk & Asset Loan, dan menggariskan tanggungjawab peranan (staf, pegawai kelulusan, admin, super admin) melalui portal dan panel Filament 4.1.10. Sistem dibina menggunakan Laravel 12.40.1, Livewire 3.7.0, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, dan Laravel Reverb 1.6.2 untuk komunikasi masa nyata.

---

## 2. LATAR BELAKANG (Background)

Versi terdahulu (≤2.0.0) mengandaikan akaun staf MOTAC dengan peranan berlapis. Analisis semula aliran kerja dan laporan pematuhan v2.1.0 mengesahkan bahawa model tersebut tidak lagi relevan. Sistem kini memfokuskan borang tetamu terbuka yang mematuhi WCAG 2.2 AA, Core Web Vitals, dan standard MyGOV Digital Service. Semua operasi dalaman dihadkan kepada dua peranan pentadbir yang log masuk ke panel Filament 4.1.10. Teknologi semasa termasuk Laravel 12.40.1 dengan Livewire 3.7.0 untuk komponen interaktif, Volt 1.10.1 untuk single-file components, Alpine.js 3 untuk interaktiviti frontend, Tailwind CSS 4.1.17 untuk styling, dan Laravel Reverb 1.6.2 untuk WebSocket real-time.

---

## 3. SKOP PERNIAGAAN (Business Scope)

- **Helpdesk Ticketing (Dalaman):** Borang dalaman untuk aduan kerosakan ICT, pengurusan SLA, notifikasi, dan audit trail.
- **ICT Asset Loan (Dalaman):** Borang dalaman untuk permohonan pinjaman aset dengan kelulusan berperingkat mengikut bahagian/gred.
- **Pentadbiran Filament:** Operasi back-office oleh `admin` (pengurusan harian) dan `super admin` (governance, audit, konfigurasi).
- **Portal Staf Dalaman:** Pengguna log masuk untuk akses fungsi; sistem tidak dibuka kepada orang awam.

---

## 4. TUJUAN SISTEM (Business Objectives)

1. **Memudahkan akses dalaman** kepada perkhidmatan ICT BPM melalui portal intranet dengan login yang selamat.
2. **Memastikan ketelusan dan auditabiliti** melalui rekod automatik, cap masa, dan laporan digital.
3. **Mematuhi standard kebolehcapaian & prestasi** (WCAG 2.2 AA, Lighthouse ≥90, LCP <2.5s).
4. **Menguatkuasakan dasar peminjaman & SLA** secara automatik dengan pengesanan konflik dan peringatan.
5. **Melindungi data peribadi** tetamu dan pegawai kelulusan melalui token bertanda tangan, encryption, dan polisi retention.

---

## 5. MODEL AKSES PENGGUNA (User Access Model) - HYBRID ARCHITECTURE

**Hybrid Access Model**: Sistem menyokong dua mod akses untuk staf MOTAC:

1. **Authenticated Staff**: Log masuk via Laravel Breeze (akaun pangkalan data) untuk akses 'My Dashboard', view submission history, auto-fill borang.
2. **Guest/Quick Access**: Gunakan borang tetamu tanpa log masuk (tracked via token).

| Profil Pengguna       | Medium Akses                        | Nota                                                                                                                                              |
| --------------------- | ----------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| Guest (Token)         | Borang tetamu tanpa log masuk       | Staf boleh gunakan borang tetamu. Data disimpan dalam submitter*\*/applicant*\* columns.                                                          |
| Staff (Auth)          | Portal (login pilihan)              | Staf boleh log masuk untuk Dashboard/Profile. Auto-fill borang dengan user_id (nullable FK).                                                      |
| Pegawai Kelulusan     | E-mel dengan pautan bertanda tangan | Menilai permohonan melalui token e-mel. Tanpa log masuk ke sistem.                                                                                |
| Admin                 | Panel Filament 4.1.10               | Mengurus tiket, aset, notifikasi, laporan, dan konfigurasi operasi harian.                                                                        |
| Self-Registered Staff | Portal (login selepas pendaftaran)  | Staf mendaftar dengan @motac.gov.my, pengesahan e-mel, akses Dashboard/Profile.                                                                   |
| Superuser             | Panel Filament 4.1.10               | Mengurus kawalan pentadbiran, audit, integrasi, tetapan keselamatan, kelulusan konfigurasi, dan akses penuh Laravel Telescope. **Tiada sekatan.** |

---

## 6. FUNGSI UTAMA SISTEM (Business Requirements)

### FR-001: Hybrid Access Model

**Authenticated Staff Access:**

- Log masuk via Laravel Breeze untuk akses 'My Dashboard'
- View submission history (tiket & permohonan sendiri)
- Auto-fill borang dengan data profil (nama, email, bahagian, gred)
- Edit profile settings
- Submissions linked to `user_id` (nullable FK)

**Guest/Quick Access:**

- Gunakan borang tetamu tanpa log masuk
- Tracked via token (email confirmation)
- Data disimpan dalam `submitter_*` / `applicant_*` columns
- `user_id` set to NULL

### FR-050: Hybrid Data Association (CRITICAL)

**Requirement**: Sistem MESTI menyokong dual data model untuk link submissions:

1. **If user logged in (Auth::check() === true):**

   - Link submission to `user_id` (nullable FK to users.id)
   - Auto-populate `submitter_*` / `applicant_*` from user profile
   - Enable 'My Submissions' dashboard view
   - Submission tracked via user_id AND email token

2. **If guest (Auth::check() === false):**
   - Set `user_id` = NULL
   - Require manual entry of `submitter_*` / `applicant_*` fields
   - Track via email token only

**Database Schema Requirements:**

- `helpdesk_tickets.user_id` → nullable FK to users.id (ON DELETE SET NULL)
- `loan_applications.user_id` → nullable FK to users.id (ON DELETE SET NULL)
- Retain all `submitter_*` / `applicant_*` string columns for guest fallback
- Index on `user_id` for performance (My Submissions queries)

**Business Logic:**

- Form submission controller MUST check `Auth::check()` before saving
- If authenticated: `$ticket->user_id = Auth::id()`
- If guest: `$ticket->user_id = null`
- Email notifications sent to `submitter_email` regardless of auth status

### 6.1. Helpdesk Ticketing Module (Hybrid Access)

- **Borang Aduan Hybrid:**  
  Staf boleh log masuk (auto-fill dari user profile) atau gunakan borang tetamu. Livewire 3.7.0 + Volt 1.10.1 dengan validasi masa nyata. Medan: nama, e-mel, telefon, bahagian, gred, kategori, deskripsi, lampiran. Auth::check() logic untuk auto-fill. Interface menggunakan Alpine.js 3 dan Tailwind CSS 4.1.17.
- **Pengurusan Kategori & SLA:**  
  Sistem menandakan keutamaan berdasarkan kategori/SLA. `admin` boleh mengubah suai templat kategori dan pautan bantuan (rujuk D04 §4.1).
- **Automasi Notifikasi:**  
  Pengesahan e-mel dihantar kepada pengguna dengan nombor tiket. `admin` menerima pemberitahuan queue; `super admin` menerima amaran bagi pelanggaran SLA. Notifikasi masa nyata dihantar melalui Laravel Reverb 1.6.2 (WebSocket) dan Laravel Echo 2.2.6.
- **Audit & Tindak Lanjut:**  
  Semua interaksi (komen, ubah status) dicap masa. Pengguna boleh memuat naik bukti lanjutan melalui pautan selamat; Filament menyatukan komunikasi.
- **Pelaporan:**  
  Dashboard Filament 4.1.10 menyediakan laporan kategori, trend, SLA, dan statistik backlog untuk pengurusan BPM. Widget interaktif dibina menggunakan Livewire 3.7.0 dengan kemaskini masa nyata.

### 6.2. ICT Asset Loan Module (Hybrid Access)

- **Borang Permohonan Hybrid:**  
  Staf boleh log masuk (auto-fill) atau gunakan borang tetamu. Livewire 3.7.0 + Volt 1.10.1 untuk pilih aset, tempoh, lokasi, tujuan. Auth::check() logic untuk auto-fill. Sistem memeriksa konflik tempahan dan ketersediaan aset secara masa nyata dengan Alpine.js 3.
- **Workflow Kelulusan Berpautan E-mel:**
  1. Permohonan berjaya dihantar menjana `loan_application` dengan kod rujukan dan nullable `user_id` (jika logged in).
  2. Sistem mengenal pasti pegawai Gred 41 berkaitan (rujuk kamus bahagian) dan menggunakan peranan dalaman untuk kelulusan.
  3. `ApprovalService` menjana `loan_approval` yang mengandungi `approver_email`, `approver_grade`, `signed_token`, dan tarikh luput.
  4. Pegawai menerima e-mel berformat WCAG (Plain text + HTML) dengan butiran permohonan dan dua butang: **Luluskan** atau **Tolak**.
  5. Pautan membuka halaman kelulusan dalam portal dalaman yang memaparkan ringkasan permohonan; pegawai memilih keputusan, memasukkan catatan (optional), dan mengesahkan.
  6. Keputusan dicap masa. Pengguna (jika logged in) dan `admin` menerima pemberitahuan automatik.
- **Pengeluaran & Pemulangan Aset:**  
  `admin` melaksanakan check-out/in melalui Filament 4.1.10, merekod pegawai BPM yang menyerahkan/menerima, dan menandai kerosakan. Interface menggunakan Livewire 3.7.0 untuk operasi real-time.
- **Audit & Laporan:**  
  `loan_transactions`, `loan_audits`, dan `loan_approvals` menyimpan jejak lengkap. Laporan penggunaan aset, kerosakan, dan overdue dijana secara berkala.

### 6.3. Integrasi Modul

- **Pemetaan Aset dalam Tiket:** Tiket helpdesk boleh dikaitkan dengan permohonan pinjaman aktif untuk pengesanan kerosakan.
- **Automasi Penyelenggaraan:** Pemulangan aset dengan status "Damaged" mencetuskan tiket penyelenggaraan automatik.
- **Rekonsiliasi Data:** `superuser` menjalankan semakan berkala untuk memastikan rekod tiket dan transaksi pinjaman serasi.

---

## 7. KEPERLUAN BUKAN FUNGSI (Non-Functional Requirements)

- **Kebolehcapaian:** Mematuhi WCAG 2.2 AA, 44x44px touch target, 3px focus outline, struktur ARIA (rujuk D12-D14, `accessibility-testing-checklist.md`).
- **Prestasi:** LCP <2.5s untuk borang utama, TTI <4s, skor Lighthouse ≥90 (rujuk `core-web-vitals-testing-guide.md`, `performance-optimization-report.md`).
- **Keselamatan:** reCAPTCHA Enterprise, rate limiting, storage token hashed, audit log penuh (D09 §8).
- **Kebolehskalaan:** Boleh menambah borang tetamu baharu tanpa menambah peranan pengguna.
- **Kebolehgunaan:** UI dwibahasa, navigasi jelas, panduan inline untuk tetamu, status real-time.
- **Pemulihan:** Backup harian, pelan pemulihan 4 jam (RTO), kehilangan data maks 1 jam (RPO).

---

## 8. KEPERLUAN DATA (Data Requirements)

### 8.1. Kategori Data Utama

- **Data Tetamu:** Nama, e-mel, telefon, bahagian, gred, maklumat aduan/permohonan, lampiran.
- **Data Pentadbir:** Rekod `users` untuk `admin` dan `superuser` (nama, e-mel dalaman, telefon, status).
- **Data Kelulusan:** `approver_email`, `approver_grade`, keputusan, catatan, token.
- **Data Audit & Prestasi:** Rekod SLA, masa tindak balas, masa penyelesaian, log akses.

### 8.2. Implikasi Privasi Data & PDPA

- **Data Peribadi:** Tetamu dan pegawai kelulusan diklasifikasi sebagai data peribadi; simpanan terhad kepada tujuan proses.
- **Consent:** Borang menyertakan notis PDPA & checkbox perakuan.
- **Retention:** data tetamu kekal 7 tahun (selari PDPA & Arkib Negara); lampiran dibersihkan jika tidak relevan selepas 24 bulan kecuali kes audit.
- **Hak Individu:** Tetamu boleh memohon pemadaman maklumat lampiran melalui saluran rasmi BPM; log audit mengekalkan rekod perubahan.

---

## 9. KEPERLUAN PENGURUSAN (Management Requirements)

- **Pengurusan Config:** `superuser` mengawal konfigurasi SLA, senarai aset, dan templat e-mel.
- **Latihan:** `admin` menerima latihan operasi Filament & pematuhan PDPA; modul e-learning disimpan dalam LMS BPM.
- **Sokongan & Penyenggaraan:** Penyelenggaraan berkala (mingguan) untuk memastikan borang tetamu, queue, dan integrasi berfungsi.
- **Pengurusan Perubahan:** Mengikut D01 §9.3, sebarang perubahan keperluan mesti didokumenkan dengan ID perubahan, impak, dan pelan rollback.

---

## 10. KEPERLUAN UNDANG-UNDANG, PERATURAN & DASAR (Legal, Regulatory & Policy Requirements)

- **PDPA 2010** – Pengumpulan data minimum, persetujuan, hak akses, dan pelupusan.
- **MCMC Messaging Guidelines** – SMS peringatan mematuhi garis panduan SPAM/opt-out.
- **Arahan Keselamatan ICT MOTAC** – Audit log & kawalan akses terhad.
- **MyGOV Digital Service Standards v2.1.0** – Borang awam mematuhi standard perkhidmatan digital kerajaan.
- **ISO 27001 Annex A (dirujuk)** – Kawalan keselamatan am (akses, log, integriti).

---

## 11. KEPERLUAN KEBERJAYAAN (Success Criteria)

| ID    | Kriteria                                               | Sasaran                                                    |
| ----- | ------------------------------------------------------ | ---------------------------------------------------------- |
| SC-01 | 100% permohonan & aduan dihantar melalui borang tetamu | Tiada lagi pengumpulan manual/e-mel untuk tiket & pinjaman |
| SC-02 | SLA tindak balas helpdesk (4 jam kerja)                | ≥ 90% dicapai setiap bulan                                 |
| SC-03 | Kelulusan Gred 41 melalui pautan e-mel                 | ≥ 95% tanpa bantuan manual                                 |
| SC-04 | Skor Lighthouse (Desktop/Mobile)                       | ≥ 90 untuk borang utama                                    |
| SC-05 | Pematuhan audit PDPA & ICT MOTAC                       | Tiada ketakpatuhan kritikal semasa audit tahunan           |

---

## 12. GLOSARI & RUJUKAN (Glossary & References)

### 12.1. Istilah Utama Perniagaan

| Istilah                              | Takrif                                                                           |
| ------------------------------------ | -------------------------------------------------------------------------------- |
| **Tetamu**                           | Individu yang mengemukakan borang tanpa akaun aplikasi.                          |
| **Pautan Kelulusan Bertanda Tangan** | URL unik dengan token hashed dan tarikh luput untuk membuat keputusan kelulusan. |
| **Admin**                            | Pegawai BPM yang mengurus operasi harian melalui Filament.                       |
| **Superuser**                        | Pegawai BPM yang mengawal konfigurasi, audit, dan integrasi.                     |
| **SLA**                              | Service Level Agreement untuk tindak balas dan penyelesaian.                     |

### 12.2. Rujukan Piawaian

- WCAG 2.2 AA (W3C)
- MyGOV Digital Service Standards v2.1.0
- ISO/IEC 29100 (Privacy framework)
- ISO/IEC/IEEE 29148 (Requirements Engineering)
- PDPA 2010

---

## 13. LAMPIRAN (Appendices)

### 13.1. Borang Rujukan

- `helpdesk_form_to_model.md` – Mapping borang helpdesk kepada model & validasi.
- `loan_form_to_model.md` – Mapping borang pinjaman kepada model & kelulusan.

### 13.2. Carta Alir & Diagram

- Carta alir kelulusan e-mel (rujuk D04 §4.2, D11 §6).
- Diagram proses SLA (rujuk D11 §7).

### 13.3. Dokumen Sokongan

- `filament-admin-interface-compliance.md` – Bukti pematuhan panel pentadbir.
- `accessibility-testing-checklist.md` – Log ujian kebolehcapaian.

---

## 14. MATRIKS PEMETAAN KEPERLUAN (Requirements Traceability Matrix)

RTM diselenggara dalam `docs/rtm/loan_requirements_rtm.csv` dan `docs/rtm/helpdesk_requirements_rtm.csv`. Semua keperluan baharu bercap ID `BRS-3.x` dan dipetakan kepada SRS (D03), SDD (D04), serta kes ujian berkaitan (PHPUnit, Livewire, Lighthouse). Kemas kini RTM hendaklah mematuhi D01 §9.3 untuk penjejakan perubahan.
