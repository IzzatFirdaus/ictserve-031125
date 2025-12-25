# MANUAL PENGGUNA SISTEM (PENTADBIR)

## ICTSERVE
(Sistem Pengurusan Helpdesk & Pinjaman Aset ICT)

![Logo Agensi](../public/images/motac-logo.png)

| Medan                 | Nilai                                            |
| --------------------- | ------------------------------------------------ |
| **NAMA AGENSI**       | : Bahagian Pengurusan Maklumat (BPM), MOTAC      |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia |
| **TARIKH DOKUMEN**    | : 15 Disember 2025                               |
| **VERSI DOKUMEN**     | : 3.7.0 (Cloud Hybrid AI & True Hybrid Access)   |

---

## i. Keterangan Dokumen

Manual ini adalah panduan rasmi **Pentadbir Sistem (Admin/Superuser)** untuk ICTServe versi 3.7.0. Ia menerangkan operasi harian menggunakan panel pentadbiran Filament, meliputi pengurusan tiket aduan, pinjaman aset, inventori, peranan pengguna, automasi AI hibrid (Ollama + Bedrock), laporan, serta pemantauan prestasi.

---

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
|--------------|---------|-------------|----------------|
|              |         |             |                |
|              |         |             |                |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
|---------------|---------|-------------|-------------------|
|               |         |             |                   |
|               |         |             |                   |

---

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh            | Ringkasan Pindaan                                                                 | Penyedia                |
| --------- | ----------------- | --------------------------------------------------------------------------------- | ----------------------- |
| 3.7.0     | 15 Disember 2025  | Kemaskini AI Chatbot & Auto-Reply, penyelarasan True Hybrid & Cloud Hybrid AI.    | Pasukan Pembangunan BPM |
| 3.6.1     | 15 Disember 2025  | Kemaskini manual pentadbir selaras dengan versi sistem 3.6.1 (Hybrid AI & Access).| Pasukan Pembangunan BPM |
| 3.0.0     | 31 Oktober 2025   | Versi awal manual pentadbir untuk sistem ICTServe v3.0.                           | Pasukan Pembangunan BPM |

---

## iv. Kandungan

- i. Keterangan Dokumen
- ii. Semakan dan Pengesahan Dokumen
- iii. Kawalan Dokumen
- iv. Kandungan
- v. Senarai Gambarajah
- vi. Senarai Jadual
- vii. Definisi dan Akronim
- viii. Sumber Rujukan

1. **PENGENALAN**
   - 1.1 Tujuan Manual
   - 1.2 Skop Manual
   - 1.3 Sasaran Pengguna
   - 1.4 Gambaran Keseluruhan Sistem

2. **KEPERLUAN SISTEM**
   - 2.1 Keperluan Perkakasan
   - 2.2 Keperluan Perisian
   - 2.3 Keperluan Rangkaian
   - 2.4 Keperluan Keselamatan

3. **AKSES DAN KONFIGURASI**
   - 3.1 Log Masuk Pentadbir
   - 3.2 Dashboard Pentadbir

4. **PENGURUSAN PENGGUNA**
   - 4.1 Senarai Pengguna
   - 4.2 Tambah/Kemaskini Pengguna
   - 4.3 Pengurusan Peranan (Roles)

5. **PENGURUSAN HELPDESK**
   - 5.1 Senarai Tiket
   - 5.2 Kemaskini Status Tiket
   - 5.3 Konfigurasi Kategori & SLA

6. **PENGURUSAN ASET & PINJAMAN**
   - 6.1 Inventori Aset
   - 6.2 Pengurusan Permohonan Pinjaman
   - 6.3 Proses Serahan & Pemulangan (Check-in/Check-out)

7. **MODUL AI & AUTOMASI**
   - 7.1 Konfigurasi Chatbot FAQ
   - 7.2 Templat Auto-Reply

8. **LAPORAN DAN ANALISIS**
   - 8.1 Penjanaan Laporan
   - 8.2 Pemantauan Prestasi (Pulse)

9. **PENYELENGGARAAN DAN SOKONGAN**
   - 9.1 Log Audit
   - 9.2 Hubungi Sokongan Teknikal

10. **PENYELESAIAN MASALAH**
    - 10.1 Masalah Lazim

11. **LAMPIRAN**

---

## v. Senarai Gambarajah

*(Senarai gambarajah akan dikemaskini setelah tangkapan skrin dimasukkan)*

---

## vi. Senarai Jadual

*(Senarai jadual dijana secara automatik berdasarkan kandungan dokumen)*

---

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan                                      |
| ------- | ----------------------------------------------- |
| BPM     | Bahagian Pengurusan Maklumat                    |
| MOTAC   | Kementerian Pelancongan, Seni dan Budaya        |
| SLA     | Service Level Agreement (Perjanjian Tahap Perkhidmatan) |
| AI      | Artificial Intelligence (Kecerdasan Buatan)     |
| OTP     | One-Time Password                               |
| RBAC    | Role-Based Access Control                       |
| 2FA     | Two-Factor Authentication                       |

### b. Definisi

| Terma/Istilah           | Definisi                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| **Filament Panel**      | Antara muka pentadbiran (Filament v4) yang digunakan oleh Admin/Superuser untuk menguruskan sistem.                        |
| **Superuser**           | Peranan pentadbir dengan akses penuh termasuk konfigurasi sistem, audit, Pulse, dan Telescope.                             |
| **Admin**               | Peranan pentadbir operasi harian (tiket, aset, pengguna).                                                                  |
| **Hybrid Access**       | Model akses yang membenarkan staf menggunakan sistem sebagai tetamu atau pengguna berdaftar.                               |
| **Dual Audit**          | Gabungan log aktiviti dan log audit bagi mematuhi integriti data.                                                          |

---

## viii. Sumber Rujukan

1. D03_SPESIFIKASI_KEPERLUAN_SISTEM_SRS_ICTSERVE.md (v3.7.0)
2. D18_AI_CHATBOT_OLLAMA_BEDROCK.md (v1.0.0)
3. ADMINISTRATOR_GUIDE.md (v3.0.0)

---

## 1. PENGENALAN

### 1.1 Tujuan Manual

Memberi panduan operasi kepada **Pentadbir Sistem (Admin/Superuser)** untuk mengurus ICTServe v3.7.0, termasuk konfigurasi Filament, pengurusan tiket dan aset, automasi AI, laporan, dan pemantauan prestasi.

### 1.2 Skop Manual

- Akses ke Panel Pentadbiran Filament.
- Pengurusan kitaran hayat tiket Helpdesk dan SLA.
- Pengurusan inventori aset, proses pinjaman, dan aksesori.
- Pengurusan akaun pengguna, peranan, dan keselamatan (RBAC/2FA).
- Konfigurasi modul AI dan auto-reply (Ollama + Bedrock).
- Penjanaan laporan dan audit (Dual Audit, Pulse).

### 1.3 Sasaran Pengguna

| Kategori Pengguna | Keterangan |
| ----------------- | ---------- |
| **Superuser**     | Pegawai BPM dengan akses penuh konfigurasi sistem, audit log, integrasi, Pulse/Telescope. |
| **Admin**         | Pegawai BPM yang menguruskan operasi harian (tiket, aset, pinjaman, AI draf). |

### 1.4 Gambaran Keseluruhan Sistem

ICTServe ialah sistem bersepadu Helpdesk dan Pinjaman Aset dengan arkitektur **True Hybrid** (Auth + Guest). Panel pentadbiran Filament v4 menyediakan dashboard masa nyata, widget analitik, dan borang pengurusan data. AI Hibrid (Ollama + AWS Bedrock) menyokong draf auto-reply dan chatbot.

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Perkakasan

- **Pemproses**: Intel Core i5 / AMD Ryzen 5 atau setara.
- **RAM**: Minimum 8GB.
- **Paparan**: Resolusi 1920x1080 disarankan.
- **Pengimbas Kod QR**: Untuk proses serahan/pemulangan aset.

### 2.2 Keperluan Perisian

| Perisian | Keperluan |
| -------- | --------- |
| Pelayar Web | Google Chrome / Microsoft Edge / Firefox (terkini) |
| PDF Reader | Untuk melihat laporan eksport / dokumen sokongan |

### 2.3 Keperluan Rangkaian

- Sambungan Intranet MOTAC atau Internet stabil (untuk akses Cloud AI).

### 2.4 Keperluan Keselamatan

- Akaun `admin/superuser` sahaja dibenarkan ke URL pentadbir.
- 2FA disyorkan bagi akaun berisiko tinggi.
- Data sensitif hendaklah diproses di Ollama (on-prem) sebelum dihantar ke Bedrock.

---

## 3. AKSES DAN KONFIGURASI

### 3.1 Log Masuk Pentadbir

1. Buka pelayar web dan layari `https://ictserve.motac.gov.my/admin`.
2. Masukkan e-mel rasmi `@motac.gov.my` dan kata laluan.
3. *(Jika diaktifkan)* Masukkan kod 2FA.
4. Klik **Log Masuk**.

### 3.2 Dashboard Pentadbir

- Paparan widget: Statistik tiket (Terbuka/Dalam Proses/Selesai), Status aset (On Loan/Late), Pematuhan SLA.
- Widget **Laravel Pulse**: Beban pelayan, status baris gilir (Queue Jobs), dan kesihatan sistem.

---

## 4. PENGURUSAN PENGGUNA

### 4.1 Senarai Pengguna

- Navigasi ke menu **Pengguna** untuk melihat senarai akaun.
- Cari, tapis peranan, dan semak status aktif.

### 4.2 Tambah/Kemaskini Pengguna

1. Klik **Tambah Pengguna**.
2. Isi nama, e-mel rasmi, peranan, dan status.
3. Simpan untuk menjana akaun baharu.
4. Untuk kemas kini, pilih pengguna dan klik **Edit**.

### 4.3 Pengurusan Peranan (Roles)

- **Admin**: Mengurus tiket, aset, pinjaman, dan AI draf.
- **Superuser**: Semua akses Admin + konfigurasi sistem, audit, Pulse/Telescope.
- Peranan diurus melalui RBAC; hadkan akses mengikut prinsip *least privilege*.

---

## 5. PENGURUSAN HELPDESK

### 5.1 Senarai Tiket

- Menu **Tiket Aduan** memaparkan senarai tiket dengan penapis status.

### 5.2 Kemaskini Status Tiket

1. Buka tiket berstatus `OPEN` atau `IN_PROGRESS`.
2. **Assign** kepada staf teknikal.
3. Tambah catatan dalaman atau balasan pengguna.
4. Tukar status kepada `IN_PROGRESS`, `AWAITING_INFO`, atau `RESOLVED` apabila selesai.

### 5.3 Konfigurasi Kategori & SLA

- Tetapkan kategori masalah, tahap keutamaan, dan tempoh SLA di modul konfigurasi.

---

## 6. PENGURUSAN ASET & PINJAMAN

### 6.1 Inventori Aset

1. Klik **Inventori Aset**.
2. Gunakan **Tambah Aset Baru** untuk mendaftar peralatan dan jana label QR.

### 6.2 Pengurusan Permohonan Pinjaman

- Semak permohonan berstatus `PENDING` atau `APPROVED`.
- Lihat **Pegawai Bertanggungjawab** dan aksesori yang terlibat.

### 6.3 Proses Serahan & Pemulangan (Check-in/Check-out)

**Check-Out**
1. Buka permohonan berstatus `APPROVED`.
2. Klik **Check-Out** dan imbas kod QR aset.
3. Tandakan aksesori (Beg, Tetikus, Kabel) yang diserahkan.
4. Sahkan nama Pegawai Bertanggungjawab jika berbeza dari pemohon.
5. Simpan untuk ubah status kepada `ON_LOAN`.

**Check-In**
1. Cari permohonan pinjaman dan klik **Check-In**.
2. Semak keadaan aset dan aksesori. Jika tidak lengkap atau rosak, sistem merekod percanggahan dan boleh menjana tiket aduan.
3. Klik **Selesai** untuk menutup pinjaman.

---

## 7. MODUL AI & AUTOMASI

### 7.1 Konfigurasi Chatbot FAQ

- AI Hibrid menggunakan Ollama (on-prem) untuk data sensitif dan AWS Bedrock (Claude) untuk analisis kompleks.
- Pastikan dasar data: maklumat terperingkat tidak dihantar ke cloud.

### 7.2 Templat Auto-Reply

1. Terima notifikasi **"Auto-Reply Draft Available"** untuk tiket lazim.
2. Semak draf; klik **Approve & Send** atau **Edit** sebelum hantar kepada pengguna.
3. Pantau keberkesanan respons melalui maklum balas pengguna.

---

## 8. LAPORAN DAN ANALISIS

### 8.1 Penjanaan Laporan

- Menu **Laporan** membenarkan eksport PDF/Excel untuk Statistik Tiket Bulanan atau inventori.
- Tetapkan julat tarikh sebelum eksport.

### 8.2 Pemantauan Prestasi (Pulse)

- Gunakan widget **Laravel Pulse** untuk memantau beban pelayan dan Queue.
- Semak **Log Aktiviti** dan **Log Audit (Dual Audit)** untuk pematuhan integriti data.

---

## 9. PENYELENGGARAAN DAN SOKONGAN

### 9.1 Log Audit

- Semak **Log Aktiviti** dan **Log Audit** untuk menjejak perubahan data.

### 9.2 Hubungi Sokongan Teknikal

- Lapor isu kritikal kepada pasukan teknikal BPM; sertakan tangkapan skrin Pulse/Telescope jika berkaitan.

---

## 10. PENYELESAIAN MASALAH

**Masalah**: E-mel kelulusan tidak diterima.
**Penyelesaian**: Semak log e-mel/Queue; jika "Failed" jalankan **Retry**. Minta pengguna semak folder Spam.

**Masalah**: AI memberikan jawapan salah.
**Penyelesaian**: Kemaskini FAQ/knowledge base dan semak draf sebelum diluluskan.

**Masalah**: Sistem lambat.
**Penyelesaian**: Semak Pulse untuk beban CPU/memori dan baris gilir; laporkan kepada pasukan pelayan.

---

## 11. LAMPIRAN

*(Ruangan ini boleh diisi dengan templat e-mel rasmi dan senarai kod rujukan borang)*
