# Pelan Terperinci: Gambarajah Aliran “Manual → Sistem” (ICTServe v3.6.1)

**Versi dokumen**: 1.0 (dihasilkan melalui rujukan dokumentasi v3.6.1)

## 1) Objektif
Menghasilkan gambarajah aliran yang memetakan **prosedur/manual (cara pengguna mengisi borang & langkah operasi)** kepada **tindak balas sistem ICTServe** (UI laluan, validasi, penyimpanan data, notifikasi, pemantauan operasi), dengan fokus kepada:

- **Helpdesk (Tetamu)**: cipta tiket & jejak tiket.
- **Pinjaman Aset ICT (Tetamu)**: permohonan wizard & jejak permohonan.
- **Semakan Status Bersepadu**: semakan menggunakan token.
- **Pentadbir/Operasi**: akses Filament `/admin` dan pemantauan queue `/horizon`.

## 2) Skop & Prinsip “Manual → Sistem”
### 2.1. Apa maksud “Manual → Sistem” dalam konteks ini
Dalam ICTServe, “manual → sistem” bermaksud:

- **Input manual (borang/rujukan prosedur)** → dipetakan menjadi **borang web & aliran UI**.
- **Serahan/kelulusan/manual sign-off** → dipetakan menjadi **rekod transaksi, token rujukan, notifikasi (email/notification), dan tindakan staf/pentadbir**.
- **Jejak status secara telefon/bertanya** → dipetakan menjadi **semakan status melalui nombor tiket / nombor permohonan / token**.

### 2.2. Had skop (untuk elak andaian berlebihan)

- Dokumen D03 dalam repo ini ialah ringkasan SRS; ia menyatakan modul & fungsi secara umum (Helpdesk/Pinjaman/Pentadbir) tanpa butiran medan.
- Manual pengguna/pentadbir (D17) menyatakan **laluan (routes)** dan langkah asas. Butiran medan borang Helpdesk/Pinjaman tidak dihuraikan penuh dalam D17, jadi kita gunakan **dua fail pecahan borang** dalam `_reference` sebagai “jambatan” manual→sistem.
- Kandungan PDF flowchart di `_reference` dirujuk sebagai bukti kewujudan artefak manual, tetapi tidak diekstrak semantik (tiada OCR) dalam pelan ini.

## 3) Sumber Rujukan (v3.6.1)
### 3.1. Dokumen kanonik (root)

- `D03_DOKUMEN_SPESIFIKASI_KEPERLUAN_SISTEM_SRS_ictserve_3.6.1.md`
  - Menetapkan skop modul: Helpdesk/Pinjaman/Pentadbir dan fungsi aras tinggi (Cipta Tiket, Kelulusan, Pulangan, Audit).
- `D17_DOKUMEN_MANUAL_PENGGUNA_SISTEM_ictserve_3.6.1.md`
  - Menetapkan laluan tetamu: `/helpdesk/create`, `/helpdesk/submit`, `/helpdesk/track/{ticketNumber?}`, `/loan/create`, `/loan/tracking/{applicationNumber?}`, `/status` atau `/status/{token}`, `/ai/faq`.
  - Menyatakan output kejayaan (contoh): `/helpdesk/success`, `/loan/success`.
  - Menyatakan kewujudan modul staf: `/staff/dashboard`, `/staff/tickets`, `/staff/loans`.
- `D17_ADMIN_DOKUMEN_MANUAL_PENTADBIR_SISTEM_ictserve_3.6.1.md`
  - Menetapkan akses admin: `/admin`.
  - Menetapkan pemantauan queue: `/horizon` dengan gate `viewHorizon`.

### 3.2. Dokumen induk (versi rujukan)

- `_reference/versions/v3.6.1_ICTServe_System_Documentation.md`
  - Menetapkan modul: Pinjaman Aset ICT, Meja Bantuan, Panel Pentadbir (Filament), Portal Staf.
  - Menetapkan komponen operasi: Queue + Horizon, Reverb (real-time).
- `_reference/versions/v3.6.1_D17_QUEUE_MANAGEMENT_HORIZON.md`
  - Menetapkan peranan queue/worker dan contoh job berkaitan (notifikasi tiket/pinjaman, kelulusan via email).

### 3.3. Artefak manual (borang)

- `_reference/ICT DAMAGE COMPLAINT FORM (ServiceDesk ICT) - DETAILED BREAKDOWN.txt`
  - Butiran medan borang “Borang Aduan Kerosakan ICT” (Nama Penuh, Bahagian, E-mel, No Telefon, Jenis Kerosakan, Maklumat Kerosakan, Perakuan, dll.).
- `_reference/ICT EQUIPMENT LOAN APPLICATION FORM - DETAILED BREAKDOWN.txt`
  - Butiran medan & peranan borang pinjaman aset (pemohon, pegawai bertanggungjawab, sokongan gred 41+, proses keluaran/pulangan BPM).

### 3.4. Artefak manual (PDF)

- `_reference/Flowchart-Helpdesk_CamScanner 10-09-2025 10.22.pdf` (rujukan visual flowchart Helpdesk; kandungan tidak di-OCR dalam pelan ini)

## 4) Kaedah Pemetaan (Mapping Method)
Kita petakan manual→sistem melalui 5 lapisan konsisten:

1. **Aktor manual**: Tetamu / Staf / Admin / BPM (operasi).
2. **Langkah manual**: isi borang, hantar, dapat nombor rujukan, semak status, sokongan/kelulusan, ambil/pulang aset.
3. **Laluan & skrin sistem**: route seperti `/helpdesk/create`, `/loan/create`, dsb.
4. **Proses backend**: validasi, tulis DB, jana ID/Token, dispatch job queue.
5. **Output sistem**: paparan kejayaan, nombor tiket/permohonan, notifikasi, status tracking, audit/pemantauan.

## 5) Perincian Pemetaan Fungsi Utama
### 5.1. Helpdesk (Tetamu): Cipta Tiket
**Manual (rujukan borang kerosakan)** → **Sistem**

- Manual: pengguna isi borang “Borang Aduan Kerosakan ICT” (medan yang wajib & perakuan).
- Sistem:
  - UI: `/helpdesk/create` atau `/helpdesk/submit`.
  - Input: medan yang sepadan dengan borang manual (Nama Penuh, Bahagian, Gred Jawatan (opsyen), E-mel, No Telefon, Jenis Kerosakan, Maklumat Kerosakan, Perakuan).
  - Backend: validasi + simpan tiket ke DB + jana nombor tiket.
  - Output: redirect ke `/helpdesk/success` dan papar nombor tiket.
  - Operasi: dispatch notifikasi tiket melalui queue (rujuk D17 queue doc).

### 5.2. Helpdesk (Tetamu): Jejak Tiket

- UI: `/helpdesk/track/{ticketNumber?}`.
- Backend: semak tiket dalam DB (found/not found).
- Output: paparan status tiket (atau mesej ralat jika nombor tidak sah).

### 5.3. Pinjaman Aset ICT (Tetamu): Permohonan Wizard
**Manual (rujukan borang pinjaman)** → **Sistem**

- Manual: pemohon isi butiran permohonan, senarai peralatan, pengesahan, sokongan (Gred 41+), proses keluaran/pulangan BPM.
- Sistem:
  - UI: `/loan/create` (wizard 3 langkah), alias `/loan/apply`, serta `/loan/create-legacy`.
  - Input: butiran pemohon, pegawai bertanggungjawab, jadual peralatan, tarikh pinjam/pulang, lokasi, tujuan.
  - Backend: validasi + simpan permohonan + jana nombor permohonan/token.
  - Opsyen kelulusan: jika memerlukan sokongan/kelulusan, sistem hantar permintaan kelulusan melalui email/job queue.
  - Output: paparan kejayaan (contoh `/loan/success`) dan papar nombor permohonan/token.

### 5.4. Pinjaman Aset ICT: Jejak Permohonan

- UI: `/loan/tracking/{applicationNumber?}`.
- Backend: semak permohonan dalam DB.
- Output: status permohonan (atau mesej ralat jika nombor tidak sah).

### 5.5. Semakan Status Bersepadu (Token)

- UI: `/status` atau `/status/{token}`.
- Backend: token dipadankan kepada rekod (tiket atau permohonan pinjaman).
- Output: paparan ringkas status semasa + maklumat rujukan.

### 5.6. Pentadbir & Operasi

- `/admin` (Filament): pengurusan data dan konfigurasi.
- `/horizon` (Laravel Horizon): pemantauan queue, failed jobs, retry.
  - Gate `viewHorizon`: local allow; non-local admin/superuser.

## 6) Struktur Deliverable (Fail & Kandungan)
Konsisten dengan pola dokumentasi sedia ada:

1. `MANUAL_TO_SYSTEM_FLOW_PLAN.md` (fail ini)
   - Skop, rujukan, kaedah pemetaan, senarai aliran.
2. `MANUAL_TO_SYSTEM_FLOW_DIAGRAMS.md`
   - Set diagram Mermaid:
     - Gambaran keseluruhan manual→sistem.
     - Helpdesk: cipta tiket (sequence).
     - Helpdesk: jejak tiket (flowchart).
     - Pinjaman: permohonan wizard (sequence).
     - Pinjaman: jejak permohonan (flowchart).
     - Status token (flowchart).
     - Admin/ops (flowchart) termasuk `/admin` dan `/horizon`.

## 7) Konvensyen Mermaid (Untuk elak isu rendering)

- Elakkan aksara “aneh/tersembunyi” dalam label (contoh: copy-paste dari PDF).
- Guna teks ringkas dan baris baharu `\n` jika perlu.
- Untuk laluan, gunakan format literal (contoh: `/helpdesk/submit`) dan elakkan gabungan simbol tidak perlu.

## 8) Senarai Semak Validasi

- [ ] Semua laluan (routes) yang disebut wujud dalam D17 (manual pengguna/pentadbir).
- [ ] Medan borang Helpdesk & Pinjaman dipetakan dari `_reference` breakdown (jelas dinyatakan sebagai rujukan manual).
- [ ] Tiada andaian teknikal yang melangkaui dokumen (contoh: status SLA terperinci) kecuali dinyatakan sebagai opsyen/TBD.
- [ ] Mermaid boleh dirender: tiada karakter pelik, tiada label terlalu panjang.
- [ ] Bahasa: Bahasa Melayu.
