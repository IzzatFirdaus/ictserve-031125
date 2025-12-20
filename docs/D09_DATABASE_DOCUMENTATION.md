# Dokumentasi Pangkalan Data (Database Documentation)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), IEEE 1016:2009 (Huraian Reka Bentuk Perisian), ISO/IEC 27701, ISO/IEC 38505-1, ISO/IEC 33063:2015, ISO/IEC/IEEE 15289:2019, ISO 8601, TLS 1.3, AES-256, RFC 5322

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                       |
| -------------------- | ----------------------------------------------------------- |
| **Versi**            | 3.6.1                                                       |
| **Tarikh Kemaskini** | 17 Disember 2025                                            |
| **Status**           | Aktif                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                  |
| **Pematuhi**         | ISO 8000, ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1, ISO/IEC 33063:2015, ISO/IEC/IEEE 15289:2019, ISO 8601, TLS 1.3, AES-256, RFC 5322 |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu  |

> Notis Penggunaan Dalaman: Semua skema dan jadual adalah untuk sistem dalaman
> MOTAC; tiada data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan | Penulis |
| ----- | ---------------- | --------- | ------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. Cloud Hybrid AI Architecture: Integrasi D18 AI Chatbot Ollama-Bedrock v1.0.0. Tambah jadual AI: `faqs`, `documents`, `document_chunks`, `embeddings`, `message_logs`, `bedrock_conversations`, `auto_reply_templates`, `auto_reply_drafts`. Multi-model intelligence, streaming responses, web-augmented responses, conversation management. Penyelarasan dengan D00-D18 v3.6.1. | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D08 v3.5.0. Tambah jadual `loan_transaction_accessories`, `personal_access_tokens`, `pulse_*`. Tambah medan `google_id`, `form_reference_code`, `responsible_officer_*`. Laravel Pulse, Sanctum API, Google SSO. | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Restored nullable user_id FK. Added staff role to users. | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Hybrid data model: users table extended for Staff profiles | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.43.1, Filament 4.3.1, MySQL 8.0 | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal dokumentasi pangkalan data | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - [D18_AI_CHATBOT_OLLAMA_BEDROCK.md](D18_AI_CHATBOT_OLLAMA_BEDROCK.md)
- [GLOSSARY.md](GLOSSARY.md)

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menjelaskan struktur, definisi medan, piawaian kualiti data, dan
hubungan jadual bagi ICTServe sebagai sistem dalaman (internal-only).

---

## 2. Skop (Scope)

- Semua jadual utama yang menyokong borang dalaman Helpdesk & Asset Loan
- Panel Filament, kelulusan berperingkat, audit, notifikasi, dan pemantauan status
- **Cloud Hybrid AI Architecture (D18 v1.0.0)**: FAQ Bot, Document Analysis, Auto-Reply, Conversation Management
- Piawaian data, kawalan kualiti, dan persediaan migrasi

---

## 2.1. Piawaian dan Garis Panduan (Standards and Guidelines)

### 2.1.1. Piawaian Proses dan Dokumentasi (Process and Documentation Standards)

| Piawaian | Versi | Aplikasi dalam ICTServe |
|----------|-------|-------------------------|
| **ISO/IEC 33063:2015** | 2015 | Pengukuran proses dalam integrasi sistem pangkalan data |
| **ISO/IEC/IEEE 15289:2019** | 2019 | Keperluan dokumentasi sistem dan perisian untuk pangkalan data |
| **ISO/IEC TS 24748-6** | Latest | Panduan teknikal pengurusan kitaran hayat dokumentasi |
| **IEEE 1016:2009** | 2009 | Huraian reka bentuk perisian untuk struktur pangkalan data |

### 2.1.2. Piawaian Teknikal Data dan Komunikasi (Technical Data and Communication Standards)

| Piawaian | Versi | Aplikasi dalam ICTServe |
|----------|-------|-------------------------|
| **RFC 5322** | 2008 | Format e-mel yang sah untuk migrasi dan integrasi data |
| **ISO 8601** | 2019 | Format tarikh dan masa seragam di seluruh pangkalan data |
| **TLS 1.3** | 2018 | Penyulitan data sensitif semasa penghantaran |
| **AES-256** | Current | Penyulitan data sensitif semasa penyimpanan |

### 2.1.3. Garis Panduan Kerajaan Malaysia (Malaysian Government Guidelines)

| Garis Panduan | Versi | Aplikasi dalam ICTServe |
|---------------|-------|-------------------------|
| **MyGovEA 18 Prinsip** | v2.1.0 | Kerangka kerja seni bina perusahaan untuk reka bentuk pangkalan data |
| **MDGDM** | Latest | Manual Reka Bentuk Digital Kerajaan Malaysia untuk struktur data |
| **DDSA** | Current | Digital Document Standard Architecture untuk arkib dan dokumen digital |

### 2.1.4. Piawaian Keselamatan dan Reka Bentuk (Security and Design Standards)

| Piawaian | Versi | Aplikasi dalam ICTServe |
|----------|-------|-------------------------|
| **OWASP Transport Security** | Latest | Keselamatan saluran komunikasi data sistem |
| **ISO 9241-210** | 2019 | Reka bentuk berpusatkan manusia untuk antara muka pangkalan data |

> **Nota Pematuhan**: Semua piawaian tambahan ini dilaksanakan bersama dengan piawaian sedia ada (ISO 8000, ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1) untuk memastikan sistem ICTServe mematuhi keperluan organisasi kerajaan yang komprehensif.

---

## 3. Teknologi Pangkalan Data (Database Technology)

| Komponen             | Teknologi         | Versi   | Fungsi                                    |
| -------------------- | ----------------- | ------- | ----------------------------------------- |
| RDBMS                | MySQL             | 8.x     | Production database                       |
| Development DB       | SQLite            | 3.x     | Development/testing database              |
| ORM                  | Eloquent          | 12.43.1 | Laravel ORM                               |
| Migrations           | Laravel           | 12.43.1 | Schema version control                    |
| Caching              | Redis             | 7.x     | Query caching                             |
| Audit (Compliance)   | Laravel Auditing  | 14.x    | Field-level audit trail (owen-it)         |
| Audit (Operations)   | Activity Log      | 4.x     | User activity logging (spatie)            |
| Performance Monitor  | Laravel Pulse     | 1.4.7   | Performance metrics & server health       |
| API Authentication   | Laravel Sanctum   | 4.2.1   | Token-based API authentication            |
| Permissions          | Spatie Permission | 6.23    | Role-based access control                 |
| Debugging            | Laravel Telescope | 5.x     | System monitoring (superuser only)        |
| Queue Management     | Laravel Horizon   | 5.41.0  | Redis queue monitoring & management       |
| **AI Local (Ollama)**| Ollama Server     | Latest  | Local LLM untuk FAQ Bot (D18 v1.0.0)     |
| **AI Cloud (Bedrock)**| AWS Bedrock      | Latest  | Claude models untuk complex reasoning     |
| **Vector Search**    | MySQL JSON        | 8.x     | Embedding storage dan semantic search     |
| **AI Queue**         | Laravel Queue     | Redis   | Background AI jobs (document ingestion, embeddings, auto-reply) |

---

## 4. Reka Bentuk Logikal (Logical Database Design)

### 4.1. Senarai Jadual Utama (Main Tables)

| Jadual                       | Fungsi                                                    |
| ---------------------------- | --------------------------------------------------------- |
| users                        | Akaun pengguna staf & pentadbir (portal & panel Filament) |
| divisions                    | Rujukan bahagian/unit MOTAC                               |
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
| **faqs**                     | **FAQ knowledge base untuk AI Bot (D18 v1.0.0)**          |
| **documents**                | **Dokumen untuk analisis AI (D18 v1.0.0)**                |
| **document_chunks**          | **Chunks dokumen untuk RAG (D18 v1.0.0)**                 |
| **embeddings**               | **Vector embeddings untuk semantic search (D18 v1.0.0)**  |
| **message_logs**             | **Log interaksi AI dengan pengguna (D18 v1.0.0)**         |
| **bedrock_conversations**    | **Conversation management untuk AI (D18 v1.0.0)**         |
| **auto_reply_templates**     | **Template auto-reply AI (D18 v1.0.0)**                   |
| **auto_reply_drafts**        | **Draf auto-reply yang dijana AI (D18 v1.0.0)**           |

---

## 5. Definisi Jadual & Field (Table & Field Definitions)

### 5.1. Jadual: users

> **Source of truth (skema sebenar)**: `database/migrations/2025_11_03_043900_create_users_table.php`

Ringkasan medan penting (bukan senarai penuh):

| Field         | Tipe Data | Keterangan |
|--------------|----------|-----------|
| id           | bigint, PK | ID pengguna |
| name         | string(255) | Nama pegawai |
| email        | string(255) | E-mel kerajaan (unik) |
| email_verified_at | timestamp (nullable) | Tarikh pengesahan e-mel |
| role         | enum | `staff` / `approver` / `admin` / `superuser` |
| staff_number | string(50) nullable | Nombor staf (opsyen) |
| division_code| string(20) nullable | Kod bahagian/unit (string) |
| division_id  | bigint, FK nullable | FK → `divisions.id` (ON DELETE SET NULL) |
| grade_id     | bigint, FK nullable | FK → `grades.id` (ON DELETE SET NULL) |
| position_id  | bigint, FK nullable | FK → `positions.id` (ON DELETE SET NULL) |
| phone/mobile | string(20) nullable | Nombor telefon |
| locale       | string(10) | **DEPRECATED v3.6.0**: sentiasa `ms` |
| google_id    | string(255) nullable | Google OAuth ID (opsyen SSO) |
| is_active    | boolean | Status akaun |
| last_login_at| timestamp (nullable) | Tarikh login terakhir |
| created_at/updated_at/deleted_at | timestamp | Timestamps + soft delete |

**Indeks (ringkasan)**: `(email)`, `(role)`, `(division_id, grade_id)`, `(division_code)`, `(staff_number)`, `(google_id)`

> **Nota True Hybrid Architecture v3.5.0:**
>
> - **Admin/Superuser**: Akaun pentadbiran penuh (role='admin' atau 'superuser')
> - **Staff**: Pegawai MOTAC (role='staff'), boleh self-register dengan @motac.gov.my
> - **Guest**: Tidak disimpan dalam jadual users; submissions dengan user_id=NULL
> - **Google SSO**: Optional OAuth 2.0 login via `google_id` field

### 5.2. Jadual: divisions

> **Source of truth (skema sebenar)**: `database/migrations/2025_11_03_043832_create_divisions_table.php`

| Field      | Tipe Data | Keterangan |
|-----------|----------|-----------|
| id        | bigint, PK | ID bahagian/unit |
| code      | string(50) | Kod bahagian/unit (unik) |
| name_ms   | string     | Nama (BM) |
| name_en   | string     | Nama (EN) |
| parent_id | bigint, FK nullable | FK → `divisions.id` (hierarki) |
| is_active | boolean | Status aktif |
| created_at/updated_at/deleted_at | timestamp | Timestamps + soft delete |

### 5.3. Jadual: helpdesk_tickets

> **Source of truth (skema sebenar)**: `database/migrations/2025_11_03_043924_create_helpdesk_tickets_table.php`

| Field | Tipe Data | Keterangan |
|------|----------|-----------|
| id | bigint, PK | ID tiket |
| ticket_number | string(50) | Nombor tiket unik |
| form_reference_code | string(50) | Kod rujukan borang PK.(S).MOTAC.07.(L1) |
| status_token_hash | string(128) nullable | Hash token semakan status (untuk guest) |
| user_id | bigint, FK nullable | FK → `users.id` (NULL jika guest) |
| guest_name/guest_email/guest_phone | string nullable | Maklumat penghantar (untuk guest) |
| guest_grade/guest_division/guest_staff_id | string nullable | Metadata guest (opsyen) |
| division_id | bigint, FK nullable | FK → `divisions.id` (konteks organisasi) |
| assigned_to_division | bigint, FK nullable | FK → `divisions.id` (agihan tiket) |
| category_id | bigint, FK | FK → `ticket_categories.id` |
| priority | enum | low/normal/high/urgent |
| subject/description | string/text | Tajuk & keterangan aduan |
| status | enum | open/assigned/in_progress/pending_user/resolved/closed |
| asset_id | bigint, FK nullable | FK → `assets.id` (jika berkaitan perkakasan) |
| created_at/updated_at/deleted_at | timestamp | Timestamps + soft delete |

**Indeks (ringkasan)**: `(ticket_number)`, `(user_id)`, `(guest_email)`, `(status)`, `(priority)`, `(category_id)`, `(assigned_to_division)`, `(asset_id)`, `(status_token_hash)`

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

> **Source of truth (skema sebenar)**: `database/migrations/2025_11_03_043935_create_loan_applications_table.php`

Ringkasan medan penting (bukan senarai penuh):

| Field | Tipe Data | Keterangan |
|------|----------|-----------|
| id | bigint, PK | ID permohonan |
| application_number | string(20) | Nombor permohonan unik (format: LA\[YYYY\]\[MM\]\[0001-9999\]) |
| form_reference_code | string(50) | Kod rujukan borang PK.(S).MOTAC.07.(L3) |
| user_id | bigint, FK nullable | FK → `users.id` (NULL untuk permohonan guest) |
| applicant_name/email/phone | string | Maklumat pemohon (sentiasa dipopulasi) |
| staff_id | string(20) | ID staf MOTAC |
| applicant_position/applicant_grade | string | Jawatan & gred (teks) |
| grade | string(10) | Gred ringkas (contoh: 41/44/48/52/54) |
| division_id | bigint, FK | FK → `divisions.id` |
| purpose/location/return_location | text/string | Butiran permohonan |
| loan_start_date/loan_end_date | date | Tarikh pinjaman dimohon |
| status | enum | Status proses (contoh: `draft`, `submitted`, `under_review`, `approved`, `issued`, `returned`, `overdue`, dll.) |
| priority | enum | low/normal/high/urgent |
| approval_token_hash/status_token_hash | string(128) nullable | Hash token untuk kelulusan e-mel & semakan status |
| pickup_otp_hash | string nullable | OTP pengambilan aset (hashed) |
| responsible_officer_* | string/boolean/timestamp | Medan Pegawai Bertanggungjawab (jika berkaitan) |
| created_at/updated_at/deleted_at | timestamp | Timestamps + soft delete |

**Indeks (ringkasan)**: `(application_number)`, `(user_id)`, `(applicant_email)`, `(division_id)`, `(status)`, `(status_token_hash)`

> **Nota Hybrid**: `user_id` NULL = permohonan guest; NOT NULL = permohonan staf berdaftar

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

### 5.17. Jadual: faqs (Cloud Hybrid AI v3.6.1)

| Field      | Tipe Data           | Keterangan                                    |
| ---------- | ------------------- | --------------------------------------------- |
| id         | bigint, PK          | ID FAQ                                        |
| user_id    | bigint, FK nullable | FK → users.id (NULL jika system-generated)    |
| question   | text                | Soalan FAQ dalam Bahasa Melayu                |
| answer     | text                | Jawapan FAQ dalam Bahasa Melayu               |
| category   | string(100)         | Kategori (helpdesk, asset_loan, system)       |
| tags       | json nullable       | Tags untuk kategorisasi                       |
| is_active  | boolean             | Status aktif (DEFAULT TRUE)                   |
| priority   | integer             | Keutamaan paparan (DEFAULT 0)                 |
| view_count | integer             | Bilangan paparan (DEFAULT 0)                  |
| created_at | timestamp           | Tarikh cipta                                  |
| updated_at | timestamp           | Tarikh kemaskini                              |

**Indeks:** `(category, is_active)`, `(user_id)`, `(priority DESC, created_at DESC)`

> **Nota D18 v1.0.0:** Jadual FAQ untuk AI Bot dengan True Hybrid Architecture (nullable user_id FK)

### 5.18. Jadual: documents (Cloud Hybrid AI v3.6.1)

| Field           | Tipe Data           | Keterangan                                    |
| --------------- | ------------------- | --------------------------------------------- |
| id              | bigint, PK          | ID dokumen                                    |
| user_id         | bigint, FK nullable | FK → users.id (NULL jika system document)     |
| title           | string(255)         | Tajuk dokumen                                 |
| filename        | string(255)         | Nama fail asal                                |
| path            | string(500)         | Laluan fail dalam storage                     |
| mime_type       | string(100)         | Jenis MIME (pdf, docx, txt)                   |
| size_bytes      | bigint              | Saiz fail dalam bytes                         |
| checksum        | string(64)          | SHA256 checksum                               |
| status          | enum                | Status pemprosesan                            |
| processing_started_at | timestamp nullable | Tarikh mula pemprosesan                    |
| processing_completed_at | timestamp nullable | Tarikh selesai pemprosesan               |
| summary         | text nullable       | Ringkasan dokumen (AI-generated)              |
| key_topics      | json nullable       | Topik utama (AI-extracted)                    |
| metadata        | json nullable       | Metadata tambahan                             |
| created_at      | timestamp           | Tarikh cipta                                  |
| updated_at      | timestamp           | Tarikh kemaskini                              |

**Status enum values:** PENDING, PROCESSING, COMPLETED, FAILED

**Indeks:** `(user_id, status)`, `(status, created_at)`, `(checksum)`

> **Nota D18 v1.0.0:** Dokumen untuk analisis AI dengan True Hybrid Architecture

### 5.19. Jadual: document_chunks (Cloud Hybrid AI v3.6.1)

| Field       | Tipe Data     | Keterangan                                    |
| ----------- | ------------- | --------------------------------------------- |
| id          | bigint, PK    | ID chunk                                      |
| document_id | bigint, FK    | FK → documents.id                             |
| chunk_index | integer       | Indeks chunk dalam dokumen                    |
| content     | text          | Kandungan chunk                               |
| page_number | integer null  | Nombor halaman (jika berkenaan)               |
| start_char  | integer null  | Posisi karakter mula                          |
| end_char    | integer null  | Posisi karakter akhir                         |
| token_count | integer       | Bilangan token dalam chunk                    |
| metadata    | json nullable | Metadata chunk (headers, context)             |
| created_at  | timestamp     | Tarikh cipta                                  |

**Indeks:** `(document_id, chunk_index)`, `(document_id, page_number)`

> **Nota D18 v1.0.0:** Chunks dokumen untuk Retrieval-Augmented Generation (RAG)

### 5.20. Jadual: embeddings (Cloud Hybrid AI v3.6.1)

| Field          | Tipe Data           | Keterangan                                    |
| -------------- | ------------------- | --------------------------------------------- |
| id             | bigint, PK          | ID embedding                                  |
| embeddable_type| string(255)         | Model type (faqs, document_chunks)           |
| embeddable_id  | bigint              | ID model berkaitan                            |
| model_name     | string(100)         | Model embedding (nomic-embed-text)            |
| vector         | json                | Vector embedding (array of floats)            |
| dimensions     | integer             | Dimensi vector (e.g., 768, 1536)             |
| created_at     | timestamp           | Tarikh cipta                                  |

**Indeks:** `(embeddable_type, embeddable_id)`, `(model_name)`

> **Nota D18 v1.0.0:** Vector embeddings untuk semantic search menggunakan MySQL JSON

### 5.21. Jadual: message_logs (Cloud Hybrid AI v3.6.1)

| Field          | Tipe Data           | Keterangan                                    |
| -------------- | ------------------- | --------------------------------------------- |
| id             | bigint, PK          | ID log mesej                                  |
| user_id        | bigint, FK nullable | FK → users.id (NULL jika guest)               |
| conversation_id| string(36) nullable | UUID conversation (jika berkenaan)            |
| message_type   | enum                | Jenis mesej                                   |
| content        | text                | Kandungan mesej                               |
| ai_model       | string(100) null    | Model AI yang digunakan                       |
| response_time  | decimal(8,3) null   | Masa respons dalam saat                       |
| token_usage    | json nullable       | Penggunaan token (input/output)               |
| metadata       | json nullable       | Metadata tambahan                             |
| ip_address     | string(45) nullable | Alamat IP pengguna                            |
| user_agent     | text nullable       | User agent browser                            |
| created_at     | timestamp           | Tarikh cipta                                  |

**Message_type enum values:** USER_QUERY, AI_RESPONSE, SYSTEM_MESSAGE, ERROR

**Indeks:** `(user_id, created_at)`, `(conversation_id)`, `(message_type, created_at)`

> **Nota D18 v1.0.0:** Log interaksi AI dengan Dual Audit System (owen-it + spatie)

### 5.22. Jadual: bedrock_conversations (Cloud Hybrid AI v3.6.1)

| Field          | Tipe Data           | Keterangan                                    |
| -------------- | ------------------- | --------------------------------------------- |
| id             | bigint, PK          | ID conversation                               |
| user_id        | bigint, FK nullable | FK → users.id (NULL jika guest)               |
| conversation_id| string(36)          | UUID conversation                             |
| title          | string(255) null    | Tajuk conversation (AI-generated)             |
| context        | string(100) null    | Konteks (faq, document_analysis, auto_reply)  |
| messages       | json                | Array mesej dalam conversation                |
| model_used     | string(100)         | Model AI yang digunakan                       |
| total_tokens   | integer             | Jumlah token yang digunakan                   |
| last_activity  | timestamp           | Aktiviti terakhir                             |
| expires_at     | timestamp nullable  | Tarikh luput (30 minit untuk guest)          |
| metadata       | json nullable       | Metadata tambahan                             |
| created_at     | timestamp           | Tarikh cipta                                  |
| updated_at     | timestamp           | Tarikh kemaskini                              |

**Indeks:** `(user_id, last_activity)`, `(conversation_id)`, `(expires_at)`

> **Nota D18 v1.0.0:** Enhanced conversation management dengan memori jangka panjang

### 5.23. Jadual: auto_reply_templates (Cloud Hybrid AI v3.6.1)

| Field          | Tipe Data           | Keterangan                                    |
| -------------- | ------------------- | --------------------------------------------- |
| id             | bigint, PK          | ID template                                   |
| user_id        | bigint, FK          | FK → users.id (admin/superuser)               |
| name           | string(255)         | Nama template                                 |
| description    | text nullable       | Penerangan template                           |
| template_type  | enum                | Jenis template                                |
| prompt_template| text                | Template prompt untuk AI                      |
| variables      | json nullable       | Pembolehubah yang tersedia                    |
| is_active      | boolean             | Status aktif (DEFAULT TRUE)                   |
| usage_count    | integer             | Bilangan penggunaan (DEFAULT 0)               |
| created_at     | timestamp           | Tarikh cipta                                  |
| updated_at     | timestamp           | Tarikh kemaskini                              |

**Template_type enum values:** HELPDESK_RESPONSE, LOAN_APPROVAL, LOAN_REJECTION, GENERAL_INQUIRY

**Indeks:** `(template_type, is_active)`, `(user_id)`

> **Nota D18 v1.0.0:** Template untuk auto-reply AI dengan approval workflow

### 5.24. Jadual: auto_reply_drafts (Cloud Hybrid AI v3.6.1)

| Field          | Tipe Data           | Keterangan                                    |
| -------------- | ------------------- | --------------------------------------------- |
| id             | bigint, PK          | ID draf                                       |
| user_id        | bigint, FK          | FK → users.id (admin yang menjana)            |
| replyable_type | string(255)         | Model type (helpdesk_tickets, loan_applications) |
| replyable_id   | bigint              | ID model berkaitan                            |
| template_id    | bigint, FK nullable | FK → auto_reply_templates.id                  |
| generated_content | text             | Kandungan yang dijana AI                      |
| ai_model       | string(100)         | Model AI yang digunakan                       |
| generation_time| decimal(8,3)        | Masa penjanaan dalam saat                     |
| status         | enum                | Status draf                                   |
| approved_by    | bigint, FK nullable | FK → users.id (approver)                      |
| approved_at    | timestamp nullable  | Tarikh kelulusan                              |
| sent_at        | timestamp nullable  | Tarikh hantar kepada pengguna                 |
| metadata       | json nullable       | Metadata penjanaan                            |
| created_at     | timestamp           | Tarikh cipta                                  |
| updated_at     | timestamp           | Tarikh kemaskini                              |

**Status enum values:** DRAFT, PENDING_APPROVAL, APPROVED, REJECTED, SENT

**Indeks:** `(replyable_type, replyable_id)`, `(status, created_at)`, `(user_id)`

> **Nota D18 v1.0.0:** Draf auto-reply dengan approval workflow dan email tokens

---

## 6. Hubungan Antara Jadual (Relationships)

### 6.1. Hubungan Sistem Utama (Core System Relationships)

- `users` → `helpdesk_tickets.assigned_admin_id`, `loan_transactions.performed_by_admin_id`
- `users` → `personal_access_tokens` (polymorphic via tokenable)
- `helpdesk_tickets` ↔ `helpdesk_comments`, `helpdesk_attachments`, `status_tokens`
- `loan_applications` ↔ `loan_items`, `loan_transactions`, `loan_approvals`, `loan_audits`, `status_tokens`
- `loan_transactions` ↔ `loan_transaction_accessories` (v3.5.0)
- `assets` ↔ `loan_items`, `loan_transactions`
- `divisions` → `users.division_id`
- `audits` polymorphic ke semua model dengan `Auditable` trait
- `activity_log` polymorphic ke semua model untuk user activity tracking

### 6.2. Hubungan Cloud Hybrid AI (D18 v1.0.0)

- `users` → `faqs.user_id`, `documents.user_id`, `message_logs.user_id` (nullable FK - True Hybrid)
- `users` → `bedrock_conversations.user_id`, `auto_reply_templates.user_id`, `auto_reply_drafts.user_id`
- `documents` ↔ `document_chunks` (one-to-many)
- `embeddings` polymorphic ke `faqs`, `document_chunks` (via embeddable_type/embeddable_id)
- `auto_reply_templates` → `auto_reply_drafts.template_id`
- `auto_reply_drafts` polymorphic ke `helpdesk_tickets`, `loan_applications` (via replyable_type/replyable_id)
- `bedrock_conversations` → `message_logs.conversation_id` (UUID relationship)

---

## 7. Piawaian Kualiti Data (Data Quality Standards)

### 7.1. Piawaian Sistem Utama (Core System Standards)

- **Unik:** `ticket_number`, `reference`, `approval_token_hash`, `status_token_hash`, `google_id`
- **Validasi:** Format e-mel, telefon, tarikh, enumerasi (kategori, status)
- **Kelengkapan:** Medan wajib mesti diisi (tetamu tidak boleh menyahdaya perakuan)
- **Integriti Rujukan:** FK ke `users`, `assets`, `loan_applications`, `loan_transactions`
- **Audit:** Semua perubahan penting dicatat dalam `audits` dan `activity_log`
- **Privasi:** E-mel, telefon, IP disimpan hashed/encrypted di mana sesuai

### 7.2. Piawaian Cloud Hybrid AI (D18 v1.0.0)

- **Unik AI:** `conversation_id` (UUID), `checksum` (dokumen), `embeddable_type+embeddable_id` (embeddings)
- **Validasi AI:** Vector dimensions, JSON format untuk embeddings, enum values untuk AI status
- **Kelengkapan AI:** FAQ question/answer wajib, document content validation, embedding vector completeness
- **Integriti Rujukan AI:** Polymorphic relationships untuk embeddings dan auto_reply_drafts
- **Audit AI:** Semua interaksi AI dicatat dalam `message_logs` dengan dual audit system
- **Privasi AI:** PII detection dan sanitization sebelum pemprosesan AI (PDPA 2010 compliance)
- **Prestasi AI:** Vector search optimization, conversation expiry (30 minit untuk guest)
- **Data Residency:** Klasifikasi data automatik untuk pemprosesan tempatan vs cloud (Malaysia)

---

## 8. Backup & Pemulihan (Backup & Recovery)

### 8.1. Backup Sistem Utama (Core System Backup)

- Backup MySQL harian (pengepil), retention 30 hari
- Snapshot storan objek (lampiran) mingguan
- Ujian pemulihan dua kali setahun
- Laravel Pulse data retention: 7 hari (auto-pruned)

### 8.2. Backup Cloud Hybrid AI (D18 v1.0.0)

- **FAQ Knowledge Base**: Backup harian dengan retention 90 hari (critical for AI operations)
- **Document Storage**: Backup dokumen dan chunks dengan retention 1 tahun
- **Vector Embeddings**: Backup mingguan (boleh dijana semula tetapi memakan masa)
- **Conversation Logs**: Backup `message_logs` dan `bedrock_conversations` dengan retention 7 tahun (compliance)
- **AI Templates**: Backup `auto_reply_templates` dengan retention 2 tahun
- **Model Configurations**: Backup konfigurasi AI (Ollama models, Bedrock settings) harian
- **Performance Metrics**: AI-specific metrics dalam Laravel Pulse (7 hari retention)

---

## 9. Audit & Logging (Dual System)

### 9.1. Dual Audit Architecture

#### Package 1: owen-it/laravel-auditing v14.x (COMPLIANCE)

- **Table**: `audits`
- **Purpose**: Field-level change tracking untuk PDPA compliance
- **Models**: `Auditable` trait pada `HelpdeskTicket`, `LoanApplication`, `Asset`, `User`, **AI Models (D18 v1.0.0)**
- **AI Models**: `Faq`, `Document`, `MessageLog`, `BedrockConversation`, `AutoReplyTemplate`, `AutoReplyDraft`
- **Events logged**: Created, Updated, Deleted, Status Changed, **AI Interactions, Model Routing**
- **Data stored**: old_values, new_values, user_id, IP address, timestamp, **AI model used, token usage**
- **Retention**: 7 years (compliance requirement)

#### Package 2: spatie/laravel-activitylog v4.x (OPERATIONS)

- **Table**: `activity_log`
- **Purpose**: User activity tracking untuk dashboard dan reports
- **Events logged**: Login, logout, form submissions, status views, approvals, **AI queries, document analysis**
- **AI Events**: FAQ Bot queries, document uploads, auto-reply generation, conversation management
- **Data stored**: description, subject, causer, properties, batch_uuid, **AI response time, model selection**
- **Use cases**: User "Recent Activity", Filament widgets, admin reports, **AI usage analytics**

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

- Menambah medan `guest_*` pada `helpdesk_tickets` untuk menyokong permohonan tetamu (True Hybrid)
- Menghapus kebergantungan `user_id` bagi tetamu
- Menyemak `loan_approvals` supaya menyimpan e-mel pegawai secara eksplisit

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

ERD statik tidak disimpan sebagai fail imej dalam repo v3.6.1. Rujukan struktur jadual adalah melalui migrasi dalam `database/migrations/` dan ringkasan skema dalam dokumen ini.

### B. Definisi Lengkap

Fail CSV RTM untuk pemetaan keperluan ↔ jadual berada di `docs/reference/rtm/`.

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

**Indeks Cloud Hybrid AI (D18 v1.0.0):**

- `faqs_category_active_index` - Optimasi FAQ Bot queries
- `documents_status_created_index` - Tracking document processing
- `document_chunks_document_page_index` - Efficient chunk retrieval
- `embeddings_embeddable_type_id_index` - Vector search optimization
- `message_logs_user_created_index` - User activity tracking
- `bedrock_conversations_user_activity_index` - Conversation management
- `bedrock_conversations_expires_at_index` - Cleanup expired conversations
- `auto_reply_drafts_replyable_type_id_index` - Auto-reply tracking

Analisis prestasi dirujuk dalam `docs/reference/performance-optimization-guide.md`.

---

## 13. Penutup

Dokumentasi pangkalan data ini memastikan struktur ICTServe v3.5.0 konsisten dengan
True Hybrid Architecture: staff boleh self-register atau guna guest forms, kelulusan
dikendalikan melalui token bertanda tangan, dan dual audit menyeluruh mengekalkan
integriti data. Semua perubahan tambahan hendaklah mematuhi proses pengurusan
perubahan D01 §9.3.

**True Hybrid Architecture v3.6.1 Database Features:**

- Google SSO support via `google_id` field
- Responsible Officer tracking untuk loan applications
- Accessory tracking dengan `loan_transaction_accessories` table
- Form reference codes untuk compliance
- Laravel Pulse tables untuk performance monitoring
- Laravel Sanctum `personal_access_tokens` untuk API authentication
- **Cloud Hybrid AI Architecture (D18 v1.0.0):**
  - FAQ Bot dengan RAG menggunakan `faqs`, `embeddings` tables
  - Document Analysis dengan `documents`, `document_chunks` tables
  - Multi-model intelligence dengan `bedrock_conversations` table
  - Auto-reply generation dengan `auto_reply_templates`, `auto_reply_drafts` tables
  - Comprehensive AI logging dengan `message_logs` table
  - Vector search menggunakan MySQL JSON untuk embeddings
  - True Hybrid support dengan nullable `user_id` FK pada semua AI tables

---

**Dokumen ini mematuhi piawaian ISO 8000:2022 (Data Quality), ISO/IEC/IEEE 1016:2009, ISO/IEC 27701:2019, ISO/IEC 38505-1:2017, ISO/IEC 33063:2015 (Process Measurement), ISO/IEC/IEEE 15289:2019 (Documentation Requirements), ISO/IEC TS 24748-6 (Lifecycle Management), IEEE 1016:2009 (Software Design Descriptions), RFC 5322 (Email Format), ISO 8601 (Date/Time Format), TLS 1.3 dan AES-256 (Encryption), OWASP Transport Security, ISO 9241-210 (Human-Centered Design), MyGovEA 18 Prinsip, MDGDM, dan DDSA untuk memastikan pematuhan komprehensif kepada keperluan organisasi kerajaan Malaysia. Sistem ini juga mematuhi D18 AI Chatbot Ollama-Bedrock v1.0.0 untuk Cloud Hybrid AI Architecture dengan nullable user_id FK dan Dual Audit System untuk pematuhan PDPA 2010.**
