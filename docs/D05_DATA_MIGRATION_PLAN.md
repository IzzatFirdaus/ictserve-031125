# Pelan Migrasi Data (Data Migration Plan - DMP)

**Sistem ICTServe**  
**Versi:** 3.1.0 (SemVer)  
**Tarikh Kemaskini:** 29 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 27701 (Privacy Information Management)

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.1.0                                     |
| **Tarikh Kemaskini** | 29 November 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO 8000, ISO/IEC 27701                   |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

> Notis Penggunaan Dalaman: Migrasi data ini melibatkan data dalaman MOTAC dan tidak berkaitan data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                    | Penulis     |
| ----- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.1.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, PHP 8.2.12). Penyelarasan dengan D00-D04.  | Pasukan BPM |
| 3.0.0 | 22 Januari 2025  | Kemaskini kepada seni bina guest-first: tiada migrasi akaun pengguna tetamu, fokus kepada data pentadbiran dan rekod sejarah | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini rujukan teknologi: Laravel 12.40.1, PHP 8.2.12                                                                     | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                       | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal pelan migrasi data                                                                                                | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D06_DATA_MIGRATION_SPECIFICATION.md]** - Spesifikasi Migrasi Data (detail teknikal)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan perancangan menyeluruh bagi migrasi data ke sistem **Helpdesk & ICT Asset Loan** yang berasaskan Laravel 12.40.1 untuk Bahagian Pengurusan Maklumat (BPM), MOTAC. Pelan ini mematuhi piawaian **ISO 8000** untuk kualiti data (data quality) dan **ISO/IEC 27701** untuk pengurusan privasi maklumat (privacy information management).

**Nota Penting**: Sistem baharu menggunakan seni bina guest-first di mana tetamu tidak mewujudkan akaun pengguna. Oleh itu, migrasi data fokus kepada:

- Rekod sejarah tiket helpdesk (tanpa akaun pengguna tetamu)
- Rekod sejarah permohonan pinjaman aset (tanpa akaun pengguna pemohon)
- Data inventori aset ICT
- Akaun pentadbir sistem (admin & superuser sahaja)
- Metadata dan audit trail

---

## 2. SKOP MIGRASI (Scope)

- Migrasi data berkaitan aduan ICT, inventori aset, dan sejarah pinjaman dari sistem lama (manual, Excel, Access, atau sistem digital terdahulu) ke sistem baru Laravel 12.40.1.
- Data yang terlibat:
  - **Tiket Helpdesk**: Rekod sejarah tiket dengan maklumat pengadu (nama, e-mel, telefon) tetapi tanpa akaun pengguna
  - **Permohonan Pinjaman Aset**: Rekod sejarah permohonan dengan maklumat pemohon tetapi tanpa akaun pengguna
  - **Inventori Aset ICT**: Data lengkap aset termasuk kategori, status, dan sejarah penggunaan
  - **Akaun Pentadbir**: Akaun admin dan superuser sahaja (tiada akaun staf biasa)
  - **Bahagian/Unit**: Rujukan bahagian MOTAC untuk validasi
- Termasuk metadata (timestamp, status, logs) & audit trail.
- **Tidak termasuk**: Akaun pengguna tetamu atau staf biasa (sistem guest-first tidak memerlukan akaun untuk pengguna akhir)

---

## 3. SUMBER DATA (Data Sources)

- **Manual Records**: Borang kertas, fail PDF, dokumen cetak (tiket helpdesk dan permohonan pinjaman lama)
- **Digital Files**: Microsoft Excel, CSV, Access DB, sistem aduan lama
- **Sistem Sedia Ada**: Database legacy, API, atau sistem pengurusan aset terdahulu
- **Direktori LDAP/Active Directory**: Untuk validasi bahagian dan gred pegawai (rujukan sahaja, bukan migrasi akaun)

---

## 4. PRINSIP MIGRASI (Migration Principles)

- **Integrity**: Data dipindahkan tanpa kehilangan, perubahan, atau kerosakan.
- **Quality**: Data dibersihkan, distandardkan, dan valid mengikut ISO 8000 (Data Quality).
- **Privacy & Security**: Pemindahan dan penyimpanan data patuh ISO/IEC 27701; data peribadi dilindungi, hanya access role tertentu dibenarkan.
- **Traceability**: Setiap rekod migrasi boleh dijejak (audit trail).
- **Rollback Capability**: Pelan pemulihan sekiranya migrasi gagal.

---

## 5. LANGKAH-LANGKAH MIGRASI (Migration Steps)

### 5.1. Data Assessment & Mapping

- **Inventori Data**: Kenalpasti semua sumber data, struktur, dan owner
- **Data Mapping**: Padankan field sumber ke field dalam sistem Laravel:
  - Tiket helpdesk: `ticket_no` → `helpdesk_tickets.ticket_number`, `submitter_name` → `helpdesk_tickets.submitter_name`
  - Pinjaman: `loan_ref` → `loan_applications.reference`, `applicant_name` → `loan_applications.applicant_name`
  - Aset: `asset_id_legacy` → `assets.tag_id`, `asset_name` → `assets.name`
  - Pentadbir: `admin_email` → `users.email` (hanya admin & superuser)
- **Data Dictionary**: Sediakan kamus data untuk semua field
- **Nota Penting**: Sistem baharu tidak menyimpan akaun pengguna untuk tetamu. Data pengadu/pemohon disimpan sebagai field teks dalam jadual tiket/permohonan

### 5.2. Data Cleansing & Standardization

- **Deduplication**: Buang rekod berganda berdasarkan nombor rujukan unik
- **Validation**: Pastikan format, completeness, dan konsistensi:
  - Tarikh dalam format `YYYY-MM-DD`
  - E-mel dalam format valid (RFC 5322)
  - Telefon dalam format standard Malaysia
  - Enum values (status, priority, kategori) mematuhi definisi sistem baharu
- **Standardization**: Tukar kod/kategori lama ke kod baru sistem Laravel:
  - Status tiket: `OPEN`, `IN_PROGRESS`, `AWAITING_INFO`, `RESOLVED`, `CLOSED`
  - Status pinjaman: `PENDING_SUPERVISOR_APPROVAL`, `APPROVED`, `REJECTED`, `AWAITING_COLLECTION`, `ON_LOAN`, `RETURNED`, `DAMAGED`
  - Priority: `LOW`, `MEDIUM`, `HIGH`, `CRITICAL`
- **Anonymization**: Pastikan data peribadi sensitif dihashed/encrypted mengikut PDPA

### 5.3. Data Migration Tools & Scripts

- **Laravel Migrations**: Gunakan `php artisan migrate` untuk struktur database
- **Laravel Seeders**: Gunakan `php artisan db:seed` untuk data rujukan (bahagian, kategori)
- **Custom Import Scripts**: Skrip PHP untuk import data sejarah:
  - `ImportHelpdeskTicketsCommand` - Import tiket helpdesk lama
  - `ImportLoanApplicationsCommand` - Import permohonan pinjaman lama
  - `ImportAssetsCommand` - Import inventori aset
  - `ImportAdminUsersCommand` - Import akaun pentadbir sahaja
- **Laravel Excel Package**: Untuk import CSV/Excel dengan validasi
- **Batch Processing**: Gunakan Laravel Queue untuk import besar (>1000 rekod)
- **Logging**: Setiap proses import dilog dalam `storage/logs/migration.log` untuk audit dan troubleshooting
- **Progress Tracking**: Gunakan progress bar dan notification untuk pemantauan real-time

### 5.4. Data Migration Execution

- **Dry Run**: Ujian migrasi di staging/dev environment:
  - Jalankan skrip import dengan flag `--dry-run`
  - Semak hasil dalam database staging
  - Validasi integriti data dan foreign key constraints
- **Validation**: Cross-check jumlah rekod, field penting, dan random sampling:
  - Bandingkan jumlah rekod sumber vs destinasi
  - Semak field kritikal (ticket_number, reference, email, status)
  - Random sampling 5% rekod untuk validasi manual
- **Go-Live Migration**: Laksanakan migrasi pada waktu off-peak (hujung minggu/cuti umum):
  - Pastikan full backup database tersedia
  - Aktifkan maintenance mode (`php artisan down`)
  - Jalankan skrip migrasi dengan monitoring
  - Verify data integrity selepas migrasi
  - Nyahaktif maintenance mode (`php artisan up`)
- **Post-Migration Review**: Audit data dalam sistem baru:
  - Semak error log (`storage/logs/migration.log`)
  - Verify foreign key relationships
  - Test functionality utama (create ticket, create loan application)
  - Generate migration report untuk dokumentasi

### 5.5. Data Protection & Privacy

- **Encryption**:
  - Data at-rest: MySQL encryption untuk field sensitif
  - Data in-transit: HTTPS/TLS untuk semua komunikasi
  - Token hashing: SHA-512 untuk approval tokens dan status tokens
- **Access Control**:
  - Data migrasi hanya boleh diakses oleh admin & superuser
  - Gunakan Laravel Policies untuk authorization
  - Audit trail untuk semua akses data migrasi
- **Data Retention**:
  - Hapus data peribadi dari sistem lama mengikut polisi retention MOTAC (7 tahun untuk rekod audit)
  - Archive data lama ke cold storage selepas migrasi berjaya
  - Secure deletion menggunakan `shred` atau equivalent untuk fail sensitif
- **PDPA Compliance**:
  - Pastikan consent declaration untuk semua rekod lama
  - Anonymize data jika consent tidak tersedia
  - Document data processing activities (DPA)

---

## 6. JADUAL MIGRASI (Migration Schedule)

| Fasa                | Tempoh   | Aktiviti                                        | Output                                 |
| ------------------- | -------- | ----------------------------------------------- | -------------------------------------- |
| Penilaian & Mapping | 1 minggu | Data inventory, mapping, dictionary             | Data mapping document, data dictionary |
| Cleansing/Standard  | 1 minggu | Deduplication, validation, standardization      | Cleaned data files, validation report  |
| Skrip & Ujian       | 2 minggu | Scripting, dry run, validation, testing         | Migration scripts, test report         |
| Migrasi Sebenar     | 1-2 hari | Go-live migration, backup, verification         | Migrated database, backup files        |
| Audit & Review      | 3 hari   | Post-migration review, reporting, documentation | Migration report, audit log            |

**Nota**: Jadual ini adalah anggaran dan boleh diselaraskan berdasarkan saiz data dan kompleksiti migrasi.

---

## 7. RISIKO & MITIGASI (Risks & Mitigation)

| Risiko                         | Kesan     | Kebarangkalian | Langkah Mitigasi                                                           |
| ------------------------------ | --------- | -------------- | -------------------------------------------------------------------------- |
| Data rosak/kehilangan          | Tinggi    | Rendah         | Full backup sebelum migrasi, dry run di staging, rollback script tersedia  |
| Data duplikasi/tidak konsisten | Sederhana | Sederhana      | Cleansing menyeluruh, validation rules ketat, mapping yang teliti          |
| Kebocoran data peribadi        | Tinggi    | Rendah         | Encryption at-rest & in-transit, access control ketat, audit trail lengkap |
| Fail integrasi legacy          | Sederhana | Sederhana      | Early testing dengan sample data, manual import sebagai fallback           |
| Foreign key constraint errors  | Sederhana | Sederhana      | Validate relationships sebelum import, import dalam urutan yang betul      |
| Performance degradation        | Rendah    | Sederhana      | Batch processing, queue jobs, optimize database indexes                    |
| Downtime melebihi window       | Sederhana | Rendah         | Rehearsal migration, optimize scripts, parallel processing jika sesuai     |

---

## 8. KAWALAN KUALITI & AUDIT (Quality & Audit Controls)

- **Verification**:
  - Setiap batch migrasi diverifikasi dengan random sampling (5% rekod)
  - Cross-check jumlah rekod sumber vs destinasi
  - Validate foreign key relationships
  - Test functionality dengan data yang dimigrasi
- **Audit Trail**:
  - Skrip log semua aktiviti migrasi dalam `storage/logs/migration.log`
  - Laravel Auditing package merekod semua perubahan data
  - Timestamp dan user ID untuk setiap operasi migrasi
- **Reporting**:
  - Laporan status migrasi real-time kepada BPM
  - Error report dengan details dan recommended actions
  - Data quality report dengan metrics (completeness, accuracy, consistency)
  - Final migration report dengan summary dan lessons learned
- **Quality Metrics**:
  - Data completeness: >95% field wajib diisi
  - Data accuracy: >98% validation pass rate
  - Data consistency: 100% foreign key integrity
  - Migration success rate: >99% rekod berjaya dimigrasi

---

## 9. PELAN PEMULIHAN BENCANA (Disaster Recovery Plan)

**Pematuhan Standard**: ISO/IEC/IEEE 12207:2017 (Software Lifecycle) §7.5 (Maintenance & Support)

Sistem **Helpdesk & ICT Asset Loan MOTAC BPM** mesti memiliki pelan pemulihan bencana (Disaster Recovery Plan) yang komprehensif untuk memastikan kontinuitas bisnis dan perlindungan data dalam situasi darurat.

### 9.1. Tujuan & Scope

- **Tujuan**: Memastikan sistem dapat dipulihkan dengan cepat & data dapat di-restore dengan aman dalam event bencana
- **Scope**: Seluruh sistem termasuk aplikasi, database, backup storage, dan infrastructure

### 9.2. Sasaran Pemulihan (Recovery Targets)

| Target                               | Nilai                | Justifikasi                                                      |
| ------------------------------------ | -------------------- | ---------------------------------------------------------------- |
| **RTO (Recovery Time Objective)**    | 4 jam                | Sistem mesti online dalam 4 jam selepas bencana terdeteksi       |
| **RPO (Recovery Point Objective)**   | 1 jam                | Data loss tidak boleh lebih dari 1 jam (automated hourly backup) |
| **MTBF (Mean Time Between Failure)** | >8000 jam (11 bulan) | Target uptime 99.5% → ~3.5 hours/month allowable downtime        |
| **MTTR (Mean Time To Recover)**      | 2 jam                | Average recovery time target                                     |

### 9.3. Skenario Bencana & Tindakan (Disaster Scenarios & Response)

| Skenario                                 | Jenis          | Tindakan Respons                                                    | Waktu Estimasi |
| ---------------------------------------- | -------------- | ------------------------------------------------------------------- | -------------- |
| **Database Corruption**                  | Data           | Run DB integrity check; restore from last clean backup              | 30-60 min      |
| **Server Disk Full**                     | Infrastructure | Extend disk space; purge old logs                                   | 15-30 min      |
| **Network Outage**                       | Infrastructure | Reroute via backup network; alert admin                             | 10-20 min      |
| **Cybersecurity Incident (Data Breach)** | Security       | Isolate system; forensics; patch vulnerability; restore from backup | 2-4 hours      |
| **Complete Data Center Failure**         | Critical       | Activate DR site; restore from encrypted backup (cold storage)      | 4 hours        |
| **Ransomware Attack**                    | Security       | Immediate isolation; restore from immutable backup                  | 2-4 hours      |

### 9.4. Backup Strategy

- **Type**: Incremental daily + full weekly backups
- **Location**: Local (NAS) + remote (cloud/encrypted storage off-site)
- **Encryption**: AES-256 encrypted with keys stored in separate HSM/Vault
- **Schedule**:
  - Full backup: Every Sunday 2:00 AM UTC (6 hours full retention)
  - Incremental: Daily Mon-Sat 2:00 AM UTC (30 days retention)
  - Archive: Move >30 day backups to cold storage (7 years retention for audit)
- **Verification**: Monthly restore test pada staging environment untuk confirm integrity

### 9.5. Failover & Failback Procedures

**Failover Procedure** (when primary system down):

1. Detect failure via monitoring alerts (email/SMS to on-call)
2. Validate failure confirmed (manual check on critical issues)
3. Initiate failover to secondary/DR site:
   - Start standby application servers
   - Point DNS to secondary server IP
   - Restore latest database backup to DR DB
   - Verify system accessibility untuk users
4. Notify stakeholders (BPM, management) of incident & ETA
5. Begin incident investigation & forensics

**Failback Procedure** (when primary system recovered):

1. Fix underlying issue on primary system
2. Restore data consistency (sync with secondary)
3. Perform full system test on primary
4. Graceful switchback (during low-usage window if possible)
5. Monitor primary for stability (30 minutes)
6. Document incident & lessons learned

### 9.6. Dokumentasi & Testing

- **Runbook**: Step-by-step failover/failback procedures documented dan ditest quarterly
- **Contact List**: Emergency contacts (DBA, DevOps, Management) accessible 24/7
- **DR Test**: Full DR drill conducted semi-annually (Oct & Apr)
- **Documentation**: Keep runbook updated post-incident with actual timings & issues encountered

**Rujukan**: Lihat **[D09_DATABASE_DOCUMENTATION.md]** §7-8 untuk backup & audit logging strategy yang complementary dengan disaster recovery plan ini.

---

## 10. PERTIMBANGAN KHUSUS GUEST-FIRST ARCHITECTURE

### 10.1. Migrasi Tanpa Akaun Pengguna

Sistem baharu menggunakan seni bina guest-first di mana tetamu tidak mewujudkan akaun pengguna. Implikasi untuk migrasi:

- **Tiket Helpdesk**: Data pengadu (nama, e-mel, telefon, bahagian) disimpan sebagai field dalam jadual `helpdesk_tickets`, bukan sebagai foreign key ke jadual `users`
- **Permohonan Pinjaman**: Data pemohon disimpan sebagai field dalam jadual `loan_applications`
- **Kelulusan**: Kelulusan direkod dengan e-mel pegawai dalam jadual `loan_approvals`, bukan foreign key ke `users`
- **Audit Trail**: Audit trail merekod aktiviti berdasarkan e-mel atau token, bukan user ID

### 10.2. Mapping Data Legacy

Jika sistem lama mempunyai jadual `users` untuk staf:

- **Akaun Pentadbir**: Migrate hanya admin & superuser ke jadual `users` baharu
- **Akaun Staf Biasa**: Tidak dimigrasi; data staf disimpan sebagai field teks dalam rekod tiket/permohonan
- **Sejarah Aktiviti**: Link aktiviti lama ke e-mel pengguna, bukan user ID

### 10.3. Token-Based Approval Migration

Sistem baharu menggunakan token untuk kelulusan e-mel:

- **Legacy Approvals**: Migrate rekod kelulusan lama dengan generate token hash untuk audit
- **Approval Metadata**: Simpan metadata kelulusan (IP hash, timestamp, decision) untuk compliance
- **Token Expiry**: Set token lama sebagai expired untuk keselamatan

---

## 11. PENUTUP

Pelan migrasi ini memastikan data lama dipindahkan ke sistem Helpdesk & ICT Asset Loan MOTAC BPM secara selamat, berkualiti, dan patuh piawaian antarabangsa (ISO 8000, ISO/IEC 27701). Semua proses didokumen, diaudit, dan boleh disemak oleh pihak pengurusan BPM.

**Nota Penting**: Migrasi ini diselaraskan dengan seni bina guest-first sistem baharu, di mana fokus adalah kepada data sejarah dan rekod audit, bukan akaun pengguna. Rujuk **[D09_DATABASE_DOCUMENTATION.md]** untuk struktur database lengkap dan **[D06_DATA_MIGRATION_SPECIFICATION.md]** untuk spesifikasi teknikal terperinci.

---
