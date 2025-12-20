# Spesifikasi Integrasi Sistem (System Integration Specification - SIS)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 15289, ISO/IEC TS 24748-6

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.6.1                                     |
| **Tarikh Kemaskini** | 17 Disember 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 15289, ISO/IEC TS 24748-6 |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu |

> Notis Penggunaan Dalaman: Spesifikasi integrasi ini adalah untuk kegunaan
> dalaman MOTAC sahaja dan tidak melibatkan API awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. Penyelarasan dengan D00-D18 v3.6.1. | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D07 v3.5.0. Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), Laravel Pulse (performance monitoring), Laravel Sanctum (API authentication), Google SSO (optional), Responsible Officer tracking, Accessory Tracking, Form Reference Codes, MOTAC Branding, Enhanced UX. Hapus rujukan LDAP/SSO legacy. Pematuhan Jabatan Digital Negara. | Pasukan BPM |
| 3.7.0 | 15 Disember 2025 | AI Chatbot Integration: Tambah spesifikasi integrasi AI (Ollama API, AWS Bedrock API, model routing, RAG pipeline, streaming responses, MCP server). Rujukan D18 v1.0.0 Cloud Hybrid AI Architecture.                                                                                                                                                                                                                                                                                                                                                                      | Pasukan BPM |
| 3.4.0 | 30 November 2025 | Hybrid Architecture v3.4.0: Restore LDAP/SSO integration sebagai optional authentication untuk staff. Penyelarasan dengan D00-D08 v3.4.0.                                                                                                                                                                                                                                                                                                                                                                                                                           | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Penyelarasan penuh Guest-First: hapus semua rujukan LDAP/SSO/User Sync. Hanya admin/superuser authenticate.                                                                                                                                                                                                                                                                                                                                                                                                                                                         | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Hapus LDAP/SSO; klarifikasi Guest-First (staf guna guest forms tanpa authentication)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                | Pasukan BPM |
| 2.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.43.1, Filament 4.3.1, Livewire 3.7.3, Tailwind 4.1.18                                                                                                                                                                                                                                                                                                                                                                                                                                                                              | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini teknologi: Laravel Reverb 1.6.3, Laravel Echo 2.2.6 untuk real-time WebSocket                                                                                                                                                                                                                                                                                                                                                                                                                                                                             | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal spesifikasi integrasi sistem                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Ringkasan Sistem
- [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) - Pelan Integrasi Sistem
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Dokumentasi Rekabentuk Teknikal
- [D18_AI_CHATBOT_OLLAMA_BEDROCK.md](D18_AI_CHATBOT_OLLAMA_BEDROCK.md) - AI Chatbot Integration (Cloud Hybrid AI, v1.0.0)
- [GLOSSARY.md](GLOSSARY.md) - Glosari Istilah Sistem

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini mendefinisikan spesifikasi teknikal, keperluan, dan kriteria integrasi
untuk Sistem **Helpdesk & ICT Asset Loan BPM MOTAC**. Spesifikasi ini mengikut
garis panduan dan piawaian **ISO/IEC/IEEE 15288** (system engineering),
**ISO/IEC/IEEE 15289** (system/software documentation), dan **ISO/IEC TS 24748-6**
(lifecycle management guide).

---

## 2. Skop Spesifikasi (Scope)

Meliputi integrasi antara semua modul dalaman:

- Helpdesk Ticketing
- ICT Asset Loan
- Inventory & Asset Management
- Admin Authentication (Laravel Breeze untuk Admin/Superuser sahaja)
- Reporting & Dashboard
- Audit Trail

Meliputi integrasi luaran dengan sistem sedia ada MOTAC:

- Email Server (SMTP untuk notifikasi)
- Sistem Pengurusan Aset Legacy (import data)

**Nota Penting**: Sistem menggunakan True Hybrid Architecture v3.5.0:

- **Staff**: Self-register dengan @motac.gov.my, flexible login (email/username), optional account linking
- **Admin/Superuser**: Log masuk via Laravel Breeze (required)
- **Guest**: Submit melalui guest forms tanpa authentication
- **Google SSO (Optional)**: OAuth 2.0 integration untuk @motac.gov.my domain
- **Tiada LDAP/SSO Legacy**: Semua authentication melalui Laravel Breeze atau Google SSO sahaja
- **Dual Audit**: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Performance Monitoring**: Laravel Pulse untuk admin/superuser
- **API Authentication**: Laravel Sanctum untuk token-based API access
- **Debugging**: Laravel Telescope untuk superuser sahaja (tiada sekatan)
- **Responsible Officer**: Tracking pegawai bertanggungjawab untuk loan applications
- **Accessory Tracking**: Check-out/check-in aksesori dengan discrepancy detection
- **Form Reference Codes**: PK.(S).MOTAC.07.(L1) dan PK.(S).MOTAC.07.(L3)
- **MOTAC Branding**: Government header, logos, visual identity compliance

Menyedia keperluan untuk masa depan: API versioning (/api/v1/), integrasi aplikasi luaran, mobile apps.

---

## 3. Objektif Integrasi (Integration Objectives)

- Semua modul berfungsi sebagai satu sistem bersepadu (seamless).
- Data sentiasa konsisten dan tidak ada duplikasi (data consistency & single
  source of truth).
- Memudahkan interoperability dan pertukaran data antara sistem dalaman MOTAC.
- Memastikan integrasi selamat, boleh diaudit, dan mematuhi dasar serta
  undang-undang berkaitan.

---

## 4. Komponen & Antara Muka Integrasi (Integration Components & Interfaces)

### 4.1. Komponen Dalaman

| Modul                  | Antaramuka Integrasi        | Tujuan                                                              |
| ---------------------- | --------------------------- | ------------------------------------------------------------------- |
| Helpdesk Ticketing     | API dalaman, model relation | Link aduan kerosakan dengan aset pinjaman                           |
| ICT Asset Loan         | API dalaman, model relation | Status aset, automasi tiket maintenance                             |
| Inventory              | API dalaman                 | Data aset, status, penggunaan, sejarah                              |
| Responsible Officer    | Custom Service              | Tracking pegawai bertanggungjawab untuk loan applications           |
| Accessory Tracking     | Custom Service              | Check-out/check-in aksesori dengan discrepancy detection            |
| Staff Authentication   | Laravel Breeze              | Self-registration @motac.gov.my, flexible login, email verification |
| Google SSO (Optional)  | Laravel Socialite           | OAuth 2.0 authentication untuk @motac.gov.my domain                 |
| Account Linking        | Custom Service              | Optional guest-to-account linking based on email matching           |
| Admin Authentication   | Laravel Breeze              | Login Admin/Superuser (required)                                    |
| Audit (Compliance)     | Laravel Auditing            | Field-level audit trail (owen-it/laravel-auditing v14.x)            |
| Audit (Operations)     | Activity Log                | User activity logging (spatie/laravel-activitylog v4.x)             |
| Performance Monitoring | Laravel Pulse               | Real-time performance metrics, server health (admin/superuser)      |
| API Authentication     | Laravel Sanctum             | Token-based API authentication untuk integrasi luaran               |
| Debugging              | Laravel Telescope           | System monitoring, superuser only, unrestricted access              |
| Reporting              | Query, API                  | Laporan rentas modul                                                |
| Audit Trail            | Laravel Auditing, logging   | Audit semua aktiviti integrasi                                      |
| MOTAC Branding         | Blade Components            | Government header, logos, visual identity compliance                |
| Form Reference Codes   | Blade Components            | PK.(S).MOTAC.07.(L1) dan PK.(S).MOTAC.07.(L3) display               |

### 4.2. Komponen Luaran

| Sistem             | Integrasi Dengan    | Mekanisme                                  |
| ------------------ | ------------------- | ------------------------------------------ |
| Email Server       | Notification, Alert | SMTP, Laravel Notification                 |
| Sistem Aset Legacy | Inventory, Loan     | CSV import, ETL, atau API                  |
| Google Workspace   | Authentication      | OAuth 2.0 via Laravel Socialite (optional) |
| reCAPTCHA          | Guest Forms         | Bot protection untuk public forms          |
| ClamAV             | File Upload         | Virus scanning untuk attachments           |

**Nota True Hybrid Architecture v3.5.0**:

- Staff self-register dengan @motac.gov.my dan log masuk via Laravel Breeze (flexible: email penuh ATAU username pendek)
- Optional Google Workspace SSO untuk @motac.gov.my domain (OAuth 2.0)
- Guest forms kekal tersedia untuk quick access (identiti dari input manual)
- Optional account linking untuk guest submissions sedia ada
- **Tiada LDAP/SSO Legacy** - semua authentication melalui Laravel Breeze atau Google SSO sahaja

### 4.3. API & Data Exchange

- Semua pertukaran data (internal & external) guna RESTful API (JSON).
- Endpoint utama didokumenkan (e.g. `/api/v1/assets`, `/api/v1/tickets`, `/api/v1/users`).
- Semua API guna token authentication (Laravel Sanctum Bearer token).
- API versioning menggunakan prefix `/api/v1/` untuk backward compatibility.

---

## 5. Proses & Kaedah Integrasi (Integration Processes & Methods)

### 5.1. Data Mapping & Transformation

- Setiap field modul dipadankan dengan field sistem sedia ada.
- Mapping dokumen disediakan (contoh: `asset_no` lama → `tag_id` baru).
- Data transformation: format tarikh, kod status, normalisasi string.
- **True Hybrid Model v3.5.0**: Staff self-registration data disimpan dalam users table dengan medan baharu (email_verified_at, locale, notify_*, staff_number, guest_submissions_linked, google_id). Guest form data disimpan sebagai field dalam helpdesk_tickets dan loan_applications (user_id=NULL).
- **Responsible Officer Data**: Loan applications menyimpan is_applicant_responsible flag dan responsible_officer_* fields.
- **Accessory Tracking Data**: loan_transaction_accessories table untuk tracking aksesori check-out/check-in.
- **Form Reference Codes**: form_reference_code field dalam helpdesk_tickets dan loan_applications.

### 5.2. Validasi & Konsistensi Data

- Foreign key constraints diaktifkan di database.
- Transactional integrity untuk operasi rentas modul.
- Validasi data secara automatik semasa import/migrasi.

### 5.3. Pengurusan Ralat & Audit

- Semua operasi integrasi dilogkan (audit trail).
- Exception handling: fallback, retry, atau notifikasi kepada admin jika gagal.
- Semua integrasi boleh di-rollback jika berlaku error kritikal.

### 5.4. Jadual & Workflow Integrasi

- Jadual proses integrasi (cron, job queue) untuk sync/import berkala.
- Sequence diagram & flowchart untuk setiap integrasi utama didokumenkan.

---

## 6. Keselamatan & Pematuhan (Security & Compliance)

- Semua komunikasi antara modul/sistem mesti melalui HTTPS/TLS.
- Pengesahan (authentication) dan kebenaran (authorization) mengikut peranan.
- Data sensitif dienkripsi at-rest & in-transit.
- Audit compliance: semua aktiviti integrasi boleh diaudit mengikut dasar MOTAC
  & undang-undang (contoh: PDPA).

---

## 7. Keperluan Pengujian Integrasi (Integration Testing Requirements)

- **Unit Test**: Setiap fungsi integrasi diuji secara berasingan.
- **Integration Test**: Ujian penuh antara modul (contoh: aduan → asset → loan).
- **System Test**: End-to-end test, termasuk integrasi luaran.
- **UAT**: User Acceptance Test bersama BPM & pentadbir sistem.
- **Regression Test**: Selepas setiap kemas kini utama.

---

## 8. Dokumentasi & Tadbir Urus (Documentation & Governance)

- Setiap endpoint API, data mapping, dan flow integrasi didokumenkan (technical
  & user manual).
- Dokumen traceability dan versioning untuk setiap perubahan.
- Governance: Approval sebelum dan selepas setiap aktiviti integrasi.

---

## 9. Spesifikasi Endpoint API (API Endpoint Specifications)

**Pematuhan Standard**: ISO/IEC/IEEE 15289:2019 (Documentation Requirements)

Sistem menyediakan RESTful API endpoints untuk integrasi internal & future
applications (mobile, dashboard). Semua endpoints menggunakan JSON format dan
Laravel Sanctum Bearer token authentication.

### 9.1. Endpoint Utama (Main API Endpoints)

| Endpoint                             | Method | Autentikasi              | Fungsi                                  |
| ------------------------------------ | ------ | ------------------------ | --------------------------------------- |
| `/api/v1/tickets`                    | GET    | `auth:sanctum` + `ability:read:tickets,admin:all` | Senarai tiket (Cross-Module Integration API) |
| `/api/v1/tickets`                    | POST   | `auth:sanctum` + `ability:write:tickets,admin:all` | Cipta tiket (Cross-Module Integration API) |
| `/api/v1/loans`                      | GET    | `auth:sanctum` + `ability:read:loans,admin:all` | Senarai permohonan pinjaman (Cross-Module Integration API) |
| `/api/v1/loans`                      | POST   | `auth:sanctum` + `ability:write:loans,admin:all` | Cipta permohonan pinjaman (Cross-Module Integration API) |
| `/api/v1/asset-returns/notify-damage` | POST  | `auth:sanctum`            | Notifikasi kerosakan / trigger penyelenggaraan |
| `/api/v1/asset-returns/create-maintenance-ticket` | POST | `auth:sanctum` | Cipta tiket penyelenggaraan daripada pemulangan aset |
| `/api/v1/ticket-asset/link`          | POST   | `auth:sanctum`            | Paut tiket ↔ aset |
| `/api/v1/ticket-asset/unlink/{ticket}` | DELETE | `auth:sanctum`           | Nyahpaut tiket ↔ aset |
| `/api/v1/ticket-asset/ticket/{ticket}/asset` | GET | `auth:sanctum`          | Senarai aset bagi sesuatu tiket |
| `/api/v1/ticket-asset/asset/{asset}/tickets` | GET | `auth:sanctum`          | Senarai tiket bagi sesuatu aset |
| `/api/v1/memory/import`              | POST   | Token aplikasi (MEMORY_API_TOKEN) | Import memori agen (bukan `auth:sanctum`) |
| `/api/v1/memory/search`              | GET    | Token aplikasi (MEMORY_API_TOKEN) | Carian memori agen (bukan `auth:sanctum`) |
| `/api/v1/ollama/faq/query`           | POST   | Hybrid (guest + auth)     | FAQ Bot query (throttle) |
| `/api/v1/ollama/faq/history`         | GET    | Hybrid (guest + auth)     | Sejarah perbualan FAQ (throttle) |
| `/api/v1/ollama/faq/claim`           | POST   | `auth:sanctum`            | Claim perbualan guest kepada akaun |
| `/api/v1/ollama/documents/*`         | GET/POST/DELETE | `auth:sanctum` + `ability:admin:all` | Pengurusan dokumen AI (admin) |
| `/api/v1/ollama/auto-reply/*`        | GET/POST | Campuran (`auth:sanctum` untuk admin; token-based untuk approve/reject) | Auto-reply AI (D18) |
| `/api/assets/search`                 | GET    | `auth:web`                | Carian aset (digunakan oleh UI staf) |
| `/api/loan-applications`             | GET    | `auth:web`                | Senarai permohonan pinjaman (digunakan oleh UI staf) |
| `/api/health/*`                      | GET    | Public                     | Health checks (basic/ai/performance/detailed) |
| `/api/analytics/web-vitals`          | POST   | Public (throttle)          | Penghantaran metrik Web Vitals (observability) |
| `/api/tickets`, `/api/loans`         | GET/POST | `auth:sanctum` + abilities | Laluan serasi-ke-belakang (non-versioned) |

### 9.2. Contoh Permintaan & Respons (Request/Response Examples)

#### POST /api/v1/tickets (Cipta Tiket Baru)

**Request:**

```json
{
  "subject": "Laptop tidak menyala selepas kemas kini BIOS",
  "description": "Laptop tidak boleh dihidupkan selepas kemas kini BIOS. Tiada bunyi kipas dan tiada paparan.",
  "category_id": 1,
  "priority": "HIGH",
  "asset_id": 2001
}
```

**Response (Success - 201 Created):**

```json
{
  "success": true,
  "data": {
    "id": 1001,
    "ticket_number": "HD2025000001",
    "form_reference_code": "PK.(S).MOTAC.07.(L1)",
    "status": "OPEN",
    "created_at": "2025-12-01T10:30:00Z"
  }
}
```

**Response (Error - 422 Unprocessable Entity):**

```json
{
  "success": false,
  "errors": {
    "subject": ["The subject field is required."],
    "description": ["The description field is required."],
    "category_id": ["The selected category id is invalid."]
  }
}
```

> **Nota (repo v3.6.1)**: Operasi kelulusan, pengeluaran (issuance), dan pemulangan aset dilaksanakan melalui UI dalaman (Filament Actions + aliran kelulusan e-mel bertoken). Endpoint REST seperti `/api/v1/loans/{id}/approve`, `/api/v1/loans/{id}/checkout`, dan `/api/v1/loans/{id}/checkin` **tidak wujud** dalam `routes/api.php`.

---

### 9.3. Error Handling & Status Codes

| HTTP Status                   | Pemakaian                           | Contoh Response                     |
| ----------------------------- | ----------------------------------- | ----------------------------------- |
| **200 OK**                    | Permintaan successful               | `{"success": true, "data": ...}`    |
| **201 Created**               | Sumber baru cipta successful        | `{"success": true, "id": 1001}`     |
| **400 Bad Request**           | Request malformed, missing fields   | `{"success": false, "errors": ...}` |
| **401 Unauthorized**          | No/invalid authentication token     | `{"error": "Unauthorized"}`         |
| **403 Forbidden**             | Authenticated tapi tidak authorized | `{"error": "Forbidden"}`            |
| **404 Not Found**             | Resource tidak wujud                | `{"error": "Ticket not found"}`     |
| **422 Unprocessable Entity**  | Validation errors                   | `{"success": false, "errors": ...}` |
| **429 Too Many Requests**     | Rate limiting (>100 req/min)        | `{"error": "Rate limit exceeded"}`  |
| **500 Internal Server Error** | Server error                        | `{"error": "Server error"}`         |

### 9.4. Rate Limiting & Security

- **Rate Limit**: 100 requests/minute per token (tracked via Redis)
- **Timeout**: 30 seconds per request
- **CORS**: Disabled for external domains (internal API only)
- **Token Expiry**: 30 days (Laravel Sanctum token, configurable)
- **Token Abilities**: Scoped permissions (read:tickets, write:tickets, admin:all)
- **Refresh Token**: Available untuk extend session without re-login

**Rujukan**: Lihat [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
§8 untuk implementasi teknikal API & integration patterns.

---

## 10. Teknologi Integrasi (Integration Technology Stack)

| Komponen             | Teknologi         | Versi   | Fungsi                                    |
| -------------------- | ----------------- | ------- | ----------------------------------------- |
| Framework            | Laravel           | 12.43.1 | Backend application framework             |
| Admin Panel          | Filament          | 4.3.1   | CRUD interfaces, dashboard                |
| Reactive UI          | Livewire          | 3.7.3   | Server-driven UI components               |
| Single-file Livewire | Volt              | 1.10.1  | Single-file Livewire components           |
| WebSocket Server     | Laravel Reverb    | 1.6.3   | Real-time communication                   |
| WebSocket Client     | Laravel Echo      | 2.2.6   | Client-side WebSocket integration         |
| CSS Framework        | Tailwind CSS      | 4.1.18  | Utility-first styling                     |
| Database             | MySQL             | 8.x     | Production database                       |
| Queue & Cache        | Redis             | 7.x     | Job queue & caching                       |
| Testing              | PHPUnit           | 11.5.46 | Unit & integration testing                |
| E2E Testing          | Playwright        | 1.57.0  | Browser automation testing                |
| Static Analysis      | Larastan          | 3.8.1   | PHP static analysis                       |
| Code Style           | Laravel Pint      | 1.26.0  | PSR-12 code formatting                    |
| Permissions          | Spatie Permission | 6.23    | Role-based access control                 |
| Audit (Compliance)   | Laravel Auditing  | 14.x    | Field-level audit trail (owen-it)         |
| Audit (Operations)   | Activity Log      | 4.x     | User activity logging (spatie)            |
| Performance Monitor  | Laravel Pulse     | 1.4.7   | Performance metrics & server health       |
| API Authentication   | Laravel Sanctum   | 4.2.1   | Token-based API authentication            |
| OAuth SSO            | Laravel Socialite | 5.24.0  | Google Workspace SSO (optional)           |
| Debugging            | Laravel Telescope | 5.16.0  | System monitoring (superuser only)        |
| Queue Management     | Laravel Horizon   | 5.41.0  | Redis queue monitoring & management       |

---

## 11. Penutup

Spesifikasi ini menjadi rujukan rasmi bagi semua aktiviti integrasi sistem
Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan integrasi dilakukan secara
teratur, selamat, dan mematuhi piawaian antarabangsa **ISO/IEC/IEEE 15288, 15289,
TS 24748-6** serta dasar dalaman MOTAC.

**True Hybrid Architecture v3.5.0 Integration Points:**

**Core Authentication & Access:**

- Self-registration dengan @motac.gov.my domain validation
- Email verification flow sebelum akses penuh
- Flexible login (email penuh ATAU username pendek)
- Optional Google Workspace SSO untuk @motac.gov.my domain
- Optional guest-to-account linking service

**Audit & Monitoring:**

- Dual audit system untuk compliance (owen-it) dan operations (spatie)
- Laravel Pulse untuk performance monitoring (admin/superuser)
- Laravel Telescope untuk superuser debugging (tiada sekatan)

**API & Integration:**

- Laravel Sanctum untuk API token authentication
- RESTful API endpoints (/api/v1/) untuk integrasi luaran
- Token abilities untuk scoped permissions

**Enhanced Features:**

- Responsible Officer tracking untuk loan applications
- Accessory tracking dengan discrepancy detection
- Form reference codes (PK.(S).MOTAC.07.(L1), PK.(S).MOTAC.07.(L3))
- MOTAC branding compliance (Jata Negara, logos, government header)
- Enhanced UX (onboarding tour, fuzzy search, saved filters, theme preference)

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk [GLOSSARY.md](GLOSSARY.md) untuk istilah teknikal seperti:

- **Spesifikasi Integrasi (Integration Specification)**: Dokumen kriteria dan
  keperluan teknikal integrasi
- **Interface Specification**: Definisi antara muka antara komponen sistem
- **Integration Testing**: Pengujian interaksi antara komponen sistem
- **ISO/IEC/IEEE 15288**: Piawaian kitaran hayat sistem
- **ISO/IEC/IEEE 15289**: Piawaian dokumentasi sistem/perisian

### Dokumen Rujukan

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Gambaran keseluruhan sistem
- [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) - Pelan integrasi sistem
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Rekabentuk teknikal terperinci

---

## Lampiran (Appendices)

### A. Matriks Antara Muka Komponen (Component Interface Matrix)

Rujuk Seksyen 4 untuk pemetaan lengkap antara muka integrasi.

### B. Kes Ujian Integrasi Terperinci (Detailed Integration Test Cases)

Rujuk Seksyen 7 untuk keperluan pengujian integrasi.

### C. Daftar Risiko & Mitigasi (Risk Register & Mitigation)

Rujuk [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) untuk
daftar risiko integrasi.

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 15288:2015, ISO/IEC/IEEE 15289:2019,
dan ISO/IEC TS 24748-6:2016.**
