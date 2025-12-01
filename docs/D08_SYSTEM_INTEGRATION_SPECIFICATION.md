# Spesifikasi Integrasi Sistem (System Integration Specification - SIS)

**Sistem ICTServe**
**Versi:** 3.5.0 (SemVer)
**Tarikh Kemaskini:** 30 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 15289, ISO/IEC TS 24748-6

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.5.0                                     |
| **Tarikh Kemaskini** | 30 November 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO/IEC/IEEE 15288, 15289, TS 24748-6     |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

> Notis Penggunaan Dalaman: Spesifikasi integrasi ini adalah untuk kegunaan
> dalaman MOTAC sahaja dan tidak melibatkan API awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                               | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D04 v3.5.0. Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only). Hapus rujukan LDAP/SSO. Tukar `divisions` kepada `departments`. Pematuhan Jabatan Digital Negara. | Pasukan BPM |
| 3.4.0 | 6 Januari 2026   | Hybrid Architecture v3.4.0: Restore LDAP/SSO integration sebagai optional authentication untuk staff. Penyelarasan dengan D00-D08 v3.4.0.                                                                                                                                                                                                               | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Penyelarasan penuh Guest-First: hapus semua rujukan LDAP/SSO/User Sync. Hanya admin/superuser authenticate.                                                                                                                                                                                                                                             | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Hapus LDAP/SSO; klarifikasi Guest-First (staf guna guest forms tanpa authentication)                                                                                                                                                                                                                                                                    | Pasukan BPM |
| 2.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.40.1, Filament 4.1.10, Livewire 3.7.0, Tailwind 4.1.17                                                                                                                                                                                                                                                                  | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini teknologi: Laravel Reverb 1.6.2, Laravel Echo 2.2.6 untuk real-time WebSocket                                                                                                                                                                                                                                                                 | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                                  | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal spesifikasi integrasi sistem                                                                                                                                                                                                                                                                                                                 | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Ringkasan Sistem
- [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) - Pelan Integrasi Sistem
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Dokumentasi Rekabentuk Teknikal
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
- **Tiada LDAP/SSO**: Semua authentication melalui Laravel Breeze sahaja
- **Dual Audit**: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Debugging**: Laravel Telescope untuk superuser sahaja (tiada sekatan)

Menyedia keperluan untuk masa depan: API, integrasi aplikasi luaran.

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

| Modul                | Antaramuka Integrasi        | Tujuan                                                              |
| -------------------- | --------------------------- | ------------------------------------------------------------------- |
| Helpdesk Ticketing   | API dalaman, model relation | Link aduan kerosakan dengan aset pinjaman                           |
| ICT Asset Loan       | API dalaman, model relation | Status aset, automasi tiket maintenance                             |
| Inventory            | API dalaman                 | Data aset, status, penggunaan, sejarah                              |
| Staff Authentication | Laravel Breeze              | Self-registration @motac.gov.my, flexible login, email verification |
| Account Linking      | Custom Service              | Optional guest-to-account linking based on email matching           |
| Admin Authentication | Laravel Breeze              | Login Admin/Superuser (required)                                    |
| Audit (Compliance)   | Laravel Auditing            | Field-level audit trail (owen-it/laravel-auditing v14.x)            |
| Audit (Operations)   | Activity Log                | User activity logging (spatie/laravel-activitylog v4.x)             |
| Debugging            | Laravel Telescope           | System monitoring, superuser only, unrestricted access              |
| Reporting            | Query, API                  | Laporan rentas modul                                                |
| Audit Trail          | Laravel Auditing, logging   | Audit semua aktiviti integrasi                                      |

### 4.2. Komponen Luaran

| Sistem             | Integrasi Dengan    | Mekanisme                  |
| ------------------ | ------------------- | -------------------------- |
| Email Server       | Notification, Alert | SMTP, Laravel Notification |
| Sistem Aset Legacy | Inventory, Loan     | CSV import, ETL, atau API  |

**Nota True Hybrid Architecture v3.5.0**:

- Staff self-register dengan @motac.gov.my dan log masuk via Laravel Breeze (flexible: email penuh ATAU username pendek)
- Guest forms kekal tersedia untuk quick access (identiti dari input manual)
- Optional account linking untuk guest submissions sedia ada
- **Tiada LDAP/SSO** - semua authentication melalui Laravel Breeze sahaja

### 4.3. API & Data Exchange

- Semua pertukaran data (internal & external) guna RESTful API (JSON).
- Endpoint utama didokumenkan (e.g. `/api/assets`, `/api/tickets`, `/api/users`).
- Semua API guna token authentication (Bearer, OAuth jika perlu).

---

## 5. Proses & Kaedah Integrasi (Integration Processes & Methods)

### 5.1. Data Mapping & Transformation

- Setiap field modul dipadankan dengan field sistem sedia ada.
- Mapping dokumen disediakan (contoh: `asset_no` lama â†’ `tag_id` baru).
- Data transformation: format tarikh, kod status, normalisasi string.
- **True Hybrid Model v3.5.0**: Staff self-registration data disimpan dalam users table dengan medan baharu (email*verified_at, locale, notify*\*, staff_number, guest_submissions_linked). Guest form data disimpan sebagai field dalam helpdesk_tickets dan loan_applications (user_id=NULL).

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
- **Integration Test**: Ujian penuh antara modul (contoh: aduan â†’ asset â†’ loan).
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
Bearer token authentication.

### 9.1. Endpoint Utama (Main API Endpoints)

| Endpoint                        | Method | Autentikasi              | Fungsi                                  |
| ------------------------------- | ------ | ------------------------ | --------------------------------------- |
| `/api/tickets`                  | GET    | Bearer token             | Senarai tiket (paginated)               |
| `/api/tickets`                  | POST   | Bearer token             | Cipta tiket baru                        |
| `/api/tickets/{id}`             | GET    | Bearer token             | Detail tiket                            |
| `/api/tickets/{id}`             | PUT    | Bearer token + Policy    | Update tiket                            |
| `/api/assets`                   | GET    | Bearer token             | Senarai aset                            |
| `/api/assets/{id}`              | GET    | Bearer token             | Detail aset + sejarah                   |
| `/api/loans`                    | POST   | Bearer token             | Cipta permohonan pinjaman               |
| `/api/loans/{id}/approve`       | PATCH  | Bearer token + Approver  | Luluskan pinjaman                       |
| `/api/departments`              | GET    | Public                   | Senarai bahagian/unit                   |
| `/api/users/profile`            | GET    | Bearer token             | Profil staff login                      |
| `/api/auth/register`            | POST   | Public                   | Staff self-registration (@motac.gov.my) |
| `/api/auth/verify-email`        | POST   | Bearer token             | Email verification                      |
| `/api/auth/login`               | POST   | Public                   | Login (email/username + password)       |
| `/api/account/link-submissions` | POST   | Bearer token + Staff     | Link guest submissions to account       |
| `/api/account/preferences`      | PUT    | Bearer token             | Update notification preferences         |
| `/api/telescope/*`              | GET    | Bearer token + Superuser | Debugging/monitoring (unrestricted)     |
| `/api/audit-logs`               | GET    | Bearer token + Admin     | Senarai audit logs                      |

### 9.2. Contoh Permintaan & Respons (Request/Response Examples)

#### POST /api/tickets (Cipta Tiket Baru)

**Request:**

```json
{
 "damage_type": "Hardware",
 "damage_info": "Laptop tidak menyala selepas update BIOS",
 "asset_no": "LT-2024-001",
 "category": "Critical"
}
```

**Response (Success - 201 Created):**

```json
{
 "success": true,
 "ticket": {
  "id": 1001,
  "ticket_no": "TK-20251129-001",
  "status": "Open",
  "created_at": "2025-11-29T10:30:00Z",
  "assigned_to": null
 }
}
```

**Response (Error - 422 Unprocessable Entity):**

```json
{
 "success": false,
 "errors": {
  "damage_type": ["The damage_type field is required."],
  "damage_info": ["The damage_info must be at least 10 characters."]
 }
}
```

#### PATCH /api/loans/{id}/approve (Luluskan Pinjaman)

**Request:**

```json
{
 "approved": true,
 "remarks": "Diluluskan sebagai dimaklumkan"
}
```

**Response (Success - 200 OK):**

```json
{
 "success": true,
 "loan": {
  "id": 5001,
  "status": "APPROVED",
  "approved_by": "BPM_ADMIN_01",
  "approved_at": "2025-11-29T14:00:00Z"
 }
}
```

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
- **Token Expiry**: 8 hours (JWT token)
- **Refresh Token**: Available untuk extend session without re-login

**Rujukan**: Lihat [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
Â§8 untuk implementasi teknikal API & integration patterns.

---

## 10. Teknologi Integrasi (Integration Technology Stack)

| Komponen             | Teknologi         | Versi   | Fungsi                             |
| -------------------- | ----------------- | ------- | ---------------------------------- |
| Framework            | Laravel           | 12.40.1 | Backend application framework      |
| Admin Panel          | Filament          | 4.1.10  | CRUD interfaces, dashboard         |
| Reactive UI          | Livewire          | 3.7.0   | Server-driven UI components        |
| Single-file Livewire | Volt              | 1.10.1  | Single-file Livewire components    |
| WebSocket Server     | Laravel Reverb    | 1.6.2   | Real-time communication            |
| WebSocket Client     | Laravel Echo      | 2.2.6   | Client-side WebSocket integration  |
| CSS Framework        | Tailwind CSS      | 4.1.17  | Utility-first styling              |
| Database             | MySQL             | 8.x     | Production database                |
| Queue & Cache        | Redis             | -       | Job queue & caching                |
| Testing              | PHPUnit           | 11.5.44 | Unit & integration testing         |
| Static Analysis      | Larastan          | 3.8.0   | PHP static analysis                |
| Code Style           | Laravel Pint      | 1.26.0  | PSR-12 code formatting             |
| Permissions          | Spatie Permission | 6.23    | Role-based access control          |
| Audit (Compliance)   | Laravel Auditing  | 14.x    | Field-level audit trail (owen-it)  |
| Audit (Operations)   | Activity Log      | 4.x     | User activity logging (spatie)     |
| Debugging            | Laravel Telescope | 5.x     | System monitoring (superuser only) |

---

## 11. Penutup

Spesifikasi ini menjadi rujukan rasmi bagi semua aktiviti integrasi sistem
Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan integrasi dilakukan secara
teratur, selamat, dan mematuhi piawaian antarabangsa **ISO/IEC/IEEE 15288, 15289,
TS 24748-6** serta dasar dalaman MOTAC.

**True Hybrid Architecture v3.5.0 Integration Points:**

- Self-registration dengan @motac.gov.my domain validation
- Email verification flow sebelum akses penuh
- Flexible login (email penuh ATAU username pendek)
- Optional guest-to-account linking service
- Dual audit system untuk compliance dan operations
- Laravel Telescope untuk superuser debugging (tiada sekatan)

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
