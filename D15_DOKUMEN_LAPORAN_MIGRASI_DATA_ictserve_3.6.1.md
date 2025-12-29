# D15 DOKUMEN LAPORAN MIGRASI DATA

**ICTServe**

*(Modul: Helpdesk & ICT Asset Loan)*

| | |
| :--- | :--- |
| **NAMA AGENSI** | : Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **NAMA AGENSI INDUK** | : Bahagian Pengurusan Maklumat (BPM), MOTAC |
| **TARIKH DOKUMEN** | : 17 Disember 2025 |
| **VERSI DOKUMEN** | : 3.6.1 |

---

## i. Keterangan Dokumen

Dokumen ini merupakan **Laporan Migrasi Data** bagi sistem **ICTServe v3.6.1** untuk merekod status, keputusan, dan perincian migrasi data daripada sumber legacy ke destinasi baharu.

Kandungan laporan ini adalah berasaskan dokumen rujukan v3.6.1 berikut:

- **Pelan Migrasi Data (D05)**: skop, prinsip, langkah migrasi, jadual migrasi, risiko & mitigasi.
- **Spesifikasi Migrasi Data (D06)**: struktur data sasaran (table/field utama) dan piawaian data.

**Nota penting (metrik migrasi)**: Nilai **jumlah baris table sumber**, **jumlah baris berjaya dimigrasi**, dan **ratio** perlu direkod semasa pelaksanaan migrasi sebenar. Sekiranya tiada angka pelaksanaan tersedia dalam dokumen rujukan, ruangan metrik ditanda sebagai **TBD (To Be Determined)**.

## ii. Semakan dan Pengesahan Dokumen

Dengan ini adalah disahkan dokumen laporan migrasi data ini telah disemak dan disahkan untuk kegunaan dalaman MOTAC.

Dokumen ini turut menjadi rekod rasmi yang menyokong audit migrasi (rujuk D05) dan perlu dilengkapkan dengan bukti pelaksanaan (log, ringkasan verifikasi, dan keputusan kiraan baris) selepas migrasi sebenar dilaksanakan.

**SEMAKAN DOKUMEN**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
|  |  |  |  |
|  |  |  |  |

**PENGESAHAN DOKUMEN**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
|  |  |  |  |
|  |  |  |  |

---

## 1. PENGENALAN PROJEK

Projek migrasi data ICTServe bertujuan memindahkan data berkaitan **aduan ICT (helpdesk)**, **inventori aset ICT**, **rekod pinjaman peralatan**, serta **profil staf** daripada pelbagai sumber legacy (manual/digital/sistem sedia ada) ke sistem baharu berasaskan **Laravel 12.43.1**.

Objektif utama migrasi data adalah:

- Memastikan data kritikal (staf, tiket, pinjaman, aset) tersedia dan boleh digunakan dalam ICTServe v3.6.1.
- Memastikan integriti dan konsistensi data (termasuk hubungan `user_id`, data rujukan, dan audit trail) dikekalkan.
- Menyediakan asas bukti pelaksanaan (log, verifikasi, dan metrik) untuk tujuan audit dalaman.

Selaras dengan pendekatan **True Hybrid Architecture**, migrasi memberi penekanan kepada:

- Memasukkan rekod **staf** ke table `users` untuk membolehkan log masuk / pendaftaran kendiri menggunakan e-mel `@motac.gov.my`.
- Memautkan rekod sejarah (tiket/pinjaman) kepada `user_id` melalui padanan e-mel (jika staf berdaftar), atau mengekalkan `user_id = NULL` untuk rekod tetamu (guest).

## 2. JADUAL PELAKSANAAN ASAL DAN SEBENAR

Jadual pelaksanaan asal adalah berasaskan anggaran fasa dalam Pelan Migrasi Data (D05). Jadual pelaksanaan sebenar perlu dikemaskini selepas pelaksanaan.

| Fasa | Pelaksanaan Asal (Anggaran) | Pelaksanaan Sebenar | Catatan |
| :--- | :--- | :--- | :--- |
| Penilaian & Mapping | 1 minggu | TBD | Data inventory, mapping, data dictionary |
| Cleansing/Standard | 1 minggu | TBD | Deduplication, validation, standardization |
| Skrip & Ujian | 2 minggu | TBD | Scripting, dry run, validation, testing |
| Migrasi Sebenar | 1–2 hari | TBD | Go-live migration, backup, verification |
| Audit & Review | 3 hari | TBD | Post-migration review, reporting, documentation |

## 3. STATUS MIGRASI

Status migrasi data bagi ICTServe v3.6.1 direkod seperti berikut:

- **Skop, prinsip, langkah migrasi dan jadual migrasi**: Dirancang (rujuk D05).
- **Struktur data sasaran & piawaian data**: Ditakrifkan (rujuk D06).
- **Pelaksanaan migrasi sebenar & metrik hasil (row/ratio)**: TBD (perlu direkod semasa pelaksanaan migrasi).

Ringkasan status semasa:

| Komponen | Status | Rujukan | Bukti/Nota |
| :--- | :--- | :--- | :--- |
| Pelan migrasi | Dirancang | D05 | Berasaskan dokumen rujukan v3.6.1 |
| Spesifikasi migrasi (sasaran) | Ditakrifkan | D06 | Berasaskan dokumen rujukan v3.6.1 |
| Log pelaksanaan migrasi data legacy | TBD | D05 | Perlu dilampirkan selepas pelaksanaan |
| Kiraan baris sumber/destinasi dan ratio | TBD | D05/D06 | Perlu dilampirkan selepas pelaksanaan |

Rajah aliran ringkas migrasi data:

```mermaid
flowchart LR
    S[Sumber legacy] --> C[Cleansing/Mapping]
    C --> I[Import scripts]
    I --> D[DB ICTServe]
    D --> V[Semakan/Audit]
```

## 4. SUMBER DATA

Sumber data migrasi adalah daripada rekod manual dan digital seperti yang dikenal pasti dalam D05, termasuk:

- Rekod manual (borang kertas, dokumen bercetak/PDF)
- Fail digital (Excel/CSV/Access DB)
- Sistem sedia ada (pangkalan data legacy, API, atau sistem pengurusan aset terdahulu)
- Direktori staf legacy (untuk validasi bahagian dan gred)

**Maklumat server/pangkalan data dan table sumber** perlu direkod berdasarkan persekitaran pelaksanaan (staging/production). Jika maklumat terperinci belum tersedia dalam bukti pelaksanaan, ia ditandakan sebagai **TBD**.

**Nota (bukti daripada repositori)**: Kod aplikasi turut mengandungi utiliti migrasi **persekitaran pembangunan** (Docker → XAMPP) termasuk sandaran pangkalan data (mysqldump) dan fail log migrasi. Ini **bukan** bukti metrik migrasi data legacy (row/ratio), tetapi boleh dijadikan rujukan mekanisme sandaran/log semasa migrasi.

| Sumber | Format | Server/Host | Nama Pangkalan Data / Lokasi Fail | Table/Entiti Sumber | Catatan |
| :--- | :--- | :--- | :--- | :--- | :--- |
| Rekod manual (tiket/pinjaman) | PDF/Kertas | TBD | TBD | TBD | Perlu proses digitasi & validasi |
| Fail digital | Excel/CSV/Access | TBD | TBD | TBD | Perlu standardisasi format (cth: tarikh ISO 8601) |
| Sistem sedia ada | DB/API legacy | TBD | TBD | TBD | Perlu kawalan akses & audit |
| Direktori staf legacy | Fail/DB | TBD | TBD | TBD | Digunakan untuk migrasi/padanan `users.email` |

## 5. DESTINASI BAHARU DATA

Destinasi baharu data ialah pangkalan data ICTServe (Laravel 12) yang menyimpan entiti utama berikut (rujuk struktur sasaran dalam D06):

- **Tiket aduan ICT**: `helpdesk_tickets`
- **Permohonan pinjaman peralatan ICT**: `loan_applications`
- **Inventori aset ICT**: `assets`
- **Profil pengguna/staf**: `users`

Selain itu, migrasi juga melibatkan data rujukan dan integriti, contohnya `divisions`, `grades`, `positions`, serta rekod audit trail yang berkaitan dengan pematuhan dan jejak perubahan.

**Maklumat server/pangkalan data destinasi** perlu direkod berdasarkan persekitaran pelaksanaan (staging/production). Berdasarkan konfigurasi contoh yang disediakan dalam repositori (rujuk `.env.example` dan `config/database.php`), destinasi menggunakan driver **MySQL (`DB_CONNECTION=mysql`)** dan nama pangkalan data **`ictserve`**. Nilai sebenar bagi produksi (hostname/port/akaun DB) tertakluk kepada tetapan `.env` persekitaran.

| Destinasi | Nama Pangkalan Data | Lokasi/Server | Catatan |
| :--- | :--- | :--- | :--- |
| DB ICTServe (Laravel 12) | `ictserve` | XAMPP (lokal): `127.0.0.1:3306` / Docker Compose: `db:3306` | Struktur table rujuk D06; konfigurasi persekitaran melalui `.env` |

## 6. JUMLAH BARIS DALAM TABLE SUMBER

Jadual berikut merekod jumlah baris bagi setiap table/entiti sumber. Nilai perlu diisi semasa pelaksanaan migrasi sebenar.

| Entiti | Table/Fail Sumber | Jumlah Baris (Row) | Catatan |
| :--- | :--- | :---: | :--- |
| Profil staf (legacy) | TBD | TBD | Digunakan untuk populasi `users` |
| Tiket helpdesk (legacy) | TBD | TBD | Rekod sejarah tiket |
| Permohonan pinjaman (legacy) | TBD | TBD | Rekod sejarah pinjaman |
| Inventori aset (legacy) | TBD | TBD | Rekod aset & status |

## 7. JUMLAH BARIS YANG BERJAYA DIMIGRASI

Jadual berikut merekod jumlah baris yang berjaya dimigrasi bagi setiap table destinasi. Nilai perlu diisi semasa pelaksanaan migrasi sebenar.

| Entiti | Table Destinasi | Jumlah Berjaya Dimigrasi (Row) | Catatan |
| :--- | :--- | :---: | :--- |
| Profil staf | `users` | TBD | Staf dimigrasi / dipadankan e-mel `@motac.gov.my` |
| Tiket helpdesk | `helpdesk_tickets` | TBD | `user_id` dipaut melalui padanan e-mel (jika ada) |
| Permohonan pinjaman | `loan_applications` | TBD | Kelulusan token & metadata kekal (rujuk D06) |
| Inventori aset | `assets` | TBD | Padanan tag/serial mengikut format baharu |

## 8. RATIO

Ratio kejayaan migrasi ditakrifkan sebagai peratusan baris yang berjaya dimigrasi berbanding jumlah baris sumber:

$$\text{Ratio} = \frac{\text{Row berjaya dimigrasi}}{\text{Row sumber}} \times 100\%$$

| Entiti | Row Sumber | Row Berjaya | Ratio | Catatan |
| :--- | :---: | :---: | :---: | :--- |
| Profil staf | TBD | TBD | TBD | Akan dikemaskini selepas pelaksanaan |
| Tiket helpdesk | TBD | TBD | TBD | Akan dikemaskini selepas pelaksanaan |
| Permohonan pinjaman | TBD | TBD | TBD | Akan dikemaskini selepas pelaksanaan |
| Inventori aset | TBD | TBD | TBD | Akan dikemaskini selepas pelaksanaan |

## 9. PERINCIAN

Seksyen ini merekod perincian migrasi yang gagal (jika ada), termasuk sebab dan tindakan pembetulan.

**Rekod kegagalan sebenar semasa pelaksanaan migrasi** (diisi selepas pelaksanaan):

| Isu/Kegagalan | Sebab | Tindakan Pembetulan | Status |
| :--- | :--- | :--- | :--- |
| TBD | TBD | TBD | TBD |

**Risiko/isu berpotensi (rujuk D05) untuk rujukan semasa pelaksanaan**:

| Isu/Kegagalan | Sebab Berkemungkinan | Tindakan Pembetulan / Mitigasi |
| :--- | :--- | :--- |
| Data rosak/kehilangan | Proses import tidak lengkap / gangguan semasa cutover | Full backup, dry run di staging, pelan rollback |
| Duplikasi / tidak konsisten | Rekod berganda dari sumber berbeza | Cleansing & deduplication, validation rules ketat |
| Kebocoran data peribadi | Kawalan akses lemah / pemindahan tidak selamat | Encryption, access control, audit trail, pematuhan PDPA |
| Foreign key constraint error | Urutan import tidak tepat / data rujukan tidak lengkap | Import ikut urutan, sediakan data rujukan (seeders), validasi FK |
| Downtime melebihi window | Skrip lambat / data besar | Rehearsal migration, batch processing/queue, optimasi skrip |

**Log pelaksanaan**: D05 mensyaratkan aktiviti migrasi direkodkan dalam log (contoh: `storage/logs/migration.log`) untuk tujuan audit dan troubleshooting. Bukti log sebenar dan ringkasan kegagalan perlu dilampirkan selepas pelaksanaan.

**Bukti log/sandaran tersedia dalam kod (migrasi persekitaran)**: `app/Services/EnvironmentMigrationService.php` membina sandaran DB menggunakan `mysqldump` dan menjana fail log JSON di `storage/backups/environment_migration/migration_log_<timestamp>.json`.

## 10. LAMPIRAN

Lampiran yang disyorkan untuk disertakan bersama laporan ini (mengikut ketersediaan semasa pelaksanaan):

- Dokumen rujukan v3.6.1: Pelan Migrasi Data (D05) (`_reference/versions/v3.6.1_D05_DATA_MIGRATION_PLAN.md`) dan Spesifikasi Migrasi Data (D06) (`_reference/versions/v3.6.1_D06_DATA_MIGRATION_SPECIFICATION.md`)
- Struktur table destinasi baharu dan kamus data (rujuk D06 untuk ringkasan medan/table sasaran)
- Bukti pelaksanaan migrasi: Log migrasi (`storage/logs/migration.log` atau setara), ringkasan hasil verifikasi (semakan rawak, semakan integriti FK), laporan ralat dan tindakan pembetulan (jika ada)

Lampiran bukti teknikal (daripada repositori) yang menyokong mekanisme sandaran/log persekitaran:

- Konfigurasi contoh DB: `.env.example` (DB_CONNECTION/DB_HOST/DB_DATABASE)
- Konfigurasi sambungan DB: `config/database.php`
- Arahan sandaran/pemulihan DB (Docker): `docker/README.md`
- Implementasi sandaran/log migrasi persekitaran: `app/Services/EnvironmentMigrationService.php`
