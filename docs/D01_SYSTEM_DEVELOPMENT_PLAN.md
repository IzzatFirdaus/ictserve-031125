# Pelan Pembangunan Sistem (System Development Plan - SDP)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.6.1                                     |
| **Tarikh Kemaskini** | 17 Disember 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO/IEC/IEEE 12207                        |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0+)            |

> Notis Penggunaan Dalaman: Pelan ini dirangka untuk sistem dalaman MOTAC (bukan untuk kegunaan awam).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.6.1 | 17 Disember 2025 | **Kemaskini Teknologi Stack**: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. **Cloud Hybrid AI Integration**: Kemaskini pelan pembangunan dengan integrasi D18 Cloud Hybrid Architecture (Ollama + AWS Bedrock). Tambah fasa pembangunan AI: model routing pintar, streaming responses, web-augmented responses, conversation management. Kemaskini metodologi pembangunan dengan AI-enhanced workflows dan testing strategies. Cross-reference D18 v1.0.1. | Pasukan BPM |
| 3.6.0 | 8 Disember 2025  | **Penyeragaman Dokumentasi v3.6.0:** Bahasa Melayu sahaja untuk antara muka pengguna. Language switcher dilumpuhkan (kod dikekalkan sebagai komen). Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal. Penyeragaman versi semua dokumen D00-D18 kepada v3.6.0 dengan integrasi Cloud Hybrid AI (D18 v1.0.0). Penyelarasan dokumentasi lengkap. | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only). Penambahan Laravel Pulse v1.3.0 (performance monitoring), Laravel Sanctum v4.0 (API authentication), Laravel Socialite v5.x (Google Workspace SSO opsyen). Spec files: 38 requirements, 100 correctness properties, 19 implementation phases. | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Staf boleh log masuk (Laravel Breeze - akaun pangkalan data) untuk Dashboard/Profile ATAU gunakan borang tetamu. Nullable user_id FK dalam tickets/loans. Penyelarasan dengan D00/D02/D03/D04 v3.4.0.                                                | Pasukan BPM |
| 3.2.1 | 29 November 2025 | Penjajaran kepada seni bina "Guest-First": Staf menggunakan borang tetamu (tanpa log masuk). Authentication terhad kepada admin/superuser sahaja. Penyelarasan dengan D00 v3.2.1 dan D04 v3.2.1.                                                                          | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.43.1, PHP 8.2.12, Livewire 3.7.3, Filament 4.3.1, PHPUnit 11.5.46, Larastan 3.8.1, Laravel Pint 1.26.0). Penyelarasan dengan D00 v3.2.0.                                                     | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini tarikh semasa, perbaiki isu markdownlint, tambah best practices Laravel 12 (streamlined structure, attribute-based observers/scopes), kemaskini testing framework versions.                                                                                     | Pasukan BPM |
| 3.0.0 | 31 Oktober 2025  | Kemaskini stack teknologi: Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.18, Laravel Reverb 1.6.3. Tambah Docker development environment, real-time communication, enhanced testing framework.                             | Pasukan BPM |
| 2.0.0 | 17 Oktober 2024  | Penyeragaman mengikut D00-D14, SemVer, cross-reference, tambah rujukan dokumen                                                                                                                                                                                            | Pasukan BPM |
| 1.0.0 | September 2024   | Versi awal pelan pembangunan sistem                                                                                                                                                                                                                                       | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perniagaan
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D15_LANGUAGE_MS_EN.md]** - Language Implementation (Bahasa Melayu sahaja v3.6.0+)
- **[D16_BROADCASTING_SETUP.md]** - Real-time Broadcasting & WebSocket Setup
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue Management & Background Jobs
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Cloud Hybrid AI Architecture (v1.0.1)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini bertujuan memberi perancangan lengkap dan terperinci bagi pembangunan sistem **Helpdesk & ICT Asset Loan** berasaskan Laravel 12.43.1 untuk penggunaan dalaman MOTAC BPM, mematuhi piawaian antarabangsa **ISO/IEC/IEEE 12207** untuk lifecycle pembangunan perisian (software lifecycle processes) dan **WCAG 2.2 AA** untuk aksesibiliti.

---

## 2. SKOP PROJEK (Project Scope)

### 2.1. Skop Sistem

- Sistem web berasaskan **Laravel 12.43.1** dengan **Livewire 3.7.3**, **Filament 4.3.1**, dan **Volt 1.10.1** untuk pengurusan tiket aduan ICT & permohonan pinjaman aset ICT.
- **Pengguna Sasaran:** Staf MOTAC, Pegawai ICT BPM, Ketua Bahagian, Admin BPM.
- **Platform:** Web-based intranet MOTAC (akses dalaman sahaja).
- **Stack Teknologi:** PHP 8.2.12, MySQL 8.0, Alpine.js 3, Tailwind CSS 4.1.18, Laravel Reverb 1.6.3, Laravel Pulse 1.4.7, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, Laravel MCP 0.3.4, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0.

### 2.2. Modul Utama

1. **Helpdesk Ticketing** - Pengurusan aduan dan masalah ICT dengan internal comments
2. **Asset Loan** - Permohonan dan pengurusan pinjaman peralatan ICT dengan dual approval
3. **Inventory Management** - Pengurusan inventori aset ICT
4. **Authentication & Authorization** - Hybrid login: Staff optional (Laravel Breeze), Admin/Superuser required (Laravel Breeze 2.3.8), role-based access control (Spatie Permissions), Google Workspace SSO (opsyen via Laravel Socialite v5.x), API token authentication (Laravel Sanctum v4.0).
5. **Reporting & Dashboard** - Laporan dan analitik dengan Filament widgets
6. **Audit Trail** - Logging dan audit compliance dengan owen-it/laravel-auditing
7. **Real-time Communication** - WebSocket dengan Laravel Reverb 1.6.3 dan Laravel Echo 2.2.6
8. **Performance Monitoring** - Laravel Pulse v1.4.7 untuk real-time performance dashboard (admin/superuser)
9. **API Authentication** - Laravel Sanctum v4.2.1 untuk token-based API access (future mobile/external integrations)
10. **Cloud Hybrid AI** - Ollama (local) + AWS Bedrock (cloud) untuk FAQ Bot, Document Analysis, Auto-Reply (D18 v1.0.0)

**Rujukan:** Lihat **[D00_SYSTEM_OVERVIEW.md]** untuk ringkasan modul dan **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** untuk spesifikasi fungsional lengkap.

---

## 3. ORGANISASI PROJEK (Project Organization)

| Peranan                | Tanggungjawab Utama                                     |
| ---------------------- | ------------------------------------------------------- |
| **Project Owner**      | Bahagian Pengurusan Maklumat (BPM), MOTAC               |
| **Project Manager**    | Menyelia jadual, milestone, komunikasi                  |
| **System Analyst**     | Analisis keperluan, dokumen spesifikasi                 |
| **Lead Developer**     | Reka bentuk, pantau kod, deployment                     |
| **Frontend Developer** | UI/UX, Livewire/Volt, Tailwind CSS, Filament, Alpine.js |
| **Backend Developer**  | API, Eloquent, logic sistem, Laravel Reverb             |
| **QA/Test Engineer**   | PHPUnit, Playwright E2E, accessibility testing          |
| **DevOps Engineer**    | Docker, deployment, monitoring                          |
| **End User**           | Staf MOTAC, Pegawai BPM ICT                             |

---

## 4. PROSES PEMBANGUNAN (Development Processes)

Mematuhi ISO/IEC/IEEE 12207 lifecycle:

### 4.1. Inisiasi & Keperluan (Initiation & Requirements)

- **Pengumpulan Keperluan**: Interview pengguna utama, BPM, review dokumen borang aduan kerosakan & permohonan pinjaman.
- **Dokumentasi**: Spesifikasi keperluan fungsional & bukan fungsional.

### 4.2. Rekabentuk Sistem (System Design)

- **Architecture**: MVC + SDUI (Server-Driven UI) dengan Filament 4.3.1, modular, scalable.
- **Database Design**: Entity Relationship Diagram (ERD) untuk users, tickets, assets, loans. MySQL 8.0 dengan foreign key constraints.
- **Interface Design**: Wireframe untuk semua modul utama, responsive dengan Tailwind CSS 4.1.18, komponen Filament, dan Alpine.js 3.
- **Real-time Design**: WebSocket architecture dengan Laravel Reverb untuk live updates.

### 4.3. Pembangunan (Implementation)

- **Setup Environment**: Docker Compose untuk development (app + db containers), Vite 7.0.7 untuk HMR.
- **Authentication**: Laravel Breeze 2.3.8 untuk staff, admin, dan superuser (akaun pangkalan data).
  - **Self-Registration**: Staf boleh mendaftar dengan e-mel @motac.gov.my
  - **Login Flexibility**: E-mel penuh ATAU nama pengguna pendek (selepas pendaftaran)
  - **Email Verification**: Diperlukan sebelum akses penuh
  - **Google Workspace SSO (Opsyen)**: Laravel Socialite v5.x untuk OAuth 2.0 dengan Google (@motac.gov.my sahaja)
  - Spatie Laravel Permission 6.23 untuk role-based access control
- **API Authentication**: Laravel Sanctum v4.0 untuk token-based API access
  - Configurable abilities: `read:tickets`, `write:tickets`, `read:loans`, `write:loans`, `admin:all`
  - Token expiration management dan usage logging
  - Rate limiting 60 requests/minute untuk API endpoints
- **Laravel 12 Architecture**:
  - Struktur ringkas (Laravel 12): Tiada Kernel HTTP dalam `app/`, middleware didaftarkan dalam `bootstrap/app.php`
  - Struktur ringkas (Laravel 12): Tiada Kernel Console dalam `app/`, commands auto-register dari `app/Console/Commands/`
  - Service providers in `bootstrap/providers.php`
  - Attribute-based observers: `#[ObservedBy]` untuk model events
  - Attribute-based scopes: `#[ScopedBy]` dan `#[Scope]` untuk query scopes
- **CRUD Modules**:
  - Helpdesk Tickets dengan HelpdeskComment model
  - Asset Loans dengan LoanApplication model
  - Internal Comments untuk komunikasi staf
- **Livewire Components**: Reactive components dengan Livewire 3.7.3 + Volt 1.10.1 untuk single-file components.
- **Filament Resources**: Admin panel dengan Filament 4.3.1 untuk CRUD operations.
- **Relational Models**: Eloquent relationships (User, HelpdeskTicket, HelpdeskComment, LoanApplication), Soft Deletes, UUID/ULID support.
- **Real-time Features**: Laravel Reverb 1.6.3 + Laravel Echo 2.2.6 untuk WebSocket communication.
- **Audit Trail (Dual System)**:
  - `owen-it/laravel-auditing` v14.x untuk field-level audit trail (compliance, PDPA)
  - `spatie/laravel-activitylog` v4.x untuk user activity logging (operations, dashboard)
  - Laravel Telescope v5.x untuk debugging (superuser only, unrestricted)
- **Performance Monitoring**:
  - Laravel Pulse v1.4.7 untuk real-time performance dashboard (admin/superuser)
  - Slow query tracking (>500ms threshold), queue job metrics, request response times
  - Server health metrics (CPU, memory, disk), cache hit/miss rates
  - 7-day data retention dengan automatic pruning
- **Notification & Queue**: Email notifications, database notifications, Laravel queue dengan database driver.

### 4.4. Ujian (Testing)

- **Unit Testing**: PHPUnit 11.5.46 untuk model, service classes, validation.
- **Feature Testing**: Laravel testing untuk workflow penuh borang aduan & pinjaman.
- **E2E Testing**: Playwright untuk end-to-end testing dengan browser automation.
- **Accessibility Testing**: Axe-core 4.11.0 untuk WCAG 2.2 AA compliance.
- **Static Analysis**: Larastan 3.8.1 (PHPStan untuk Laravel) untuk type safety.
- **Code Quality**: Laravel Pint 1.26.0 untuk PSR-12 compliance.
- **User Acceptance Testing (UAT)**: Bersama BPM & staf MOTAC.

### 4.5. Deployment & Maintenance

- **Development**: Docker Compose dengan multi-container setup (app: PHP 8.2-FPM + Nginx, db: MySQL 8.0).
- **Production Deployment**: Server intranet MOTAC (Linux, Nginx/Apache), PHP-FPM 8.2.12.
- **Asset Building**: Vite build untuk production assets dengan optimization.
- **WebSocket Server**: Laravel Reverb deployment untuk real-time features.
- **Maintenance Mode**: php artisan down/up untuk update, backup berkala.
- **Performance Optimization**:
  - php artisan config:cache, route:cache, view:cache, optimize
  - Vite asset optimization dengan code splitting
  - Database query optimization dengan eager loading
- **Log & Monitoring**: storage/logs, audit trail dengan owen-it/laravel-auditing, backup DB automatik.
- **Security**: HTTPS enforcement, CSRF protection, rate limiting, input sanitization.

---

## 5. STANDARD & BEST PRACTICES

- **Pematuhan ISO/IEC/IEEE 12207**: Setiap fasa documented & traceable.
- **Pematuhan WCAG 2.2 AA**: Accessibility compliance untuk semua UI components.
- **Security**:
  - CSRF protection untuk semua forms
  - Input validation dengan Laravel Form Requests
  - Role-based access control dengan Spatie Laravel Permission 6.23
  - Rate limiting untuk API endpoints
  - Secure environment configuration
- **Code Standards**:
  - PSR-12 compliance dengan Laravel Pint 1.26.0
  - Type safety dengan Larastan 3.8.1 (Level 9)
  - Strict typing: `declare(strict_types=1);` untuk semua PHP files
  - Constructor property promotion untuk dependency injection
  - Match expressions untuk value returns
  - Explicit return type declarations untuk semua methods
- **Documentation**:
  - PHPDoc blocks untuk semua public methods
  - Kod sumber dengan inline comments
  - Dokumen teknikal lengkap (D00-D14)
  - API documentation
- **Quality Assurance**:
  - Unit tests dengan PHPUnit 11.5.46
  - E2E tests dengan Playwright
  - Accessibility tests dengan Axe-core
  - Code review untuk setiap pull request
  - Continuous integration dengan automated testing

---

## 6. JADUAL & MILESTONE (Schedule & Milestones)

| Fasa                 | Tempoh     | Deliverable                                          |
| -------------------- | ---------- | ---------------------------------------------------- |
| Inisiasi & Keperluan | 2 minggu   | D02, D03, ERD                                        |
| Rekabentuk Sistem    | 2 minggu   | D04, Wireframe, Database Schema                      |
| Setup Development    | 1 minggu   | Docker environment, CI/CD pipeline                   |
| Pembangunan Core     | 4 minggu   | Authentication, Models, Migrations                   |
| Pembangunan Modules  | 6 minggu   | Helpdesk, Asset Loan, Filament Admin                 |
| Real-time Features   | 2 minggu   | Laravel Reverb integration                           |
| Performance & API    | 2 minggu   | Laravel Pulse, Sanctum API, Google SSO (opsyen)      |
| UI/UX Implementation | 3 minggu   | Livewire components, Tailwind styling, Accessibility |
| Ujian & UAT          | 3 minggu   | PHPUnit, Playwright, Accessibility tests, UAT        |
| Documentation        | 2 minggu   | D09-D14, User Manual, API docs                       |
| Deployment           | 1 minggu   | Production deployment, monitoring setup              |
| Maintenance          | Berterusan | Patch, backup, support, monitoring                   |

### 6.1. Fasa Pembangunan AI (AI Development Phases)

> **Trace:** D18 v1.0.1 (Cloud Hybrid AI Architecture), 13 fasa pelaksanaan komprehensif

| Fasa AI              | Tempoh     | Deliverable                                          | Status      |
| -------------------- | ---------- | ---------------------------------------------------- | ----------- |
| **Fasa 1:** Asas & Infrastruktur | 1 minggu | OllamaClient, config/ollama.php, health checks | ✅ Selesai |
| **Fasa 2:** Skema Pangkalan Data | 1 minggu | Faq, Document, MessageLog models, migrations | ✅ Selesai |
| **Fasa 3:** Core AI Services | 2 minggu | RagService, DocumentService, EmbeddingService | ✅ Selesai |
| **Fasa 4:** Background Jobs | 1 minggu | DocumentIngestJob, EmbeddingJob, queue setup | ✅ Selesai |
| **Fasa 5:** API Endpoints | 1 minggu | FaqController, DocumentController, routes | ✅ Selesai |
| **Fasa 6:** Filament Admin | 1 minggu | FaqResource, DocumentResource, widgets | ✅ Selesai |
| **Fasa 7:** Security & Compliance | 1 minggu | PIIDetectionService, Policies, audit | ✅ Selesai |
| **Fasa 8:** Livewire Components | 1 minggu | FaqBot, FaqBotWidget, chat interface | ✅ Selesai |
| **Fasa 9:** Email Notifications | 1 minggu | ApprovalEmailToken, signed URLs | ✅ Selesai |
| **Fasa 10:** Performance Optimization | 1 minggu | Redis caching, Laravel Pulse integration | ✅ Selesai |
| **Fasa 11:** Testing & Documentation | 1 minggu | PHPUnit 12 tests, API documentation | ✅ Selesai |
| **Fasa 12:** Deployment & Monitoring | 1 minggu | Health checks, alerting, production setup | ✅ Selesai |
| **Fasa 13:** Cloud Hybrid AI (Bedrock) | 2 minggu | BedrockService, ModelRouter, hybrid responses | ✅ Selesai |

**Total Tempoh AI:** 14 minggu (termasuk dalam jadual utama)

---

## 7. RISIKO & MITIGASI (Risks & Mitigation)

| Risiko                        | Strategi Mitigasi                                     |
| ----------------------------- | ----------------------------------------------------- |
| Kelewatan keperluan pengguna  | Weekly review, early prototype dengan Livewire        |
| Perubahan scope               | Change request & impact analysis, SemVer              |
| Isu integrasi sistem legacy   | Early integration testing, API documentation          |
| Kerosakan/kehilangan data     | Automated backup, audit trail, soft deletes           |
| Masalah keselamatan           | Security audit, dependency updates, Larastan analysis |
| Kekurangan dokumentasi        | Dedicated documentation phase (D00-D14)               |
| Performance issues            | Load testing, query optimization, caching             |
| Accessibility non-compliance  | Automated Axe-core testing, manual WCAG audit         |
| Docker environment issues     | Documented setup scripts, fallback to manual setup    |
| Real-time connection failures | Graceful degradation, polling fallback                |
| API token misuse              | Rate limiting, token expiration, usage logging        |
| Google SSO unavailability     | Fallback to Laravel Breeze login                      |
| Performance monitoring gaps   | Laravel Pulse dengan 7-day retention, alerts          |
| **AI Service Failures**       | **Multi-system fallback: Ollama → Bedrock → static FAQ** |
| **AWS Bedrock Cost Overrun**   | **Cost monitoring, rate limiting, Ollama prioritization** |
| **Ollama Server Crashes**      | **Health monitoring, auto-restart, graceful degradation** |
| **Model Access Denied**        | **Inference profile format, model access verification** |
| **Data Residency Violations**  | **Automatic data classification, local-first processing** |
| **AI Response Quality Issues** | **Content filtering, confidence scoring, human review** |
| **Vector Embedding Failures**  | **Fallback to keyword search, embedding regeneration** |
| **MCP Server Integration**      | **Standardized interface, error handling, monitoring** |

---

## 8. KAWALAN KUALITI & PEMATUHAN (Quality Control & Compliance)

### 8.1. Piawaian Kualiti

- **ISO 9001**: Quality Management System
- **ISO/IEC/IEEE 12207**: Software lifecycle processes
- **ISO 8000**: Data quality
- **ISO/IEC 27701**: Privacy information management

### 8.2. Proses Kawalan Kualiti

- **Code Review**: Peer review untuk setiap pull request
- **Testing**: Unit test, integration test, UAT
- **Documentation**: Lengkap dan up-to-date
- **Security Audit**: Regular security assessment
- **Performance Monitoring**: Continuous monitoring

**Rujukan:** Lihat **[D10_SOURCE_CODE_DOCUMENTATION.md]** untuk piawaian kod dan **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** untuk kawalan kualiti teknikal.

---

## 9. DOKUMENTASI PROJEK (Project Documentation)

### 9.1. Dokumentasi Wajib

Semua dokumentasi mesti disediakan mengikut standard D00-D14:

- D00: System Overview
- D01: System Development Plan (dokumen ini)
- D02: Business Requirements Specification
- D03: Software Requirements Specification
- D04: Software Design Document
- D05-D06: Data Migration Plan & Specification
- D07-D08: System Integration Plan & Specification
- D09: Database Documentation
- D10: Source Code Documentation
- D11: Technical Design Documentation
- D12-D14: UI/UX Documentation

### 9.2. Dokumen Sokongan

- User Manual
- Administrator Guide
- API Documentation
- Deployment Guide
- Troubleshooting Guide

---

## 9.3. Pengurusan Perubahan (Change Management Process)

Semua perubahan ke sistem, dokumentasi, atau konfigurasi mesti melalui proses pengurusan perubahan formal untuk memastikan traceability, quality assurance, dan audit compliance mengikut **ISO/IEC/IEEE 12207:2017, Section 7.3** (Change Management).

### 9.3.1. Langkah-langkah Pengurusan Perubahan (Change Management Workflow)

| Langkah                 | Aktiviti                                                 | Pihak Bertanggungjawab | Kriteria Kelulusan     | Dokumentasi    |
| ----------------------- | -------------------------------------------------------- | ---------------------- | ---------------------- | -------------- |
| 1. Permohonan Perubahan | Cipta Change Request (CR) dengan deskripsi, impact, risk | Pembangun/PM           | CR form lengkap        | CR ticket      |
| 2. Penilaian Impact     | Analisis dampak teknikal, jadual, sumber daya            | Technical Lead         | Impact documented      | Impact report  |
| 3. Kelulusan Teknikal   | Semak CR, persetujuan teknikal atau penolakan            | Lead Developer         | Teknikally sound       | Approval note  |
| 4. Kelulusan Pengurusan | Kelulusan akhir daripada Project Manager                 | Project Manager        | Schedule OK, budget OK | PM sign-off    |
| 5. Pelaksanaan          | Laksanakan perubahan mengikut CR detail, test, document  | Developer              | Code review pass       | Commit message |
| 6. Ujian & Validasi     | Ujian regresi, UAT jika perlu                            | QA/Tester              | All tests pass         | Test report    |
| 7. Deployment           | Deploy ke staging/production dengan runbook              | DevOps                 | Deployment checklist   | Deployment log |
| 8. Dokumentasi          | Kemas kini D00-D14 dokumentasi & RTM jika perlu          | Technical Writer       | Doc aligned with code  | Updated doc    |
| 9. Penutupan CR         | Tutup CR dengan nota penutupan & lessons learned         | PM                     | All steps complete     | CR closed      |

### 9.3.2. Jenis-jenis Perubahan (Change Categories)

- **Critical**: Perubahan keselamatan, compliance, data integrity → Perlu pengulusan dalam 24 jam, UAT wajib
- **Major**: Perubahan fungsional besar → Kelulusan dalam 48 jam, regression test wajib
- **Minor**: Bug fixes, dokumentasi → Kelulusan dalam 1 minggu, unit test wajib
- **Trivial**: Typo, format → Auto-approve untuk tier teknikal, dokumentasi

### 9.3.3. Rollback Plan

Semua perubahan major/critical mesti ada rollback plan tertulis:

- Deskripsi langkah-langkah rollback
- Testing rollback di staging sebelum go-live
- Contact person untuk escalation sekiranya rollback diperlukan

**Rujukan**: Lihat **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** §10 untuk testing strategy dan §11 untuk deployment procedures yang complementary dengan proses ini.

---

## 10. GLOSARI & RUJUKAN (Glossary & References)

Untuk memahami istilah teknikal yang digunakan dalam dokumen ini:

**Rujukan:** Lihat **[GLOSSARY.md]** untuk definisi lengkap semua istilah, akronim, dan konsep.

### 10.1. Istilah Utama

- **SDP**: System Development Plan
- **SLA**: Service Level Agreement
- **UAT**: User Acceptance Testing
- **MVC**: Model-View-Controller
- **CRUD**: Create, Read, Update, Delete
- **ERD**: Entity Relationship Diagram
- **API**: Application Programming Interface
- **Pulse**: Laravel performance monitoring dashboard
- **Sanctum**: Laravel API token authentication system
- **Socialite**: Laravel OAuth 2.0 library untuk social login
- **SSO**: Single Sign-On

---

## 11. PENUTUP (Conclusion)

Dokumen ini memberi roadmap lengkap dan terperinci untuk membangunkan sistem **Helpdesk & ICT Asset Loan MOTAC BPM** secara teratur, selamat, dan mengikut piawaian **ISO/IEC/IEEE 12207**.

Setiap fasa pembangunan, peranan, deliverable, dan milestone telah dirancang supaya projek dapat:

✅ **Dijalankan secara efisien** dengan jadual yang jelas  
✅ **Memenuhi keperluan kualiti** mengikut standard antarabangsa  
✅ **Mematuhi piawaian keselamatan** dan privasi data  
✅ **Menghasilkan sistem yang scalable** dan maintainable  
✅ **Menyediakan dokumentasi lengkap** untuk maintenance jangka panjang

---

**Dokumen ini disediakan mengikut piawaian ISO/IEC/IEEE 12207 dan akan dikemaskini mengikut keperluan projek.**
