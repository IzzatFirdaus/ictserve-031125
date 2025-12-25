# Ringkasan Sistem (System Overview)

**Sistem ICTServe**  
**Versi:** 4.0.0 (SemVer)  
**Tarikh Kemaskini:** 24 Disember 2025  
**Status:** Aktif — PKS-Compliant SSO-Only Architecture  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, ISO/IEC 33063:2015, MyGovEA 18 Prinsip, OWASP ASVS L2, **PKS 5.2.1 / 9.2.1 / 4.2 / 5.4.3**, PSPM MyGovCloud

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                       |
| -------------------- | ------------------------------------------------------------------------------------------- |
| **Versi**            | 4.0.0                                                                                       |
| **Tarikh Kemaskini** | 24 Disember 2025                                                                            |
| **Status**           | Aktif — PKS-Compliant SSO-Only Architecture                                                 |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**         | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, ISO/IEC 33063:2015, MyGovEA 18 Prinsip, OWASP ASVS L2, **PKS 5.2.1 / 9.2.1 / 4.2 / 5.4.3**, PSPM MyGovCloud |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                                   |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                                                                                                                     | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 4.0.0 | 24 Disember 2025 | **PKS Compliance Migration (v4.0)**: Seni bina **SSO-Only (LDAP/AD) + HRMIS auto-provisioning**, tiada akses tetamu; `user_id` diwajibkan (NOT NULL) untuk semua rekod; buang kolum `guest_*`/`applicant_*`; **PKS 9.2.1 DLP Filtering + Data Classification** (SENSITIVE → Ollama tempatan, PUBLIC → Bedrock melalui gateway selamat, audit laluan data); **PKS 4.2 Data Sovereignty** (intranet-only, MyGovCloud keutamaan); **PKS 5.4.3 Password Policy** melalui Active Directory; saluran Reverb terhad kepada pengguna diautentikasi; kemaskini rujukan KRISA/PSPM. | Pasukan Pembangunan BPM |
| 3.6.1 | 17 Disember 2025 | **Kemaskini Teknologi Stack**: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Filament 4.3.1, Laravel Horizon 5.41.0. **Cloud Hybrid AI Integration**: Kemaskini §3.2 dengan D18 Cloud Hybrid Architecture (Ollama + AWS Bedrock). Tambah model routing pintar, streaming responses, web-augmented responses, conversation management. Kemaskini §7 dengan AI Assistant comprehensive features dan Asset Management lifecycle. Cross-reference D18 v1.0.1. | Pasukan Pembangunan BPM |
| 3.6.0 | 8 Disember 2025  | **Bahasa Melayu sahaja untuk antara muka pengguna**: Pelaksanaan keputusan menggunakan Bahasa Melayu eksklusif untuk semua UI. Language switcher dilumpuhkan (kod dikekalkan sebagai komen). Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal. Dokumentasi D00-D17 dikemaskini. Rujuk D15 v3.6.0 untuk butiran penuh. | Pasukan Pembangunan BPM |
| 3.5.0 | 1 Disember 2025  | Penambahan Laravel Pulse v1.3.0 (performance monitoring untuk admin/superuser), Laravel Sanctum v4.0 (API token authentication), Laravel Socialite v5.x (Google Workspace SSO opsyen untuk @motac.gov.my). Kemaskini spec files dengan 38 requirements, 100 correctness properties, dan 19 implementation phases.                        | Pasukan Pembangunan BPM |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), multi-channel notifications. Pematuhan Jabatan Digital Negara.                                                                | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Staf boleh pilih login (Laravel Breeze - akaun pangkalan data) untuk Dashboard/Profile ATAU gunakan borang tetamu. Database: user_id nullable FK. Matriks pengguna: Guest (Token), Staff (Auth), Admin (Filament).                                                                                                  | Pasukan Pembangunan BPM |
| 3.3.0 | 29 November 2025 | Penyelarasan versi dengan D04 v3.3.0 dan D05 v3.3.0: standardisasi dokumentasi guest-first architecture, token-based workflows, dan teknologi stack terkini. Kemaskini rujukan Playwright 1.56.1.                                                                                                                                        | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Volt 1.10.1, Tailwind CSS 4.1.18, PHPUnit 11.5.46, Larastan 3.8.1, Laravel Pint 1.26.0). Penambahbaikan format jadual dan pematuhan markdownlint.                                                                     | Pasukan Pembangunan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini versi teknologi: Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.18. Penambahan Laravel MCP 0.3.4, Laravel Reverb 1.6.3. Kemaskini seni bina dengan Docker support.                                                                                                               | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Peralihan penuh kepada seni bina dalaman (internal-only): portal staf MOTAC berasaskan Laravel 12 dengan Login, kelulusan dalam sistem (role-based), Filament v4 untuk pentadbiran, dan pematuhan WCAG 2.2 AA. Rujukan silang D02, D03, D04, D09, D11, D12–D14. | Pasukan Pembangunan BPM |
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
- **[D15_LANGUAGE_MS_EN.md]** - Language Implementation (Bahasa Melayu sahaja v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - Real-time Broadcasting & WebSocket Setup
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue Management & Background Jobs
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Cloud Hybrid AI Architecture (v1.0.1)
- **Rujukan implementasi Helpdesk (kod):** `app/Livewire/Helpdesk/TicketForm.php`, `database/migrations/2025_11_03_043924_create_helpdesk_tickets_table.php`
- **Rujukan implementasi Pinjaman Aset (kod):** `app/Livewire/Forms/LoanApplicationForm.php`, `database/migrations/2025_11_03_043935_create_loan_applications_table.php`
- **Aksesibiliti (WCAG 2.2 AA):** `docs/D12_UI_UX_DESIGN_GUIDE.md`, `docs/D14_UI_UX_STYLE_GUIDE.md`, `tests/e2e/accessibility.comprehensive.spec.ts`, `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`
- **Prestasi (CWV/Lighthouse):** `tests/e2e/performance/core-web-vitals.spec.ts`, `tests/e2e/performance/lighthouse-audit.spec.ts`, `docs/reference/performance-optimization-guide.md`
- **Pematuhan panel Filament (UI/komponen):** `docs/reference/FILAMENT_UPDATE_STATUS.md`, `tests/e2e/filament.components.debug.spec.ts`

---

## Ringkasan Eksekutif (Executive Summary)

ICTServe beroperasi sebagai platform dalaman (intranet-only) untuk warga kerja MOTAC dengan **seni bina PKS-Compliant SSO-Only**: semua akses memerlukan **SSO LDAP/Active Directory** dengan akaun yang diselaraskan melalui **HRMIS auto-provisioning**. Tiada akses tetamu atau akaun manual; setiap aktiviti mesti dipautkan kepada `user_id` (NOT NULL) bagi pematuhan **PKS 5.2.1**.

Modul utama ialah Helpdesk (aduan ICT) dan Pinjaman Aset ICT, ditadbir melalui portal staf dan panel Filament dengan empat peranan (`staff`, `approver`, `admin`, `superuser`). Jejak audit dwilapis (owen-it + spatie) dikekalkan 7 tahun, antaramuka eksklusif Bahasa Melayu, pematuhan WCAG 2.2 AA, dan pemantauan prestasi melalui Laravel Pulse. Integrasi AI hibrid mematuhi **PKS 9.2.1/4.2** dengan penapisan DLP dan penghalaan Ollama (sensitif) vs Bedrock (awam sahaja).

---

## 1. Modul Helpdesk ICT (SSO Sahaja)

Modul helpdesk memerlukan **SSO LDAP/Active Directory** untuk semua penyerahan (Walk-in/Kiosk dan portal staf). `user_id` adalah wajib (NOT NULL) dan disegerakkan dengan HRMIS.

### 1.1. Fungsi Utama Helpdesk

- **Borang WCAG 2.2 AA (SSO diperlukan)**  
  Livewire v3 mengekalkan borang bertahap dengan validasi masa nyata, sasaran sentuh 44×44px, dan fokus visual mengikut D12–D14. Tiada borang tetamu.
- **Pautan Identiti Mandatori**  
  `helpdesk_tickets.user_id` wajib dan disahkan melalui LDAP/AD; data staf (bahagian/gred) diselaraskan melalui HRMIS.
- **Lampiran & Bukti**  
  Sehingga 5 fail (gambar, PDF) dengan penukaran automatik WebP dan imbasan virus ClamAV.
- **Notifikasi Automatik**  
  E-mel + WebSocket (Reverb) dihantar kepada staf yang diautentikasi dan `admin`; SLA diurus melalui queue Horizon.
- **Laluan Penyelesaian (Resolution Paths)**  
  Tugas ditugaskan melalui Filament; `superuser` memantau SLA, audit, dan eskalasi dengan Pulse/Telescope.
- **Dashboard Operasi Filament**  
  Akses `admin`/`superuser` sahaja; menyokong laporan SLA, backlog, audit, dan Pulse metrics.

### 1.2. Manfaat untuk BPM

- **Akauntabiliti Penuh (PKS 5.2.1)**  
  Semua tiket dipautkan kepada staf diautentikasi; tiada rekod tanpa `user_id`.
- **Saluran Tunggal & Selamat**  
  Semua aduan melalui SSO; kelulusan/eskalasi dilog oleh dual-audit.
- **Prestasi Boleh Ukur**  
  Pulse + Filament menyediakan metrik SLA, kekerapan kategori, dan purata masa pemulihan.

---

## 2. Modul Peminjaman Aset ICT (SSO Sahaja)

Modul pinjaman memerlukan **SSO LDAP/Active Directory** untuk semua permohonan (Walk-in/Kiosk & portal). `loan_applications.user_id` wajib dan disahkan melalui HRMIS; kelulusan mesti diverifikasi pegawai Gred 41+.

### 2.1. Fungsi Utama Asset Loan

- **Permohonan SSO + WCAG**  
  Livewire borang berbilang langkah dengan validasi stok/konflik tarikh masa nyata; tiada borang tetamu. `user_id` wajib (NOT NULL).
- **Kelulusan Disahkan HRMIS**  
  Pautan e-mel bertanda masa dan portal kelulusan memerlukan pengesahan gred/identiti melalui HRMIS sebelum keputusan (APPROVE/REJECT). Token masa tamat 7 hari.
- **Pengurusan Kitaran Hidup Aset**  
  `admin` merekod pengeluaran, pemulangan, kerosakan; `superuser` menyelaras audit berkala dan pematuhan.
- **Notifikasi & Peringatan**  
  E-mel/WebSocket dihantar kepada peminjam yang diautentikasi; SMS opsyenal melalui gateway BPM. SLA pinjaman diselia oleh queue Horizon.
- **Rekod Automatik & Audit**  
  Semua keputusan kelulusan, perubahan status, dan catatan pulangan dilog dalam `loan_transactions` + `loan_audits` dengan `user_id` wajib.

### 2.2. Manfaat untuk BPM

- **Ketelusan & Akauntabiliti**  
  Semua permohonan dipautkan kepada staf; jejak audit penuh dengan identiti teresah.
- **Penguatkuasaan Polisi**  
  Matriks gred/tempoh/catuan aset dikuatkuasa; kelulusan sah selepas HRMIS verification.
- **Analitik Aset**  
  Laporan penggunaan, kerosakan, backlog disediakan dalam Filament; data menyokong Pulse/Telescope untuk prestasi.

---

## 3. Integrasi Kedua Modul (Module Integration)

### 3.1. Integrasi Antara Helpdesk & Asset Loan

- **Konteks Aset dalam Tiket**  
  Laporan kerosakan bagi aset yang sedang dipinjam akan mengaitkan tiket dengan entri `loan_transactions` semasa untuk tindakan segera.
- **Pemantauan SLA**  
  Kemas kini pemulangan aset boleh mencetuskan tiket penyelenggaraan automatik jika kerosakan dilaporkan.
- **Analitik Gabungan**  
  `superuser` mengakses papan pemuka yang menggabungkan data tiket dan pinjaman untuk analisa trend (contoh, aset dengan kadar kerosakan tinggi).

### 3.2. Integrasi AI & Automasi (v4.0) - Cloud Hybrid + DLP

**PKS-Compliant Cloud Hybrid AI** — Semua permintaan AI mesti melalui **DLP Filtering Service** (PKS 9.2.1) yang mengklasifikasikan data:

- **SENSITIVE** → Ollama (on-prem, PKS 4.2) dengan log residensi data
- **PUBLIC** → AWS Bedrock (Claude) melalui Secure API Gateway dengan audit laluan data
- Semua permintaan dilog dengan `user_id`, klasifikasi, keputusan routing, dan cap masa; akses AI hanya untuk pengguna yang diautentikasi (Walk-in/Kiosk SSO & portal staf).

#### 3.2.1. Seni Bina AI (DLP-First)

- **ModelRouter + DLPFilteringService**: Menjalankan PII/government data detection sebelum sebarang panggilan cloud. Blok automatik jika SENSITIVE.
- **Data Classification Engine**: Menentukan laluan Ollama vs Bedrock; rekod audit per keputusan.
- **Data Residency Logging**: Mencatat lokasi pemprosesan (tempatan vs awan) untuk pematuhan PKS 4.2 / PSPM MyGovCloud.
- **Queue + Reverb**: Kerja AI (ingest, embeddings, auto-reply) diproses melalui Redis/Horizon; status dihantar melalui Reverb saluran diautentikasi.

#### 3.2.2. Ciri AI Utama

- **AI Chat (Portal/SSO)**: FAQ + reasoning dengan streaming SSE; sumber dirujuk, masa sasaran <5s (Ollama) / <15s (Bedrock).
- **Auto-Reply Drafts**: Draf jawapan tiket/permohonan untuk semakan `admin`/`superuser`; semua draf dilog.
- **Document Analysis**: Bedrock multimodal untuk dokumen awam sahaja selepas DLP; fail sensitif diproses Ollama tempatan.
- **Conversation Management**: Simpan/muat/arkib per pengguna diautentikasi; audit jejak penuh.

#### 3.2.3. Pematuhan & Keselamatan

- **PKS 9.2.1**: DLP wajib, audit keputusan, dan amaran jika percubaan menghantar data sensitif ke awan.
- **PKS 4.2**: Keutamaan MyGovCloud; laluan Ollama untuk data sensitif; log residensi data.
- **PDPA 2010**: PII detection, sanitasi, dan hak subjek data dihormati.
- **Audit Dwilapis**: Semua interaksi AI dicatat dalam owen-it + activitylog dengan `user_id` mandatori.

#### 3.2.4. Pengurusan Admin (Filament)

- **AI Dashboard**: Metrik penggunaan/model/kos, ralat, dan keputusan DLP.
- **Konfigurasi Model**: Had akses per peranan, tetapan Ollama/Bedrock, dan kawalan gateway.
- **DLP Rules**: Pengurusan peraturan klasifikasi, senarai hitam istilah sensitif, serta log semakan.
- **Health Monitoring**: Pulse/Telescope memantau status Ollama, Bedrock, Reverb, dan Horizon.

---

## 4. Aspek Teknikal (Technical Aspects)

### 4.1. Senibina Sistem (System Architecture)

**Stack Teknologi Terkini:**

- **Backend Framework**: Laravel 12.43.1 (PHP 8.2.12)
- **Frontend Reactive**: Livewire 3.7.3 + Volt 1.10.1
- **Admin Panel**: Filament 4.3.1
- **JavaScript Framework**: Alpine.js 3 (included with Livewire)
- **CSS Framework**: Tailwind CSS 4.1.18
- **Real-time**: Laravel Reverb 1.6.3 + Laravel Echo 2.2.6
- **Performance Monitoring**: Laravel Pulse 1.4.7
- **API Authentication**: Laravel Sanctum 4.2.1
- **OAuth Integration**: Laravel Socialite 5.24.0
- **MCP Server**: Laravel MCP 0.3.4
- **CLI Prompts**: Laravel Prompts 0.3.8
- **Debugging**: Laravel Telescope 5.16.0
- **Testing**: PHPUnit 11.5.46, Playwright 1.56.1 (E2E)
- **Code Quality**: Laravel Pint 1.26.0, Larastan 3.8.1

**Komponen Utama:**

- **Portal Staf / Kiosk SSO**  
  Livewire v3 + Volt menggunakan layout portal; semua akses melalui **LDAP/AD SSO** dengan akaun yang disegerakkan HRMIS (tiada borang tetamu atau self-registration). Walk-in/Kiosk menggunakan sesi SSO yang sama; `user_id` diwajibkan.
- **Backend Filament v4**  
  Panel pentadbiran tunggal (`/admin`) dengan SSO; peranan diurus Spatie Permission untuk **empat peranan** (`staff`, `approver`, `admin`, `superuser`) + Laravel Policies. HRMIS metadata (gred/bahagian) digunakan untuk kawalan kelayakan.
- **Servis Notifikasi & Kelulusan**  
  Queue Laravel mengendalikan e-mel, SMS (gateway BPM), dan pautan kelulusan bertanda tangan (JWT + hashed token) yang masih memerlukan pengesahan identiti/gred melalui HRMIS sebelum keputusan direkod.
- **Audit & Logging (Dual System)**
  - `owen-it/laravel-auditing` v14.x merekod jejak audit field-level (old/new values) untuk pematuhan PDPA dan audit 7 tahun
  - `spatie/laravel-activitylog` v4.x merekod aktiviti pengguna untuk dashboard dan laporan operasi
  - Laravel Telescope v5.x untuk debugging dan monitoring (akses `superuser` sahaja, tiada sekatan)
- **Performance Monitoring (Laravel Pulse)**
  - Laravel Pulse v1.4.6 menyediakan dashboard prestasi masa nyata untuk `admin` dan `superuser`
  - Pemantauan slow queries (>500ms threshold), queue job metrics, request response times
  - Server health metrics (CPU, memory, disk), cache hit/miss rates
  - Data retention 7 hari dengan automatic pruning
- **API Authentication (Laravel Sanctum)**
  - Laravel Sanctum v4.2.1 untuk token-based API authentication
  - Configurable abilities: `read:tickets`, `write:tickets`, `read:loans`, `write:loans`, `admin:all`
  - Token expiration management dan usage logging
  - Rate limiting 60 requests/minute untuk API endpoints
- **Google Workspace SSO (Sekunder, diluluskan admin)**  
  Laravel Socialite v5.24.0 sebagai **opsyen sekunder** selepas LDAP/AD; hanya domain `@motac.gov.my`, dinyahaktif secara lalai dan diaktifkan melalui keputusan `superuser`/BPM. Tiada auto-registration; akaun mesti wujud/di-provision melalui HRMIS/AD dahulu (link-only).
- **Keselamatan**  
  CSRF untuk semua borang, rate limiting, sanitasi input lampiran, 2FA berasaskan TOTP untuk akaun `superuser` pada panel Filament, imbasan virus ClamAV sebelum simpan, dan pematuhan PKS 5.4.3 melalui polisi kata laluan AD. Tiada reCAPTCHA tetamu kerana tiada borang awam.
- **Real-time Communication (D16)**  
  Laravel Reverb 1.6.3 (WebSocket server) dengan **saluran diautentikasi sahaja**: `private-user.{id}` dan saluran sumber (`private-ticket.{id}`, `private-loan.{id}`, `private-approval.{id}`) yang memerlukan `user_id` sah atau token bertanda masa + verifikasi HRMIS. Laravel Echo 2.2.6 untuk client-side event handling. AI/Reverb notifikasi hanya untuk pengguna diautentikasi.
- **Queue Management (D17)**  
  Laravel Queue dengan Redis backend untuk pemprosesan asinkron (**Laravel Horizon 5.41.0 dipasang** dengan dashboard `/horizon`). Queue name yang digunakan: `default`, `notifications`, `emails`, `digests`, `documents`, `embeddings`, dan `auto-reply`. Rujuk D17 v3.6.1 untuk arahan worker dan katalog job.

### 4.2. Database Design

**Database Engine**: MySQL 8.0 / MariaDB 10.6+

**Model Eloquent Utama** (berdasarkan `application-info`):

- **`User`** (`App\Models\User`)  
  Menyimpan akaun `staff`, `approver`, `admin`, `superuser` yang disegerakkan melalui LDAP/AD + HRMIS (tiada tetamu). `role` dikawal Spatie Permission; medan HRMIS seperti gred/bahagian disimpan untuk kawalan kelayakan.
- **`HelpdeskTicket`** (`App\Models\HelpdeskTicket`)  
  Menyimpan tiket dengan `user_id` **NOT NULL** (FK `users.id`). Tiada kolum `guest_*`; semua metadata pengadu diambil dari HRMIS/AD dan disegerakkan pada waktu SSO.
- **`HelpdeskComment`** (`App\Models\HelpdeskComment`)  
  Menyimpan komen dalaman; `user_id` wajib.
- **`LoanApplication`** (`App\Models\LoanApplication`)  
  Menyimpan permohonan pinjaman dengan `user_id` **NOT NULL**. Jadual `loan_approvals` merekod `approver_id` (FK users), `approver_grade`, `signed_token`, cap masa, dan status; gred disahkan melalui HRMIS.
- **Audit Tables**  
  `loan_audits`, `audits`, `activity_log` menyokong keperluan D09 untuk jejak audit 7 tahun.

**Ciri Database:**

- Soft Deletes untuk logical deletion
- UUID/ULID support via Laravel traits
- Foreign key constraints dengan cascade
- Composite indexes untuk query optimization
- `user_id` wajib (NOT NULL) untuk semua rekod domain (tiket, pinjaman, approval, audit)

---

## 5. Peranan BPM sebagai Pemilik Sistem (BPM as System Owner)

### 5.1. Tanggungjawab BPM

| Peranan       | Tanggungjawab                                                                                                                                                                                                                        |
| ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **admin**     | Menjalankan triage tiket, mengurus inventori aset, memproses permohonan, memantau SLA harian, dan memastikan data HRMIS/AD konsisten untuk operasi.                                                                                   |
| **superuser** | Mentadbir konfigurasi sistem, pengurusan akaun pentadbir, menyemak audit & keselamatan, meluluskan konfigurasi modul, mengurus integrasi, bertindak sebagai pegawai pematuhan, dan akses penuh Laravel Telescope. **Tiada sekatan.** |

---

## 6. Saluran Interaksi (SSO Sahaja)

- **Portal Staf / Kiosk (SSO LDAP/AD)**  
  Semua penyerahan tiket/pinjaman melalui SSO; Walk-in/Kiosk menggunakan sesi SSO yang sama. `user_id` wajib dan disahkan HRMIS/AD.
- **Kelulusan Berpaut Token (Bersyarat)**  
  Pautan kelulusan bertanda masa memerlukan pengesahan identiti/gred melalui HRMIS sebelum keputusan direkod; tiada akses anonim.
- **Penjejakan Status Authenticated**  
  Sejarah tiket/pinjaman disemak melalui My Dashboard (SSO) sahaja; tiada URL status awam bertoken.

---

## 7. Modul Utama

| Modul                      | Deskripsi                                                                     | Peranan Backend                                                                                          |
| -------------------------- | ----------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Helpdesk (SSO)             | Borang aduan SSO, pengurusan SLA, lampiran, notifikasi Reverb                | `admin` memproses tiket melalui Filament                                                                 |
| Asset Loan (SSO)           | Borang pinjaman SSO, kelulusan berverifikasi HRMIS, rekod transaksi           | `admin` mengurus permohonan & aset                                                                       |
| My Dashboard (Portal Staf) | Sejarah tiket/permohonan, profil, notifikasi untuk staf diautentikasi         | `staff` melalui guard `web`; data dari users/helpdesk_tickets/loan_applications                          |
| AI Assistant (Cloud Hybrid + DLP) | DLP-first routing, streaming SSE, conversation management, document analysis, auto-reply | Ollama (sensitif) + Bedrock (awam) melalui gateway; dikawal `admin`/`superuser` dalam Filament           |
| Asset Management           | Pengurusan lifecycle aset: pendaftaran, tracking, penyelenggaraan, pindahan   | `admin` urus aset inventory, preventive maintenance, inter-department transfers                          |
| Filament Admin             | Dashboard operasi, laporan gabungan, pengurusan aset                          | `admin` & `superuser` sahaja                                                                             |
| Sistem Audit & Notifikasi  | Queue e-mel/SMS, log audit dwilapis, pemantauan                               | `superuser` memantau, `admin` bertindak                                                                  |
| Performance Monitoring     | Laravel Pulse dashboard untuk prestasi sistem masa nyata                      | `admin` & `superuser` sahaja melalui `/pulse`                                                            |
| API Authentication         | Token-based API access untuk integrasi dalaman (berkemampuan Sanctum)         | `admin` & `superuser` mengurus token melalui Filament                                                    |

---

## 8. Konteks MOTAC

- Menyokong inisiatif **Digital MOTAC 2025** dengan fokus perkhidmatan awam digital.
- Menggantikan sistem dalaman lama (intranet/Excel) dengan portal intranet/SSO (MyGovCloud keutamaan); tiada akses awam.
- Mematuhi polisi PDPA, garis panduan MCMC untuk SMS OTP, dan keperluan Arkib Negara untuk rekod digital.

---

## 9. UI/UX & Aksesibiliti (User Interface & Accessibility)

### 9.1. Piawaian UI/UX

- Mematuhi D12–D14 serta bukti ujian aksesibiliti (rujuk `tests/e2e/accessibility.comprehensive.spec.ts` dan `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`).
- Palet baharu: Primary `#0056B3`, Secondary `#0B4D8F`, Success `#1B7C54`, Warning `#CC7700`, Danger `#B3002D`.
- Inline focus ring 3px warna `#0B4D8F`, jarak minimum 16px.
- Layout asas `resources/views/layouts/guest.blade.php` (diguna sebagai kerangka portal SSO) menggunakan `aria` landmarks (`header`, `main`, `footer`, `nav`).
- **Bahasa Melayu sahaja** (v3.6.0): Sistem menggunakan Bahasa Melayu secara eksklusif untuk semua antara muka pengguna. Language switcher telah dilumpuhkan dan semua komponen UI menggunakan `lang="ms"`. Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal sahaja. Komponen yang dilumpuhkan: `LanguageSwitcher`, `BilingualSupportService`, `SetLocale` middleware, `ictserve_locale` cookie (rujuk D15 v3.6.0).
- Semua komponen diuji melalui suite E2E aksesibiliti serta audit prestasi (rujuk `tests/e2e/accessibility.comprehensive.spec.ts`, `tests/e2e/performance/core-web-vitals.spec.ts`).

### 9.2. Ciri-ciri Utama

- Navigasi breadcrumb ringkas tanpa menu pengguna.
- Borang berbilang langkah dengan status indicator.
- Komponen tetingkap modal untuk status kelulusan (pengguna SSO) dengan tumpuan (focus trap) mematuhi ARIA.

### 9.3. Implementasi Bahasa (Language Implementation) - D15 v3.6.0

**Bahasa Melayu Sahaja (Monolingual Implementation):**

- **Antara Muka Pengguna**: 100% Bahasa Melayu untuk semua UI components, form labels, error messages, dan navigation
- **HTML Lang Attributes**: Semua halaman menggunakan `lang="ms"` untuk Bahasa Melayu Malaysia
- **Komponen Dilumpuhkan**: Language switcher, bilingual support service, locale middleware, dan locale cookies telah dipadam/dilumpuhkan
- **Fail Terjemahan**: `lang/en/` dikekalkan untuk rujukan teknikal sahaja; `lang/ms/` sebagai sumber utama
- **WCAG 2.2 AA Compliance**: Audit score 96/100 dengan sokongan screen reader (NVDA/JAWS) dalam Bahasa Melayu
- **Contoh Label**: `Nama Penuh`, `Bahagian`, `Hantar`, `Laman Utama`, `Perkhidmatan`, `Hubungi`
- **Mesej Ralat**: `Medan ini wajib diisi`, `Emel tidak sah`, `Sila pilih kategori`
- **Notifikasi**: Email dan WebSocket notifications dalam Bahasa Melayu dengan format yang konsisten

**Technical Implementation:**

- `config('app.locale')` ditetapkan kepada 'ms' secara kekal
- Middleware `SetLocale` sentiasa mengembalikan 'ms' locale
- User model `locale` column dilumpuhkan (sentiasa 'ms')
- Frontend JavaScript tidak lagi menghandle language switching
- Translation files coverage: 36 files per bahasa (72 files keseluruhan)

---

## 10. Komunikasi Masa Nyata & Penyiaran (Real-time Communication & Broadcasting) - D16 v4.0

### 10.1. Seni Bina Penyiaran (Broadcasting Architecture)

**Laravel Reverb WebSocket Server:**

- **Penyedia Utama**: Laravel Reverb 1.6.3 sebagai WebSocket server rasmi Laravel
- **Konfigurasi**: `BROADCAST_CONNECTION=reverb` dengan sokongan horizontal scaling via Redis
- **Performance**: Dioptimumkan untuk 100+ concurrent connections dengan low latency
- **Security**: Private channel authorization berasaskan sesi SSO (Laravel auth) atau token bertanda masa untuk kelulusan yang tetap memerlukan `user_id` sah; **tiada saluran tetamu**.

### 10.2. Strategi Saluran Authenticated

- **Pengguna Diautentikasi**: `private-user.{userId}` untuk notifikasi peribadi; `private-ticket.{ticketId}`, `private-loan.{loanId}`, `private-approval.{approvalId}` untuk status domain; `private-ai.{conversationId}` untuk AI streaming.
- **Kawalan Akses**: Setiap saluran memerlukan `user_id` dan semakan kelayakan (HRMIS gred untuk kelulusan/eskalasi).

### 10.3. Acara Penyiaran Sedia Ada (Authenticated Only)

**Core System Events:**

| Acara | Saluran Auth | Tujuan |
|-------|--------------|--------|
| `NotificationCreated` | `private-user.{id}` | Notifikasi baharu |
| `StatusUpdated` | `private-user.{id}` / `private-ticket.{id}` | Kemaskini status |
| `CommentPosted` | `private-user.{id}` / `private-ticket.{id}` | Ulasan baharu |
| `AssetReturnedDamaged` | `private-user.{id}` / `private-loan.{id}` | Aset rosak |
| `EmailVerified` | `private-user.{id}` | Email disahkan |
| `AccountLinked` | `private-user.{id}` | Akaun dipautkan |

**AI Real-time Events (D18 Integration):**

| Acara AI | Saluran Auth | Tujuan |
|----------|--------------|--------|
| `AiStreamingStarted` | `private-ai.{id}` | AI streaming dimulakan |
| `AiStreamingChunk` | `private-ai.{id}` | Chunk respons AI |
| `AiStreamingCompleted` | `private-ai.{id}` | AI streaming selesai |
| `AiModelSwitched` | `private-ai.{id}` | Model AI ditukar |
| `AiWebSearchStarted` | `private-ai.{id}` | Web search dimulakan |
| `AiErrorOccurred` | `private-ai.{id}` | Ralat AI berlaku |

### 10.4. Integrasi Frontend (Frontend Integration)

**Laravel Echo Configuration:**

- **Reverb Primary**: Auto-detection dengan fallback ke Pusher
- **Client Libraries**: Laravel Echo 2.2.6 + Pusher-JS untuk WebSocket communication
- **Event Handling**: Livewire v3 integration dengan `#[On]` attributes untuk real-time updates
- **Error Handling**: Automatic reconnection dengan exponential backoff

**JavaScript Implementation (Authenticated):**

```javascript
if (window.userId) {
  window.Echo.private(`user.${window.userId}`)
    .listen('.notification.created', handleNotification)
    .listen('.status.updated', updateStatus);

  if (window.ticketId) {
    window.Echo.private(`ticket.${window.ticketId}`)
      .listen('.status.updated', updateStatus)
      .listen('.comment.posted', handleComment);
  }
}
```

---

## 11. Pengurusan Baris Gilir & Pekerjaan Latar Belakang (Queue Management & Background Jobs) - D17 v3.6.0

### 11.1. Seni Bina Baris Gilir (Queue Architecture)

**Queue Configuration:**

- **Default Driver**: Database untuk development, Redis untuk production
- **Queue Workers**: Supervisor-managed processes dengan auto-restart
- **Failed Jobs**: Database storage dengan UUID tracking untuk retry/recovery
- **Monitoring**: Laravel Pulse integration untuk queue metrics dan performance

### 11.2. Pekerjaan Sistem Sedia Ada (System Background Jobs)

**Core Application Jobs (5 jobs):**

| Pekerjaan | Tujuan | Nota |
|-----------|--------|------|
| `SendTicketCreatedEmail` | Email pengesahan tiket | SSO sahaja; rekod dalam notifications DB |
| `SendLoanApprovedEmail` | Email kelulusan pinjaman | SSO sahaja; kelulusan sah selepas verifikasi HRMIS |
| `SendAssetOverdueEmail` | Email peringatan tertunggak | SSO sahaja; disalurkan ke peminjam diautentikasi |
| `RetryFailedEmail` | Retry email gagal | Mekanisme retry standard |
| `ExportSubmissionsJob` | Export data submissions | Untuk pengguna diautentikasi sahaja |

**Job Sistem (berdasarkan `app/Jobs/`):**

| Pekerjaan | Queue | Timeout | Tujuan |
|----------|-------|---------|--------|
| `SendTicketNotification` | notifications | (worker) | Notifikasi tiket (created/assigned/status_updated) |
| `SendLoanNotification` | notifications | (worker) | Notifikasi pinjaman aset |
| `SendApprovalRequest` | emails | (worker) | E-mel kelulusan (signed URL/token) |
| `ProcessNotificationDigest` | digests | (worker) | Ringkasan notifikasi (digest) |
| `ExportSubmissionsJob` | default | 300s | Eksport submission |

**AI/RAG Jobs (D18, berdasarkan `app/Jobs/`):**

| Pekerjaan AI | Queue | Timeout | Tujuan |
|-------------|-------|---------|--------|
| `DocumentIngestJob` | documents | 600s | Ingest dokumen untuk RAG |
| `EmbeddingJob` | embeddings | 900s | Jana embeddings |
| `AutoReplyGenerationJob` | auto-reply | 300s | Jana draf auto-reply |

### 11.3. Notifikasi Bergilir (Queued Notifications)

**SSO-Only Notification Logic:**

- Semua notifikasi dihantar kepada pengguna diautentikasi (`user_id` wajib) melalui database notification + e-mel.
- Kelulusan melalui pautan bertanda masa masih memerlukan verifikasi identiti/gred HRMIS sebelum tindakan disahkan.
- Tiada mod e-mel sahaja kerana semua aliran memerlukan SSO.

**Notification Types (25+ notifications):**

- Helpdesk: Ticket created, status updated, comment added, assigned, SLA breach
- Asset Loan: Application submitted, approved/rejected, asset overdue, returned
- System: Email verification, welcome, account linked, API token events
- AI: Conversation saved, model switched, error occurred, usage metrics

### 11.4. Pengurusan Pekerja (Worker Management)

**Supervisor Configuration:**

```ini
[program:ictserve-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work redis --queue=default,notifications,emails,digests,documents,embeddings,auto-reply --sleep=3 --tries=3 --timeout=1200
autostart=true
autorestart=true
numprocs=2
```

**Cadangan pemisahan worker (dengan Horizon v5.41.0):**

- Asingkan worker “cepat” (`default,notifications,emails,digests`) dan worker “lama” (`documents,embeddings,auto-reply`) untuk stabiliti dan timeout (rujuk D17 v3.6.1).

---

## 12. Migrasi Data & Integrasi (Data Migration & Integration)

### 12.1. Integrasi Luaran (External Integration)

| Sistem                         | Tujuan                                       | Integrasi                                                                   |
| ------------------------------ | -------------------------------------------- | --------------------------------------------------------------------------- |
| LDAP/Active Directory (Mandatori) | Identiti utama & SSO                        | Laravel auth + SSO; polisi kata laluan PKS 5.4.3; provisioning disegerak HRMIS |
| HRMIS                          | Penyelarasan data staf (gred/bahagian)       | Sync atribut semasa login untuk kawalan kelayakan dan audit                 |
| SMTP / GOV Mail                | Penghantaran e-mel & pautan kelulusan        | Laravel queue + MOU BPM                                                     |
| SMS Gateway BPM                | Peringatan due date & OTP (opsyen)           | REST API (token service)                                                    |
| MyIdentity (Opsyen Masa Depan) | Pengesahan identiti pegawai Gred 41          | Belum diaktifkan; memerlukan MAMPU clearance                                |
| Google Workspace SSO (Sekunder) | Log masuk alternatif (domain @motac.gov.my)  | Laravel Socialite v5.x; diaktifkan oleh `superuser`, tiada auto-registration |
| API External (Future-Ready)    | Integrasi dengan aplikasi mobile/luaran      | Laravel Sanctum v4.0 token authentication (internal-first)                  |

Migrasi data daripada sistem terdahulu melibatkan import rekod tiket & pinjaman ke jadual baharu dengan memetakan `staff_no` lama kepada `users.id` melalui HRMIS. Tiada konsep tetamu; semua rekod mesti dipautkan kepada pengguna sah.

---

## 13. Pematuhan Piawaian (Standards Compliance)

- **PKS 5.2.1** – Akauntabiliti identiti: `user_id` wajib, SSO LDAP/AD + HRMIS auto-provisioning; tiada tetamu/akaun manual.
- **PKS 9.2.1** – DLP wajib untuk semua permintaan AI; audit keputusan routing (Ollama vs Bedrock) dan amaran percubaan data sensitif ke awan.
- **PKS 4.2** – Keutamaan MyGovCloud dan residensi data tempatan; laluan Ollama untuk data sensitif dengan log residensi.
- **PKS 5.4.3** – Polisi kata laluan AD diguna pakai; 2FA TOTP untuk `superuser` Filament.
- **PDPA 2010 & Audit 7 Tahun** – Dual-audit (owen-it + activitylog) dengan retention minimum 7 tahun.
- **WCAG 2.2 AA** – Lihat `docs/D12_UI_UX_DESIGN_GUIDE.md`, `docs/D14_UI_UX_STYLE_GUIDE.md`, dan bukti ujian di `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`.
- **Performance Optimisation** – Rujuk `docs/reference/performance-optimization-guide.md` serta ujian `tests/e2e/performance/core-web-vitals.spec.ts`.
- **Documentation Traceability** – D01 §9.3 memastikan semua perubahan direkod.
- **MyGOV Digital Service Standards v2.1.0** – Bukti pematuhan UI/komponen Filament dan audit prestasi dirujuk dalam `docs/reference/FILAMENT_UPDATE_STATUS.md` serta `docs/reference/performance-optimization-guide.md`.

---

## 13a. Arsitektur Penempatan (Deployment Architecture)

### 13a.1. Infrastruktur Penempatan (Deployment Infrastructure)

**Development Environment:**

- **Docker Compose**: Multi-container setup dengan `app` (PHP 8.2-FPM + Nginx) dan `db` (MySQL 8.0)
- **Vite Dev Server**: Hot Module Replacement (HMR) untuk frontend development
- **Laravel Reverb**: WebSocket server untuk real-time features

**Production Environment:**

- **Frontend**: Laravel served via Nginx/Apache, Vite build assets, HTTP/2, Brotli compression
- **Backend**: PHP-FPM 8.2.12, Supervisor queue workers untuk notifikasi
- **Database**: MySQL 8 dengan replikasi read-only (opsyen), backup automatik harian
- **Object Storage**: MinIO/S3 untuk lampiran pengguna dengan polisi retention
- **WebSocket**: Laravel Reverb untuk real-time communication

### 13a.2. Keselamatan Penempatan (Deployment Security)

- Enforce HTTPS + HSTS
- WAF menapis trafik robot/spam ke borang SSO (portal/kiosk)
- Secrets diurus melalui `.env` & Azure Key Vault (perancangan)
- Audit log disalurkan ke SIEM BPM setiap 15 minit
- Docker secrets management untuk sensitive data
- Rate limiting pada API endpoints dan form submissions

---

## 14. Glosari & Rujukan (Glossary & References)

### 14.1. Istilah Utama

| Istilah                  | Takrif                                                                      |
| ------------------------ | --------------------------------------------------------------------------- |
| **Admin**                | Pegawai BPM yang memproses tiket & permohonan melalui Filament.             |
| **Superuser**            | Pegawai pengurusan BPM yang mentadbir konfigurasi, keselamatan, dan audit.  |
| **LDAP/AD SSO**          | Mekanisme autentikasi utama; semua pengguna mesti log masuk melalui AD.      |
| **HRMIS Sync**           | Penyelarasan gred/bahagian pengguna semasa log masuk untuk kawalan kelayakan.|
| **Signed Approval Link** | Pautan e-mel ber-token untuk kelulusan; sah selepas verifikasi HRMIS/AD.    |
| **DLP Filtering Service**| Komponen yang mengklasifikasi data (PKS 9.2.1) sebelum pemprosesan AI.       |
| **Livewire**             | Full-stack framework untuk dynamic interfaces dengan server-side rendering. |
| **Volt**                 | Single-file Livewire components dengan simplified syntax.                   |
| **Filament**             | Server-Driven UI (SDUI) framework untuk admin panel.                        |
| **Reverb**               | Laravel WebSocket server untuk real-time communication.                     |
| **Pulse**                | Laravel performance monitoring dashboard untuk admin/superuser.             |
| **Sanctum**              | Laravel API token authentication system.                                    |
| **Socialite (Sekunder)** | Laravel OAuth 2.0 library untuk Google Workspace SSO (diaktifkan jika diluluskan). |
| **API Token**            | Token Sanctum untuk akses API dengan abilities dan expiration.              |
| **Broadcasting**         | Laravel real-time event system menggunakan WebSocket.                       |
| **Queue Worker**         | Background process yang memproses queued jobs secara asinkron.              |
| **Private Channel**      | WebSocket channel dengan authorization untuk pengguna diautentikasi.        |
| **AI Streaming**         | Real-time AI response delivery menggunakan Server-Sent Events.              |
| **Bahasa Melayu Sahaja** | Monolingual implementation - UI menggunakan Bahasa Melayu eksklusif.        |

### 14.2. Versi Teknologi

| Teknologi         | Versi   | Tujuan                                |
| ----------------- | ------- | ------------------------------------- |
| PHP               | 8.2.12  | Backend programming language          |
| Laravel           | 12.43.1 | Web application framework             |
| Livewire          | 3.7.3   | Reactive components                   |
| Volt              | 1.10.1  | Single-file components                |
| Filament          | 4.3.1   | Admin panel                           |
| Alpine.js         | 3       | Lightweight JavaScript framework      |
| Tailwind CSS      | 4.1.18  | Utility-first CSS framework           |
| Vite              | 7.0.7   | Asset bundler/build tool              |
| Nginx             | 1.24    | Reverse proxy / web server            |
| Redis             | 7.0     | Queue, cache, broadcasting backend    |
| Laravel Reverb    | 1.6.3   | WebSocket server                      |
| Laravel Echo      | 2.2.6   | Client-side event handling            |
| Laravel Breeze    | 2.3.8   | Authentication scaffolding            |
| Laravel Pint      | 1.26.0  | Code formatter (PSR-12)               |
| Larastan          | 3.8.1   | Static analysis (PHPStan for Laravel) |
| PHPUnit           | 11.5.46 | Testing framework                     |
| Playwright        | 1.56.1  | End-to-end browser testing            |
| axe-core          | 4.11.0  | Accessibility testing library         |
| Laravel Auditing  | 14.x    | Model audit trail (owen-it)           |
| Activity Log      | 4.x     | User activity logging (spatie)        |
| Laravel Telescope | 5.x     | Debugging & monitoring (superuser)    |
| Laravel Pulse     | 1.4.6   | Performance monitoring (admin/superuser) |
| Laravel Sanctum   | 4.2.1   | API token authentication              |
| Laravel Socialite | 5.24.0  | Google Workspace SSO (opsyen)         |
| Spatie Permission | 6.23    | Role-based access control             |
| ClamAV            | -       | File upload virus scanning            |

---

## Kesimpulan (Conclusion)

Seni bina **SSO-Only** berasaskan LDAP/AD + HRMIS memastikan setiap rekod dipautkan kepada `user_id` yang sah, mematuhi **PKS 5.2.1** dan menyingkirkan aliran tetamu. Integrasi AI hibrid kini **DLP-first (PKS 9.2.1)** dengan laluan sensitif → Ollama (on-prem) dan awam → Bedrock melalui gateway diaudit, sejajar dengan **PKS 4.2** dan keutamaan MyGovCloud. Polisi kata laluan dan 2FA mengikuti **PKS 5.4.3**, manakala audit dwilapis dikekalkan selama 7 tahun.

Laravel Pulse, Reverb, Horizon, dan Sanctum terus menyokong operasi dalaman yang dipantau dengan rapi; Google Workspace SSO dikekalkan sebagai opsyen sekunder yang diaktifkan hanya dengan kelulusan `superuser`. Antaramuka kekal monolingual Bahasa Melayu (v3.6.0) dengan pematuhan WCAG 2.2 AA.

Keseluruhan kemaskini v4.0 ini menegaskan kedudukan ICTServe sebagai platform intranet BPM MOTAC yang patuh PKS, berasaskan SSO, berjejak audit penuh, dan bersedia untuk pengembangan terkawal melalui MyGovCloud.
