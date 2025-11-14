# Spesifikasi Integrasi Sistem (System Integration Specification - SIS)

**Sistem ICTServe**
**Versi:** 2.0.0 (SemVer)
**Tarikh Kemaskini:** 17 Oktober 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 15289, ISO/IEC TS 24748-6

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO/IEC/IEEE 15288, 15289, TS 24748-6    |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Spesifikasi integrasi ini adalah untuk kegunaan dalaman MOTAC sahaja dan tidak melibatkan API awam.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal spesifikasi integrasi sistem        | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D07_SYSTEM_INTEGRATION_PLAN.md]** - Pelan Integrasi Sistem (strategi integrasi)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[GLOSSARY.md]** - Glosari Istilah Sistem


---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini mendefinisikan spesifikasi teknikal, keperluan, dan kriteria integrasi untuk Sistem **Helpdesk & ICT Asset Loan BPM MOTAC**. Spesifikasi ini mengikut garis panduan dan piawaian **ISO/IEC/IEEE 15288** (system engineering), **ISO/IEC/IEEE 15289** (system/software documentation), dan **ISO/IEC TS 24748-6** (lifecycle management guide).

---

## 2. SKOP SPESIFIKASI (Scope)

- Meliputi integrasi antara semua modul dalaman:
  - Helpdesk Ticketing
  - ICT Asset Loan
  - Inventory & Asset Management
  - Authentication & Authorization
  - Reporting & Dashboard
  - Audit Trail
- Meliputi integrasi luaran dengan sistem sedia ada MOTAC (LDAP/SSO, Email Server, Database Staf, Sistem Pengurusan Aset Legacy).
- Menyedia keperluan untuk masa depan: API, integrasi aplikasi luaran.


---

## 3. OBJEKTIF INTEGRASI (Integration Objectives)

- Semua modul berfungsi sebagai satu sistem bersepadu (seamless).
- Data sentiasa konsisten dan tidak ada duplikasi (data consistency & single source of truth).
- Memudahkan interoperability dan pertukaran data antara sistem dalaman MOTAC.
- Memastikan integrasi selamat, boleh diaudit, dan mematuhi dasar serta undang-undang berkaitan.


---

## 4. KOMPONEN & ANTARA MUKA INTEGRASI (Integration Components & Interfaces)

### 4.1. Komponen Dalaman

| Modul               | Antaramuka Integrasi             | Tujuan                                          |
|---------------------|----------------------------------|-------------------------------------------------|
| Helpdesk Ticketing  | API dalaman, model relation      | Link aduan kerosakan dengan aset pinjaman       |
| ICT Asset Loan      | API dalaman, model relation      | Status aset, automasi tiket maintenance         |
| Inventory           | API dalaman                      | Data aset, status, penggunaan, sejarah          |
| Authentication      | LDAP/SSO, database users         | Single Sign-On, role mapping                    |
| Reporting           | Query, API                       | Laporan rentas modul                            |
| Audit Trail         | Laravel Auditing, logging        | Audit semua aktiviti integrasi                   |

### 4.2. Komponen Luaran

| Sistem              | Integrasi Dengan                 | Mekanisme                                       |
|---------------------|----------------------------------|-------------------------------------------------|
| LDAP / SSO MOTAC    | Authentication                  | LDAP bind, user sync                            |
| Email Server        | Notification, Alert             | SMTP, Laravel Notification                      |
| Database Staf       | User Management, Approval       | Direct DB access, scheduled import, API         |
| Sistem Aset Legacy  | Inventory, Loan                 | CSV import, ETL, atau API jika tersedia         |

### 4.3. API & Data Exchange

- Semua pertukaran data (internal & external) guna RESTful API (JSON).
- Endpoint utama didokumenkan (e.g. `/api/assets`, `/api/tickets`, `/api/users`).
- Semua API guna token authentication (Bearer, OAuth jika perlu).


---

## 5. PROSES & KAEDAH INTEGRASI (Integration Processes & Methods)

### 5.1. Data Mapping & Transformation

- Setiap field modul dipadankan dengan field sistem sedia ada.
- Mapping dokumen disediakan (contoh: `asset_no` lama → `tag_id` baru).
- Data transformation: format tarikh, kod status, normalisasi string.


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

## 6. KESELAMATAN & PEMATUHAN (Security & Compliance)

- Semua komunikasi antara modul/sistem mesti melalui HTTPS/TLS.
- Pengesahan (authentication) dan kebenaran (authorization) mengikut peranan.
- Data sensitif dienkripsi at-rest & in-transit.
- Audit compliance: semua aktiviti integrasi boleh diaudit mengikut dasar MOTAC & undang-undang (contoh: PDPA).


---

## 7. KEPERLUAN PENGUJIAN INTEGRASI (Integration Testing Requirements)

- **Unit Test**: Setiap fungsi integrasi diuji secara berasingan.
- **Integration Test**: Ujian penuh antara modul (contoh: aduan → asset → loan).
- **System Test**: End-to-end test, termasuk integrasi luaran.
- **UAT**: User Acceptance Test bersama BPM & pentadbir sistem.
- **Regression Test**: Selepas setiap kemas kini utama.


---

## 8. DOKUMENTASI & TADBIR URUS (Documentation & Governance)

- Setiap endpoint API, data mapping, dan flow integrasi didokumenkan (technical & user manual).
- Dokumen traceability dan versioning untuk setiap perubahan.
- Governance: Approval sebelum dan selepas setiap aktiviti integrasi.


---

## 8. SPESIFIKASI ENDPOINT API (API Endpoint Specifications)

**Pematuhan Standard**: ISO/IEC/IEEE 15289:2019 (Documentation Requirements)

Sistem menyediakan RESTful API endpoints untuk integrasi internal & future applications (mobile, dashboard). Semua endpoints menggunakan JSON format dan Bearer token authentication.

### 8.1. Endpoint Utama (Main API Endpoints)

| Endpoint | Method | Autentikasi | Fungsi | Contoh Response |
|----------|--------|-------------|--------|-----------------|
| `/api/tickets` | GET | Bearer token | Senarai tiket (paginated) | `data: [id, ticket_no, user_id, status, ...], total: 150, current_page: 1` |
| `/api/tickets` | POST | Bearer token | Cipta tiket baru | `success: true, ticket_id: 1001, ticket_no: "TK-20251018-001"` |
| `/api/tickets/id` | GET | Bearer token | Detail tiket | `id, ticket_no, user, status, history: [...]` |
| `/api/tickets/id` | PUT | Bearer token + Policy | Update tiket | `success: true, message: "Updated"` |
| `/api/assets` | GET | Bearer token | Senarai aset | `data: [id, name, model, status, ...], total: 250` |
| `/api/assets/id` | GET | Bearer token | Detail aset + sejarah pinjaman | `asset_details, loan_history: [...]` |
| `/api/loans` | POST | Bearer token | Cipta permohonan pinjaman | `success: true, loan_id: 5001` |
| `/api/loans/id/approve` | PATCH | Bearer token + Approver Role | Luluskan pinjaman | `success: true, status: "APPROVED"` |
| `/api/divisions` | GET | Public | Senarai bahagian/unit | `data: [id, name, ...]` |
| `/api/users/profile` | GET | Bearer token | Profil pengguna login | `id, name, email, role, division` |
| `/api/audit-logs` | GET | Bearer token + Admin Role | Senarai audit logs | `data: [id, event, user, timestamp, ...], total: 5000` |

### 8.2. Contoh Permintaan & Respons (Request/Response Examples)

**Example 1: POST /api/tickets (Cipta Tiket Baru)**

**Request:**
```json

  "damage_type": "Hardware",
  "damage_info": "Laptop tidak menyala selepas update BIOS",
  "asset_no": "LT-2024-001",
  "category": "Critical"

```

**Response (Success - 201 Created):**
```json

  "success": true,
  "ticket":
    "id": 1001,
    "ticket_no": "TK-20251018-001",
    "status": "Open",
    "created_at": "2025-10-18T10:30:00Z",
    "assigned_to": null

```

**Response (Error - 422 Unprocessable Entity):**
```json

  "success": false,
  "errors":
    "damage_type": ["The damage_type field is required."],
    "damage_info": ["The damage_info must be at least 10 characters."]

```

**Example 2: PATCH /api/loans/id/approve (Luluskan Pinjaman)**

**Request:**
```json

  "approved": true,
  "remarks": "Diluluskan sebagai dimaklumkan"

```

**Response (Success - 200 OK):**
```json

  "success": true,
  "loan":
    "id": 5001,
    "status": "APPROVED",
    "approved_by": "BPM_ADMIN_01",
    "approved_at": "2025-10-18T14:00:00Z"

```

### 8.3. Error Handling & Status Codes

| HTTP Status | Pemakaian | Contoh Error Response |
|-------------|-----------|----------------------|
| **200 OK** | Permintaan successful, data dikembalikan | `success: true, data: ...` |
| **201 Created** | Sumber baru cipta successful | `success: true, id: 1001` |
| **400 Bad Request** | Request malformed, missing fields | `success: false, errors: ...` |
| **401 Unauthorized** | No/invalid authentication token | `error: "Unauthorized"` |
| **403 Forbidden** | Authenticated tapi tidak authorized (e.g. non-admin accessing admin endpoint) | `error: "Forbidden"` |
| **404 Not Found** | Resource tidak wujud | `error: "Ticket not found"` |
| **422 Unprocessable Entity** | Validation errors | `success: false, errors: ...` |
| **429 Too Many Requests** | Rate limiting (>100 req/min) | `error: "Rate limit exceeded"` |
| **500 Internal Server Error** | Server error | `error: "Server error", message: "..."` |

### 8.4. Rate Limiting & Security

- **Rate Limit**: 100 requests/minute per token (tracked via Redis)
- **Timeout**: 30 seconds per request
- **CORS**: Disabled for external domains (internal API only)
- **Token Expiry**: 8 hours (JWT token)
- **Refresh Token**: Available untuk extend session without re-login


**Rujukan**: Lihat **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** §8 untuk implementasi teknikal API & integration patterns.

---

## 9. PENUTUP

Spesifikasi ini menjadi rujukan rasmi bagi semua aktiviti integrasi sistem Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan integrasi dilakukan secara teratur, selamat, dan mematuhi piawaian antarabangsa **ISO/IEC/IEEE 15288, 15289, TS 24748-6** serta dasar dalaman MOTAC.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Spesifikasi Integrasi (Integration Specification)**: Dokumen kriteria dan keperluan teknikal integrasi
- **Interface Specification**: Definisi antara muka antara komponen sistem
- **Integration Testing**: Pengujian interaksi antara komponen sistem
- **ISO/IEC/IEEE 15288**: Piawaian kitaran hayat sistem
- **ISO/IEC/IEEE 15289**: Piawaian dokumentasi sistem/perisian


**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D07_SYSTEM_INTEGRATION_PLAN.md** - Pelan integrasi sistem
- **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** - Rekabentuk teknikal terperinci


---

## Lampiran (Appendices)

### A. Matriks Antara Muka Komponen (Component Interface Matrix)

Rujuk Seksyen 4 untuk pemetaan lengkap antara muka integrasi.

### B. Kes Ujian Integrasi Terperinci (Detailed Integration Test Cases)

Rujuk Seksyen 7 untuk keperluan pengujian integrasi.

### C. Daftar Risiko & Mitigasi (Risk Register & Mitigation)

Rujuk **D07_SYSTEM_INTEGRATION_PLAN.md** untuk daftar risiko integrasi.

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 15288:2015, ISO/IEC/IEEE 15289:2019, dan ISO/IEC TS 24748-6:2016.**
