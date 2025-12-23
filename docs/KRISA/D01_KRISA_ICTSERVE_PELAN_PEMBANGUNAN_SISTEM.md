# D01 DOKUMEN PELAN PEMBANGUNAN SISTEM (PPS)

**SISTEM ICTSERVE**  
**Platform Pengurusan Helpdesk & Pinjaman Aset ICT**

| | |
| :--- | :--- |
| **NAMA AGENSI** | : Bahagian Pengurusan Maklumat (BPM) |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **TARIKH DOKUMEN** | : 17 Disember 2025 |
| **VERSI DOKUMEN** | : 3.6.1 |

---

## i. Keterangan Dokumen

Dokumen ini menyatakan pelan bagi pengurusan dan pembangunan Sistem ICTServe. Ia bertujuan untuk menerangkan secara terperinci perancangan-perancangan yang telah dibangunkan merangkumi serahan projek, pengendalian projek, perancangan proses teknikal seperti pendekatan projek, perkakasan dan perisian yang akan digunakan, dokumen-dokumen yang akan disediakan serta jadual pelaksanaan pembangunan aplikasi.

Sistem ICTServe adalah platform web berasaskan Laravel 12.43.1 untuk pengurusan tiket helpdesk dan permohonan pinjaman aset ICT bagi kegunaan dalaman staf MOTAC. Sistem ini mematuhi piawaian ISO/IEC/IEEE 12207 (Software Lifecycle Processes), WCAG 2.2 AA (Web Content Accessibility Guidelines), dan MyGOV Digital Service Standards v2.1.0.

## ii. Semakan dan Pengesahan Dokumen

**SEMAKAN DOKUMEN**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | Ketua Pembangun Sistem | | |
| | Penganalisis Sistem Kanan | | |

**PENGESAHAN DOKUMEN**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | Pengurus Projek | | |
| | Ketua Bahagian Pengurusan Maklumat | | |

## iii. Kawalan Dokumen

**KAWALAN DOKUMEN**

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0.0 | September 2024 | Versi awal pelan pembangunan sistem | Pasukan BPM |
| 2.0.0 | 17 Oktober 2024 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM |
| 3.0.0 | 31 Oktober 2025 | Kemaskini stack teknologi: Laravel 12, Livewire 3, Filament 4 | Pasukan BPM |
| 3.5.0 | 1 Disember 2025 | True Hybrid Architecture, Laravel Pulse, Sanctum, Socialite | Pasukan BPM |
| 3.6.0 | 8 Disember 2025 | Penyeragaman Bahasa Melayu sahaja, Cloud Hybrid AI (D18) | Pasukan BPM |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack, AI integration, metodologi | Pasukan BPM |

## iv. Kandungan

1. PENGENALAN
   - 1.1. Tujuan Projek
   - 1.2. Skop Projek
   - 1.3. Serahan Projek
2. PENGENDALIAN PROJEK PEMBANGUNAN APLIKASI
   - 2.1. Model Proses
   - 2.2. Struktur Organisasi Pasukan
   - 2.3. Peranan dan Tanggungjawab
3. PROSES PENGURUSAN
   - 3.1. Andaian, Kebergantungan dan Kekangan
   - 3.2. Risiko
   - 3.3. Tahap Kebarangkalian Risiko dan Tahap Impak
   - 3.4. Pemantauan dan Kawalan
4. PROSES TEKNIKAL
   - 4.1. Pendekatan, Teknik dan Alat Bantu
   - 4.2. Dokumen Aplikasi
   - 4.3. Dokumen Fungsi Sokongan
5. PAKEJ KERJA, JADUAL DAN PERUNTUKAN
   - 5.1. Pakej Kerja
   - 5.2. Kebergantungan
   - 5.3. Sumber
   - 5.4. Peruntukan Kos
   - 5.5. Jadual Perancangan
6. KOMPONEN TAMBAHAN
7. LAMPIRAN

## v. Senarai Gambarajah

| No. | Tajuk Gambarajah | Muka Surat |
| :--- | :--- | :--- |
| 1 | Struktur Organisasi Pasukan Projek | §2.2 |
| 2 | Model Proses Pembangunan (Waterfall-Agile Hybrid) | §2.1 |
| 3 | Matriks Risiko Projek | §3.3 |
| 4. | Jadual Gantt Pelaksanaan Projek | §5.5 |

## vi. Senarai Jadual

| No. | Tajuk Jadual | Muka Surat |
| :--- | :--- | :--- |
| 1 | Modul Utama Sistem ICTServe | §1.2 |
| 2 | Peranan dan Tanggungjawab Pasukan | §2.3 |
| 3 | Risiko Projek dan Strategi Mitigasi | §3.2 |
| 4 | Pakej Kerja Personel | §5.1 |
| 5 | Peruntukan Kos Projek | §5.4 |
| 6 | Jadual Milestone Projek | §5.5 |

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan |
| :--- | :--- |
| API | Application Programming Interface |
| BPM | Bahagian Pengurusan Maklumat |
| CRUD | Create, Read, Update, Delete |
| ERD | Entity Relationship Diagram |
| ICT | Information and Communication Technology |
| MOTAC | Kementerian Pelancongan, Seni dan Budaya Malaysia |
| MVC | Model-View-Controller |
| PDPA | Personal Data Protection Act 2010 |
| PPS | Pelan Pembangunan Sistem |
| PSR | PHP Standards Recommendation |
| SDUI | Server-Driven User Interface |
| SLA | Service Level Agreement |
| SSO | Single Sign-On |
| UAT | User Acceptance Testing |
| WCAG | Web Content Accessibility Guidelines |

### b. Definisi

| Terma/Istilah | Definisi |
| :--- | :--- |
| Helpdesk Ticketing | Sistem pengurusan tiket aduan dan masalah ICT |
| Asset Loan | Sistem permohonan dan pengurusan pinjaman peralatan ICT |
| True Hybrid Architecture | Seni bina sistem yang menyokong akses tetamu dan pengguna berdaftar |
| Livewire | Framework PHP untuk membina antara muka reaktif tanpa menulis JavaScript |
| Filament | Framework admin panel berasaskan Laravel dengan SDUI |
| Laravel Reverb | Pelayan WebSocket native Laravel untuk komunikasi real-time |
| Laravel Pulse | Dashboard pemantauan prestasi aplikasi Laravel |
| Laravel Sanctum | Sistem pengesahan API berasaskan token |
| Dual Audit System | Sistem audit berganda (owen-it + spatie) untuk pematuhan dan operasi |

## viii. Sumber Rujukan

1. **ISO/IEC/IEEE 12207:2017** - Systems and software engineering - Software life cycle processes
2. **ISO/IEC/IEEE 15289:2019** - Systems and software engineering - Content of life-cycle information items (documentation)
3. **ISO/IEC TS 24748-6:2016** - Systems and software engineering - Life cycle management - Part 6: System integration engineering
4. **IEEE 1016:2009** - IEEE Standard for Information Technology - Systems Design - Software Design Descriptions
5. **WCAG 2.2** - Web Content Accessibility Guidelines Level AA
6. **MyGOV Digital Service Standards v2.1.0** - Malaysian Government Digital Service Standards
7. **Personal Data Protection Act 2010 (PDPA)** - Malaysian data protection legislation
8. **Laravel 12 Documentation** - <https://laravel.com/docs/12.x>
9. **Livewire 3 Documentation** - <https://livewire.laravel.com/docs/3.x>
10. **Filament 4 Documentation** - <https://filamentphp.com/docs/4.x>
11. **D00_SYSTEM_OVERVIEW.md** - Ringkasan Sistem ICTServe v3.6.1
12. **D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md** - Spesifikasi Keperluan Perniagaan v3.6.1
13. **D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md** - Spesifikasi Keperluan Perisian v3.6.1
14. **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Dokumen Rekabentuk Perisian v3.6.1
15. **D18_AI_CHATBOT_OLLAMA_BEDROCK.md** - Cloud Hybrid AI Architecture v1.0.1

---

## 1. PENGENALAN

### 1.1. Tujuan Projek

Projek pembangunan Sistem ICTServe dilaksanakan untuk memenuhi keperluan Bahagian Pengurusan Maklumat (BPM) MOTAC dalam menguruskan perkhidmatan sokongan ICT secara sistematik dan teratur. Sistem ini bertujuan untuk:

1. **Meningkatkan Kecekapan Operasi**: Mengautomasikan proses pengurusan tiket helpdesk dan permohonan pinjaman aset ICT yang sebelum ini dilakukan secara manual menggunakan borang kertas dan e-mel.

2. **Meningkatkan Ketelusan**: Menyediakan platform berpusat untuk staf MOTAC memantau status aduan dan permohonan pinjaman secara real-time.

3. **Mematuhi Piawaian Kerajaan**: Memastikan sistem mematuhi MyGOV Digital Service Standards v2.1.0, WCAG 2.2 AA untuk aksesibiliti, dan PDPA 2010 untuk perlindungan data peribadi.

4. **Meningkatkan Kualiti Perkhidmatan**: Menyediakan mekanisme SLA (Service Level Agreement) untuk memastikan aduan diselesaikan dalam tempoh yang ditetapkan.

5. **Menyokong Keputusan Pengurusan**: Menyediakan dashboard analitik dan laporan untuk membantu pengurusan BPM membuat keputusan berasaskan data.

Projek ini telah dipersetujui oleh pihak pengurusan MOTAC dan BPM sebagai inisiatif transformasi digital dalaman.

### 1.2. Skop Projek

Skop projek pembangunan Sistem ICTServe merangkumi:

#### 1.2.1. Skop Fungsional

**Jadual 1: Modul Utama Sistem ICTServe**

| Bil. | Modul | Keterangan Fungsi |
| :--- | :--- | :--- |
| 1 | Helpdesk Ticketing | Pengurusan tiket aduan ICT dengan kategori, keutamaan, SLA tracking, internal comments, dan cross-module integration |
| 2 | Asset Loan Management | Permohonan pinjaman aset ICT dengan dual approval workflow, accessory tracking, pickup OTP, dan check-in/check-out management |
| 3 | Inventory Management | Pengurusan inventori aset ICT dengan QR code, status tracking, maintenance scheduling |
| 4 | Authentication & Authorization | True Hybrid: Self-registration (@motac.gov.my), flexible login (email/username), optional Google SSO, role-based access control |
| 5 | Reporting & Dashboard | Dashboard analitik dengan Filament widgets, laporan terjadual, export PDF/Excel |
| 6 | Audit Trail (Dual System) | Owen-it (compliance, 7-year retention) + Spatie (operations) untuk audit lengkap |
| 7 | Real-time Communication | Laravel Reverb WebSocket untuk notifikasi real-time dan live updates |
| 8 | Performance Monitoring | Laravel Pulse dashboard untuk slow queries, queue metrics, server health (admin/superuser) |
| 9 | API Authentication | Laravel Sanctum token-based API untuk future mobile/external integrations |
| 10 | Cloud Hybrid AI | Ollama (local) + AWS Bedrock (cloud) untuk FAQ Bot, Document Analysis, Auto-Reply |

#### 1.2.2. Skop Teknikal

- **Platform**: Aplikasi web berasaskan Laravel 12.43.1
- **Bahasa Pengaturcaraan**: PHP 8.2.12
- **Pangkalan Data**: MySQL 8.0
- **Frontend Framework**: Livewire 3.7.3, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.18
- **Admin Panel**: Filament 4.3.1
- **Real-time**: Laravel Reverb 1.6.3, Laravel Echo 2.2.6
- **Deployment**: Docker Compose (development), Linux server (production)

#### 1.2.3. Skop Pengguna

- **Staf MOTAC**: Pengguna utama untuk submit tiket dan permohonan pinjaman
- **Pegawai ICT BPM**: Admin untuk proses tiket dan loan applications
- **Ketua Bahagian**: Approver untuk permohonan pinjaman (Grade 41+)
- **Superuser BPM**: Pengurusan sistem, konfigurasi, audit review

#### 1.2.4. Had Skop (Out of Scope)

- Integrasi dengan sistem HRMIS (future phase)
- Mobile application (future phase)
- Public-facing portal (sistem dalaman sahaja)
- Procurement module (future phase)

### 1.3. Serahan Projek

**Jadual 2: Serahan Projek Mengikut Fasa**

| Bil. | Nama Serahan | Fasa | Tarikh Serahan | Kuantiti | Penyedia | Pengesah | Pelulus |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | D00 System Overview | Inisiasi | Minggu 1 | 1 dokumen | System Analyst | Lead Developer | Project Manager |
| 2 | D02 Business Requirements | Keperluan | Minggu 2 | 1 dokumen | System Analyst | BPM Stakeholders | Project Manager |
| 3 | D03 Software Requirements | Keperluan | Minggu 2 | 1 dokumen | System Analyst | Lead Developer | Project Manager |
| 4 | D04 Software Design | Rekabentuk | Minggu 4 | 1 dokumen | Lead Developer | System Analyst | Project Manager |
| 5 | Database Schema (ERD) | Rekabentuk | Minggu 4 | 1 diagram | Backend Developer | Lead Developer | Project Manager |
| 6 | Wireframes & Mockups | Rekabentuk | Minggu 4 | 10+ screens | Frontend Developer | UX Lead | Project Manager |
| 7 | Docker Environment | Setup | Minggu 5 | 1 setup | DevOps Engineer | Lead Developer | Project Manager |
| 8 | Authentication Module | Pembangunan | Minggu 7 | 1 modul | Backend Developer | Lead Developer | Project Manager |
| 9 | Helpdesk Module | Pembangunan | Minggu 10 | 1 modul | Full Stack Team | Lead Developer | Project Manager |
| 10 | Asset Loan Module | Pembangunan | Minggu 13 | 1 modul | Full Stack Team | Lead Developer | Project Manager |
| 11 | Filament Admin Panel | Pembangunan | Minggu 15 | 1 panel | Frontend Developer | Lead Developer | Project Manager |
| 12 | Real-time Features | Pembangunan | Minggu 17 | 1 modul | Backend Developer | Lead Developer | Project Manager |
| 13 | Cloud Hybrid AI | Pembangunan | Minggu 19 | 1 modul | AI Developer | Lead Developer | Project Manager |
| 14 | Unit Test Suite | Ujian | Minggu 20 | 100+ tests | QA Engineer | Lead Developer | Project Manager |
| 15 | E2E Test Suite | Ujian | Minggu 21 | 50+ tests | QA Engineer | Lead Developer | Project Manager |
| 16 | D09 Database Documentation | Dokumentasi | Minggu 22 | 1 dokumen | Backend Developer | Lead Developer | Project Manager |
| 17 | D10 Source Code Documentation | Dokumentasi | Minggu 22 | 1 dokumen | Lead Developer | System Analyst | Project Manager |
| 18 | User Manual | Dokumentasi | Minggu 23 | 1 manual | Technical Writer | System Analyst | Project Manager |
| 19 | UAT Report | Ujian | Minggu 24 | 1 laporan | QA Engineer | BPM Stakeholders | Project Manager |
| 20 | Production Deployment | Deployment | Minggu 25 | 1 sistem | DevOps Engineer | Lead Developer | Project Manager |

---
