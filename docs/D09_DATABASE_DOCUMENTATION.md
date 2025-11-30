# Dokumentasi Pangkalan Data (Database Documentation)

**Sistem ICTServe**
**Versi:** 3.5.0 (SemVer)
**Tarikh Kemaskini:** 30 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman BPM MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                       |
| -------------------- | ----------------------------------------------------------- |
| **Versi**            | 3.5.0                                                       |
| **Tarikh Kemaskini** | 30 November 2025                                            |
| **Status**           | Aktif                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                  |
| **Pematuhi**         | ISO 8000, ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                   |

> Notis Penggunaan Dalaman: Semua skema dan jadual adalah untuk sistem dalaman
> MOTAC; tiada data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                                                                                                                                               | Penulis                 |
| ----- | ---------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D04 v3.5.0. Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only). Hapus rujukan LDAP/SSO. Tukar `divisions` kepada `departments`. Tambah medan baharu users table (email*verified_at, locale, notify*\*, staff_number, guest_submissions_linked). Pematuhan Jabatan Digital Negara. | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Restored nullable user_id FK in helpdesk_tickets/loan_applications. Added staff role to users. Added department_id, grade columns to users. Penyelarasan dengan D00/D02/D03/D04 v3.4.0.                                                                                                                                                                                                                                                            | Pasukan Pembangunan BPM |
| 3.3.0 | 29 November 2025 | Hybrid data model: users table extended for Staff profiles, submissions support both authenticated Staff and Guests                                                                                                                                                                                                                                                                                                                                                     | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.40.1, Filament 4.1.10, MySQL 8.0                                                                                                                                                                                                                                                                                                                                                                                                        | Pasukan Pembangunan BPM |
| 3.1.0 | 6 Januari 2025   | Kemaskini struktur database untuk Laravel Reverb 1.6.2 WebSocket                                                                                                                                                                                                                                                                                                                                                                                                        | Pasukan Pembangunan BPM |
| 3.0.1 | 31 Oktober 2025  | Standardisasi pautan rujukan ke GLOSSARY                                                                                                                                                                                                                                                                                                                                                                                                                                | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Penyelarasan pangkalan data kepada seni bina dalaman (internal-only)                                                                                                                                                                                                                                                                                                                                                                                                    | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                                                                                                                                                  | Pasukan BPM             |
| 1.0.0 | September 2025   | Versi awal dokumentasi pangkalan data                                                                                                                                                                                                                                                                                                                                                                                                                                   | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md)
- [D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
- [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D05_DATA_MIGRATION_PLAN.md](D05_DATA_MIGRATION_PLAN.md)
- [D06_DATA_MIGRATION_SPECIFICATION.md](D06_DATA_MIGRATION_SPECIFICATION.md)
- [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md)
- [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [GLOSSARY.md](GLOSSARY.md)

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menjelaskan struktur, definisi medan, piawaian kualiti data, dan
hubungan jadual bagi ICTServe sebagai sistem dalaman (internal-only). Akaun
pengguna staf dan pentadbir disimpan dalam jadual `users` dengan kawalan peranan;
kelulusan dan audit direkod secara menyeluruh.

---

## 2. Skop (Scope)

- Semua jadual utama yang menyokong borang dalaman Helpdesk & Asset Loan, panel
  Filament, kelulusan berperingkat, audit, notifikasi, dan pemantauan status.
- Piawaian data, kawalan kualiti, dan persediaan migrasi.
- Tidak meliputi modul RBAC legasi yang tidak lagi digunakan.

---

## 3. Teknologi Pangkalan Data (Database Technology)

| Komponen           | Teknologi         | Versi   | Fungsi                             |
| ------------------ | ----------------- | ------- | ---------------------------------- |
| RDBMS              | MySQL             | 8.x     | Production database                |
| Development DB     | SQLite            | 3.x     | Development/testing database       |
| ORM                | Eloquent          | 12.40.1 | Laravel ORM                        |
| Migrations         | Laravel           | 12.40.1 | Schema version control             |
| Caching            | Redis             | -       | Query caching                      |
| Audit (Compliance) | Laravel Auditing  | 14.x    | Field-level audit trail (owen-it)  |
| Audit (Operations) | Activity Log      | 4.x     | User activity logging (spatie)     |
| Permissions        | Spatie Permission | 6.23    | Role-based access control          |
| Debugging          | Laravel Telescope | 5.x     | System monitoring (superuser only) |

---

## 4. Reka Bentuk Logikal (Logical Database Design)

### 4.1. Senarai Jadual Utama (Main Tables)

| Jadual               | Fungsi                                                    |
| -------------------- | --------------------------------------------------------- |
| users                | Akaun pengguna staf & pentadbir (portal & panel Filament) |
| departments          | Rujukan bahagian/unit MOTAC                               |
| audits               | Jejak audit field-level (owen-it/laravel-auditing)        |
| helpdesk_tickets     | Rekod tiket helpdesk pengguna dalaman                     |
| helpdesk_comments    | Komen pentadbir terhadap tiket                            |
| helpdesk_attachments | Fail lampiran tiket                                       |
| loan_applications    | Permohonan pinjaman aset pengguna dalaman                 |
| loan_items           | Item aset dalam permohonan                                |
| loan_transactions    | Pengeluaran & pemulangan aset                             |
| loan_approvals       | Rekod kelulusan e-mel bertanda tangan                     |
| loan_audits          | Jejak audit khusus modul pinjaman                         |
| status_tokens        | Token semakan status tetamu (opsyen)                      |
| activity_log         | Log aktiviti sistem (Spatie)                              |
| notifications        | Notifikasi Laravel                                        |

---

## 5. Definisi Jadual & Field (Table & Field Definitions)

### 5.1. Jadual: users

| Field                    | Tipe Data                      | Keterangan                               | Kualiti Data        |
| ------------------------ | ------------------------------ | ---------------------------------------- | ------------------- |
| id                       | bigint, PK                     | ID pengguna                              | Unique, not null    |
| name                     | string(255)                    | Nama pegawai                             | Not null            |
| email                    | string(255)                    | E-mel kerajaan @motac.gov.my (unik)      | Unique, not null    |
| email_verified_at        | timestamp (nullable)           | Tarikh pengesahan e-mel                  | Required for access |
| phone                    | string(30)                     | Telefon pegawai                          | Not null            |
| department_id            | bigint, FK nullable            | FK → departments.id                      | Optional            |
| grade                    | string(50) nullable            | Gred pegawai (e.g. 41, 44)               | Optional            |
| staff_number             | string(50) nullable            | Nombor staf (optional)                   | Optional            |
| role                     | enum(staff, admin, superuser)  | Peranan sistem (DEFAULT 'staff')         | Not null            |
| password                 | string(255)                    | Hash kata laluan                         | Not null            |
| two_factor_secret        | text (nullable)                | Rahsia TOTP (untuk superuser)            | Optional, encrypted |
| two_factor_confirmed_at  | timestamp (nullable)           | Tarikh pengesahan 2FA                    | Optional            |
| locale                   | enum(ms, en)                   | Bahasa pilihan (DEFAULT 'ms')            | Not null            |
| notify_email_frequency   | enum(immediate, daily, weekly) | Kekerapan e-mel (DEFAULT 'immediate')    | Not null            |
| notify_in_app            | boolean                        | Notifikasi dalam aplikasi (DEFAULT TRUE) | Not null            |
| guest_submissions_linked | integer                        | Bilangan submissions dilink (DEFAULT 0)  | Not null            |
| remember_token           | string(100) nullable           | Token remember me                        | Optional            |
| last_login_at            | timestamp (nullable)           | Tarikh login terakhir                    | Optional            |
| last_login_ip            | string(45) nullable            | IP login terakhir                        | Optional            |
| created_at               | timestamp                      | Tarikh cipta                             | Not null            |
| updated_at               | timestamp                      | Tarikh kemaskini                         | Not null            |
| deleted_at               | timestamp (nullable)           | Soft delete timestamp                    | Optional            |

**Indeks:** `(email)`, `(email_prefix via SUBSTRING_INDEX)`, `(role)`, `(department_id)`, `(staff_number)`

> **Nota True Hybrid Architecture v3.5.0:** Jadual ini menyimpan:
>
> - **Admin/Superuser**: Akaun pentadbiran penuh (role='admin' atau 'superuser')
> - **Staff**: Pegawai MOTAC (role='staff'), boleh self-register dengan @motac.gov.my
> - **Guest**: Tidak disimpan dalam jadual users; submissions dengan user_id=NULL
>
> **Self-Registration & Login:**
>
> - **Self-Registration**: Staf mendaftar dengan e-mel @motac.gov.my (email verification WAJIB)
> - **Flexible Login**: E-mel penuh (`user@motac.gov.my`) ATAU username pendek (`user`)
> - **Admin Creation**: Admin/Superuser boleh mencipta akaun melalui Filament
> - **Tiada LDAP/SSO**: Semua authentication melalui Laravel Breeze sahaja
>
> **Optional Account Linking:**
>
> - Selepas pendaftaran, jika e-mel sepadan dengan submissions sedia ada, paparkan prompt
> - Pengguna boleh pilih link ATAU decline
> - `guest_submissions_linked` merekod bilangan submissions yang dilink
>
> **Staff Capabilities (role='staff')**:
>
> - `view-own-history`: Lihat tiket/permohonan sendiri (WHERE user_id = Auth::id())
> - `edit-profile`: Kemaskini maklumat peribadi (name, phone, department*id, grade, locale, notify*\*)
> - `access-dashboard`: Akses Dashboard Staf (My Dashboard)
> - `submit-as-authenticated`: Hantar tiket/permohonan dengan user_id linkage (auto-fill forms)
> - `link-guest-submissions`: Link submissions tetamu sedia ada kepada akaun

### 5.2. Jadual: departments

| Field      | Tipe Data           | Keterangan                     | Kualiti Data |
| ---------- | ------------------- | ------------------------------ | ------------ |
| id         | bigint, PK          | ID bahagian                    | Unique       |
| code       | string(20)          | Kod bahagian (unik)            | Unique       |
| name       | string(255)         | Nama bahagian                  | Not null     |
| parent_id  | bigint, FK nullable | FK → departments.id (hierarki) | Optional     |
| created_at | timestamp           | Tarikh cipta                   | Not null     |
| updated_at | timestamp           | Tarikh kemaskini               | Not null     |

### 5.3. Jadual: helpdesk_tickets

| Field                   | Tipe Data                                                | Keterangan                         |
| ----------------------- | -------------------------------------------------------- | ---------------------------------- |
| id                      | bigint, PK                                               | ID tiket                           |
| ticket_number           | string(20)                                               | Nombor tiket unik (HD-YYYYMM-XXXX) |
| user_id                 | bigint, FK nullable                                      | FK → users.id (NULL jika Guest)    |
| submitter_name          | string(255)                                              | Nama penghantar                    |
| submitter_email         | string(255)                                              | E-mel penghantar                   |
| submitter_phone         | string(50)                                               | Telefon penghantar                 |
| submitter_division_code | string(20)                                               | Kod bahagian                       |
| submitter_grade         | string(50)                                               | Gred (optional)                    |
| category                | string(100)                                              | Kategori kerosakan                 |
| priority                | enum(LOW, MEDIUM, HIGH, CRITICAL)                        | Keutamaan SLA                      |
| description             | text                                                     | Maklumat kerosakan                 |
| asset_tag               | string(100)                                              | Tag aset (optional)                |
| declaration             | boolean                                                  | Perakuan PDPA (mesti TRUE)         |
| status                  | enum(OPEN, IN_PROGRESS, AWAITING_INFO, RESOLVED, CLOSED) | Status tiket                       |
| assigned_admin_id       | bigint, FK nullable                                      | FK → users.id                      |
| sla_due_at              | timestamp                                                | Tarikh sasaran SLA                 |
| closed_at               | timestamp                                                | Tarikh tiket ditutup               |
| created_at              | timestamp                                                | Tarikh cipta                       |
| updated_at              | timestamp                                                | Tarikh kemaskini                   |

**Indeks:** `(user_id, status)`, `(submitter_email, status)`, `(ticket_number)`

> **Nota Hybrid:** `user_id` NULL = Guest submission; NOT NULL = Authenticated Staff submission

---

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

| Field                     | Tipe Data           | Keterangan                       |
| ------------------------- | ------------------- | -------------------------------- |
| id                        | bigint, PK          | ID permohonan                    |
| reference                 | string(20)          | Kod rujukan (LA-YYYYMM-XXXX)     |
| user_id                   | bigint, FK nullable | FK → users.id (NULL jika Guest)  |
| applicant_name            | string(255)         | Nama pemohon                     |
| applicant_email           | string(255)         | E-mel pemohon                    |
| applicant_phone           | string(50)          | Telefon pemohon                  |
| applicant_division_code   | string(20)          | Kod bahagian                     |
| applicant_grade           | string(50)          | Gred pemohon                     |
| purpose                   | text                | Tujuan pinjaman                  |
| location                  | string(255)         | Lokasi penggunaan                |
| loan_start_date           | date                | Tarikh mula                      |
| loan_end_date             | date                | Tarikh akhir                     |
| acknowledgement           | boolean             | Perakuan PDPA                    |
| status                    | enum                | Status permohonan                |
| approval_token_hash       | string(128)         | Hash token kelulusan (SHA512)    |
| approval_token_expires_at | timestamp           | Tarikh luput token               |
| status_token_hash         | string(128)         | Hash token semakan status tetamu |
| created_at                | timestamp           | Tarikh cipta                     |
| updated_at                | timestamp           | Tarikh kemaskini                 |

**Indeks:** `(user_id, status)`, `(applicant_email, status)`, `(reference)`

> **Nota Hybrid:** `user_id` NULL = Guest submission; NOT NULL = Authenticated Staff submission

**Status enum values:** PENDING_SUPERVISOR_APPROVAL, APPROVED, REJECTED,
AWAITING_COLLECTION, ON_LOAN, RETURNED, DAMAGED

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
| type                  | enum(CHECK_OUT, CHECK_IN) | Jenis transaksi           |
| performed_by_admin_id | bigint, FK                | FK → users.id             |
| performed_at          | timestamp                 | Tarikh tindakan           |
| condition_notes       | text                      | Catatan keadaan aset      |
| attachments_json      | json (optional)           | Bukti foto/document       |

### 5.9. Jadual: loan_approvals

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

### 5.10. Jadual: loan_audits

Menyimpan jejak audit khusus modul pinjaman (rujuk Seksyen 9).

### 5.11. Jadual: status_tokens

| Field          | Tipe Data   | Keterangan                                            |
| -------------- | ----------- | ----------------------------------------------------- |
| id             | bigint, PK  | ID token status                                       |
| token_hash     | string(128) | Hash token                                            |
| reference_type | string(50)  | Model berkaitan (helpdesk_tickets, loan_applications) |
| reference_id   | bigint      | ID model berkaitan                                    |
| expires_at     | timestamp   | Tarikh luput                                          |
| created_at     | timestamp   | Tarikh cipta                                          |

---

## 6. Hubungan Antara Jadual (Relationships)

- `users` → `helpdesk_tickets.assigned_admin_id`, `loan_transactions.performed_by_admin_id`
- `helpdesk_tickets` ↔ `helpdesk_comments`, `helpdesk_attachments`, `status_tokens`
- `loan_applications` ↔ `loan_items`, `loan_transactions`, `loan_approvals`,
  `loan_audits`, `status_tokens`
- `assets` ↔ `loan_items`, `loan_transactions`
- `departments` digunakan untuk memvalidasi `submitter_division_code` & `applicant_division_code` (melalui kamus); juga FK untuk `users.department_id`
- `audits` polymorphic ke semua model yang menggunakan `Auditable` trait (HelpdeskTicket, LoanApplication, Asset, User)
- `activity_log` polymorphic ke semua model untuk user activity tracking

---

## 7. Piawaian Kualiti Data (Data Quality Standards)

- **Unik:** `ticket_number`, `reference`, `approval_token_hash`, `status_token_hash`
- **Validasi:** Format e-mel, telefon, tarikh, enumerasi (kategori, status)
- **Kelengkapan:** Medan wajib mesti diisi (tetamu tidak boleh menyahdaya perakuan)
- **Integriti Rujukan:** FK ke `users`, `assets`, `loan_applications`
- **Audit:** Semua perubahan penting dicatat dalam `activity_log` dan `loan_audits`
- **Privasi:** E-mel, telefon, IP disimpan hashed/encrypted di mana sesuai

---

## 8. Backup & Pemulihan (Backup & Recovery)

- Backup MySQL harian (pengepil), retention 30 hari
- Snapshot storan objek (lampiran) mingguan
- Ujian pemulihan dua kali setahun

---

## 9. Audit & Logging (Dual System)

### 9.1. Dual Audit Architecture

**Package 1: owen-it/laravel-auditing v14.x (COMPLIANCE)**

- **Table**: `audits`
- **Purpose**: Field-level change tracking untuk PDPA compliance
- **Models**: `Auditable` trait pada `HelpdeskTicket`, `LoanApplication`, `Asset`, `User`
- **Events logged**: Created, Updated, Deleted, Status Changed
- **Data stored**: old_values, new_values, user_id, IP address, timestamp
- **Retention**: 7 years (compliance requirement)

**Package 2: spatie/laravel-activitylog v4.x (OPERATIONS)**

- **Table**: `activity_log`
- **Purpose**: User activity tracking untuk dashboard dan reports
- **Events logged**: Login, logout, form submissions, status views, approvals
- **Data stored**: description, subject, causer, properties, batch_uuid
- **Use cases**: User "Recent Activity", Filament widgets, admin reports

**Package 3: Laravel Telescope v5.x (DEBUGGING)**

- **Access**: Superuser ONLY (tiada sekatan)
- **Purpose**: System monitoring dan debugging
- **Features**: ALL enabled (requests, commands, jobs, exceptions, logs, queries)
- **Retention**: 7 days (configurable)

### 9.2. Additional Audit Tables

- `loan_audits` menyimpan rekod granular khusus modul pinjaman (permohonan, kelulusan, pengembalian)
- `audit_exports` (opsyen) menyimpan eksport yang dihantar ke SIEM
- Log kelulusan dalam `loan_approvals` menyimpan `token_hash`, `decision_at`, `decision_ip_hash` bagi pengesahan

---

### 9.3. Jadual: audits (owen-it/laravel-auditing v14.x)

| Field            | Tipe Data             | Keterangan                             |
| ---------------- | --------------------- | -------------------------------------- |
| id               | bigint, PK            | ID audit                               |
| user_type        | string(255) nullable  | Model type of causer                   |
| user_id          | bigint nullable       | ID of causer                           |
| event            | string(255)           | Event type (created, updated, deleted) |
| auditable_type   | string(255)           | Model type being audited               |
| auditable_id     | bigint                | ID of model being audited              |
| old_values       | json nullable         | Previous values (for updates/deletes)  |
| new_values       | json nullable         | New values (for creates/updates)       |
| url              | text nullable         | Request URL                            |
| ip_address       | string(45) nullable   | Client IP address                      |
| user_agent       | string(1023) nullable | Client user agent                      |
| tags             | string(255) nullable  | Custom tags                            |
| guest_identifier | string(255) nullable  | Identifier for guest users             |
| created_at       | timestamp             | Tarikh audit                           |
| updated_at       | timestamp             | Tarikh kemaskini                       |

**Indeks:** `(auditable_type, auditable_id)`, `(user_type, user_id)`, `(event)`, `(created_at)`, `(guest_identifier)`

> **Nota**: Jadual ini untuk **field-level audit trail** bagi pematuhan PDPA dan keperluan audit 7 tahun. Digunakan bersama `activity_log` (spatie) untuk dual audit system.

---

## 10. Pengurusan Migrasi (Migration Notes)

### 10.1. Migrasi ke v3.5.0 (True Hybrid Architecture)

Migrasi ke v3.5.0 melibatkan:

- **Users table expansion**: Menambah medan baharu:
  - `email_verified_at` (timestamp, untuk email verification)
  - `staff_number` (varchar, optional staff ID)
  - `locale` (enum: ms, en)
  - `notify_email_frequency` (enum: immediate, daily, weekly)
  - `notify_in_app` (boolean)
  - `guest_submissions_linked` (integer)
  - `last_login_at`, `last_login_ip` (tracking)
  - `deleted_at` (soft deletes)
- **Index additions**:
  - `idx_email_prefix` untuk flexible login (username search)
  - `idx_staff_number` untuk staff lookup
- **Audit tables**:
  - `audits` (owen-it/laravel-auditing v14.x) untuk compliance
  - `activity_log` (spatie/laravel-activitylog v4.x) untuk operations
- **Table rename**: `divisions` → `departments`

- **Self-registration support**: Email domain validation (@motac.gov.my)

### 10.2. Migrasi Terdahulu (v3.0.0)

Migrasi ke v3.0.0 melibatkan:

- Menambah medan `submitter_*` pada `helpdesk_tickets`
- Menghapus kebergantungan `user_id` bagi tetamu
- Menyemak `loan_approvals` supaya menyimpan e-mel pegawai secara eksplisit
- Menyahaktifkan/memadam data peranan lama (`staff`, `technician`, `approver`)

Skrip migrasi diselaras melalui `database/migrations` (rujuk D05 & D06).

Seeder `RolePermissionSeeder` digantikan dengan `AdminUserSeeder` (mewujudkan
`admin` dan `superuser` sahaja).

---

## 11. Glosari & Rujukan (Glossary & References)

- **Tetamu:** Pengguna tanpa akaun yang mengisi borang
- **Signed Approval Link:** Pautan ber-token untuk kelulusan e-mel
- **Status Token:** Token unik membolehkan tetamu menyemak status permohonan/tiket
- **Self-Registration**: Proses staff mendaftar akaun sendiri dengan e-mel @motac.gov.my
- **Flexible Login**: Log masuk dengan e-mel penuh ATAU username pendek selepas pendaftaran
- **Account Linking**: Proses pilihan untuk menghubungkan submissions tetamu kepada akaun baharu
- **Dual Audit System**: Penggunaan owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Laravel Telescope**: Alat debugging untuk superuser (tiada sekatan akses)

Rujuk [GLOSSARY.md](GLOSSARY.md) untuk istilah tambahan.

---

## 12. Lampiran (Appendices)

### A. ERD

ERD dikemas kini boleh didapati dalam repositori `/design/erd/ictserve_guest-first.png`.

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

Analisis prestasi disimpan dalam `performance-optimization-report.md`.

---

## 13. Penutup

Dokumentasi pangkalan data ini memastikan struktur ICTServe konsisten dengan
realiti guest-first: tetamu tidak mewujudkan rekod pengguna, kelulusan dikendalikan
melalui token bertanda tangan, dan audit menyeluruh mengekalkan integriti data.
Semua perubahan tambahan hendaklah mematuhi proses pengurusan perubahan D01 §9.3.

---

**Dokumen ini mematuhi piawaian ISO 8000:2022 (Data Quality), ISO/IEC/IEEE 1016:2009,
ISO/IEC 27701:2019, dan ISO/IEC 38505-1:2017.**
