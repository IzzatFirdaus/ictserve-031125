# Pelan Integrasi Sistem (System Integration Plan - SIP)

**Sistem ICTServe**  
**Versi:** 2.0.0 (SemVer)  
**Tarikh Kemaskini:** 17 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO/IEC/IEEE 15288, 12207                |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Integrasi melibatkan sistem dalaman MOTAC (bukan integrasi awam). Gunakan kredensial ujian untuk environment pembangunan/pementasan.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal pelan integrasi sistem              | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]** - Spesifikasi Integrasi Sistem (detail teknikal)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan strategi dan proses integrasi untuk sistem **Helpdesk & ICT Asset Loan** BPM MOTAC, berpandukan piawaian **ISO/IEC/IEEE 15288** (system lifecycle processes) dan **ISO/IEC/IEEE 12207** (software lifecycle processes). Ia memastikan semua komponen dan modul sistem digabung secara berstruktur, bermutu, dan dapat beroperasi di persekitaran sebenar MOTAC.

---

## 2. SKOP INTEGRASI (Scope)

- Integrasi dalaman antara modul Helpdesk, Asset Loan, Inventory, Authentication, Reporting, dan Audit Trail.
- Integrasi dengan sistem sedia ada di MOTAC (contoh: LDAP/SSO, Email Server, Database Staf, sistem pengurusan aset legacy).
- Integrasi luaran dengan API (jika ada keperluan masa depan).

---

## 3. OBJEKTIF INTEGRASI (Integration Objectives)

- Memastikan semua modul berfungsi dengan lancar sebagai satu sistem bersepadu.
- Menjamin data konsisten (data consistency) antara Helpdesk, Asset Loan, dan Inventory.
- Memudahkan pertukaran data antara sistem (data exchange & interoperability).
- Mematuhi keperluan keselamatan, privasi, dan tadbir urus data MOTAC.

---

## 4. KOMPONEN UNTUK INTEGRASI (Components for Integration)

| Modul/Komponen         | Integrasi Dengan                  | Tujuan                                           |
|------------------------|----------------------------------|--------------------------------------------------|
| Helpdesk Ticketing     | Asset Loan, Inventory            | Link aduan kerosakan dengan aset dipinjam        |
| Asset Loan             | Inventory, Helpdesk              | Status aset, tiket penyelenggaraan automatik     |
| Inventory              | Asset Loan, Helpdesk             | Data aset, status penggunaan, sejarah            |
| Authentication         | LDAP/SSO MOTAC                   | Single Sign-On staf, kawalan akses peranan       |
| Reporting              | Semua modul                      | Laporan bersatu, analitik, eksport data          |
| Audit Trail            | Semua modul                      | Logging perubahan, audit compliance              |
| Email Notification     | SMTP Server MOTAC                | Email notifikasi untuk aduan, pinjaman, reminder |
| Staff Directory        | Database Staf MOTAC              | Autofill info pengguna, validasi peranan         |
| API (optional/future)  | External Apps                    | Integrasi masa depan (contoh: mobile, dashboard) |

---

## 5. STRATEGI INTEGRASI (Integration Strategy)

### 5.1. Pendekatan Modular (Modular Approach)

- Setiap modul (Helpdesk, Asset Loan, Inventory, dll) dibangunkan sebagai komponen berasingan dengan API dalaman (Laravel service layer).
- Menggunakan Eloquent ORM untuk hubungan model (One-to-Many, Many-to-Many).

### 5.2. Data Mapping & Consistency

- Field utama seperti asset_id, user_id, status, dan timestamp mesti selaras di semua modul.
- Foreign key constraint dan policy validation pada peringkat database dan aplikasi.

### 5.3. Interface & API Integration

- Endpoint API dalaman untuk fetch/sync data antara modul.
- Integrasi LDAP/SSO menggunakan Laravel Socialite atau package SSO lain.
- Integrasi email menggunakan Laravel Notification (mail channel) dan queue.

### 5.4. Pengurusan Error & Fallback

- Semua proses integrasi perlu ada exception handling dan logging.
- Jika integrasi gagal (contoh: LDAP down), fallback ke login dalaman atau notifikasi error kepada admin.

---

## 6. PELAN & JADUAL INTEGRASI (Integration Plan & Schedule)

| Fasa Integrasi         | Aktiviti Utama                                   | Deliverable                | Tempoh        |
|------------------------|--------------------------------------------------|----------------------------|--------------|
| Reka bentuk integrasi  | Data mapping, interface design                   | Data map, API spec         | 1 minggu     |
| Pembangunan modul      | Build, unit test setiap modul                    | Modul siap (unit tested)   | 4 minggu     |
| Integrasi dalaman      | Test hubungan Helpdesk-AssetLoan-Inventory       | Laporan integrasi dalaman  | 1 minggu     |
| Integrasi authentication| Connect ke LDAP/SSO, test login                 | SSO live, fallback tested  | 1 minggu     |
| Integrasi notifikasi   | Email, queue, notifikasi database                | Notifikasi berfungsi       | 3 hari       |
| Integrasi sistem sedia ada | Sync data staf, import aset legacy           | Data imported, validated   | 1 minggu     |
| Ujian integrasi penuh  | End-to-end, UAT bersama BPM & pentadbir sistem   | Laporan UAT & penambahbaikan| 1 minggu    |

---

## 7. UJIAN INTEGRASI (Integration Testing)

- **Unit Test** setiap modul (php artisan test).
- **Integration Test** antara modul utama (tiket pinjam, asset status sync).
- **System Test** untuk end-to-end scenario (user login, buat aduan, link asset, notifikasi).
- **UAT** bersama BPM dan staf terpilih.
- **Regression Test** selepas setiap perubahan fungsi integrasi.

---

## 8. KAWALAN KUALITI & RISIKO (Quality & Risk Control)

| Risiko Integrasi             | Strategi Kawalan/Mitigasi                |
|-----------------------------|------------------------------------------|
| Data tidak konsisten        | Foreign key, transaction, policy check   |
| Gagal connect ke LDAP/Email | Fallback login, error notification       |
| Duplikasi/kehilangan data   | Audit log, backup, rollback plan         |
| Isu performance             | Optimize query, caching, queue           |
| Isu keselamatan             | Role-based access, input validation      |
| Legacy data tidak padan     | Data mapping, cleansing, manual import   |

---

## 9. DOKUMENTASI & LATIHAN (Documentation & Training)

- Semua endpoint API, data mapping, dan flow integrasi didokumenkan (technical & user manual).
- Sesi latihan kepada BPM/IT admin tentang flow integrasi, troubleshooting, dan audit.

---

## 10. PENUTUP

Pelan ini memastikan integrasi sistem Helpdesk & ICT Asset Loan BPM MOTAC berjalan secara teratur, selamat, telus, dan patuh piawaian **ISO/IEC/IEEE 15288** (system lifecycle) & **ISO/IEC/IEEE 12207** (software lifecycle). Proses integrasi disemak melalui ujian sistematik, audit, dan penambahbaikan berterusan agar sistem kekal mantap serta mudah dikembangkan di masa depan.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Integrasi Sistem (System Integration)**: Proses menggabungkan komponen sistem secara teratur
- **Big Bang Integration**: Strategi integrasi semua komponen serentak
- **Incremental Integration**: Strategi integrasi bertahap komponen sistem
- **ISO/IEC/IEEE 15288**: Piawaian kitaran hayat sistem
- **ISO/IEC/IEEE 12207**: Piawaian kitaran hayat perisian

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D08_SYSTEM_INTEGRATION_SPECIFICATION.md** - Spesifikasi teknikal integrasi
- **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** - Rekabentuk teknikal sistem

---

## Lampiran (Appendices)

### A. Matriks Kebergantungan Komponen (Component Dependency Matrix)

Rujuk **D08_SYSTEM_INTEGRATION_SPECIFICATION.md** untuk matriks kebergantungan modul.

### B. Kes Ujian Integrasi (Integration Test Cases)

Kes ujian terperinci disenaraikan dalam **D08_SYSTEM_INTEGRATION_SPECIFICATION.md**.

### C. Daftar Risiko Integrasi (Integration Risk Register)

Rujuk **D08** untuk daftar risiko dan strategi mitigasi.

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 15288:2015, ISO/IEC/IEEE 12207:2017, dan ISO/IEC 33063:2015 (pengukuran proses).**
