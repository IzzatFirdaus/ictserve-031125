# Dokumentasi Pangkalan Data (Database Documentation)

**Sistem ICTServe**
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 8 Disember 2025  
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman BPM MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                       |
| -------------------- | ----------------------------------------------------------- |
| **Versi**            | 3.5.0                                                       |
| **Tarikh Kemaskini** | 1 Disember 2025                                             |
| **Status**           | Aktif                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                  |
| **Pematuhi**         | ISO 8000, ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                   |

> Notis Penggunaan Dalaman: Semua skema dan jadual adalah untuk sistem dalaman
> MOTAC; tiada data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan | Penulis |
| ----- | ---------------- | --------- | ------- |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D08 v3.5.0. Tambah jadual `loan_transaction_accessories`, `personal_access_tokens`, `pulse_*`. Tambah medan `google_id`, `form_reference_code`, `responsible_officer_*`. Laravel Pulse, Sanctum API, Google SSO. | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Restored nullable user_id FK. Added staff role to users. | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Hybrid data model: users table extended for Staff profiles | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.40.1, Filament 4.1.10, MySQL 8.0 | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal dokumentasi pangkalan data | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - [GLOSSARY.md](GLOSSARY.md)

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menjelaskan struktur, definisi medan, piawaian kualiti data, dan
hubungan jadual bagi ICTServe sebagai sistem dalaman (internal-only).

---

## 2. Skop (Scope)

- Semua jadual utama yang menyokong borang dalaman Helpdesk & Asset Loan
- Panel Filament, kelulusan berperingkat, audit, notifikasi, dan pemantauan status
- Piawaian data, kawalan kualiti, dan persediaan migrasi

---

## 3. Teknologi Pangkalan Data (Database Technology)

| Komponen             | Teknologi         | Versi   | Fungsi                                    |
| -------------------- | ----------------- | ------- | ----------------------------------------- |
| RDBMS                | MySQL             | 8.x     | Production database                       |
| Development DB       | SQLite            | 3.x     | Development/testing database              |
| ORM                  | Eloquent          | 12.40.1 | Laravel ORM                               |
| Migrations           | Laravel           | 12.40.1 | Schema version control                    |
| Caching              | Redis             | 7.x     | Query caching                             |
| Audit (Compliance)   | Laravel Auditing  | 14.x    | Field-level audit trail (owen-it)         |
| Audit (Operations)   | Activity Log      | 4.x     | User activity logging (spatie)            |
| Performance Monitor  | Laravel Pulse     | 1.3.0   | Performance metrics & server health       |
| API Authentication   | Laravel Sanctum   | 4.0     | Token-based API authentication            |
| Permissions          | Spatie Permission | 6.23    | Role-based access control                 |
| Debugging            | Laravel Telescope | 5.x     | System monitoring (superuser only)        |

---

## 4. Reka Bentuk Logikal (Logical Database Design)

### 4.1. Senarai Jadual Utama (Main Tables)

| Jadual                       | Fungsi                                                    |
| ---------------------------- | --------------------------------------------------------- |
| users                        | Akaun pengguna staf & pentadbir (portal & panel Filament) |
| departments                  | Rujukan bahagian/unit MOTAC                               |
| audits                       | Jejak audit field-level (owen-it/laravel-auditing)        |
| activity_log                 | Log aktiviti sistem (spatie/laravel-activitylog)          |
| helpdesk_tickets             | Rekod tiket helpdesk pengguna dalaman                     |
| helpdesk_comments            | Komen pentadbir terhadap tiket                            |
| helpdesk_attachments         | Fail lampiran tiket                                       |
| loan_applications            | Permohonan pinjaman aset pengguna dalaman                 |
| loan_items                   | Item aset dalam permohonan                                |
| loan_transactions            | Pengeluaran & pemulangan aset                             |
| loan_transaction_accessories | Aksesori check-out/check-in (v3.5.0)                      |
| loan_approvals               | Rekod kelulusan e-mel bertanda tangan                     |
| loan_audits                  | Jejak audit khusus modul pinjaman                         |
| status_tokens                | Token semakan status tetamu (opsyen)                      |
| notifications                | Notifikasi Laravel                                        |
| personal_access_tokens       | API tokens (Laravel Sanctum v3.5.0)                       |
| pulse_aggregates             | Performance metrics aggregates (Laravel Pulse v3.5.0)     |
| pulse_entries                | Performance metrics entries (Laravel Pulse v3.5.0)        |
| pulse_values                 | Performance metrics values (Laravel Pulse v3.5.0)         |

---

## 5. Definisi Jadual & Field (Table & Field Definitions)

### 5.1. Jadual: users

| Field                    | Tipe Data                      | Keterangan                               |
| ------------------------ | ------------------------------ | ---------------------------------------- |
| id                       | bigint, PK                     | ID pengguna                              |
| name                     | string(255)                    | Nama pegawai                             |
| email                    | string(255)                    | E-mel kerajaan @motac.gov.my (unik)      |
| email_verified_at        | timestamp (nullable)           | Tarikh pengesahan e-mel                  |
| phone                    | string(30)                     | Telefon pegawai                          |
| department_id            | bigint, FK nullable            | FK → departments.id                      |
| grade                    | string(50) nullable            | Gred pegawai (e.g. 41, 44)               |
| staff_number             | string(50) nullable            | Nombor staf (optional)                   |
| role                     | enum(staff, admin, superuser)  | Peranan sistem (DEFAULT 'staff')         |
| password                 | string(255)                    | Hash kata laluan                         |
| google_id                | string(255) nullable           | Google OAuth ID (v3.5.0)                 |
| two_factor_secret        | text (nullable)                | Rahsia TOTP (untuk superuser)            |
| two_factor_confirmed_at  | timestamp (nullable)           | Tarikh pengesahan 2FA                    |
| locale                   | enum(ms, en)                   | Bahasa pilihan (DEFAULT 'ms')            |
| notify_email_frequency   | enum(immediate, daily, weekly) | Kekerapan e-mel (DEFAULT 'immediate')    |
| notify_in_app            | boolean                        | Notifikasi dalam aplikasi (DEFAULT TRUE) |
| guest_submissions_linked | integer                        | Bilangan submissions dilink (DEFAULT 0)  |
| remember_token           | string(100) nullable           | Token remember me                        |
| last_login_at            | timestamp (nullable)           | Tarikh login terakhir                    |
| last_login_ip            | string(45) nullable            | IP login terakhir                        |
| created_at               | timestamp                      | Tarikh cipta                             |
| updated_at               | timestamp                      | Tarikh kemaskini                         |
| deleted_at               | timestamp (nullable)           | Soft delete timestamp                    |

**Indeks:** `(email)`, `(google_id)`, `(role)`, `(department_id)`, `(staff_number)`

> **Nota True Hybrid Architecture v3.5.0:**
>
> - **Admin/Superuser**: Akaun pentadbiran penuh (role='admin' atau 'superuser')
> - **Staff**: Pegawai MOTAC (role='staff'), boleh self-register dengan @motac.gov.my
> - **Guest**: Tidak disimpan dalam jadual users; submissions dengan user_id=NULL
> - **Google SSO**: Optional OAuth 2.0 login via `google_id` field

### 5.2. Jadual: departments

| Field      | Tipe Data           | Keterangan                     |
| ---------- | ------------------- | ------------------------------ |
| id         | bigint, PK          | ID bahagian                    |
| code       | string(20)          | Kod bahagian (unik)            |
| name       | string(255)         | Nama bahagian                  |
| parent_id  | bigint, FK nullable | FK → departments.id (hierarki) |
| created_at | timestamp           | Tarikh cipta                   |
| updated_at | timestamp           | Tarikh kemaskini               |

### 5.3. Jadual: helpdesk_tickets

| Field                   | Tipe Data                                                | Keterangan                              |
| ----------------------- | -------------------------------------------------------- | --------------------------------------- |
| id                      | bigint, PK                                               | ID tiket                                |
| ticket_number           | string(20)                                               | Nombor tiket unik (HD-YYYYMM-XXXX)      |
| form_reference_code     | string(50)                                               | Kod rujukan borang PK.(S).MOTAC.07.(L1) |
| user_id                 | bigint, FK nullable                                      | FK → users.id (NULL jika Guest)         |
| submitter_name          | string(255)                                              | Nama penghantar                         |
| submitter_email         | string(255)                                              | E-mel penghantar                        |
| submitter_phone         | string(50)                                               | Telefon penghantar                      |
| submitter_division_code | string(20)                                               | Kod bahagian                            |
| submitter_grade         | string(50)                                               | Gred (optional)                         |
| category                | string(100)                                              | Kategori kerosakan                      |
| priority                | enum(LOW, MEDIUM, HIGH, CRITICAL)                        | Keutamaan SLA                           |
| description             | text                                                     | Maklumat kerosakan                      |
| asset_tag               | string(100)                                              | Tag aset (optional)                     |
| declaration             | boolean                                                  | Perakuan PDPA (mesti TRUE)              |
| status                  | enum(OPEN, IN_PROGRESS, AWAITING_INFO, RESOLVED, CLOSED) | Status tiket                            |
| assigned_admin_id       | bigint, FK nullable                                      | FK → users.id                           |
| sla_due_at              | timestamp                                                | Tarikh sasaran SLA                      |
| closed_at               | timestamp                                                | Tarikh tiket ditutup                    |
| status_token_hash       | string(128)                                              | Hash token semakan status               |
| created_at              | timestamp                                                | Tarikh cipta                            |
| updated_at              | timestamp                                                | Tarikh kemaskini                        |

**Indeks:** `(user_id, status)`, `(submitter_email, status)`, `(ticket_number)`, `(status_token_hash)`

> **Nota Hybrid:** `user_id` NULL = Guest submission; NOT NULL = Authenticated Staff submission
> **Nota v3.5.0:** `form_reference_code` menyimpan kod rujukan borang rasmi

### 5.4. Jadual: helpdesk_comments

| Field      | Tipe Data              | Keterangan               |
| ---------- | ---------------------- | ------------------------ |
| id         | bigint, PK             | ID komen                 |
| ticket_id  | bigint, FK             | FK → helpdesk_tickets.id |
| admin_id   | bigint, FK             | FK → users.id            |
| body       | text                   | Komen/kemas kini         |
| visibility | enum(INTERNAL, PUBLIC) | Tahap paparan            |
| created_at | timestamp              | Tarikh cipta             |

### 5.5. Jadual: helpdesk_attachments

| Field         | Tipe Data   | Keterangan               |
| ------------- | ----------- | ------------------------ |
| id            | bigint, PK  | ID lampiran              |
| ticket_id     | bigint, FK  | FK → helpdesk_tickets.id |
| path          | string(255) | Laluan fail (S3)         |
| original_name | string(255) | Nama fail asal           |
| mime_type     | string(100) | Jenis MIME               |
| size_bytes    | bigint      | Saiz                     |
| checksum      | string(64)  | SHA256 checksum          |
| created_at    | timestamp   | Tarikh muat naik         |

### 5.6. Jadual: loan_applications

| Field                                | Tipe Data           | Keterangan                                    |
| ------------------------------------ | ------------------- | --------------------------------------------- |
| id                                   | bigint, PK          | ID permohonan                                 |
| reference                            | string(20)          | Kod rujukan (LA-YYYYMM-XXXX)                  |
| form_reference_code                  | string(50)          | Kod rujukan borang PK.(S).MOTAC.07.(L3)       |
| user_id                              | bigint, FK nullable | FK → users.id (NULL jika Guest)               |
| applicant_name                       | string(255)         | Nama pemohon                                  |
| applicant_email                      | string(255)         | E-mel pemohon                                 |
| applicant_phone                      | string(50)          | Telefon pemohon                               |
| applicant_division_code              | string(20)          | Kod bahagian                                  |
| applicant_grade                      | string(50)          | Gred pemohon                                  |
| is_applicant_responsible             | boolean             | Pemohon = Pegawai Bertanggungjawab (v3.5.0)   |
| responsible_officer_name             | string(255) null    | Nama Pegawai Bertanggungjawab (v3.5.0)        |
| responsible_officer_grade            | string(50) null     | Gred Pegawai Bertanggungjawab (v3.5.0)        |
| responsible_officer_phone            | string(50) null     | Telefon Pegawai Bertanggungjawab (v3.5.0)     |
| responsible_officer_acknowledgement  | boolean             | Perakuan Pegawai Bertanggungjawab (v3.5.0)    |
| purpose                              | text                | Tujuan pinjaman                               |
| location                             | string(255)         | Lokasi penggunaan                             |
| loan_start_date                      | date                | Tarikh mula                                   |
| loan_end_date                        | date                | Tarikh akhir                                  |
| acknowledgement                      | boolean             | Perakuan PDPA                                 |
| status                               | enum                | Status permohonan                             |
| approval_token_hash                  | string(128)         | Hash token kelulusan (SHA512)                 |
| approval_token_expires_at            | timestamp           | Tarikh luput token                            |
| status_token_hash                    | string(128)         | Hash token semakan status tetamu              |
| created_at                           | timestamp           | Tarikh cipta                                  |
| updated_at                           | timestamp           | Tarikh kemaskini                              |

**Indeks:** `(user_id, status)`, `(applicant_email, status)`, `(reference)`, `(status_token_hash)`

> **Nota Hybrid:** `user_id` NULL = Guest submission; NOT NULL = Authenticated Staff submission
> **Nota v3.5.0:** Responsible Officer fields untuk PK.(S).MOTAC.07.(L3) Part 2 & 4

**Status enum values:** PENDING_SUPERVISOR_APPROVAL, APPROVED, REJECTED, AWAITING_COLLECTION, ON_LOAN, RETURNED, DAMAGED

### 5.7. Jadual: loan_items

| Field               | Tipe Data  | Keterangan                |
| ------------------- | ---------- | ------------------------- |
| id                  | bigint, PK | ID item                   |
| loan_application_id | bigint, FK | FK → loan_applications.id |
| asset_id            | bigint, FK | FK → assets.id            |
| quantity            | integer    | Kuantiti                  |
| notes               | text       | Catatan (optional)        |

### 5.8. Jadual: loan_transactions

| Field                 | Tipe Data                 | Keterangan                |
| --------------------- | ------------------------- | ------------------------- |
| id                    | bigint, PK                | ID transaksi              |
| loan_application_id   | bigint, FK                | FK → loan_applications.id |
| asset_id              | bigint, FK                | FK → assets.id            |
| type                  | enum(CHECK_OUT, CHECK_IN) | Jenis transaksi           |
| performed_by_admin_id | bigint, FK                | FK → users.id             |
| performed_at          | timestamp                 | Tarikh tindakan           |
| condition_notes       | text                      | Catatan keadaan aset      |
| damage_reported       | boolean                   | Kerosakan dilaporkan      |
| damage_photos         | json (nullable)           | Bukti foto kerosakan      |
| created_at            | timestamp                 | Tarikh cipta              |

### 5.9. Jadual: loan_transaction_accessories (v3.5.0)

| Field               | Tipe Data                                                                    | Keterangan                        |
| ------------------- | ---------------------------------------------------------------------------- | --------------------------------- |
| id                  | bigint, PK                                                                   | ID aksesori                       |
| loan_transaction_id | bigint, FK                                                                   | FK → loan_transactions.id         |
| accessory_type      | enum(POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)   | Jenis aksesori                    |
| accessory_name      | string(100) nullable                                                         | Nama aksesori (untuk OTHERS)      |
| present_at_checkout | boolean                                                                      | Ada semasa check-out              |
| present_at_checkin  | boolean nullable                                                             | Ada semasa check-in (NULL sehingga check-in) |
| condition_notes     | text nullable                                                                | Catatan keadaan aksesori          |
| created_at          | timestamp                                                                    | Tarikh cipta                      |
| updated_at          | timestamp                                                                    | Tarikh kemaskini                  |

**Indeks:** `(loan_transaction_id)`, `(accessory_type)`

> **Nota v3.5.0:** Jadual baharu untuk tracking aksesori semasa check-out/check-in dengan discrepancy detection

### 5.10. Jadual: loan_approvals

| Field               | Tipe Data                | Keterangan                |
| ------------------- | ------------------------ | ------------------------- |
| id                  | bigint, PK               | ID kelulusan              |
| loan_application_id | bigint, FK               | FK → loan_applications.id |
| approver_email      | string(255)              | E-mel pegawai kelulusan   |
| approver_grade      | string(50)               | Gred pegawai              |
| decision            | enum(APPROVED, REJECTED) | Keputusan                 |
| remarks             | text (optional)          | Catatan tambahan          |
| decision_at         | timestamp                | Tarikh keputusan          |
| decision_ip_hash    | string(128)              | Hash alamat IP            |
| token_hash          | string(128)              | Hash token yang digunakan |
| metadata            | json                     | Metadata tambahan         |

### 5.11. Jadual: loan_audits

Menyimpan jejak audit khusus modul pinjaman (rujuk Seksyen 9).

### 5.12. Jadual: status_tokens

| Field          | Tipe Data   | Keterangan                                            |
| -------------- | ----------- | ----------------------------------------------------- |
| id             | bigint, PK  | ID token status                                       |
| token_hash     | string(128) | Hash token                                            |
| reference_type | string(50)  | Model berkaitan (helpdesk_tickets, loan_applications) |
| reference_id   | bigint      | ID model berkaitan                                    |
| expires_at     | timestamp   | Tarikh luput                                          |
| created_at     | timestamp   | Tarikh cipta                                          |

### 5.13. Jadual: personal_access_tokens (Laravel Sanctum v3.5.0)

| Field          | Tipe Data             | Keterangan                    |
| -------------- | --------------------- | ----------------------------- |
| id             | bigint, PK            | ID token                      |
| tokenable_type | string(255)           | Model type (App\Models\User)  |
| tokenable_id   | bigint                | User ID                       |
| name           | string(255)           | Nama token                    |
| token          | string(64)            | Hash token (SHA256)           |
| abilities      | text nullable         | JSON array of abilities       |
| last_used_at   | timestamp nullable    | Tarikh guna terakhir          |
| expires_at     | timestamp nullable    | Tarikh luput                  |
| created_at     | timestamp             | Tarikh cipta                  |
| updated_at     | timestamp             | Tarikh kemaskini              |

**Indeks:** `(tokenable_type, tokenable_id)`, `(token)`

> **Nota v3.5.0:** Jadual Laravel Sanctum untuk API token authentication

### 5.14. Jadual: pulse_aggregates (Laravel Pulse v3.5.0)

| Field     | Tipe Data    | Keterangan              |
| --------- | ------------ | ----------------------- |
| id        | bigint, PK   | ID aggregate            |
| bucket    | int unsigned | Time bucket             |
| period    | mediumint    | Aggregation period      |
| type      | string(255)  | Metric type             |
| key       | text         | Metric key              |
| key_hash  | binary(16)   | MD5 hash of key         |
| aggregate | string(255)  | Aggregation function    |
| value     | decimal      | Aggregated value        |
| count     | int unsigned | Count of entries        |

### 5.15. Jadual: pulse_entries (Laravel Pulse v3.5.0)

| Field     | Tipe Data    | Keterangan              |
| --------- | ------------ | ----------------------- |
| id        | bigint, PK   | ID entry                |
| timestamp | int unsigned | Unix timestamp          |
| type      | string(255)  | Entry type              |
| key       | text         | Entry key               |
| key_hash  | binary(16)   | MD5 hash of key         |
| value     | bigint null  | Entry value             |

### 5.16. Jadual: pulse_values (Laravel Pulse v3.5.0)

| Field     | Tipe Data    | Keterangan              |
| --------- | ------------ | ----------------------- |
| id        | bigint, PK   | ID value                |
| timestamp | int unsigned | Unix timestamp          |
| type      | string(255)  | Value type              |
| key       | text         | Value key               |
| key_hash  | binary(16)   | MD5 hash of key         |
| value     | text         | Stored value            |

> **Nota v3.5.0:** Jadual Laravel Pulse untuk performance monitoring (admin/superuser access)

---

## 6. Hubungan Antara Jadual (Relationships)

- `users` → `helpdesk_tickets.assigned_admin_id`, `loan_transactions.performed_by_admin_id`
- `users` → `personal_access_tokens` (polymorphic via tokenable)
- `helpdesk_tickets` ↔ `helpdesk_comments`, `helpdesk_attachments`, `status_tokens`
- `loan_applications` ↔ `loan_items`, `loan_transactions`, `loan_approvals`, `loan_audits`, `status_tokens`
- `loan_transactions` ↔ `loan_transaction_accessories` (v3.5.0)
- `assets` ↔ `loan_items`, `loan_transactions`
- `departments` → `users.department_id`
- `audits` polymorphic ke semua model dengan `Auditable` trait
- `activity_log` polymorphic ke semua model untuk user activity tracking

---

## 7. Piawaian Kualiti Data (Data Quality Standards)

- **Unik:** `ticket_number`, `reference`, `approval_token_hash`, `status_token_hash`, `google_id`
- **Validasi:** Format e-mel, telefon, tarikh, enumerasi (kategori, status)
- **Kelengkapan:** Medan wajib mesti diisi (tetamu tidak boleh menyahdaya perakuan)
- **Integriti Rujukan:** FK ke `users`, `assets`, `loan_applications`, `loan_transactions`
- **Audit:** Semua perubahan penting dicatat dalam `audits` dan `activity_log`
- **Privasi:** E-mel, telefon, IP disimpan hashed/encrypted di mana sesuai

---

## 8. Backup & Pemulihan (Backup & Recovery)

- Backup MySQL harian (pengepil), retention 30 hari
- Snapshot storan objek (lampiran) mingguan
- Ujian pemulihan dua kali setahun
- Laravel Pulse data retention: 7 hari (auto-pruned)

---

## 9. Audit & Logging (Dual System)

### 9.1. Dual Audit Architecture

#### Package 1: owen-it/laravel-auditing v14.x (COMPLIANCE)

- **Table**: `audits`
- **Purpose**: Field-level change tracking untuk PDPA compliance
- **Models**: `Auditable` trait pada `HelpdeskTicket`, `LoanApplication`, `Asset`, `User`
- **Events logged**: Created, Updated, Deleted, Status Changed
- **Data stored**: old_values, new_values, user_id, IP address, timestamp
- **Retention**: 7 years (compliance requirement)

#### Package 2: spatie/laravel-activitylog v4.x (OPERATIONS)

- **Table**: `activity_log`
- **Purpose**: User activity tracking untuk dashboard dan reports
- **Events logged**: Login, logout, form submissions, status views, approvals
- **Data stored**: description, subject, causer, properties, batch_uuid
- **Use cases**: User "Recent Activity", Filament widgets, admin reports

#### Package 3: Laravel Telescope v5.x (DEBUGGING)

- **Access**: Superuser ONLY (tiada sekatan)
- **Purpose**: System monitoring dan debugging
- **Features**: ALL enabled (requests, commands, jobs, exceptions, logs, queries)
- **Retention**: 7 days (configurable)

#### Package 4: Laravel Pulse v1.3.0 (PERFORMANCE) - v3.5.0

- **Tables**: `pulse_aggregates`, `pulse_entries`, `pulse_values`
- **Access**: Admin and Superuser
- **Purpose**: Real-time performance monitoring
- **Features**: Slow queries, queue metrics, server health, cache stats
- **Retention**: 7 days (auto-pruned)

### 9.2. Jadual: audits (owen-it/laravel-auditing v14.x)

| Field            | Tipe Data             | Keterangan                             |
| ---------------- | --------------------- | -------------------------------------- |
| id               | bigint, PK            | ID audit                               |
| user_type        | string(255) nullable  | Model type of causer                   |
| user_id          | bigint nullable       | ID of causer                           |
| event            | string(255)           | Event type (created, updated, deleted) |
| auditable_type   | string(255)           | Model type being audited               |
| auditable_id     | bigint                | ID of model being audited              |
| old_values       | json nullable         | Previous values                        |
| new_values       | json nullable         | New values                             |
| url              | text nullable         | Request URL                            |
| ip_address       | string(45) nullable   | Client IP address                      |
| user_agent       | string(1023) nullable | Client user agent                      |
| tags             | string(255) nullable  | Custom tags                            |
| guest_identifier | string(255) nullable  | Identifier for guest users             |
| created_at       | timestamp             | Tarikh audit                           |
| updated_at       | timestamp             | Tarikh kemaskini                       |

**Indeks:** `(auditable_type, auditable_id)`, `(user_type, user_id)`, `(event)`, `(created_at)`

---

## 10. Pengurusan Migrasi (Migration Notes)

### 10.1. Migrasi ke v3.5.0 (True Hybrid Architecture)

Migrasi ke v3.5.0 melibatkan:

**Users table expansion:**

- `google_id` (varchar, untuk Google SSO)
- `email_verified_at` (timestamp, untuk email verification)
- `staff_number` (varchar, optional staff ID)
- `locale` (enum: ms, en)
- `notify_email_frequency` (enum: immediate, daily, weekly)
- `notify_in_app` (boolean)
- `guest_submissions_linked` (integer)
- `last_login_at`, `last_login_ip` (tracking)
- `deleted_at` (soft deletes)

**Helpdesk tickets expansion:**

- `form_reference_code` (varchar, kod rujukan borang rasmi)
- `status_token_hash` (varchar, untuk guest status checking)

**Loan applications expansion:**

- `form_reference_code` (varchar, kod rujukan borang rasmi)
- `is_applicant_responsible` (boolean, Applicant = Responsible Officer)
- `responsible_officer_name` (varchar, nama pegawai bertanggungjawab)
- `responsible_officer_grade` (varchar, gred pegawai)
- `responsible_officer_phone` (varchar, telefon pegawai)
- `responsible_officer_acknowledgement` (boolean, perakuan pegawai)

**New tables:**

- `loan_transaction_accessories` (aksesori check-out/check-in)
- `personal_access_tokens` (Laravel Sanctum API tokens)
- `pulse_aggregates`, `pulse_entries`, `pulse_values` (Laravel Pulse)

**Index additions:**

- `idx_google_id` untuk Google SSO lookup
- `idx_form_reference_code` untuk form tracking

### 10.2. Migrasi Terdahulu (v3.0.0)

Migrasi ke v3.0.0 melibatkan:

- Menambah medan `submitter_*` pada `helpdesk_tickets`
- Menghapus kebergantungan `user_id` bagi tetamu
- Menyemak `loan_approvals` supaya menyimpan e-mel pegawai secara eksplisit
- Table rename: `divisions` → `departments`

Skrip migrasi diselaras melalui `database/migrations` (rujuk D05 & D06).

---

## 11. Glosari & Rujukan (Glossary & References)

- **Tetamu:** Pengguna tanpa akaun yang mengisi borang
- **Signed Approval Link:** Pautan ber-token untuk kelulusan e-mel
- **Status Token:** Token unik membolehkan tetamu menyemak status permohonan/tiket
- **Self-Registration**: Proses staff mendaftar akaun sendiri dengan e-mel @motac.gov.my
- **Flexible Login**: Log masuk dengan e-mel penuh ATAU username pendek
- **Account Linking**: Proses pilihan untuk menghubungkan submissions tetamu kepada akaun baharu
- **Google SSO**: OAuth 2.0 authentication via Google Workspace (optional)
- **Responsible Officer**: Pegawai bertanggungjawab untuk aset pinjaman (v3.5.0)
- **Accessory Tracking**: Tracking aksesori semasa check-out/check-in (v3.5.0)
- **Form Reference Code**: Kod rujukan borang rasmi (PK.(S).MOTAC.07.(L1/L3))
- **Dual Audit System**: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Laravel Pulse**: Performance monitoring untuk admin/superuser (v3.5.0)
- **Laravel Sanctum**: API token authentication (v3.5.0)
- **Laravel Telescope**: Alat debugging untuk superuser (tiada sekatan akses)

Rujuk [GLOSSARY.md](GLOSSARY.md) untuk istilah tambahan.

---

## 12. Lampiran (Appendices)

### A. ERD

ERD dikemas kini boleh didapati dalam repositori `/design/erd/ictserve_v3.5.0.png`.

### B. Definisi Lengkap

Fail CSV terdapat di `/docs/rtm/*` untuk pemetaan keperluan ↔ jadual.

### C. Piawaian Penamaan

- Jadual & medan menggunakan huruf kecil + `_`
- Enum menggunakan huruf besar (snake case)

### D. Daftar Indeks & Prestasi

Indeks utama:

- `helpdesk_tickets_ticket_number_unique`
- `loan_applications_reference_unique`
- `loan_approvals_token_hash_index`
- `users_google_id_index` (v3.5.0)
- `loan_transaction_accessories_transaction_id_index` (v3.5.0)

Analisis prestasi disimpan dalam `performance-optimization-report.md`.

---

## 13. Penutup

Dokumentasi pangkalan data ini memastikan struktur ICTServe v3.5.0 konsisten dengan
True Hybrid Architecture: staff boleh self-register atau guna guest forms, kelulusan
dikendalikan melalui token bertanda tangan, dan dual audit menyeluruh mengekalkan
integriti data. Semua perubahan tambahan hendaklah mematuhi proses pengurusan
perubahan D01 §9.3.

**True Hybrid Architecture v3.5.0 Database Features:**

- Google SSO support via `google_id` field
- Responsible Officer tracking untuk loan applications
- Accessory tracking dengan `loan_transaction_accessories` table
- Form reference codes untuk compliance
- Laravel Pulse tables untuk performance monitoring
- Laravel Sanctum `personal_access_tokens` untuk API authentication

---

**Dokumen ini mematuhi piawaian ISO 8000:2022 (Data Quality), ISO/IEC/IEEE 1016:2009,
ISO/IEC 27701:2019, dan ISO/IEC 38505-1:2017.**
