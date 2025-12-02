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
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), Laravel Pulse (performance monitoring), Laravel Sanctum (API authentication), Laravel Socialite (Google SSO optional), multi-channel notifications. Pematuhan Jabatan Digital Negara. | Pasukan Pembangunan BPM |
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
- **Pemantauan Prestasi:** Dashboard Laravel Pulse untuk pemantauan prestasi aplikasi secara masa nyata.
- **Infrastruktur API:** Laravel Sanctum untuk pengesahan API bagi integrasi masa depan.
- **Pengesahan Sosial (Pilihan):** Laravel Socialite untuk Google Workspace SSO.

## 3.1. GAMBARAN KESELURUHAN PROJEK (Project Overview)

Bahagian Pengurusan Maklumat (BPM) MOTAC bertanggungjawab mengurus perkhidmatan ICT untuk kakitangan dalaman. Sistem ICTServe v3.5.0 menyediakan platform bersepadu untuk:

```text
+---------------------------------------------------------------------+
|                        PENGURUSAN MOTAC                             |
+---------------------------------------------------------------------+
                              |
              +---------------v----------------+
              |  BAHAGIAN PENGURUSAN MAKLUMAT  |
              |            (BPM)               |
              +---------------+----------------+
                              |
               +--------------+--------------+
               |                             |
          +----v----+                   +----v----+
          |  UNIT   |                   |  UNIT   |
          |TEKNIKAL |                   |  ASET   |
          |  ICT    |                   |  ICT    |
          +---------+                   +---------+
               |                             |
          +----v----+                   +----v----+
          |HELPDESK |                   |PINJAMAN |
          |/SERVICE |                   |ASET ICT |
          |  DESK   |                   |         |
          +---------+                   +---------+
               |                             |
               v                             v
+---------------------------------------------------------------------+
|                     PENGGUNA AKHIR                                  |
|                   (WARGA MOTAC)                                     |
+---------------------------------------------------------------------+
```

## 3.2. SENARAI PEMEGANG TARUH (Stakeholder List)

| Pemegang Taruh              | Peranan / Tanggungjawab                                                                                      | Kepentingan |
| --------------------------- | ------------------------------------------------------------------------------------------------------------ | ----------- |
| Pengurusan MOTAC            | Menetapkan polisi, memerlukan laporan KPI & statistik untuk keputusan strategik                             | Tinggi      |
| Bahagian Pengurusan Maklumat (BPM) | Pemilik Sistem: pengurusan keseluruhan, skop dan kawalan proses bisnes                                      | Tinggi      |
| Unit Teknikal ICT           | Mengendalikan operasi helpdesk/servicedesk, menyelesaikan kes teknikal, pemantauan SLA                      | Tinggi      |
| Unit Aset ICT               | Mengurus inventori aset, proses pengeluaran dan pemulangan aset, penyelenggaraan                            | Tinggi      |
| Pentadbir Sistem (Admin)    | Pentadbiran sistem, pengurusan tiket dan pinjaman, konfigurasi operasi harian                               | Tinggi      |
| Pentadbir Sistem (Superuser)| Konfigurasi sistem, audit, keselamatan, akses Laravel Telescope dan Pulse, pengurusan penuh                 | Tinggi      |
| Warga MOTAC (Staff)         | Pengguna akhir yang membuat aduan, permohonan pinjaman, self-registration, akses dashboard                  | Sederhana   |
| Warga MOTAC (Guest)         | Pengguna tetamu yang menggunakan borang tanpa log masuk untuk akses pantas                                  | Sederhana   |
| Pegawai Kelulusan (Gred 41+)| Meluluskan/menolak permohonan pinjaman aset melalui pautan e-mel bertanda tangan                            | Sederhana   |
| Pembekal / Vendor           | Sokongan luaran, pembaikan perkhidmatan, penyelenggaraan peralatan (jika diperlukan)                        | Rendah      |
| Unit Sumber Manusia         | Maklumat perubahan staf (pindah/persaraan) untuk pengurusan profil pengguna                                 | Rendah      |

---

## 4. TUJUAN SISTEM (Business Objectives)

### 4.1. Matlamat Utama

Menyediakan sistem pengurusan helpdesk, servicedesk, dan pinjaman aset ICT yang bersepadu, cekap, dan telus untuk meningkatkan kualiti perkhidmatan ICT di MOTAC dengan seni bina True Hybrid yang fleksibel.

### 4.2. Objektif Terukur

1. **Memudahkan akses dalaman** kepada perkhidmatan ICT BPM melalui portal intranet dengan login yang selamat atau akses tetamu pantas.
2. **Memastikan ketelusan dan auditabiliti** melalui rekod automatik, cap masa, dual audit system (owen-it + spatie), dan laporan digital.
3. **Mematuhi standard kebolehcapaian & prestasi** (WCAG 2.2 AA, Lighthouse ≥90, LCP <2.5s, Core Web Vitals).
4. **Menguatkuasakan dasar peminjaman & SLA** secara automatik dengan pengesanan konflik dan peringatan masa nyata.
5. **Melindungi data peribadi** tetamu dan pegawai kelulusan melalui token bertanda tangan, encryption AES-256, dan polisi retention PDPA.
6. **Meningkatkan kecekapan operasi** dengan mengurangkan masa pemprosesan permohonan helpdesk ≥40% dan pinjaman aset ≥50% dalam 6 bulan.
7. **Menyediakan pemantauan proaktif** melalui Laravel Pulse untuk mengesan isu prestasi sebelum memberi kesan kepada pengguna.
8. **Mempersiapkan integrasi masa depan** dengan infrastruktur API (Laravel Sanctum) untuk aplikasi mudah alih dan sistem luaran.
9. **Meningkatkan kepuasan pengguna** dengan sasaran ≥85% kepuasan pelanggan melalui maklum balas dan pengalaman pengguna yang dioptimumkan.

### 4.3. Arkitektur Bisnes (Business Architecture)

```text
+-------------------------------------------------------------------------+
|                        MEDIUM PERKHIDMATAN                              |
|  Aplikasi Web | Portal Dalaman | E-mel | Notifikasi Push | API (Future) |
+-------------------------------------------------------------------------+
|                       PENGGUNA PERKHIDMATAN                             |
| DALAMAN: Warga MOTAC (Staff/Guest) | Admin | Superuser | Pegawai Kelulusan |
+-------------------------------------------------------------------------+
|                        PERKHIDMATAN UTAMA                               |
| Pengurusan Helpdesk | Pengurusan Pinjaman Aset | Pemantauan Prestasi   |
| Pengurusan Pengguna | Audit & Keselamatan | Laporan & Dashboard         |
+-------------------------------------------------------------------------+
|             SISTEM APLIKASI YANG MENYOKONG PERKHIDMATAN                 |
|  +---------------------------+  +----------------------------------+     |
|  | Modul Helpdesk/ServiceDesk|  | Modul Pinjaman Aset ICT          |     |
|  | - Hybrid Submission       |  | - Hybrid Application             |     |
|  | - SLA Management          |  | - Email Approval Workflow        |     |
|  | - Real-time Notifications |  | - Asset Check-out/Check-in       |     |
|  | - Status Tracking         |  | - Conflict Detection             |     |
|  +---------------------------+  +----------------------------------+     |
|  +---------------------------+  +----------------------------------+     |
|  | Modul Pengurusan Pengguna |  | Modul Pemantauan & Audit         |     |
|  | - Self-Registration       |  | - Laravel Pulse Dashboard        |     |
|  | - Flexible Login          |  | - Laravel Telescope (Superuser)  |     |
|  | - Account Linking         |  | - Dual Audit (owen-it + spatie)  |     |
|  | - Profile Management      |  | - Performance Monitoring         |     |
|  +---------------------------+  +----------------------------------+     |
|  +---------------------------+  +----------------------------------+     |
|  | Modul API & Integrasi     |  | Modul Laporan & Dashboard        |     |
|  | - Laravel Sanctum         |  | - Real-time Widgets              |     |
|  | - Google Workspace SSO    |  | - KPI Metrics                    |     |
|  | - Token Management        |  | - Export Reports                 |     |
|  +---------------------------+  +----------------------------------+     |
+-------------------------------------------------------------------------+
|                          MAKLUMAT (DATA)                                |
| DALAMAN: Pengguna | Tiket | Aset | Pinjaman | Audit | Performance Metrics |
| LUARAN: Google Workspace (SSO) | Email Gateway | External APIs (Future)  |
+-------------------------------------------------------------------------+
|                           TEKNOLOGI                                     |
| Laravel 12.40.1 | PHP 8.2.12 | MySQL 8.0 | Redis 7.0 | Livewire 3.7.0    |
| Filament 4.1.10 | Laravel Reverb 1.6.2 | Laravel Pulse 1.3.0              |
| Laravel Sanctum 4.0 | Laravel Socialite 5.x | Tailwind CSS 4.1.17          |
+-------------------------------------------------------------------------+
```

### 4.4. Arkitektur Maklumat (Information Architecture)

```text
+------------+--------------------------------------------------+------------------+
|  PENGGUNA  |                 PROSES BISNES                    |    MAKLUMAT      |
+------------+--------------------------------------------------+------------------+
| Warga      | Mengurus Profil Pengguna                         | Maklumat         |
| MOTAC      | - Self-Registration                              | Pengguna         |
| (Staff/    | - Flexible Login                                 | (users table)    |
| Guest)     | - Account Linking                                |                  |
|            |                                                  |                  |
|            | Mengurus Helpdesk & ServiceDesk <-------------> | Maklumat Tiket   |
|            | - Hybrid Submission (Auth/Guest)                 | (helpdesk_tickets|
|            | - Status Tracking                                | submitter_*)     |
|            | - SLA Management                                 |                  |
|            |                                                  |                  |
| Pegawai    | Mengurus Pinjaman Aset ICT <------------------> | Maklumat Aset    |
| Kelulusan  | - Hybrid Application                             | (assets,         |
|            | - Email Approval Workflow                        | loan_applications|
|            | - Asset Check-out/Check-in                       | applicant_*)     |
|            |                                                  |                  |
| Admin      | Mengurus Operasi Harian                          | Maklumat         |
|            | - Ticket Management                              | Transaksi        |
|            | - Asset Management                               | (loan_transactions|
|            | - Notification Management                        | loan_approvals)  |
|            |                                                  |                  |
| Superuser  | Mengurus Konfigurasi & Audit <----------------> | Maklumat Audit   |
|            | - System Configuration                           | (audits,         |
|            | - Dual Audit Review                              | activity_log)    |
|            | - Laravel Telescope Access                       |                  |
|            | - Laravel Pulse Monitoring                       | Performance Data |
|            |                                                  | (pulse_entries,  |
|            |                                                  | pulse_values)    |
|            |                                                  |                  |
|            | Mengurus Laporan & Dashboard                     | Maklumat Laporan |
|            | - Real-time Metrics                              | (aggregated data)|
|            | - KPI Dashboards                                 |                  |
|            | - Export Reports                                 |                  |
+------------+--------------------------------------------------+------------------+
|            | SISTEM MEMBEKAL MAKLUMAT                         |                  |
|            | - Google Workspace (SSO - Optional)              |                  |
|            | - Email Gateway (Notifications)                  |                  |
|            | - External APIs (Future Integration)             |                  |
+------------+--------------------------------------------------+------------------+
```

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

### 6.4. Pemantauan Prestasi Aplikasi (Laravel Pulse)

- **Dashboard Prestasi Masa Nyata:**  
  `admin` dan `superuser` boleh mengakses dashboard Laravel Pulse 1.3.0 di `/pulse` untuk memantau prestasi aplikasi secara proaktif. Dashboard memaparkan query database yang perlahan (>500ms), prestasi queue job, corak permintaan pengguna, dan metrik kesihatan pelayan.
- **Pengesanan Isu Proaktif:**  
  Sistem menjejaki masa tindak balas, penggunaan memori, kadar cache hit, dan pola kegagalan queue. Apabila ambang prestasi melebihi had, sistem menghantar amaran melalui saluran notifikasi yang dikonfigurasi.
- **Pengurusan Data:**  
  Data Pulse disimpan selama 7 hari dengan pemangkasan automatik untuk mengurus keperluan storan. Metrik prestasi digunakan untuk mengenal pasti bottleneck dan mengoptimumkan operasi sistem.
- **Kawalan Akses:**  
  Akses kepada dashboard Pulse terhad kepada peranan `admin` dan `superuser` sahaja melalui authorization gates Laravel.

### 6.5. Pengesahan API (Laravel Sanctum) - Pertimbangan Masa Depan

- **Keupayaan API Token:**  
  Sistem menyediakan pengesahan berasaskan token menggunakan Laravel Sanctum 4.0 untuk menyokong aplikasi mudah alih masa depan dan integrasi luaran. Token API boleh dikonfigurasi dengan tempoh tamat masa (lalai: 30 hari) dan keupayaan terperinci (read:tickets, write:tickets, read:loans, write:loans, admin:all).
- **Keselamatan API:**  
  Sistem menguatkuasakan rate limiting pada endpoint API (60 permintaan/minit untuk token yang disahkan, 10 permintaan/minit untuk tidak disahkan). Semua percubaan pengesahan API dan penggunaan token dilog dalam audit trail untuk pematuhan keselamatan.
- **Persediaan Integrasi:**  
  Infrastruktur API disediakan untuk menyokong integrasi masa depan dengan sistem MOTAC lain, aplikasi mudah alih, atau perkhidmatan luaran sambil mengekalkan standard keselamatan yang ketat.

### 6.6. Google Workspace SSO (Peningkatan Pilihan)

- **Pengesahan OAuth 2.0:**  
  Sistem menyokong log masuk menggunakan akaun Google Workspace melalui Laravel Socialite 5.x untuk staf MOTAC. Ciri ini adalah pilihan dan bergantung kepada dasar IT MOTAC mengenai penggunaan Google Workspace untuk e-mel rasmi.
- **Sekatan Domain:**  
  Log masuk Google terhad kepada domain `@motac.gov.my` sahaja. Sistem menolak semua domain lain untuk memastikan hanya staf MOTAC yang sah boleh mengakses sistem.
- **Penciptaan Akaun Automatik:**  
  Apabila pengguna Google kali pertama log masuk, sistem mencipta akaun staf secara automatik dengan peranan `staff` dan status `active` menggunakan data profil Google (nama, e-mel). Pengguna sedia ada boleh menghubungkan akaun Google mereka dengan akaun sistem sedia ada.
- **Pengalaman Pengguna:**  
  Butang "Sign in with Google" dipaparkan pada halaman log masuk bersama log masuk tradisional e-mel/kata laluan. Jika OAuth Google gagal, sistem memaparkan mesej ralat yang jelas dan fallback kepada log masuk tradisional.
- **Audit dan Keselamatan:**  
  Semua peristiwa pengesahan Google OAuth dilog dalam audit trail untuk pematuhan keselamatan dan governance.

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
| SC-06 | Pemantauan prestasi Laravel Pulse                      | Dashboard diakses oleh admin/superuser untuk pengesanan isu proaktif |
| SC-07 | Infrastruktur API (Laravel Sanctum)                    | Token API berfungsi untuk integrasi masa depan             |
| SC-08 | Google Workspace SSO (jika diaktifkan)                 | ≥ 80% staf menggunakan SSO untuk log masuk                 |

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
| **Laravel Pulse**                    | Dashboard pemantauan prestasi masa nyata untuk menjejaki query perlahan, queue jobs, dan kesihatan pelayan. |
| **Laravel Sanctum**                  | Sistem pengesahan token API untuk akses API yang selamat dengan keupayaan dan tamat masa yang boleh dikonfigurasi. |
| **Laravel Socialite**                | Perpustakaan pengesahan sosial OAuth 2.0 untuk integrasi Google Workspace SSO.  |
| **API Token**                        | Token pengesahan berasaskan Sanctum untuk akses API dengan keupayaan terperinci (read:tickets, write:tickets, read:loans, write:loans, admin:all). |
| **Google Workspace SSO**             | Pengesahan OAuth 2.0 menggunakan akaun Google Workspace terhad kepada domain @motac.gov.my. |

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

## 14. HIERARKI FUNGSI BISNES (Business Function Hierarchy)

### 14.1. Penggunaan Notasi

| Notasi      | Keterangan                                                        |
| ----------- | ----------------------------------------------------------------- |
| [ ]         | Fungsi utama — menunjukkan modul atau domain perniagaan utama    |
| [ ]-[ ]     | Fungsi dan subfungsi — hubungan fungsi utama dengan subfungsi    |
| [ ]-[ ]-[ ] | Fungsi, subfungsi dan aktiviti — langkah spesifik dalam subfungsi |
| BF-IS-*     | Penamaan kod fungsi (contoh: BF-IS-HS untuk Helpdesk/ServiceDesk) |

### 14.2. Struktur Hierarki Fungsi Bisnes

```text
+-----------------------------------------------------------------------+
|                              BF-IS                                    |
|           Mengurus Perkhidmatan ICT MOTAC Dengan Efisien              |
+-----------------------------------------------------------------------+
                              |
        +---------------------+---------------------+---------------------+
        |                     |                     |                     |
   +----------+          +----------+          +----------+          +----------+
   | BF-IS-MP |          | BF-IS-HS |          | BF-IS-PA |          | BF-IS-PM |
   | Mengurus |          | Helpdesk |          | Pinjaman |          | Pemantauan|
   | Pengguna |          | Service  |          | Aset ICT |          | & Audit  |
   +----------+          | Desk     |          +----------+          +----------+
        |                +----------+               |                     |
        |                     |                     |                     |
   +---------+          +---------+           +---------+           +---------+
   |BF-IS-MP-|          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |SR       |          |TK       |           |PP       |           |PS       |
   |Self-Reg |          |Tiket    |           |Permohonan|          |Pulse    |
   +---------+          +---------+           +---------+           +---------+
   +---------+          +---------+           +---------+           +---------+
   |BF-IS-MP-|          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |FL       |          |SLA      |           |KL       |           |TS       |
   |Flexible |          |Pengurusan|          |Kelulusan|           |Telescope|
   |Login    |          +---------+           +---------+           +---------+
   +---------+          +---------+           +---------+           +---------+
   +---------+          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |BF-IS-MP-|          |NT       |           |CO       |           |DA       |
   |AL       |          |Notifikasi|          |Check-out|           |Dual     |
   |Account  |          +---------+           |Check-in |           |Audit    |
   |Linking  |                                +---------+           +---------+
   +---------+                                                      +---------+
                                                                    |BF-IS-PM-|
                                                                    |API      |
                                                                    |Sanctum  |
                                                                    +---------+
        |
   +----------+
   | BF-IS-JL |
   | Dashboard|
   | & Laporan|
   +----------+
```

### 14.3. Keterangan Fungsi Bisnes

| Kod Fungsi   | Nama Fungsi                          | Keterangan                                                                                      |
| ------------ | ------------------------------------ | ----------------------------------------------------------------------------------------------- |
| BF-IS        | Pengurusan Perkhidmatan ICT          | Fungsi utama: Pengurusan perkhidmatan ICT di MOTAC secara bersepadu                            |
| BF-IS-MP     | Mengurus Pengguna                    | Pengurusan profil pengguna: self-registration, login, account linking                          |
| BF-IS-MP-SR  | Self-Registration                    | Pendaftaran staf dengan @motac.gov.my, pengesahan e-mel, aktivasi akaun                        |
| BF-IS-MP-FL  | Flexible Login                       | Log masuk menggunakan e-mel penuh atau username pendek                                         |
| BF-IS-MP-AL  | Account Linking                      | Menghubungkan submission tetamu terdahulu dengan akaun staf baharu                             |
| BF-IS-HS     | Helpdesk/ServiceDesk                 | Pengurusan helpdesk: hybrid submission, SLA, notifikasi, status tracking                       |
| BF-IS-HS-TK  | Pengurusan Tiket                     | Daftar, kemaskini, selesai tiket; hybrid mode (authenticated/guest)                            |
| BF-IS-HS-SLA | Pengurusan SLA                       | Pemantauan SLA, peringatan breach, eskalasi automatik                                           |
| BF-IS-HS-NT  | Pengurusan Notifikasi                | Notifikasi multi-channel (e-mel, database, WebSocket) dengan user preferences                  |
| BF-IS-PA     | Pinjaman Aset ICT                    | Pengurusan pinjaman: hybrid application, email approval, check-out/check-in                    |
| BF-IS-PA-PP  | Permohonan Pinjaman                  | Proses permohonan hybrid (authenticated/guest), conflict detection, soft-lock                  |
| BF-IS-PA-KL  | Kelulusan E-mel                      | Workflow kelulusan melalui signed approval link (SAL) untuk Gred 41+                           |
| BF-IS-PA-CO  | Check-out & Check-in                 | Pengeluaran dan pemulangan aset, rekod transaksi, damage reporting                             |
| BF-IS-PM     | Pemantauan & Audit                   | Pemantauan prestasi, audit, debugging, API management                                           |
| BF-IS-PM-PS  | Laravel Pulse                        | Dashboard prestasi masa nyata: slow queries, queue jobs, server health                         |
| BF-IS-PM-TS  | Laravel Telescope                    | Debugging dan monitoring tool (superuser only, unrestricted access)                            |
| BF-IS-PM-DA  | Dual Audit System                    | Audit field-level (owen-it) + activity logging (spatie) untuk compliance                       |
| BF-IS-PM-API | API Authentication (Sanctum)         | Token-based API authentication untuk integrasi masa depan                                       |
| BF-IS-JL     | Dashboard & Laporan                  | Paparan KPI, dashboard real-time, penjanaan laporan, export data                                |

### 14.4. Senarai Pengguna dan Peranan

| Pengguna            | Peranan / Kebenaran Akses                                                                                   |
| ------------------- | ----------------------------------------------------------------------------------------------------------- |
| Pentadbir Sistem (Superuser) | Akses pentadbiran penuh: konfigurasi, kawalan akses, audit, Laravel Telescope, Laravel Pulse, backup       |
| Pentadbir Sistem (Admin)      | Akses pengurusan operasi: tiket, aset, notifikasi, laporan, Laravel Pulse, konfigurasi harian              |
| Pengurus BPM        | Akses laporan KPI, dashboard eksekutif, kemampuan menjana & menjadual laporan                              |
| Kakitangan Teknikal | Akses pengurusan kes: lihat/kemaskini/resolve tiket, catat tindakan, SLA monitoring                        |
| Pengurus Aset ICT   | Akses inventori, semak/kelulusan permohonan pinjaman, sediakan aset, damage reporting                      |
| Kakitangan Aset ICT | Akses pengeluaran/penerimaan aset, rekod check-out/check-in, accessory tracking                            |
| Pegawai Kelulusan (Gred 41+) | Akses kelulusan via e-mel: approve/reject loan applications melalui signed approval link                   |
| Warga MOTAC (Staff) | Akses authenticated: self-register, login, dashboard, submistory, profilet, auto-fill    |
| Warga MOTAC | Akses tetamu: submit forms tanpa login, track status via token, quick access untuk urgent submissions      |

## 15. PENGIRAAN SAIZ SISTEM APLIKASI (Function Point Analysis)

### 15.1. Ringkasan Pengiraan Function Points

Pengiraan saiz sistem menggunakan kaedah Function Point Analysis (FPA) untuk anggaran awal perancangan projek:

| Komponen | Rendah (Bil×FP) | Sederhana (Bil×FP) | Tinggi (Bil×FP) | Jumlah FP |
| -------- | --------------- | ------------------ | --------------- | --------- |
| EI (External Input) | 8×3 = 24 | 15×4 = 60 | 3×6 = 18 | 102 |
| EO (External Output) | 5×4 = 20 | 10×5 = 50 | 3×7 = 21 | 91 |
| EQ (External Inquiry) | 6×3 = 18 | 12×4 = 48 | 4×6 = 24 | 90 |
| ILF (Internal Logical File) | 10×7 = 70 | 3×10 = 30 | 1×15 = 15 | 115 |
| EIF (External Interface File) | 2×5 = 10 | 2×7 = 14 | 0×10 = 0 | 24 |
| **Jumlah Unadjusted Function Points (UFP)** | | | | **422** |

**Value Adjustment Factor (VAF)**: 1.08 (berdasarkan 14 General System Characteristics)

**Adjusted Function Points (AFP)**: 422 × 1.08 = **455.76 ≈ 456 FP**

### 15.2. Perincian Komponen Function Points

#### 15.2.1 External Inputs (EI) - 102 FP

| Bil. | Fungsi Input                          | Kompleksiti | FP  | Catatan                                      |
| ---- | ------------------------------------- | ----------- | --- | -------------------------------------------- |
| 1    | Self-Registration Form                | Sederhana   | 4   | Validation @motac.gov.my, email verification |
| 2    | Login (Email/Username)                | Rendah      | 3   | Flexible authentication                      |
| 3    | Helpdesk Ticket Submission (Auth)     | Sederhana   | 4   | Auto-fill, attachments, PDPA                 |
| 4    | Helpdesk Ticket Submission (Guest)    | Sederhana   | 4   | Manual entry, token generation               |
| 5    | Loan Application (Auth)               | Tinggi      | 6   | Multi-step wizard, conflict detection        |
| 6    | Loan Application (Guest)              | Tinggi      | 6   | Multi-step wizard, asset availability        |
| 7    | Email Approval Decision               | Sederhana   | 4   | Signed token validation, decision recording  |
| 8    | Asset Check-out                       | Sederhana   | 4   | Transaction recording, condition notes       |
| 9    | Asset Check-in                        | Sederhana   | 4   | Damage reporting, photo attachments          |
| 10   | Profile Update                        | Rendah      | 3   | Phone, division, grade fields                |
| 11   | Account Linking Request               | Sederhana   | 4   | Email matching, confirmation workflow        |
| 12   | Ticket Status Update (Admin)          | Sederhana   | 4   | Comment required, audit logging              |
| 13   | SLA Configuration                     | Rendah      | 3   | Threshold settings                           |
| 14   | Email Template Configuration          | Rendah      | 3   | Template editing                             |
| 15   | User Feedback Submission              | Rendah      | 3   | Rating and comments                          |
| ...  | (Additional inputs)                   | Various     | ... | API token creation, notification preferences |

#### 15.2.2 External Outputs (EO) - 91 FP

| Bil. | Fungsi Output                         | Kompleksiti | FP  | Catatan                                      |
| ---- | ------------------------------------- | ----------- | --- | -------------------------------------------- |
| 1    | Email Confirmation (Ticket)           | Sederhana   | 5   | Bilingual, token link, formatted             |
| 2    | Email Confirmation (Loan)             | Sederhana   | 5   | Bilingual, status tracking                   |
| 3    | Email Approval Request                | Tinggi      | 7   | Signed URL, application summary, WCAG        |
| 4    | SLA Breach Alert                      | Sederhana   | 5   | Multi-channel notification                   |
| 5    | Dashboard KPI Report                  | Tinggi      | 7   | Real-time metrics, charts                    |
| 6    | Ticket Statistics Report              | Sederhana   | 5   | Category, trend, SLA compliance              |
| 7    | Asset Usage Report                    | Sederhana   | 5   | Utilization, overdue, damage                 |
| 8    | Audit Trail Export (CSV)              | Tinggi      | 7   | Dual audit system, compliance format         |
| 9    | Performance Report (Pulse)            | Sederhana   | 5   | Slow queries, queue jobs, server health      |
| 10   | WebSocket Notification                | Rendah      | 4   | Real-time push via Laravel Reverb            |
| ...  | (Additional outputs)                  | Various     | ... | PDF reports, email digests                   |

#### 15.2.3 External Inquiries (EQ) - 90 FP

| Bil. | Fungsi Inquiry                        | Kompleksiti | FP  | Catatan                                      |
| ---- | ------------------------------------- | ----------- | --- | -------------------------------------------- |
| 1    | Ticket Status Check (Token)           | Rendah      | 3   | Token validation, status display             |
| 2    | Loan Status Check (Token)             | Rendah      | 3   | Token validation, approval chain             |
| 3    | My Dashboard (Staff)                  | Tinggi      | 6   | Submission history, profile, notifications   |
| 4    | Asset Availability Search             | Sederhana   | 4   | Real-time availability, conflict detection   |
| 5    | Ticket List (Admin)                   | Sederhana   | 4   | Filtering, pagination, SLA indicators        |
| 6    | Loan Application List (Admin)         | Sederhana   | 4   | Filtering, approval status                   |
| 7    | Audit Log Viewer (Superuser)          | Tinggi      | 6   | Dual audit, filtering, export                |
| 8    | Laravel Pulse Dashboard               | Tinggi      | 6   | Performance metrics, slow queries            |
| 9    | Laravel Telescope Viewer              | Tinggi      | 6   | Debugging, request inspection                |
| 10   | Asset Inventory List                  | Sederhana   | 4   | Status, location, availability               |
| ...  | (Additional inquiries)                | Various     | ... | User search, division lookup                 |

#### 15.2.4 Internal Logical Files (ILF) - 115 FP

| Bil. | Entiti (Table)                        | Atribut (DET) | RET | Kompleksiti | FP  | Catatan                                      |
| ---- | ------------------------------------- | ------------- | --- | ----------- | --- | -------------------------------------------- |
| 1    | users                                 | 15            | 1   | Sederhana   | 10  | Self-registration, flexible login, nullable FK |
| 2    | helpdesk_tickets                      | 18            | 1   | Sederhana   | 10  | Hybrid submission, user_id nullable          |
| 3    | loan_applications                     | 20            | 1   | Tinggi      | 15  | Hybrid application, approval workflow        |
| 4    | assets                                | 12            | 1   | Rendah      | 7   | Inventory management                         |
| 5    | loan_approvals                        | 10            | 1   | Rendah      | 7   | Email approval, signed tokens                |
| 6    | loan_transactions                     | 14            | 1   | Sederhana   | 10  | Check-out/check-in, condition tracking       |
| 7    | audits (owen-it)                      | 12            | 1   | Rendah      | 7   | Field-level compliance audit                 |
| 8    | activity_log (spatie)                 | 10            | 1   | Rendah      | 7   | User activity logging                        |
| 9    | pulse_entries                         | 8             | 1   | Rendah      | 7   | Performance monitoring data                  |
| 10   | pulse_values                          | 6             | 1   | Rendah      | 7   | Aggregated performance metrics               |
| 11   | personal_access_tokens (Sanctum)      | 8             | 1   | Rendah      | 7   | API token management                         |
| 12   | notifications                         | 10            | 1   | Rendah      | 7   | Multi-channel notification queue             |
| 13   | divisions                             | 5             | 1   | Rendah      | 7   | Organizational structure                     |
| 14   | categories (tickets)                  | 4             | 1   | Rendah      | 7   | Ticket categorization                        |
| 15   | sla_configurations                    | 6             | 1   | Rendah      | 7   | SLA thresholds and rules                     |

#### 15.2.5 External Interface Files (EIF) - 24 FP

| Bil. | Interface Luaran                      | Atribut (DET) | RET | Kompleksiti | FP  | Catatan                                      |
| ---- | ------------------------------------- | ------------- | --- | ----------- | --- | -------------------------------------------- |
| 1    | Google Workspace API (SSO)            | 8             | 1   | Sederhana   | 7   | OAuth 2.0 authentication                     |
| 2    | Email Gateway (SMTP)                  | 6             | 1   | Sederhana   | 7   | Notification delivery                        |
| 3    | LDAP Directory (Future)               | 7             | 1   | Rendah      | 5   | Staff directory integration                  |
| 4    | External API (Future)                 | 5             | 1   | Rendah      | 5   | Third-party integrations                     |

### 15.3. General System Characteristics (GSC)

| No. | Karakteristik                         | Nilai | Justifikasi                                                          |
| --- | ------------------------------------- | ----- | -------------------------------------------------------------------- |
| 1   | Data Communications                   | 5     | Real-time WebSocket, API, email integration                          |
| 2   | Distributed Data Processing           | 4     | Redis queue, background jobs, WebSocket server                       |
| 3   | Performance                           | 5     | Core Web Vitals, LCP <2.5s, Laravel Pulse monitoring                |
| 4   | Heavily Used Configuration            | 4     | Multi-user concurrent access, real-time updates                      |
| 5   | Transaction Rate                      | 4     | Moderate transaction volume, peak during office hours                |
| 6   | Online Data Entry                     | 5     | All forms online, hybrid submission modes                            |
| 7   | End-User Efficiency                   | 5     | Auto-fill, flexible login, guest mode, responsive UI                 |
| 8   | Online Update                         | 5     | Real-time status updates, WebSocket notifications                    |
| 9   | Complex Processing                    | 4     | SLA calculation, conflict detection, dual audit                      |
| 10  | Reusability                           | 3     | Modular design, reusable components                                  |
| 11  | Installation Ease                     | 3     | Standard Laravel deployment                                          |
| 12  | Operational Ease                      | 4     | Laravel Pulse, Telescope, comprehensive logging                      |
| 13  | Multiple Sites                        | 2     | Single deployment (MOTAC internal)                                   |
| 14  | Facilitate Change                     | 4     | Modular architecture, API-ready, extensible                          |

**Total Degree of Influence (TDI)**: 57  
**Value Adjustment Factor (VAF)**: 0.65 + (0.01 × 57) = 1.22

**Corrected AFP**: 422 × 1.22 = **514.84 ≈ 515 FP**

### 15.4. Anggaran Usaha dan Kos

Berdasarkan 515 Function Points:

- **Anggaran Usaha**: 515 FP × 6 jam/FP = **3,090 jam** ≈ **387 hari manusia**
- **Anggaran Tempoh**: 387 hari ÷ 3 pembangun = **129 hari** ≈ **6 bulan**
- **Anggaran Kos** (RM 150/jam): 3,090 jam × RM 150 = **RM 463,500**

*Nota: Anggaran ini adalah indikatif untuk perancangan awal. Perincian terperinci akan dimuktamadkan semasa peringkat analisis dan perancangan projek.*

## 16. MATRIKS PEMETAAN KEPERLUAN (Requirements Traceability Matrix)

RTM diselenggara dalam `docs/rtm/loan_requirements_rtm.csv` dan `docs/rtm/helpdesk_requirements_rtm.csv`. Semua keperluan baharu bercap ID `BRS-3.x` dan dipetakan kepada SRS (D03), SDD (D04), serta kes ujian berkaitan (PHPUnit, Livewire, Lighthouse). Kemas kini RTM hendaklah mematuhi D01 §9.3 untuk penjejakan perubahan.
