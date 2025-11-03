# Spesifikasi Migrasi Data (Data Migration Specification - DMS)

**Sistem ICTServe**  
**Versi:** 2.0.0 (SemVer)  
**Tarikh Kemaskini:** 17 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 38505-1 (Governance of Data)

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO 8000, ISO/IEC 38505-1                |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Spesifikasi ini adalah untuk migrasi data dalaman MOTAC; pastikan pematuhan PDPA.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal spesifikasi migrasi data            | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D05_DATA_MIGRATION_PLAN.md]** - Pelan Migrasi Data (strategy & timeline)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menggariskan spesifikasi teknikal dan piawaian yang perlu dipatuhi bagi proses migrasi data ke sistem **Helpdesk & ICT Asset Loan** berasaskan Laravel 12 untuk Bahagian Pengurusan Maklumat (BPM), MOTAC. Ia mematuhi **ISO 8000** untuk kualiti data (data quality) dan **ISO/IEC 38505-1** untuk tadbir urus data (data governance).

---

## 2. SKOP SPESIFIKASI (Scope)

- Migrasi data merangkumi:
  - **Tiket Aduan Kerosakan ICT** (Complaint Tickets)
  - **Data Pinjaman Peralatan ICT** (Equipment Loan Records)
  - **Inventori Peralatan ICT** (Asset Inventory)
  - **Profil Pengguna** (User Profiles)
- Semua metadata berkaitan (timestamp, status, logs, audit trail) juga mesti dimigrasikan.

---

## 3. PIAWAIAN DATA (Data Standards)

### 3.1. Kualiti Data (ISO 8000 Data Quality)

- **Ketepatan (Accuracy)**: Data mesti tepat, tanpa kesilapan ejaan atau digit.
- **Kelengkapan (Completeness)**: Semua field wajib dan penting mesti diisi (rujuk breakdown borang rasmi).
- **Konsistensi (Consistency)**: Format data seperti tarikh, nombor telefon, kod aset mesti seragam.
- **Keunikan (Uniqueness)**: Tiada rekod duplikat (duplicate ticket, asset, atau user).
- **Kebolehan Jejak (Traceability)**: Setiap perubahan atau migrasi mesti direkod untuk audit.

### 3.2. Tadbir Urus Data (ISO/IEC 38505-1)

- **Accountability**: Tanggungjawab setiap proses migrasi jelas (team, person in charge).
- **Transparency**: Semua proses migrasi didokumen dan boleh diaudit.
- **Security & Privacy**: Data peribadi dilindungi sepanjang proses migrasi.
- **Compliance**: Pematuhan kepada polisi dalaman BPM & undang-undang berkaitan (contoh: PDPA).

---

## 4. STRUKTUR DATA SASARAN (Target Data Structure)

### 4.1. Tiket Aduan Kerosakan ICT

| Field                  | Jenis Data    | Mandatori | Keterangan                                   |
|------------------------|-------------|-----------|----------------------------------------------|
| id                     | bigint      | Ya        | Auto increment (primary key)                 |
| full_name              | string(255) | Ya        | Nama pemohon                                 |
| division_id            | int         | Ya        | Foreign key ke jadual division               |
| grade                  | string(50)  | Tidak     | Gred jawatan                                 |
| email                  | string(255) | Ya        | E-mel pemohon                                |
| phone                  | string(50)  | Ya        | Nombor telefon                               |
| damage_type            | string(100) | Ya        | Kategori kerosakan (dropdown)                |
| damage_info            | text        | Ya        | Keterangan masalah                           |
| asset_no               | string(100) | Conditional | Diisi jika aduan berkaitan perkakasan       |
| declaration            | boolean     | Ya        | Perakuan/disclaimer                          |
| status                 | string(50)  | Ya        | Status tiket                                 |
| created_at             | timestamp   | Ya        | Tarikh aduan dibuat                          |
| updated_at             | timestamp   | Ya        | Tarikh kemaskini terakhir                    |

### 4.2. Data Pinjaman Peralatan ICT

| Field                  | Jenis Data    | Mandatori | Keterangan                                   |
|------------------------|-------------|-----------|----------------------------------------------|
| id                     | bigint      | Ya        | Primary key                                  |
| applicant_id           | bigint      | Ya        | FK ke jadual users                           |
| responsible_officer_id | bigint      | Conditional| FK jika pegawai bertanggungjawab berbeza     |
| position_grade         | string(50)  | Ya        | Jawatan & gred                               |
| division_id            | int         | Ya        | FK ke division                               |
| purpose                | string(255) | Ya        | Tujuan pinjaman                              |
| phone                  | string(50)  | Ya        | Telefon pemohon                              |
| location               | string(255) | Ya        | Lokasi penggunaan                            |
| loan_start_date        | date        | Ya        | Tarikh mula pinjam                           |
| loan_end_date          | date        | Ya        | Tarikh dijangka pulang                       |
| equipment_list         | json/text   | Ya        | Senarai peralatan, kuantiti, catatan         |
| declaration            | boolean     | Ya        | Perakuan/disclaimer                          |
| endorsed_by            | bigint      | Ya        | FK ke users (pegawai pengesah)               |
| endorsement_status     | string(20)  | Ya        | DISOKONG / TIDAK DISOKONG                    |
| endorsement_date       | date        | Ya        | Tarikh disahkan oleh ketua                   |
| bpm_issued_by          | bigint      | Ya        | FK ke users (pegawai BPM pengeluar)          |
| bpm_received_by        | bigint      | Ya        | FK ke users (pegawai BPM terima pulangan)    |
| return_notes           | text        | Tidak     | Catatan semasa pulang                        |
| created_at             | timestamp   | Ya        | Tarikh permohonan                            |
| updated_at             | timestamp   | Ya        | Tarikh kemaskini terakhir                    |

### 4.3. Inventori Peralatan ICT (Asset Inventory)

| Field                  | Jenis Data    | Mandatori | Keterangan                                   |
|------------------------|-------------|-----------|----------------------------------------------|
| id                     | bigint      | Ya        | Primary Key                                  |
| asset_type             | string(100) | Ya        | Jenis peralatan                              |
| brand                  | string(100) | Ya        | Jenama                                       |
| model                  | string(100) | Ya        | Model                                        |
| serial_no              | string(100) | Ya        | No Siri / Tag ID                             |
| accessories            | json/text   | Tidak     | Senarai aksesori                             |
| status                 | string(50)  | Ya        | Status (Available, Loaned, Returned, etc.)   |
| created_at             | timestamp   | Ya        | Tarikh daftar                                |
| updated_at             | timestamp   | Ya        | Tarikh terakhir kemaskini                    |

### 4.4. Profil Pengguna (User Profiles)

| Field                  | Jenis Data    | Mandatori | Keterangan                                   |
|------------------------|-------------|-----------|----------------------------------------------|
| id                     | bigint      | Ya        | Primary Key                                  |
| name                   | string(255) | Ya        | Nama pengguna                                |
| email                  | string(255) | Ya        | E-mel                                        |
| phone                  | string(50)  | Tidak     | Telefon                                      |
| position_grade         | string(50)  | Tidak     | Jawatan & Gred                               |
| division_id            | int         | Tidak     | FK ke division                               |
| role                   | string(30)  | Ya        | staff / admin / technician / approver        |
| created_at             | timestamp   | Ya        | Tarikh daftar pengguna                       |
| updated_at             | timestamp   | Ya        | Tarikh kemaskini terakhir                    |

---

## 5. KESELAMATAN & PRIVASI (Security & Privacy)

- **Data encryption** semasa pemindahan (at-rest & in-transit).
- **Access control**: Hanya team migrasi diberi akses data mentah.
- **Logging**: Semua aktiviti migrasi direkod dalam audit trail.
- **Anonimisasi/Pseudonimisasi** untuk data sensitif jika perlu.

---

## 6. PROSES VALIDASI & UJIAN MIGRASI (Validation & Testing)

- **Dry run migration** pada database staging; bandingkan jumlah dan sampel data.
- **Validation rules**: Semua field wajib mesti diisi, foreign key sah, tiada duplikasi.
- **Post-migration audit**: Laporan error, rekod gagal, perbandingan data asal vs data baru.
- **User Acceptance Test (UAT)**: BPM semak data selepas migrasi.

---

## 7. KAWALAN TADBIR URUS DATA (Data Governance Controls)

- **Dokumentasi penuh** setiap langkah migrasi.
- **Approval & sign-off** oleh BPM sebelum dan selepas migrasi.
- **Backup & rollback**: Backup sebelum migrasi; pelan rollback sekiranya gagal.
- **Compliance review**: Semakan pematuhan ISO 8000 & ISO/IEC 38505-1 selepas migrasi.

---

## 8. PENUTUP (Conclusion)

Spesifikasi ini memastikan semua migrasi data ke sistem Helpdesk & ICT Asset Loan MOTAC BPM adalah berkualiti, selamat, boleh jejak, dan patuh kepada piawaian antarabangsa dan polisi BPM. Semua pihak terlibat wajib mematuhi dokumen ini sepanjang proses migrasi.

---
