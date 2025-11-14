# Dokumentasi Pangkalan Data (Database Documentation)

**Sistem ICTServe**
**Versi:** 3.0.0 (SemVer)
**Tarikh Kemaskini:** 31 Oktober 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman BPM MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1

---

## Maklumat Dokumen (Document Information)

| Atribut            | Nilai                                                                            |
|--------------------|----------------------------------------------------------------------------------|
| **Versi**          | 3.0.0                                                                            |
| **Tarikh Kemaskini** | 31 Oktober 2025                                                                |
| **Status**         | Aktif                                                                            |
| **Klasifikasi**    | Terhad - Dalaman BPM MOTAC                                                       |
| **Pematuhi**       | ISO 8000, ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1                      |
| **Bahasa**         | Bahasa Melayu (utama), English (teknikal)                                        |

> Notis Penggunaan Dalaman: Semua skema dan jadual adalah untuk sistem dalaman MOTAC; tiada data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh          | Perubahan                                                                                                                                     | Penulis                 |
|-------|-----------------|------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------|
| 3.0.1 | 31 Oktober 2025 | Standardisasi pautan: rujukan ke GLOSSARY dipusatkan ke `docs/GLOSSARY.md`; pengemaskinian indeks dokumentasi.                                | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025 | Penyelarasan pangkalan data kepada seni bina dalaman (internal-only): akaun pengguna staf & pentadbir, struktur jadual kelulusan berperingkat, serta pengukuhan audit dan kualiti data. | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                        | Pasukan BPM             |
| 1.0.0 | September 2025  | Versi awal dokumentasi pangkalan data                                                                                                         | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]**
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]**
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]**
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]**
- **[D05_DATA_MIGRATION_PLAN.md]**
- **[D06_DATA_MIGRATION_SPECIFICATION.md]**
- **[D07_SYSTEM_INTEGRATION_PLAN.md]**
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]**
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]**
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**
- **docs/frontend/accessibility-guidelines.md**


---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menjelaskan struktur, definisi medan, piawaian kualiti data, dan hubungan jadual bagi ICTServe sebagai sistem dalaman (internal-only). Akaun pengguna staf dan pentadbir disimpan dalam jadual `users` dengan kawalan peranan; kelulusan dan audit direkod secara menyeluruh.

---

## 2. SKOP (Scope)

- Semua jadual utama yang menyokong borang dalaman Helpdesk & Asset Loan, panel Filament, kelulusan berperingkat, audit, notifikasi, dan pemantauan status.
- Piawaian data, kawalan kualiti, dan persediaan migrasi.
- Tidak meliputi modul RBAC legasi yang tidak lagi digunakan.


---

## 3. REKA BENTUK LOGIKAL (Logical Database Design)

### 3.1. Senarai Jadual Utama (Main Tables)

| Jadual       | Fungsi                                                                 |
|--------------|------------------------------------------------------------------------|
| users        | Akaun pengguna staf & pentadbir (portal & panel Filament)              |
| divisions    | Rujukan bahagian/ unit MOTAC                                          |
| helpdesk_tickets | Rekod tiket helpdesk pengguna dalaman                             |
| helpdesk_comments | Komen pentadbir terhadap tiket                                   |
| helpdesk_attachments | Fail lampiran tiket                                           |
| loan_applications | Permohonan pinjaman aset pengguna dalaman                        |
| loan_items   | Item aset dalam permohonan                                            |
| loan_transactions | Pengeluaran & pemulangan aset                                    |
| loan_approvals | Rekod kelulusan e-mel bertanda tangan                               |
| loan_audits  | Jejak audit khusus modul pinjaman                                     |
| status_tokens | Token semakan status tetamu (opsyen)                                 |
| activity_log | Log aktiviti sistem (Spatie)                                          |
| notifications | Notifikasi Laravel                                                   |

---

## 4. DEFINISI JADUAL & FIELD (Table & Field Definitions)

### 4.1. Jadual: `users`

| Field       | Tipe Data      | Keterangan                               | Kualiti Data                          |
|-------------|----------------|------------------------------------------|---------------------------------------|
| id          | bigint, PK     | ID pengguna                              | Unique, not null                      |
| name        | string(255)    | Nama pegawai                             | Not null                              |
| email       | string(255)    | E-mel kerajaan (unik)                    | Unique, not null, domain whitelisted |
| phone       | string(30)     | Telefon pegawai                          | Not null                              |
| role        | enum(`admin`,`superuser`) | Peranan pentadbiran Filament         | Not null                              |
| password    | string(255)    | Hash kata laluan                         | Not null                              |
| two_factor_secret | text (nullable) | Rahsia TOTP (untuk `superuser`)        | Optional, encrypted                  |
| created_at  | timestamp      | Tarikh cipta                             | Not null                              |
| updated_at  | timestamp      | Tarikh kemaskini                         | Not null                              |

> **Nota:** Jadual ini **hanya** menyimpan akaun pentadbiran. Tiada rekod staf MOTAC atau tetamu.

### 4.2. Jadual: `divisions`

| Field | Tipe Data   | Keterangan           | Kualiti Data |
|-------|-------------|----------------------|--------------|
| id    | bigint, PK  | ID bahagian          | Unique       |
| code  | string(20)  | Kod bahagian (unik)  | Unique       |
| name  | string(255) | Nama bahagian        | Not null     |

### 4.3. Jadual: `helpdesk_tickets`

| Field             | Tipe Data      | Keterangan                                                  | Kualiti Data |
|-------------------|----------------|-------------------------------------------------------------|--------------|
| id                | bigint, PK     | ID tiket                                                    | Unique       |
| ticket_number     | string(20)     | Nombor tiket unik (format `HD-YYYYMM-XXXX`)                 | Unique       |
| submitter_name    | string(255)    | Nama tetamu                                                 | Not null     |
| submitter_email   | string(255)    | E-mel tetamu                                                | Not null     |
| submitter_phone   | string(50)     | Telefon tetamu                                              | Not null     |
| submitter_division_code | string(20) | Kod bahagian tetamu                                       | Valid code   |
| submitter_grade   | string(50)     | Gred tetamu (optional)                                      | Optional     |
| category          | string(100)    | Kategori kerosakan                                          | Not null     |
| priority          | enum(`LOW`,`MEDIUM`,`HIGH`,`CRITICAL`) | Keutamaan SLA                | Not null     |
| description       | text           | Maklumat kerosakan                                          | Not null     |
| asset_tag         | string(100)    | Tag aset (optional)                                         | Optional     |
| declaration       | boolean        | Perakuan PDPA (mesti TRUE)                                  | Not null     |
| status            | enum(`OPEN`,`IN_PROGRESS`,`AWAITING_INFO`,`RESOLVED`,`CLOSED`) | Status | Not null |
| assigned_admin_id | bigint, FK nullable | FK → `users.id` (`admin` pemilik tiket)               | Optional     |
| sla_due_at        | timestamp      | Tarikh sasaran SLA                                          | Not null     |
| closed_at         | timestamp      | Tarikh tiket ditutup                                        | Optional     |
| created_at        | timestamp      | Tarikh cipta                                                | Not null     |
| updated_at        | timestamp      | Tarikh kemaskini                                            | Not null     |

### 4.4. Jadual: `helpdesk_comments`

| Field        | Tipe Data  | Keterangan                                 |
|--------------|------------|--------------------------------------------|
| id           | bigint, PK | ID komen                                   |
| ticket_id    | bigint, FK | FK → `helpdesk_tickets.id`                 |
| admin_id     | bigint, FK | FK → `users.id`                            |
| body         | text       | Komen/ kemas kini                          |
| visibility   | enum(`INTERNAL`,`PUBLIC`) | Tahap paparan                |
| created_at   | timestamp  | Tarikh cipta                               |

### 4.5. Jadual: `helpdesk_attachments`

| Field        | Tipe Data  | Keterangan                                   |
|--------------|------------|----------------------------------------------|
| id           | bigint, PK | ID lampiran                                  |
| ticket_id    | bigint, FK | FK → `helpdesk_tickets.id`                   |
| path         | string(255)| Laluan fail (S3)                             |
| original_name| string(255)| Nama fail asal                               |
| mime_type    | string(100)| Jenis MIME                                   |
| size_bytes   | bigint     | Saiz                                         |
| checksum     | string(64) | SHA256 checksum                              |
| created_at   | timestamp  | Tarikh muat naik                             |

### 4.6. Jadual: `loan_applications`

| Field                  | Tipe Data      | Keterangan                                                          |
|------------------------|----------------|---------------------------------------------------------------------|
| id                     | bigint, PK     | ID permohonan                                                       |
| reference              | string(20)     | Kod rujukan (`LA-YYYYMM-XXXX`)                                      |
| applicant_name         | string(255)    | Nama tetamu                                                         |
| applicant_email        | string(255)    | E-mel tetamu                                                        |
| applicant_phone        | string(50)     | Telefon tetamu                                                      |
| applicant_division_code| string(20)     | Kod bahagian                                                        |
| applicant_grade        | string(50)     | Gred pemohon                                                        |
| purpose                | text           | Tujuan pinjaman                                                     |
| location               | string(255)    | Lokasi penggunaan                                                   |
| loan_start_date        | date           | Tarikh mula                                                         |
| loan_end_date          | date           | Tarikh akhir                                                        |
| acknowledgement        | boolean        | Perakuan PDPA                                                       |
| status                 | enum(`PENDING_SUPERVISOR_APPROVAL`,`APPROVED`,`REJECTED`,`AWAITING_COLLECTION`,`ON_LOAN`,`RETURNED`,`DAMAGED`) | Status |
| approval_token_hash    | string(128)    | Hash token kelulusan (SHA512)                                       |
| approval_token_expires_at | timestamp   | Tarikh luput token                                                  |
| status_token_hash      | string(128)    | Hash token semakan status tetamu                                    |
| created_at             | timestamp      | Tarikh cipta                                                        |
| updated_at             | timestamp      | Tarikh kemaskini                                                    |

### 4.7. Jadual: `loan_items`

| Field         | Tipe Data  | Keterangan                                 |
|---------------|------------|--------------------------------------------|
| id            | bigint, PK | ID item                                    |
| loan_application_id | bigint, FK | FK → `loan_applications.id`          |
| asset_id      | bigint, FK | FK → `assets.id`                           |
| quantity      | integer    | Kuantiti                                   |
| notes         | text       | Catatan (optional)                         |

### 4.8. Jadual: `loan_transactions`

| Field            | Tipe Data      | Keterangan                                                  |
|------------------|----------------|-------------------------------------------------------------|
| id               | bigint, PK     | ID transaksi                                                |
| loan_application_id | bigint, FK  | FK → `loan_applications.id`                                 |
| type             | enum(`CHECK_OUT`,`CHECK_IN`) | Jenis transaksi                             |
| performed_by_admin_id | bigint, FK | FK → `users.id` (`admin` yang menjalankan tindakan)        |
| performed_at     | timestamp      | Tarikh tindakan                                             |
| condition_notes  | text           | Catatan keadaan aset                                        |
| attachments_json | json (optional)| Bukti foto/document                                        |

### 4.9. Jadual: `loan_approvals`

| Field         | Tipe Data      | Keterangan                                         |
|---------------|----------------|----------------------------------------------------|
| id            | bigint, PK     | ID kelulusan                                      |
| loan_application_id | bigint, FK | FK → `loan_applications.id`                    |
| approver_email| string(255)    | E-mel pegawai kelulusan                           |
| approver_grade| string(50)     | Gred pegawai                                      |
| decision      | enum(`APPROVED`,`REJECTED`) | Keputusan                               |
| remarks       | text (optional)| Catatan tambahan                                  |
| decision_at   | timestamp      | Tarikh keputusan                                  |
| decision_ip_hash | string(128) | Hash alamat IP                                    |
| token_hash    | string(128)    | Hash token yang digunakan                         |
| metadata      | json           | Metadata tambahan (contoh: user-agent)            |

### 4.10. Jadual: `loan_audits`

Menyimpan jejak audit khusus modul pinjaman (rujuk D09 §8).

### 4.11. Jadual: `status_tokens`

| Field        | Tipe Data  | Keterangan                                      |
|--------------|------------|-------------------------------------------------|
| id           | bigint, PK | ID token status                                 |
| token_hash   | string(128)| Hash token                                      |
| reference_type | string(50) | Model berkaitan (`helpdesk_tickets`,`loan_applications`) |
| reference_id | bigint     | ID model berkaitan                              |
| expires_at   | timestamp  | Tarikh luput                                   |
| created_at   | timestamp  | Tarikh cipta                                   |

---

## 5. HUBUNGAN ANTARA JADUAL (Relationships)

- `users` → `helpdesk_tickets.assigned_admin_id`, `loan_transactions.performed_by_admin_id`.
- `helpdesk_tickets` ↔ `helpdesk_comments`, `helpdesk_attachments`, `status_tokens`.
- `loan_applications` ↔ `loan_items`, `loan_transactions`, `loan_approvals`, `loan_audits`, `status_tokens`.
- `assets` ↔ `loan_items`, `loan_transactions`.
- `divisions` digunakan untuk memvalidasi `submitter_division_code` & `applicant_division_code` (melalui kamus).


---

## 6. PIAWAIAN KUALITI DATA (Data Quality Standards)

- **Unik:** `ticket_number`, `reference`, `approval_token_hash`, `status_token_hash`.
- **Validasi:** Format e-mel, telefon, tarikh, enumerasi (kategori, status).
- **Kelengkapan:** Medan wajib mesti diisi (tetamu tidak boleh menyahdaya perakuan).
- **Integriti Rujukan:** FK ke `users`, `assets`, `loan_applications`.
- **Audit:** Semua perubahan penting dicatat dalam `activity_log` dan `loan_audits`.
- **Privasi:** E-mel, telefon, IP disimpan hashed/ encrypted di mana sesuai.


---

## 7. BACKUP & PEMULIHAN (Backup & Recovery)

- Backup MySQL harian (pengepil), retention 30 hari.
- Snapshot storan objek (lampiran) mingguan.
- Ujian pemulihan dua kali setahun.


---

## 8. AUDIT & LOGGING

- `activity_log` (Spatie) merekod tindakan `admin`/`superuser`.
- `loan_audits` menyimpan rekod granular (permohonan, kelulusan, pengembalian).
- `audit_exports` (opsyen) menyimpan eksport yang dihantar ke SIEM.
- Log kelulusan menyimpan `token_hash`, `decision_at`, `decision_ip_hash` bagi pengesahan.


---

## 9. PENGURUSAN MIGRASI (Migration Notes)

- Migrasi ke v3.0.0 melibatkan:
  - Menambah medan `submitter_*` pada `helpdesk_tickets`.
  - Menghapus kebergantungan `user_id` bagi tetamu.
  - Menyemak `loan_approvals` supaya menyimpan e-mel pegawai secara eksplisit.
  - Menyahaktifkan/ memadam data peranan lama (`staff`, `technician`, `approver`).
- Skrip migrasi diselaras melalui `database/migrations` (rujuk D05 & D06).
- Seeder `RolePermissionSeeder` digantikan dengan `AdminUserSeeder` (mewujudkan `admin` dan `superuser` sahaja).


---

## 10. GLOSARI & RUJUKAN (Glossary & References)

- **Tetamu:** Pengguna tanpa akaun yang mengisi borang.
- **Signed Approval Link:** Pautan ber-token untuk kelulusan e-mel.
- **Status Token:** Token unik membolehkan tetamu menyemak status permohonan/tiket.
- Rujuk `GLOSSARY.md` (dikemas kini) untuk istilah tambahan.


---

## 11. LAMPIRAN (Appendices)

### A. ERD

ERD dikemas kini boleh didapati dalam repositori `/design/erd/ictserve_guest-first.png`.

### B. Definisi Lengkap

Fail CSV terdapat di `/docs/rtm/*` untuk pemetaan keperluan ↔ jadual.

### C. Piawaian Penamaan

- Jadual & medan menggunakan huruf kecil + `_`.
- Enum menggunakan huruf besar (snake case).


### D. Daftar Indeks & Prestasi

- Indeks utama: `helpdesk_tickets_ticket_number_unique`, `loan_applications_reference_unique`, `loan_approvals_token_hash_index`.
- Analisis prestasi disimpan dalam `performance-optimization-report.md`.


---

## 12. PENUTUP

Dokumentasi pangkalan data ini memastikan struktur ICTServe konsisten dengan realiti guest-first: tetamu tidak mewujudkan rekod pengguna, kelulusan dikendalikan melalui token bertanda tangan, dan audit menyeluruh mengekalkan integriti data. Semua perubahan tambahan hendaklah mematuhi proses pengurusan perubahan D01 §9.3.
