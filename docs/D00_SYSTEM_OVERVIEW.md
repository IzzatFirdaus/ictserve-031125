# Ringkasan Sistem (System Overview)

**Sistem ICTServe**  
**Versi:** 3.5.0 (SemVer)  
**Tarikh Kemaskini:** 1 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                       |
| -------------------- | ------------------------------------------------------------------------------------------- |
| **Versi**            | 3.5.0                                                                                       |
| **Tarikh Kemaskini** | 1 Disember 2025                                                                             |
| **Status**           | Aktif                                                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**         | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                                   |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                | Penulis                 |
| ----- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 1 Disember 2025  | Penambahan Laravel Pulse v1.3.0 (performance monitoring untuk admin/superuser), Laravel Sanctum v4.0 (API token authentication), Laravel Socialite v5.x (Google Workspace SSO opsyen untuk @motac.gov.my). Kemaskini spec files dengan 38 requirements, 100 correctness properties, dan 19 implementation phases.                        | Pasukan Pembangunan BPM |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), multi-channel notifications. Pematuhan Jabatan Digital Negara.                                                                | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Staf boleh pilih login (Laravel Breeze - akaun pangkalan data) untuk Dashboard/Profile ATAU gunakan borang tetamu. Database: user_id nullable FK. Matriks pengguna: Guest (Token), Staff (Auth), Admin (Filament).                                                                                                  | Pasukan Pembangunan BPM |
| 3.3.0 | 29 November 2025 | Penyelarasan versi dengan D04 v3.3.0 dan D05 v3.3.0: standardisasi dokumentasi guest-first architecture, token-based workflows, dan teknologi stack terkini. Kemaskini rujukan Playwright 1.56.1.                                                                                                                                        | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, Livewire 3.7.0, Filament 4.1.10, Volt 1.10.1, Tailwind CSS 4.1.17, PHPUnit 11.5.44, Larastan 3.8.0, Laravel Pint 1.26.0). Penambahbaikan format jadual dan pematuhan markdownlint.                                                                     | Pasukan Pembangunan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini versi teknologi: Laravel 12.40.1, Livewire 3.7.0, Filament 4.1.10, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17. Penambahan Laravel MCP 0.3.4, Laravel Reverb 1.6.2. Kemaskini seni bina dengan Docker support.                                                                                                               | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Peralihan penuh kepada seni bina dalaman (internal-only): portal staf MOTAC berasaskan Laravel 12 dengan Login (Breeze/Jetstream), kelulusan dalam sistem (role-based), Filament v4 untuk pentadbiran, dan pematuhan WCAG 2.2 AA. Rujukan silang D02, D03, D04, D09, D11, D12–D14, `helpdesk_form_to_model.md`, `loan_form_to_model.md`. | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                   | Pasukan BPM             |
| 1.0.0 | September 2025   | Versi awal dokumentasi sistem                                                                                                                                                                                                                                                                                                            | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]**
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]**
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]**
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]**
- **[D05_DATA_MIGRATION_PLAN.md]**
- **[D06_DATA_MIGRATION_SPECIFICATION.md]**
- **[D07_SYSTEM_INTEGRATION_PLAN.md]**
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]**
- **[D09_DATABASE_DOCUMENTATION.md]**
- **[D10_SOURCE_CODE_DOCUMENTATION.md]**
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]**
- **[D12_UI_UX_DESIGN_GUIDE.md]**
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]**
- **[D14_UI_UX_STYLE_GUIDE.md]**
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**
- **docs/frontend/accessibility-guidelines.md**
- **docs/frontend/color-contrast-accessibility.md**
- **docs/frontend/core-web-vitals-testing-guide.md**
- **docs/performance-optimization-report.md**
- **docs/frontend/filament-admin-interface-compliance.md**

---

## Ringkasan Eksekutif (Executive Summary)

ICTServe beroperasi sebagai platform dalaman (internal-only) untuk warga kerja MOTAC. Akses adalah melalui portal intranet dengan pengesahan (Login) dan kawalan berasaskan peranan (RBAC) bagi staf, pegawai gred, pentadbir, dan penyelia. Modul utama ialah Helpdesk (aduan ICT) dan Pinjaman Aset ICT; kedua-duanya berjalan dalam ekosistem Laravel 12 + Livewire v3 + Filament v4 dengan jejak audit (audit trail), pematuhan aksesibiliti WCAG 2.2 AA, dan standard keselamatan dalaman.

Ekosistem ini digerakkan oleh Laravel 12 + Livewire v3, memanfaatkan audit trail menyeluruh (D09), automasi notifikasi berasaskan queue, dan garis panduan aksesibiliti & prestasi terkini daripada pakej dokumen pematuhan v2.1.0.

---

## 1. Modul Helpdesk ICT (Hybrid Access)

Modul helpdesk digunakan oleh staf MOTAC dengan pilihan log masuk (untuk Dashboard/Profile) atau borang tetamu.

### 1.1. Fungsi Utama Helpdesk

- **Borang Hybrid WCAG 2.2 AA**  
  Livewire v3 mengekalkan borang bertahap (progressive disclosure) dengan pemeriksaan masa nyata, sasaran sentuh yang sesuai, dan fokus visual mengikut D12–D14.
- **Pilihan Akses Pengguna**  
  Staf boleh log masuk (user_id auto-fill) ATAU gunakan borang tetamu (manual input). Nullable user_id FK dalam helpdesk_tickets (rujuk `helpdesk_form_to_model.md`).
- **Lampiran & Bukti**  
  Sehingga 5 fail (gambar, PDF) disokong, dengan penukaran automatik kepada WebP apabila sesuai (rujuk `image-optimization-implementation.md`).
- **Notifikasi E-mel Automatik**  
  Tetamu menerima pengesahan, manakala `admin` menerima ping melalui queue. Status tiket diterjemahkan kepada e-mel triage & SLA (rujuk D11 §6).
- **Laluan Penyelesaian (Resolution Paths)**  
  Tugas ditugaskan kepada `admin` melalui Filament. `superuser` memantau SLA, audit, dan eskalasi.
- **Dashboard Operasi Filament**  
  Hanya boleh diakses oleh `admin` & `superuser`. Paparan menyokong laporan SLA, ringkasan backlog, dan rekod audit.

### 1.2. Manfaat untuk BPM

- **Tanggungjawab Berlapik**  
  Audit trail menyimpan identiti pengguna dalaman, masa tindakan, dan catatan dalaman.
- **Saluran Tunggal**  
  Semua aduan ditapis melalui borang dalaman; tiada lagi tiket manual.
- **Prestasi Boleh Ukur**  
  Panel Filament menyediakan metrik SLA, kekerapan kategori, dan purata masa pemulihan.

---

## 2. Modul Peminjaman Aset ICT (Hybrid Access)

Modul peminjaman mengurus permohonan aset dengan pilihan log masuk atau tetamu.

### 2.1. Fungsi Utama Asset Loan

- **Borang Permohonan Hybrid**  
  Staf boleh log masuk (auto-fill) atau gunakan borang tetamu. Validasi stok dan konflik tarikh dilakukan masa nyata. Nullable user_id FK dalam loan_applications (rujuk `loan_form_to_model.md`).
- **Kelulusan Melalui Pautan E-mel**  
  Sistem menjana permintaan kelulusan untuk Ketua Bahagian (≥ Gred 41) menggunakan token bertanda masa. Pengesahan dibuat melalui klik pautan yang mengesahkan e-mel, gred, dan keputusan (APPROVE / REJECT) tanpa log masuk.
- **Pengurusan Kitaran Hidup Aset**  
  `admin` merekod pengeluaran, pemulangan, kerosakan, dan audit menggunakan modul Filament. `superuser` menyelaras audit berkala.
- **Notifikasi & Peringatan**  
  Penyewaan menghampiri tarikh tamat memicu e-mel & SMS (gateway MCMC) kepada peminjam tetamu, dengan salinan kepada `admin`.
- **Rekod Automatik & Audit**  
  Setiap keputusan kelulusan, ubah status aset, dan catatan pulangan dicap masa dan disimpan dalam `loan_transactions` + `loan_audits`.

### 2.2. Manfaat untuk BPM

- **Ketelusan Kelulusan**  
  Rantaian kelulusan dapat ditelusuri tanpa memerlukan akaun serantau.
- **Penguatkuasaan Polisi**  
  Polisi gred, tempoh, dan catuan aset dikuatkuasa oleh peraturan backend.
- **Analitik Aset**  
  Laporan penggunaan aset, kadar kerosakan, dan backlog permohonan disediakan dalam Filament.

---

## 3. Integrasi Kedua Modul (Module Integration)

### 3.1. Integrasi Antara Helpdesk & Asset Loan

- **Konteks Aset dalam Tiket**  
  Laporan kerosakan bagi aset yang sedang dipinjam akan mengaitkan tiket dengan entri `loan_transactions` semasa untuk tindakan segera.
- **Pemantauan SLA**  
  Kemas kini pemulangan aset boleh mencetuskan tiket penyelenggaraan automatik jika kerosakan dilaporkan.
- **Analitik Gabungan**  
  `superuser` mengakses papan pemuka yang menggabungkan data tiket dan pinjaman untuk analisa trend (contoh, aset dengan kadar kerosakan tinggi).

---

## 4. Aspek Teknikal (Technical Aspects)

### 4.1. Senibina Sistem (System Architecture)

**Stack Teknologi Terkini:**

- **Backend Framework**: Laravel 12.40.1 (PHP 8.2.12)
- **Frontend Reactive**: Livewire 3.7.0 + Volt 1.10.1
- **Admin Panel**: Filament 4.1.10
- **JavaScript Framework**: Alpine.js 3 (included with Livewire)
- **CSS Framework**: Tailwind CSS 4.1.17
- **Real-time**: Laravel Reverb 1.6.2 + Laravel Echo 2.2.6
- **Testing**: PHPUnit 11.5.44, Playwright 1.56.1 (E2E)
- **Code Quality**: Laravel Pint 1.26.0, Larastan 3.8.0

**Komponen Utama:**

- **Frontend Tetamu**  
  Laravel 12 + Livewire v3 + Volt dengan layout `resources/views/layouts/guest.blade.php`. Tiada modul log masuk awam; penyimpanan status menggunakan Session + Cookie.
- **Backend Filament v4**  
  Panel pentadbiran tunggal (`/admin`) dengan SSO larangan; hanya `admin` & `superuser` (rujuk D11 §2). Peranan sistem diurus menggunakan Spatie Permission untuk tiga peranan (`staff`, `admin`, `superuser`) bersama Laravel Policies.
- **Servis Notifikasi & Kelulusan**  
  Queue Laravel mengendalikan e-mel, SMS (melalui gateway BPM), dan pautan kelulusan bertanda tangan (JWT + hashed token). Lihat D04 §4.2 serta D11 §6.
- **Audit & Logging (Dual System)**
  - `owen-it/laravel-auditing` v14.x merekod jejak audit field-level (old/new values) untuk pematuhan PDPA dan audit 7 tahun
  - `spatie/laravel-activitylog` v4.x merekod aktiviti pengguna untuk dashboard dan laporan operasi
  - Laravel Telescope v5.x untuk debugging dan monitoring (akses `superuser` sahaja, tiada sekatan)
- **Performance Monitoring (Laravel Pulse)**
  - Laravel Pulse v1.3.0 menyediakan dashboard prestasi masa nyata untuk `admin` dan `superuser`
  - Pemantauan slow queries (>500ms threshold), queue job metrics, request response times
  - Server health metrics (CPU, memory, disk), cache hit/miss rates
  - Data retention 7 hari dengan automatic pruning
- **API Authentication (Laravel Sanctum)**
  - Laravel Sanctum v4.0 untuk token-based API authentication
  - Configurable abilities: `read:tickets`, `write:tickets`, `read:loans`, `write:loans`, `admin:all`
  - Token expiration management dan usage logging
  - Rate limiting 60 requests/minute untuk API endpoints
- **Google Workspace SSO (Opsyen)**
  - Laravel Socialite v5.x untuk OAuth 2.0 integration dengan Google Workspace
  - Domain validation: hanya `@motac.gov.my` dibenarkan
  - Auto-account creation untuk pengguna baharu, account linking untuk pengguna sedia ada
  - Audit logging untuk semua OAuth events
- **Keselamatan**  
  CSRF untuk borang tetamu, rate limiting, reCAPTCHA Enterprise (mode invisible) untuk mencegah spam, sanitasi input ketat bagi lampiran, 2FA berasaskan TOTP untuk akaun `superuser` pada panel Filament, serta imbasan virus fail menggunakan ClamAV sebelum disimpan.
- **Real-time Communication**  
  Laravel Reverb (WebSocket server) untuk real-time updates, Laravel Echo untuk client-side event handling.
- **Laravel Telescope**  
  Alat debugging dan monitoring untuk `superuser` sahaja. Akses penuh tanpa sekatan kepada semua ciri: requests, commands, jobs, exceptions, logs, queries, models, events, mail, notifications, cache, dan Redis.
- **Pendaftaran Sendiri (Self-Registration)**  
  Staf MOTAC boleh mendaftar akaun menggunakan e-mel `@motac.gov.my`. Pengesahan e-mel diperlukan sebelum akses penuh. Selepas pendaftaran, staf boleh log masuk menggunakan e-mel penuh (`user@motac.gov.my`) atau nama pengguna pendek (`user`).
- **Google Workspace SSO (Opsyen)**  
  Staf MOTAC boleh log masuk menggunakan akaun Google Workspace `@motac.gov.my` sebagai alternatif kepada Laravel Breeze. Sistem akan auto-create akaun baharu atau link ke akaun sedia ada.

### 4.2. Database Design

**Database Engine**: MySQL 8.0 / MariaDB 10.6+

**Model Eloquent Utama** (berdasarkan `application-info`):

- **`User`** (`App\Models\User`)  
  Menyimpan akaun `staff`, `admin` dan `superuser`. Medan `role` diset `staff`, `admin` atau `superuser`. Staf MOTAC yang log masuk ke My Dashboard direkod sebagai `staff`, manakala tetamu kekal tanpa akaun (submissions dengan `user_id` = NULL).
- **`HelpdeskTicket`** (`App\Models\HelpdeskTicket`)  
  Menyimpan data borang tetamu; medan `submitter_name`, `submitter_email`, `submitter_phone` menggantikan `user_id`.
- **`HelpdeskComment`** (`App\Models\HelpdeskComment`)  
  Menyimpan komen dalaman untuk tiket helpdesk.
- **`LoanApplication`** (`App\Models\LoanApplication`)  
  Menyimpan permohonan, item, dan kelulusan e-mel. `loan_approvals` mengekalkan `approver_email`, `approver_grade`, `signed_token`, dan cap masa.
- **Audit Tables**  
  `loan_audits`, `audits`, `activity_log` menyokong keperluan D09 untuk jejak audit.

**Ciri Database:**

- Soft Deletes untuk logical deletion
- UUID/ULID support via Laravel traits
- Foreign key constraints dengan cascade
- Full-text search indexes
- Composite indexes untuk query optimization

---

## 5. Peranan BPM sebagai Pemilik Sistem (BPM as System Owner)

### 5.1. Tanggungjawab BPM

| Peranan       | Tanggungjawab                                                                                                                                                                                                                        |
| ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **admin**     | Menjalankan triage tiket, mengurus inventori aset, memproses permohonan, memantau SLA harian, dan mengekalkan kandungan borang tetamu.                                                                                               |
| **superuser** | Mentadbir konfigurasi sistem, pengurusan akaun pentadbir, menyemak audit & keselamatan, meluluskan konfigurasi modul, mengurus integrasi, bertindak sebagai pegawai pematuhan, dan akses penuh Laravel Telescope. **Tiada sekatan.** |

---

## 6. Saluran Interaksi Awam

- **Borang Helpdesk (`/helpdesk`)**  
  Tetamu menghantar tiket; sistem balas dengan nombor rujukan dan e-mel PDF ringkas.
- **Borang Peminjaman (`/loan`)**  
  Tetamu menghantar permohonan; status dihantar melalui e-mel, manakala kelulusan disempurnakan melalui pautan khas.
- **Penjejakan Status**  
  Tetamu menggunakan URL status khas dengan token (tiada log masuk) untuk menyemak perkembangan tiket atau permohonan.

---

## 7. Modul Utama

| Modul                      | Deskripsi                                                                     | Peranan Backend                                                                                          |
| -------------------------- | ----------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Helpdesk Guest Form        | Borang aduan, pengurusan SLA, lampiran, e-mel tetamu                          | `admin` memproses tiket melalui Filament                                                                 |
| Asset Loan Guest Form      | Borang pinjaman, kelulusan e-mel, rekod transaksi                             | `admin` mengurus permohonan & aset                                                                       |
| My Dashboard (Portal Staf) | Paparan sejarah tiket/permohonan, profil, dan notifikasi untuk staf berdaftar | `staff` (role='staff') melalui guard `web`; data diambil dari users, helpdesk_tickets, loan_applications |
| Filament Admin             | Dashboard operasi, laporan gabungan, pengurusan aset                          | `admin` & `superuser` sahaja                                                                             |
| Sistem Audit & Notifikasi  | Queue e-mel/SMS, log audit, pemantauan                                        | `superuser` memantau, `admin` bertindak                                                                  |
| Performance Monitoring     | Laravel Pulse dashboard untuk prestasi sistem masa nyata                      | `admin` & `superuser` sahaja melalui `/pulse`                                                            |
| API Authentication         | Token-based API access untuk integrasi luaran                                 | `admin` & `superuser` mengurus token melalui Filament                                                    |

---

## 8. Konteks MOTAC

- Menyokong inisiatif **Digital MOTAC 2025** dengan fokus perkhidmatan awam digital.
- Menggantikan sistem dalaman lama (intranet/Excel) dengan borang awam yang boleh diakses dari intranet & Internet.
- Mematuhi polisi PDPA, garis panduan MCMC untuk SMS OTP, dan keperluan Arkib Negara untuk rekod digital.

---

## 9. UI/UX & Aksesibiliti (User Interface & Accessibility)

### 9.1. Piawaian UI/UX

- Mematuhi D12-D14, `accessibility-guidelines.md`, dan `color-contrast-accessibility.md`.
- Palet baharu: Primary `#0056B3`, Secondary `#0B4D8F`, Success `#1B7C54`, Warning `#CC7700`, Danger `#B3002D`.
- Inline focus ring 3px warna `#0B4D8F`, jarak minimum 16px.
- Layout asas `guest.blade.php` menggunakan `aria` landmarks (`header`, `main`, `footer`, `nav`).
- Bahasa dwibahasa: pengesanan awal `Accept-Language`, fallback ke cookie `locale`, kemudian sesi (rujuk D15).
- Semua komponen diuji terhadap `accessibility-testing-checklist.md` dan pencapaian Lighthouse 90+ (rujuk `core-web-vitals-testing-guide.md`).

### 9.2. Ciri-ciri Utama

- Navigasi breadcrumb ringkas tanpa menu pengguna.
- Borang berbilang langkah dengan status indicator.
- Komponen tetingkap modal untuk status kelulusan (untuk tetamu) dengan tumpuan (focus trap) mematuhi ARIA.

---

## 10. Migrasi Data & Integrasi (Data Migration & Integration)

### 10.1. Integrasi Luaran (External Integration)

| Sistem                         | Tujuan                                       | Integrasi                                                                   |
| ------------------------------ | -------------------------------------------- | --------------------------------------------------------------------------- |
| SMTP / GOV Mail                | Penghantaran e-mel tetamu & pautan kelulusan | Laravel queue + MOU BPM                                                     |
| SMS Gateway BPM                | Peringatan due date & OTP (opsyen)           | REST API (token service)                                                    |
| MyIdentity (Opsyen Masa Depan) | Pengesahan identiti pegawai Gred 41          | Belum diaktifkan; memerlukan MAMPU clearance                                |
| Google Workspace SSO (Opsyen)  | Log masuk menggunakan akaun Google @motac.gov.my | Laravel Socialite v5.x OAuth 2.0                                         |
| API External (Future-Ready)    | Integrasi dengan aplikasi mobile/luaran      | Laravel Sanctum v4.0 token authentication                                   |
| Tiada LDAP                     | Not applicable                               | Semua tetamu tanpa log masuk; admin Filament guna credential dalaman atau Google SSO |

Migrasi data daripada sistem terdahulu melibatkan import rekod tiket & pinjaman ke jadual baharu dengan memetakan `staff_no` lama kepada metadata tetamu (rujuk D05 & D06). Tiada migrasi akaun pengguna.

---

## 11. Pematuhan Piawaian (Standards Compliance)

- **WCAG 2.2 AA** – Lihat `accessibility-guidelines.md` & `color-contrast-accessibility.md`.
- **Performance Optimisation** – `performance-optimization-report.md`, `core-web-vitals-testing-guide.md`.
- **Security & Audit** – D09 (audit trail), D11 §8 (security design).
- **Documentation Traceability** – D01 §9.3 memastikan semua perubahan direkod.
- **MyGOV Digital Service Standards v2.1.0** – Bukti pematuhan disimpan dalam `filament-admin-interface-compliance.md` & `css-js-optimization-audit.md`.

---

## 11a. Arsitektur Penempatan (Deployment Architecture)

### 11a.1. Infrastruktur Penempatan (Deployment Infrastructure)

**Development Environment:**

- **Docker Compose**: Multi-container setup dengan `app` (PHP 8.2-FPM + Nginx) dan `db` (MySQL 8.0)
- **Vite Dev Server**: Hot Module Replacement (HMR) untuk frontend development
- **Laravel Reverb**: WebSocket server untuk real-time features

**Production Environment:**

- **Frontend**: Laravel served via Nginx/Apache, Vite build assets, HTTP/2, Brotli compression
- **Backend**: PHP-FPM 8.2.12, Supervisor queue workers untuk notifikasi
- **Database**: MySQL 8 dengan replikasi read-only (opsyen), backup automatik harian
- **Object Storage**: MinIO/S3 untuk lampiran tetamu dengan polisi retention
- **WebSocket**: Laravel Reverb untuk real-time communication

### 11a.2. Keselamatan Penempatan (Deployment Security)

- Enforce HTTPS + HSTS
- WAF menapis trafik robot/spam ke borang tetamu
- Secrets diurus melalui `.env` & Azure Key Vault (perancangan)
- Audit log disalurkan ke SIEM BPM setiap 15 minit
- Docker secrets management untuk sensitive data
- Rate limiting pada API endpoints dan form submissions

---

## 12. Glosari & Rujukan (Glossary & References)

### 12.1. Istilah Utama

| Istilah                  | Takrif                                                                      |
| ------------------------ | --------------------------------------------------------------------------- |
| **Tetamu (Guest)**       | Pengguna awam yang mengisi borang tanpa log masuk.                          |
| **Admin**                | Pegawai BPM yang memproses tiket & permohonan melalui Filament.             |
| **Superuser**            | Pegawai pengurusan BPM yang mentadbir konfigurasi, keselamatan, dan audit.  |
| **Signed Approval Link** | Pautan e-mel ber-token yang membolehkan kelulusan tanpa log masuk.          |
| **guest.blade.php**      | Layout utama untuk semua paparan tetamu.                                    |
| **Livewire**             | Full-stack framework untuk dynamic interfaces dengan server-side rendering. |
| **Volt**                 | Single-file Livewire components dengan simplified syntax.                   |
| **Filament**             | Server-Driven UI (SDUI) framework untuk admin panel.                        |
| **Reverb**               | Laravel WebSocket server untuk real-time communication.                     |
| **Pulse**                | Laravel performance monitoring dashboard untuk admin/superuser.             |
| **Sanctum**              | Laravel API token authentication system.                                    |
| **Socialite**            | Laravel OAuth 2.0 library untuk Google Workspace SSO.                       |
| **API Token**            | Token Sanctum untuk akses API dengan abilities dan expiration.              |

### 12.2. Versi Teknologi

| Teknologi         | Versi   | Tujuan                                |
| ----------------- | ------- | ------------------------------------- |
| PHP               | 8.2.12  | Backend programming language          |
| Laravel           | 12.40.1 | Web application framework             |
| Livewire          | 3.7.0   | Reactive components                   |
| Volt              | 1.10.1  | Single-file components                |
| Filament          | 4.1.10  | Admin panel                           |
| Alpine.js         | 3       | Lightweight JavaScript framework      |
| Tailwind CSS      | 4.1.17  | Utility-first CSS framework           |
| Vite              | 7.0.7   | Asset bundler/build tool              |
| Nginx             | 1.24    | Reverse proxy / web server            |
| Redis             | 7.0     | Queue, cache, broadcasting backend    |
| Laravel Reverb    | 1.6.2   | WebSocket server                      |
| Laravel Echo      | 2.2.6   | Client-side event handling            |
| Laravel Breeze    | 2.3.8   | Authentication scaffolding            |
| Laravel Pint      | 1.26.0  | Code formatter (PSR-12)               |
| Larastan          | 3.8.0   | Static analysis (PHPStan for Laravel) |
| PHPUnit           | 11.5.44 | Testing framework                     |
| Playwright        | 1.56.1  | End-to-end browser testing            |
| axe-core          | 4.11.0  | Accessibility testing library         |
| Laravel Auditing  | 14.x    | Model audit trail (owen-it)           |
| Activity Log      | 4.x     | User activity logging (spatie)        |
| Laravel Telescope | 5.x     | Debugging & monitoring (superuser)    |
| Laravel Pulse     | 1.3.0   | Performance monitoring (admin/superuser) |
| Laravel Sanctum   | 4.0     | API token authentication              |
| Laravel Socialite | 5.x     | Google Workspace SSO (opsyen)         |
| Spatie Permission | 6.23    | Role-based access control             |
| ClamAV            | -       | File upload virus scanning            |

---

## Kesimpulan (Conclusion)

Peralihan kepada seni bina hybrid (guest-first + portal staf) memastikan ICTServe memenuhi mandat BPM untuk menyediakan perkhidmatan digital yang boleh diakses umum sambil mengekalkan kawalan ketat di peringkat pentadbiran. Staf MOTAC yang berdaftar boleh log masuk melalui Laravel Breeze atau Google Workspace SSO (opsyen) untuk menggunakan My Dashboard, manakala tetamu kekal menggunakan borang tetamu sebagai pintu masuk utama. Akaun pentadbir kekal terhad kepada `admin` dan `superuser` dengan 2FA berasaskan TOTP untuk `superuser`, dan semua interaksi penting direkodkan dalam jejak audit.

Sistem kini dilengkapi dengan Laravel Pulse untuk pemantauan prestasi masa nyata, Laravel Sanctum untuk API authentication (future-ready untuk aplikasi mobile), dan Laravel Socialite untuk Google Workspace SSO sebagai alternatif log masuk. Kesemua ciri baharu ini menyokong visi Digital MOTAC 2025 dan pematuhan MyGOV Digital Service Standards v2.1.0.
