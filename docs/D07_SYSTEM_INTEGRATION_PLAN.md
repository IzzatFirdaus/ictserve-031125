# Pelan Integrasi Sistem (System Integration Plan - SIP)

**Sistem ICTServe**
**Versi:** 2.2.0 (SemVer)
**Tarikh Kemaskini:** 29 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 2.2.0                                     |
| **Tarikh Kemaskini** | 29 November 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO/IEC/IEEE 15288, 12207                 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

> Notis Penggunaan Dalaman: Integrasi melibatkan sistem dalaman MOTAC (bukan
> integrasi awam). Gunakan kredensial ujian untuk environment pembangunan/pementasan.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                              | Penulis     |
| ----- | ---------------- | -------------------------------------------------------------------------------------- | ----------- |
| 2.2.0 | 29 November 2025 | Kemaskini teknologi: Laravel 12.40.1, Filament 4.1.10, Livewire 3.7.0, Tailwind 4.1.17 | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini rujukan teknologi: Laravel 12.40.1, Laravel Reverb 1.6.2 untuk real-time     | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                 | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal pelan integrasi sistem                                                      | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Ringkasan Sistem
- [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md) - Spesifikasi Integrasi Sistem (detail teknikal)
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Dokumentasi Rekabentuk Teknikal
- [GLOSSARY.md](GLOSSARY.md) - Glosari Istilah Sistem

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menerangkan strategi dan proses integrasi untuk sistem **Helpdesk &
ICT Asset Loan** BPM MOTAC, berpandukan piawaian **ISO/IEC/IEEE 15288** (system
lifecycle processes) dan **ISO/IEC/IEEE 12207** (software lifecycle processes).
Ia memastikan semua komponen dan modul sistem digabung secara berstruktur,
bermutu, dan dapat beroperasi di persekitaran sebenar MOTAC.

---

## 2. Skop Integrasi (Scope)

- Integrasi dalaman antara modul Helpdesk, Asset Loan, Inventory, Authentication,
  Reporting, dan Audit Trail.
- Integrasi dengan sistem sedia ada di MOTAC (contoh: LDAP/SSO, Email Server,
  Database Staf, sistem pengurusan aset legacy).
- Integrasi luaran dengan API (jika ada keperluan masa depan).

---

## 3. Objektif Integrasi (Integration Objectives)

- Memastikan semua modul berfungsi dengan lancar sebagai satu sistem bersepadu.
- Menjamin data konsisten (data consistency) antara Helpdesk, Asset Loan, dan Inventory.
- Memudahkan pertukaran data antara sistem (data exchange & interoperability).
- Mematuhi keperluan keselamatan, privasi, dan tadbir urus data MOTAC.

---

## 4. Komponen untuk Integrasi (Components for Integration)

| Modul/Komponen     | Integrasi Dengan      | Tujuan                                           |
| ------------------ | --------------------- | ------------------------------------------------ |
| Helpdesk Ticketing | Asset Loan, Inventory | Link aduan kerosakan dengan aset dipinjam        |
| Asset Loan         | Inventory, Helpdesk   | Status aset, tiket penyelenggaraan automatik     |
| Inventory          | Asset Loan, Helpdesk  | Data aset, status penggunaan, sejarah            |
| Authentication     | LDAP/SSO MOTAC        | Single Sign-On staf, kawalan akses peranan       |
| Reporting          | Semua modul           | Laporan bersatu, analitik, eksport data          |
| Audit Trail        | Semua modul           | Logging perubahan, audit compliance              |
| Email Notification | SMTP Server MOTAC     | Email notifikasi untuk aduan, pinjaman, reminder |
| Staff Directory    | Database Staf MOTAC   | Autofill info pengguna, validasi peranan         |
| API (optional)     | External Apps         | Integrasi masa depan (contoh: mobile, dashboard) |

---

## 5. Strategi Integrasi (Integration Strategy)

### 5.1. Pendekatan Modular (Modular Approach)

- Setiap modul (Helpdesk, Asset Loan, Inventory, dll) dibangunkan sebagai komponen
  berasingan dengan API dalaman (Laravel service layer).
- Menggunakan Eloquent ORM untuk hubungan model (One-to-Many, Many-to-Many).
- Filament 4.1.10 digunakan untuk panel pentadbiran dengan integrasi modul bersepadu.

### 5.2. Data Mapping & Consistency

- Field utama seperti `asset_id`, `user_id`, `status`, dan `timestamp` mesti selaras
  di semua modul.
- Foreign key constraint dan policy validation pada peringkat database dan aplikasi.
- Livewire 3.7.0 memastikan konsistensi data real-time antara frontend dan backend.

### 5.3. Interface & API Integration

- Endpoint API dalaman untuk fetch/sync data antara modul.
- Integrasi LDAP/SSO menggunakan Laravel Socialite atau package SSO lain.
- Integrasi email menggunakan Laravel Notification (mail channel) dan queue.
- Laravel Reverb 1.6.2 untuk komunikasi WebSocket real-time.

### 5.4. Pengurusan Error & Fallback

- Semua proses integrasi perlu ada exception handling dan logging.
- Jika integrasi gagal (contoh: LDAP down), fallback ke login dalaman atau
  notifikasi error kepada admin.
- Laravel Auditing untuk jejak audit semua operasi integrasi.

---

## 6. Teknologi Integrasi (Integration Technology Stack)

| Komponen             | Teknologi      | Versi   | Fungsi                            |
| -------------------- | -------------- | ------- | --------------------------------- |
| Framework            | Laravel        | 12.40.1 | Backend application framework     |
| Admin Panel          | Filament       | 4.1.10  | CRUD interfaces, dashboard        |
| Reactive UI          | Livewire       | 3.7.0   | Server-driven UI components       |
| Single-file Livewire | Volt           | 1.10.1  | Single-file Livewire components   |
| WebSocket Server     | Laravel Reverb | 1.6.2   | Real-time communication           |
| WebSocket Client     | Laravel Echo   | 2.2.6   | Client-side WebSocket integration |
| CSS Framework        | Tailwind CSS   | 4.1.17  | Utility-first styling             |
| Database             | MySQL          | 8.x     | Production database               |
| Queue                | Redis          | -       | Job queue & caching               |
| Testing              | PHPUnit        | 11.5.44 | Unit & integration testing        |
| Static Analysis      | Larastan       | 3.8.0   | PHP static analysis               |
| Code Style           | Laravel Pint   | 1.26.0  | PSR-12 code formatting            |

---

## 7. Pelan & Jadual Integrasi (Integration Plan & Schedule)

| Fasa Integrasi             | Aktiviti Utama                                 | Deliverable                  | Tempoh   |
| -------------------------- | ---------------------------------------------- | ---------------------------- | -------- |
| Reka bentuk integrasi      | Data mapping, interface design                 | Data map, API spec           | 1 minggu |
| Pembangunan modul          | Build, unit test setiap modul                  | Modul siap (unit tested)     | 4 minggu |
| Integrasi dalaman          | Test hubungan Helpdesk-AssetLoan-Inventory     | Laporan integrasi dalaman    | 1 minggu |
| Integrasi authentication   | Connect ke LDAP/SSO, test login                | SSO live, fallback tested    | 1 minggu |
| Integrasi notifikasi       | Email, queue, notifikasi database              | Notifikasi berfungsi         | 3 hari   |
| Integrasi sistem sedia ada | Sync data staf, import aset legacy             | Data imported, validated     | 1 minggu |
| Ujian integrasi penuh      | End-to-end, UAT bersama BPM & pentadbir sistem | Laporan UAT & penambahbaikan | 1 minggu |

---

## 8. Ujian Integrasi (Integration Testing)

### 8.1. Jenis Ujian

- **Unit Test**: Setiap modul diuji secara berasingan (`php artisan test`).
- **Integration Test**: Ujian antara modul utama (tiket pinjam, asset status sync).
- **System Test**: End-to-end scenario (user login, buat aduan, link asset, notifikasi).
- **UAT**: User Acceptance Test bersama BPM dan staf terpilih.
- **Regression Test**: Selepas setiap perubahan fungsi integrasi.

### 8.2. Alat Ujian

- PHPUnit 11.5.44 untuk unit dan feature tests.
- Livewire testing utilities untuk komponen Livewire.
- Playwright untuk E2E browser testing.
- Laravel Dusk untuk browser automation testing.

### 8.3. Kriteria Penerimaan

- Semua unit tests lulus (100% pass rate).
- Integration tests mencapai coverage minimum 80%.
- Tiada critical bugs dalam UAT.
- Performance metrics memenuhi Core Web Vitals (LCP < 2.5s, FID < 100ms, CLS < 0.1).

---

## 9. Kawalan Kualiti & Risiko (Quality & Risk Control)

| Risiko Integrasi            | Strategi Kawalan/Mitigasi               |
| --------------------------- | --------------------------------------- |
| Data tidak konsisten        | Foreign key, transaction, policy check  |
| Gagal connect ke LDAP/Email | Fallback login, error notification      |
| Duplikasi/kehilangan data   | Audit log, backup, rollback plan        |
| Isu performance             | Optimize query, caching, queue          |
| Isu keselamatan             | Role-based access, input validation     |
| Legacy data tidak padan     | Data mapping, cleansing, manual import  |
| WebSocket connection drop   | Reconnection strategy, fallback polling |

### 9.1. Quality Gates

Setiap fasa integrasi mesti lulus quality gates berikut:

1. **Code Quality**: Laravel Pint (PSR-12), Larastan level 5 minimum.
2. **Test Coverage**: Minimum 80% untuk kod kritikal.
3. **Security Review**: Input validation, authorization policies.
4. **Performance**: Response time < 200ms untuk API endpoints.
5. **Accessibility**: WCAG 2.2 AA compliance untuk UI components.

---

## 10. Dokumentasi & Latihan (Documentation & Training)

- Semua endpoint API, data mapping, dan flow integrasi didokumenkan (technical &
  user manual).
- Sesi latihan kepada BPM/IT admin tentang flow integrasi, troubleshooting, dan audit.
- Dokumentasi mengikut piawaian ISO/IEC/IEEE 15289 (documentation requirements).

### 10.1. Dokumentasi Teknikal

- API specification (OpenAPI/Swagger format).
- Database schema documentation (ERD, data dictionary).
- Integration flow diagrams (sequence diagrams, flowcharts).
- Troubleshooting guides untuk common integration issues.

### 10.2. Dokumentasi Pengguna

- User manual untuk setiap modul.
- Quick reference guides untuk operasi harian.
- FAQ dan knowledge base untuk self-service support.

---

## 11. Penutup

Pelan ini memastikan integrasi sistem Helpdesk & ICT Asset Loan BPM MOTAC berjalan
secara teratur, selamat, telus, dan patuh piawaian **ISO/IEC/IEEE 15288** (system
lifecycle) & **ISO/IEC/IEEE 12207** (software lifecycle). Proses integrasi disemak
melalui ujian sistematik, audit, dan penambahbaikan berterusan agar sistem kekal
mantap serta mudah dikembangkan di masa depan.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk [GLOSSARY.md](GLOSSARY.md) untuk istilah teknikal seperti:

- **Integrasi Sistem (System Integration)**: Proses menggabungkan komponen sistem
  secara teratur
- **Big Bang Integration**: Strategi integrasi semua komponen serentak
- **Incremental Integration**: Strategi integrasi bertahap komponen sistem
- **ISO/IEC/IEEE 15288**: Piawaian kitaran hayat sistem
- **ISO/IEC/IEEE 12207**: Piawaian kitaran hayat perisian

### Dokumen Rujukan

- [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Gambaran keseluruhan sistem
- [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md) - Spesifikasi teknikal integrasi
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Rekabentuk teknikal sistem

---

## Lampiran (Appendices)

### A. Matriks Kebergantungan Komponen (Component Dependency Matrix)

Rujuk [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
untuk matriks kebergantungan modul.

### B. Kes Ujian Integrasi (Integration Test Cases)

Kes ujian terperinci disenaraikan dalam
[D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md).

### C. Daftar Risiko Integrasi (Integration Risk Register)

Rujuk [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
untuk daftar risiko dan strategi mitigasi.

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 15288:2015, ISO/IEC/IEEE 12207:2017,
dan ISO/IEC 33063:2015 (pengukuran proses).**
