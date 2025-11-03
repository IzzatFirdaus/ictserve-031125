# Pelan Pembangunan Sistem (System Development Plan - SDP)

**Sistem ICTServe**  
**Versi:** 2.0.0 (SemVer)  
**Tarikh Kemaskini:** 17 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO/IEC/IEEE 12207                       |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Pelan ini dirangka untuk sistem dalaman MOTAC (bukan untuk kegunaan awam).

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal pelan pembangunan sistem            | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference, tambah rujukan dokumen | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perniagaan
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini bertujuan memberi perancangan lengkap dan terperinci bagi pembangunan sistem **Helpdesk & ICT Asset Loan** berasaskan Laravel 12 untuk penggunaan dalaman MOTAC BPM, mematuhi piawaian antarabangsa **ISO/IEC/IEEE 12207** untuk lifecycle pembangunan perisian (software lifecycle processes).

---

## 2. SKOP PROJEK (Project Scope)

### 2.1. Skop Sistem

- Sistem web berasaskan **Laravel 12** untuk pengurusan tiket aduan ICT & permohonan pinjaman aset ICT.
- **Pengguna Sasaran:** Staf MOTAC, Pegawai ICT BPM, Ketua Bahagian, Admin BPM.
- **Platform:** Web-based intranet MOTAC (akses dalaman sahaja).

### 2.2. Modul Utama

1. **Helpdesk Ticketing** - Pengurusan aduan dan masalah ICT
2. **Asset Loan** - Permohonan dan pengurusan pinjaman peralatan ICT
3. **Inventory Management** - Pengurusan inventori aset ICT
4. **Authentication & Authorization** - Login, role-based access control
5. **Reporting & Dashboard** - Laporan dan analitik
6. **Audit Trail** - Logging dan audit compliance

**Rujukan:** Lihat **[D00_SYSTEM_OVERVIEW.md]** untuk ringkasan modul dan **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** untuk spesifikasi fungsional lengkap.

---

## 3. ORGANISASI PROJEK (Project Organization)

| Peranan         | Tanggungjawab Utama                        |
|-----------------|--------------------------------------------|
| **Project Owner** | Bahagian Pengurusan Maklumat (BPM), MOTAC |
| **Project Manager** | Menyelia jadual, milestone, komunikasi   |
| **System Analyst** | Analisis keperluan, dokumen spesifikasi   |
| **Lead Developer** | Reka bentuk, pantau kod, deployment       |
| **Frontend Developer** | UI/UX, Blade, Tailwind CSS, Filament   |
| **Backend Developer** | API, Eloquent, logic sistem             |
| **QA/Test Engineer** | Ujian fungsi, regression, acceptance     |
| **End User**         | Staf MOTAC, Pegawai BPM ICT              |

---

## 4. PROSES PEMBANGUNAN (Development Processes)

Mematuhi ISO/IEC/IEEE 12207 lifecycle:

### 4.1. Inisiasi & Keperluan (Initiation & Requirements)

- **Pengumpulan Keperluan**: Interview pengguna utama, BPM, review dokumen borang aduan kerosakan & permohonan pinjaman.
- **Dokumentasi**: Spesifikasi keperluan fungsional & bukan fungsional.

### 4.2. Rekabentuk Sistem (System Design)

- **Architecture**: MVC Laravel, modular, scalable.
- **Database Design**: Entity Relationship Diagram (ERD) untuk users, tickets, assets, loans.
- **Interface Design**: Wireframe untuk semua modul utama, responsive dengan Tailwind CSS & komponen Filament.

### 4.3. Pembangunan (Implementation)

- **Setup Environment**: composer create-project, setup .env, database config.
- **Authentication**: Laravel Breeze atau Laravel Jetstream untuk login, registration.
- **CRUD Modules**: Inventories, Helpdesk Tickets, Asset Loans.
- **Relational Models**: One-to-Many (User-Inventory, Asset-Loan), Soft Deletes.
- **API Endpoints**: For dynamic dropdowns & asset lookup.
- **Audit Trail**: Integrasi owen-it/laravel-auditing.
- **Notification & Queue**: Email, database notification, job queue setup.

### 4.4. Ujian (Testing)

- **Unit Testing**: php artisan test untuk model, controller, request validation.
- **Integration Testing**: Workflow penuh borang aduan & pinjaman.
- **User Acceptance Testing (UAT)**: Bersama BPM & staf MOTAC.

### 4.5. Deployment & Maintenance

- **Deployment**: ke server intranet MOTAC (Linux, nginx/apache).
- **Maintenance Mode**: php artisan down/up untuk update, backup berkala.
- **Performance Optimization**: php artisan config:cache, route:cache, view:cache, optimize.
- **Log & Monitoring**: storage/logs, audit trail, backup DB.

---

## 5. STANDARD & BEST PRACTICES

- **Pematuhan ISO/IEC/IEEE 12207**: Setiap fasa documented & traceable.
- **Security**: CSRF protection, validation, role-based access, environment config.
- **Documentation**: Kod sumber lengkap dengan komen, dokumen teknikal, user manual.
- **Quality Assurance**: Ujian pelbagai tahap, bug tracking, regression testing.

---

## 6. JADUAL & MILESTONE (Schedule & Milestones)

| Fasa                   | Tempoh         | Deliverable                |
|------------------------|---------------|----------------------------|
| Inisiasi & Keperluan   | 2 minggu      | Dokumen Keperluan & ERD    |
| Rekabentuk Sistem      | 2 minggu      | Wireframe, Database Schema |
| Pembangunan            | 6 minggu      | Modul Siap, API, Integrasi |
| Ujian & UAT            | 2 minggu      | Laporan Ujian & UAT        |
| Deployment             | 1 minggu      | Live di intranet MOTAC     |
| Maintenance            | Berterusan    | Patch, backup, support     |

---

## 7. RISIKO & MITIGASI (Risks & Mitigation)

| Risiko                            | Strategi Mitigasi                   |
|------------------------------------|-------------------------------------|
| Kelewatan keperluan pengguna      | Weekly review, early prototype      |
| Perubahan scope                   | Change request & impact analysis    |
| Isu integrasi sistem legacy       | Early integration testing           |
| Kerosakan/kehilangan data         | Automated backup, audit trail       |
| Masalah keselamatan               | Penetration test, update dependency |
| Kekurangan dokumentasi            | Dedicated documentation phase       |

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

| Langkah | Aktiviti | Pihak Bertanggungjawab | Kriteria Kelulusan | Dokumentasi |
|---------|----------|----------------------|-------------------|------------|
| 1. Permohonan Perubahan | Cipta Change Request (CR) dengan deskripsi, impact, risk | Pembangun/PM | CR form lengkap | CR ticket |
| 2. Penilaian Impact | Analisis dampak teknikal, jadual, sumber daya | Technical Lead | Impact documented | Impact report |
| 3. Kelulusan Teknikal | Semak CR, persetujuan teknikal atau penolakan | Lead Developer | Teknikally sound | Approval note |
| 4. Kelulusan Pengurusan | Kelulusan akhir daripada Project Manager | Project Manager | Schedule OK, budget OK | PM sign-off |
| 5. Pelaksanaan | Laksanakan perubahan mengikut CR detail, test, document | Developer | Code review pass | Commit message |
| 6. Ujian & Validasi | Ujian regresi, UAT jika perlu | QA/Tester | All tests pass | Test report |
| 7. Deployment | Deploy ke staging/production dengan runbook | DevOps | Deployment checklist | Deployment log |
| 8. Dokumentasi | Kemas kini D00-D14 dokumentasi & RTM jika perlu | Technical Writer | Doc aligned with code | Updated doc |
| 9. Penutupan CR | Tutup CR dengan nota penutupan & lessons learned | PM | All steps complete | CR closed |

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
