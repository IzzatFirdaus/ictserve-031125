# Ringkasan Sistem (System Overview)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                       |
| -------------------- | ------------------------------------------------------------------------------------------- |
| **Versi**            | 3.6.1                                                                                       |
| **Tarikh Kemaskini** | 17 Disember 2025                                                                            |
| **Status**           | Aktif                                                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**         | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                                   |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                | Penulis                 |
| ----- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
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

ICTServe beroperasi sebagai platform dalaman (internal-only) untuk warga kerja MOTAC. Akses adalah melalui portal intranet dengan pengesahan (Login) dan kawalan berasaskan peranan (RBAC) bagi staf, pegawai gred, pentadbir, dan penyelia. Modul utama ialah Helpdesk (aduan ICT) dan Pinjaman Aset ICT; kedua-duanya berjalan dalam ekosistem Laravel 12 + Livewire v3 + Filament v4 dengan jejak audit (audit trail), pematuhan aksesibiliti WCAG 2.2 AA, dan standard keselamatan dalaman.

Ekosistem ini digerakkan oleh Laravel 12 + Livewire v3, memanfaatkan audit trail menyeluruh (D09), automasi notifikasi berasaskan queue, dan garis panduan aksesibiliti & prestasi terkini daripada pakej dokumen pematuhan v2.1.0.

---

## 1. Modul Helpdesk ICT (Hybrid Access)

Modul helpdesk digunakan oleh staf MOTAC dengan pilihan log masuk (untuk Dashboard/Profile) atau borang tetamu.

### 1.1. Fungsi Utama Helpdesk

- **Borang Hybrid WCAG 2.2 AA**  
  Livewire v3 mengekalkan borang bertahap (progressive disclosure) dengan pemeriksaan masa nyata, sasaran sentuh yang sesuai, dan fokus visual mengikut D12–D14.
- **Pilihan Akses Pengguna**  
  Staf boleh log masuk (user_id auto-fill) ATAU gunakan borang tetamu (manual input). Nullable user_id FK dalam helpdesk_tickets (rujuk `app/Livewire/Helpdesk/TicketForm.php`, `database/migrations/2025_11_03_043924_create_helpdesk_tickets_table.php`).
- **Lampiran & Bukti**  
  Sehingga 5 fail (gambar, PDF) disokong, dengan penukaran automatik kepada WebP apabila sesuai (rujuk `app/Services/ImageOptimizationService.php`).
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
  Staf boleh log masuk (auto-fill) atau gunakan borang tetamu. Validasi stok dan konflik tarikh dilakukan masa nyata. Nullable user_id FK dalam loan_applications (rujuk `app/Livewire/Forms/LoanApplicationForm.php`, `database/migrations/2025_11_03_043935_create_loan_applications_table.php`).
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

### 3.2. Integrasi AI & Automasi (v3.6.1) - Cloud Hybrid Architecture

**Cloud Hybrid AI Integration** - Sistem ICTServe melaksanakan **True Hybrid AI Architecture** yang menggabungkan Ollama (local LLM) dengan AWS Bedrock (cloud AI) untuk memberikan pengalaman AI yang optimum dengan penghalaan pintar berdasarkan jenis pertanyaan. Rujuk **D18 v1.0.1** untuk dokumentasi teknikal penuh.

#### 3.2.1. Seni Bina Hibrid AI (Hybrid AI Architecture)

- **Antara Muka Tunggal**: Pengguna berinteraksi dengan SATU sistem chat yang menghalakan pertanyaan secara automatik
- **Penghalaan Pintar**: Sistem menganalisis pertanyaan dan memilih AI yang optimum:
  - **FAQ Queries** → Ollama RAG (percuma, pantas, data sovereignty)
  - **Complex Reasoning** → AWS Bedrock Claude (berbayar, kualiti tinggi)
  - **Hybrid Queries** → Kedua-dua sistem (fakta + analisis)
- **Respons Hibrid**: Menggabungkan pengetahuan tempatan dengan keupayaan penaakulan cloud
- **Pengoptimuman Kos**: 82% penjimatan kos dengan penggunaan Ollama untuk FAQ
- **Multi-Model Intelligence**: Claude Opus 4.5 (complex), Sonnet 4.5 (balanced), Haiku 4.5 (fast), Nova Pro/Lite/Micro
- **Data Residency Compliance**: Klasifikasi automatik untuk pemprosesan tempatan vs cloud (PDPA 2010)

#### 3.2.2. Komponen AI Utama

- **FAQ Bot (Cloud Hybrid)**
  - Accessible untuk guest dan authenticated users melalui `/ai/chat`
  - **Model Routing**: Ollama (FAQ) + AWS Bedrock Claude (complex queries)
  - **Multi-Model Intelligence**: Opus 4.5 (complex), Sonnet 4.5 (balanced), Haiku 4.5 (fast), Nova Pro/Lite/Micro
  - Response time < 5 saat dengan confidence scoring dan source attribution
  - **Enhanced Conversation Management**: Save/load/delete conversations dengan memori jangka panjang
  - **Web-Augmented Responses**: Integrasi DuckDuckGo untuk konteks terkini
  - **Streaming Responses**: Server-Sent Events (SSE) untuk pengalaman responsif (future)

- **Auto-Reply Generation (AI-Enhanced)**
  - **Streaming Responses**: Server-Sent Events (SSE) untuk pengalaman responsif (future)
  - AI-generated response drafts menggunakan model routing pintar
  - Admin review dan approve melalui aliran kerja kelulusan
  - Learning dari historical resolutions dengan pattern recognition
  - **Token-based Approval**: Email approval tanpa login untuk department heads

- **Document Analysis (Multi-Modal)**
  - AI-powered parsing untuk PDF/DOCX/images menggunakan AWS Bedrock Nova Pro
  - **Semantic Search**: Vector embeddings untuk carian dokumen yang cerdas
  - Automated categorization dengan confidence scoring
  - **Data Classification**: Automatik untuk pemprosesan tempatan vs cloud
  - **PII Detection**: Automatic sanitization untuk PDPA 2010 compliance

- **MCP Server Integration**
  - 3 tools untuk AI assistants: Amazon Q, Kiro IDE, external integrations
  - Standardized interface untuk AI tool access
  - **API Gateway**: Unified access untuk multiple AI services
  - **Health Monitoring**: Multi-system status (Ollama + Bedrock + DuckDuckGo)

#### 3.2.3. Pematuhan & Keselamatan

- **Data Residency**: Klasifikasi automatik untuk pemprosesan Malaysia vs cloud
- **PDPA 2010 Compliance**: PII detection dan sanitization
- **Audit Trail**: Comprehensive logging untuk semua AI interactions
- **Content Filtering**: Government communication guidelines compliance
- **Rate Limiting**: Per-user dan per-model untuk cost control

#### 3.2.4. Pengurusan Admin (Filament Integration)

- **AI Dashboard**: Real-time metrics untuk penggunaan model dan kos
- **Model Configuration**: Parameter tuning untuk Ollama dan Bedrock models
- **FAQ Management**: Bulk operations untuk knowledge base
- **Conversation Analytics**: Usage patterns dan performance insights
- **Health Monitoring**: Multi-system status (Ollama + Bedrock + DuckDuckGo)

**Technical Architecture** (D18 v1.0.1):

- **Local**: Ollama server (localhost:11434) untuk FAQ dan data sovereignty
- **Cloud**: AWS Bedrock (us-east-1) untuk complex reasoning dan multimodal tasks
- **Models**: llama3.1 (Ollama), Claude Opus/Sonnet/Haiku 4.5, Nova Pro/Lite/Micro, Titan Text Express/Lite (Bedrock)
- **Storage**: Enhanced tables dengan conversation management dan vector embeddings
- **API**: `/api/v1/bedrock/*` dan `/api/v1/ollama/*` (documented dalam D18)
- **Integration**: MCP servers untuk external AI tool access
- **Monitoring**: Laravel Pulse + Telescope + health checks untuk multi-system status
- **Queue**: Laravel Queue (Redis) untuk AI job processing (document ingestion, embeddings, auto-reply). **Laravel Horizon 5.41.0 DIPASANG** dalam repo v3.6.1 dengan dashboard di `/horizon`.
- **Real-time**: Laravel Reverb untuk AI notifications dan streaming responses

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

- **Frontend Tetamu**  
  Laravel 12 + Livewire v3 + Volt dengan layout `resources/views/layouts/guest.blade.php`. Tiada modul log masuk awam; penyimpanan status menggunakan Session + Cookie.
- **Backend Filament v4**  
  Panel pentadbiran tunggal (`/admin`) dengan SSO larangan; hanya `admin` & `superuser` (rujuk D11 §2). Peranan sistem diurus menggunakan Spatie Permission untuk **empat peranan** (`staff`, `approver`, `admin`, `superuser`) bersama Laravel Policies.
- **Servis Notifikasi & Kelulusan**  
  Queue Laravel mengendalikan e-mel, SMS (melalui gateway BPM), dan pautan kelulusan bertanda tangan (JWT + hashed token). Lihat D04 §4.2 serta D11 §6.
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
- **Google Workspace SSO (Opsyen)**
  - Laravel Socialite v5.24.0 untuk OAuth 2.0 integration dengan Google Workspace
  - Domain validation: hanya `@motac.gov.my` dibenarkan
  - Auto-account creation untuk pengguna baharu, account linking untuk pengguna sedia ada
  - Audit logging untuk semua OAuth events
- **Keselamatan**  
  CSRF untuk borang tetamu, rate limiting, reCAPTCHA Enterprise (mode invisible) untuk mencegah spam, sanitasi input ketat bagi lampiran, 2FA berasaskan TOTP untuk akaun `superuser` pada panel Filament, serta imbasan virus fail menggunakan ClamAV sebelum disimpan.
- **Real-time Communication (D16)**  
  Laravel Reverb 1.6.3 (WebSocket server) untuk real-time updates dengan dual channel strategy: `private-user.{id}` untuk authenticated users dan `private-ticket.{uuid}`/`private-loan.{uuid}` untuk guests. Laravel Echo 2.2.6 untuk client-side event handling. Sokongan notifikasi AI (status/alert/performance/approval) melalui channel seperti `private-ai-status`, `private-ai-alerts`, `private-ai-performance`, `private-ai-approvals` (rujuk D16 v3.6.1).
- **Laravel Telescope**  
  Alat debugging dan monitoring untuk `superuser` sahaja. Akses penuh tanpa sekatan kepada semua ciri: requests, commands, jobs, exceptions, logs, queries, models, events, mail, notifications, cache, dan Redis.
- **Pendaftaran Sendiri (Self-Registration)**  
  Staf MOTAC boleh mendaftar akaun menggunakan e-mel `@motac.gov.my`. Pengesahan e-mel diperlukan sebelum akses penuh. Selepas pendaftaran, staf boleh log masuk menggunakan e-mel penuh (`user@motac.gov.my`) atau nama pengguna pendek (`user`).
- **Google Workspace SSO (Opsyen)**  
  Staf MOTAC boleh log masuk menggunakan akaun Google Workspace `@motac.gov.my` sebagai alternatif kepada Laravel Breeze. Sistem akan auto-create akaun baharu atau link ke akaun sedia ada.
- **Queue Management (D17)**  
  Laravel Queue dengan Redis backend untuk pemprosesan asinkron (**Laravel Horizon 5.41.0 dipasang** dengan dashboard `/horizon`). Queue name yang digunakan oleh job sebenar termasuk `default`, `notifications`, `emails`, `digests`, `documents`, `embeddings`, dan `auto-reply`. Rujuk D17 v3.6.1 untuk arahan worker dan katalog job.

### 4.2. Database Design

**Database Engine**: MySQL 8.0 / MariaDB 10.6+

**Model Eloquent Utama** (berdasarkan `application-info`):

- **`User`** (`App\Models\User`)  
  Menyimpan akaun `staff`, `admin` dan `superuser`. Medan `role` diset `staff`, `admin` atau `superuser`. Staf MOTAC yang log masuk ke My Dashboard direkod sebagai `staff`, manakala tetamu kekal tanpa akaun (submissions dengan `user_id` = NULL).
- **`HelpdeskTicket`** (`App\Models\HelpdeskTicket`)  
  Menyimpan data tiket; `user_id` adalah nullable. Data tetamu disimpan dalam kolum `guest_*` (contoh `guest_email`) dan diakses di aplikasi melalui accessor `submitter_*` untuk konsistensi UI/komponen.
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
| AI Assistant (Cloud Hybrid) | Cloud Hybrid AI dengan model routing pintar, streaming responses, web-augmented answers, conversation management, document analysis, auto-reply generation | Ollama + AWS Bedrock; `admin` urus model configuration, conversation analytics, FAQ management, health monitoring melalui Filament (D18 v1.0.1) |
| Asset Management           | Pengurusan lifecycle aset: registration, tracking, maintenance, transfers     | `admin` urus aset inventory, preventive maintenance schedules, inter-department transfers                |
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

- Mematuhi D12–D14 serta bukti ujian aksesibiliti (rujuk `tests/e2e/accessibility.comprehensive.spec.ts` dan `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`).
- Palet baharu: Primary `#0056B3`, Secondary `#0B4D8F`, Success `#1B7C54`, Warning `#CC7700`, Danger `#B3002D`.
- Inline focus ring 3px warna `#0B4D8F`, jarak minimum 16px.
- Layout asas `resources/views/layouts/guest.blade.php` menggunakan `aria` landmarks (`header`, `main`, `footer`, `nav`).
- **Bahasa Melayu sahaja** (v3.6.0): Sistem menggunakan Bahasa Melayu secara eksklusif untuk semua antara muka pengguna. Language switcher telah dilumpuhkan dan semua komponen UI menggunakan `lang="ms"`. Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal sahaja. Komponen yang dilumpuhkan: `LanguageSwitcher`, `BilingualSupportService`, `SetLocale` middleware, `ictserve_locale` cookie (rujuk D15 v3.6.0).
- Semua komponen diuji melalui suite E2E aksesibiliti serta audit prestasi (rujuk `tests/e2e/accessibility.comprehensive.spec.ts`, `tests/e2e/performance/core-web-vitals.spec.ts`).

### 9.2. Ciri-ciri Utama

- Navigasi breadcrumb ringkas tanpa menu pengguna.
- Borang berbilang langkah dengan status indicator.
- Komponen tetingkap modal untuk status kelulusan (untuk tetamu) dengan tumpuan (focus trap) mematuhi ARIA.

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

## 10. Komunikasi Masa Nyata & Penyiaran (Real-time Communication & Broadcasting) - D16 v3.6.0

### 10.1. Seni Bina Penyiaran (Broadcasting Architecture)

**Laravel Reverb WebSocket Server:**

- **Penyedia Utama**: Laravel Reverb 1.6.3 sebagai WebSocket server rasmi Laravel
- **Konfigurasi**: `BROADCAST_CONNECTION=reverb` dengan sokongan horizontal scaling via Redis
- **Performance**: Dioptimumkan untuk 100+ concurrent connections dengan low latency
- **Security**: Private channel authorization dengan token-based access untuk guests

### 10.2. Strategi Saluran Dwi (Dual Channel Strategy)

**True Hybrid Architecture Support:**

- **Authenticated Users**: Listen pada `private-user.{userId}` untuk personalized notifications
- **Guest Users**: Listen pada `private-ticket.{ticketUuid}` atau `private-loan.{loanUuid}` dengan status token authorization
- **AI Conversations**: Listen pada `private-conversation.{conversationUuid}` untuk AI streaming responses

### 10.3. Acara Penyiaran Sedia Ada (Broadcasting Events)

**Core System Events:**

| Acara | Saluran Auth | Saluran Guest | Tujuan |
|-------|-------------|---------------|---------|
| `NotificationCreated` | `private-user.{id}` | `private-ticket.{uuid}` | Notifikasi baharu |
| `StatusUpdated` | `private-user.{id}` | `private-ticket.{uuid}` | Kemaskini status |
| `CommentPosted` | `private-user.{id}` | `private-submission.{type}.{id}` | Ulasan baharu |
| `AssetReturnedDamaged` | `private-user.{id}` | `private-loan.{uuid}` | Aset rosak |
| `EmailVerified` | `private-user.{id}` | N/A | Email disahkan |
| `AccountLinked` | `private-user.{id}` | N/A | Akaun dipautkan |

**AI Real-time Events (D18 Integration):**

| Acara AI | Saluran Auth | Saluran Guest | Tujuan |
|----------|-------------|---------------|---------|
| `AiStreamingStarted` | `private-user.{id}` | `private-conversation.{uuid}` | AI streaming dimulakan |
| `AiStreamingChunk` | `private-user.{id}` | `private-conversation.{uuid}` | Chunk respons AI |
| `AiStreamingCompleted` | `private-user.{id}` | `private-conversation.{uuid}` | AI streaming selesai |
| `AiModelSwitched` | `private-user.{id}` | `private-conversation.{uuid}` | Model AI ditukar |
| `AiWebSearchStarted` | `private-user.{id}` | `private-conversation.{uuid}` | Web search dimulakan |
| `AiErrorOccurred` | `private-user.{id}` | `private-conversation.{uuid}` | Ralat AI berlaku |

### 10.4. Integrasi Frontend (Frontend Integration)

**Laravel Echo Configuration:**

- **Reverb Primary**: Auto-detection dengan fallback ke Pusher
- **Client Libraries**: Laravel Echo 2.2.6 + Pusher-JS untuk WebSocket communication
- **Event Handling**: Livewire v3 integration dengan `#[On]` attributes untuk real-time updates
- **Error Handling**: Automatic reconnection dengan exponential backoff

**JavaScript Implementation:**

```javascript
// Authenticated users
if (window.userId) {
  window.Echo.private(`user.${window.userId}`)
    .listen('.notification.created', handleNotification)
    .listen('.status.updated', updateStatus);
}

// Guest users dengan token authorization
if (window.ticketUuid && window.statusToken) {
  window.Echo.private(`ticket.${window.ticketUuid}`)
    .listen('.notification.created', handleNotification)
    .listen('.status.updated', updateStatus);
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

| Pekerjaan | Tujuan | Hybrid Support |
|-----------|---------|----------------|
| `SendTicketCreatedEmail` | Email pengesahan tiket | Conditional: DB+Email or Email-only |
| `SendLoanApprovedEmail` | Email kelulusan pinjaman | Conditional: DB+Email or Email-only |
| `SendAssetOverdueEmail` | Email peringatan tertunggak | Conditional: DB+Email or Email-only |
| `RetryFailedEmail` | Retry email gagal | Email-only retry mechanism |
| `ExportSubmissionsJob` | Export data submissions | Authenticated users only |

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

**Hybrid Notification Logic:**

- **Authenticated Users**: Database Notification + Email via `User::notify()`
- **Guest Users**: Email Only via `Mail::to()->send()`
- **Decision Tree**: Check `user_id` existence untuk determine notification channels

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
| SMTP / GOV Mail                | Penghantaran e-mel tetamu & pautan kelulusan | Laravel queue + MOU BPM                                                     |
| SMS Gateway BPM                | Peringatan due date & OTP (opsyen)           | REST API (token service)                                                    |
| MyIdentity (Opsyen Masa Depan) | Pengesahan identiti pegawai Gred 41          | Belum diaktifkan; memerlukan MAMPU clearance                                |
| Google Workspace SSO (Opsyen)  | Log masuk menggunakan akaun Google @motac.gov.my | Laravel Socialite v5.x OAuth 2.0                                         |
| API External (Future-Ready)    | Integrasi dengan aplikasi mobile/luaran      | Laravel Sanctum v4.0 token authentication                                   |
| Tiada LDAP                     | Not applicable                               | Semua tetamu tanpa log masuk; admin Filament guna credential dalaman atau Google SSO |

Migrasi data daripada sistem terdahulu melibatkan import rekod tiket & pinjaman ke jadual baharu dengan memetakan `staff_no` lama kepada metadata tetamu (rujuk D05 & D06). Tiada migrasi akaun pengguna.

---

## 13. Pematuhan Piawaian (Standards Compliance)

- **WCAG 2.2 AA** – Lihat `docs/D12_UI_UX_DESIGN_GUIDE.md`, `docs/D14_UI_UX_STYLE_GUIDE.md`, dan bukti ujian di `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`.
- **Performance Optimisation** – Rujuk `docs/reference/performance-optimization-guide.md` serta ujian `tests/e2e/performance/core-web-vitals.spec.ts`.
- **Security & Audit** – D09 (audit trail), D11 §8 (security design).
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
- **Object Storage**: MinIO/S3 untuk lampiran tetamu dengan polisi retention
- **WebSocket**: Laravel Reverb untuk real-time communication

### 13a.2. Keselamatan Penempatan (Deployment Security)

- Enforce HTTPS + HSTS
- WAF menapis trafik robot/spam ke borang tetamu
- Secrets diurus melalui `.env` & Azure Key Vault (perancangan)
- Audit log disalurkan ke SIEM BPM setiap 15 minit
- Docker secrets management untuk sensitive data
- Rate limiting pada API endpoints dan form submissions

---

## 14. Glosari & Rujukan (Glossary & References)

### 14.1. Istilah Utama

| Istilah                  | Takrif                                                                      |
| ------------------------ | --------------------------------------------------------------------------- |
| **Tetamu (Guest)**       | Pengguna awam yang mengisi borang tanpa log masuk.                          |
| **Admin**                | Pegawai BPM yang memproses tiket & permohonan melalui Filament.             |
| **Superuser**            | Pegawai pengurusan BPM yang mentadbir konfigurasi, keselamatan, dan audit.  |
| **Signed Approval Link** | Pautan e-mel ber-token yang membolehkan kelulusan tanpa log masuk.          |
| `resources/views/layouts/guest.blade.php`      | Layout utama untuk semua paparan tetamu.                                    |
| **Livewire**             | Full-stack framework untuk dynamic interfaces dengan server-side rendering. |
| **Volt**                 | Single-file Livewire components dengan simplified syntax.                   |
| **Filament**             | Server-Driven UI (SDUI) framework untuk admin panel.                        |
| **Reverb**               | Laravel WebSocket server untuk real-time communication.                     |
| **Pulse**                | Laravel performance monitoring dashboard untuk admin/superuser.             |
| **Sanctum**              | Laravel API token authentication system.                                    |
| **Socialite**            | Laravel OAuth 2.0 library untuk Google Workspace SSO.                       |
| **API Token**            | Token Sanctum untuk akses API dengan abilities dan expiration.              |
| **Broadcasting**         | Laravel real-time event system menggunakan WebSocket.                       |
| **Queue Worker**         | Background process yang memproses queued jobs secara asinkron.              |
| **Private Channel**      | WebSocket channel dengan authorization untuk authenticated/guest users.     |
| **Dual Channel Strategy** | Hybrid approach: private-user.{id} untuk auth, private-ticket.{uuid} untuk guest. |
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

Peralihan kepada seni bina hybrid (guest-first + portal staf) memastikan ICTServe memenuhi mandat BPM untuk menyediakan perkhidmatan digital yang boleh diakses umum sambil mengekalkan kawalan ketat di peringkat pentadbiran. Staf MOTAC yang berdaftar boleh log masuk melalui Laravel Breeze atau Google Workspace SSO (opsyen) untuk menggunakan My Dashboard, manakala tetamu kekal menggunakan borang tetamu sebagai pintu masuk utama. Akaun pentadbir kekal terhad kepada `admin` dan `superuser` dengan 2FA berasaskan TOTP untuk `superuser`, dan semua interaksi penting direkodkan dalam jejak audit.

Sistem kini dilengkapi dengan Laravel Pulse untuk pemantauan prestasi masa nyata, Laravel Sanctum untuk API authentication (future-ready untuk aplikasi mobile), dan Laravel Socialite untuk Google Workspace SSO sebagai alternatif log masuk.

**Kemaskini v3.6.0** memperkenalkan implementasi **Bahasa Melayu sahaja** (D15) dengan language switcher dilumpuhkan, **Laravel Reverb WebSocket** (D16) untuk komunikasi masa nyata dengan dual channel strategy, dan **sistem queue management** (D17) yang komprehensif dengan sokongan 29+ jenis pekerjaan termasuk 12 AI-specific jobs. Real-time broadcasting menyokong AI streaming responses, notification updates, dan status changes untuk kedua-dua authenticated users dan guests.

Kesemua ciri baharu ini menyokong visi Digital MOTAC 2025 dan pematuhan MyGOV Digital Service Standards v2.1.0 dengan penekanan kepada pengalaman pengguna yang responsif, komunikasi masa nyata, dan pemprosesan latar belakang yang cekap.
