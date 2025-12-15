# MANUAL PENGGUNA SISTEM (PENTADBIR)

## ICTSERVE
(Sistem Pengurusan Helpdesk & Pinjaman Aset ICT)

![Logo Agensi](../public/images/motac-logo.png)

| Medan                 | Nilai                                            |
| --------------------- | ------------------------------------------------ |
| **NAMA AGENSI**       | : Bahagian Pengurusan Maklumat (BPM), MOTAC      |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia |
| **TARIKH DOKUMEN**    | : 15 Disember 2025                               |
| **VERSI DOKUMEN**     | : 3.6.1                                          |

---

## i. Keterangan Dokumen

Dokumen ini adalah Manual Pengguna Sistem khusus untuk **Pentadbir Sistem (Admin)** bagi sistem ICTServe. Ia menyediakan panduan lengkap mengenai pengurusan tiket aduan, inventori aset, permohonan pinjaman, pengurusan pengguna, serta konfigurasi sistem melalui panel pentadbiran Filament.

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

### b. Definisi

| Terma/Istilah           | Definisi                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| **Filament Panel**      | Antara muka pentadbiran yang digunakan oleh Admin untuk menguruskan sistem.                                                |
| **Superuser**           | Peranan pentadbir dengan akses penuh termasuk konfigurasi sistem dan log audit.                                            |
| **Admin**               | Peranan pentadbir operasi harian (tiket, aset, pengguna).                                                                  |
| **Hybrid Access**       | Model akses yang membenarkan staf menggunakan sistem sebagai tetamu atau pengguna berdaftar.                               |

---

## viii. Sumber Rujukan

1. D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md (v3.6.1)
2. ADMINISTRATOR_GUIDE.md (v3.0.0)
3. D18_AI_CHATBOT_OLLAMA_BEDROCK.md (v1.0.0)

---

## 1. PENGENALAN

### 1.1 Tujuan Manual

Manual ini bertujuan untuk menyediakan panduan teknikal dan operasi kepada Pentadbir Sistem (Admin dan Superuser) dalam menguruskan sistem ICTServe. Ia merangkumi langkah-langkah konfigurasi, pengurusan data, pemantauan prestasi, dan penyelesaian masalah.

### 1.2 Skop Manual

Manual ini merangkumi:

- Akses ke Panel Pentadbiran Filament.
- Pengurusan kitaran hayat tiket Helpdesk dan SLA.
- Pengurusan inventori aset dan proses pinjaman.
- Pengurusan akaun pengguna dan peranan.
- Konfigurasi modul AI dan automasi.
- Penjanaan laporan dan audit.

### 1.3 Sasaran Pengguna

| Kategori Pengguna | Keterangan |
| ----------------- | ---------- |
| **Superuser**     | Pegawai BPM dengan akses penuh konfigurasi sistem, audit log, dan integrasi. |
| **Admin**         | Pegawai BPM yang menguruskan operasi harian (tiket, aset, pinjaman). |

### 1.4 Gambaran Keseluruhan Sistem

ICTServe adalah sistem bersepadu yang menggabungkan pengurusan Helpdesk dan Pinjaman Aset. Panel pentadbiran dibina menggunakan **Filament 4** yang menyediakan papan pemuka (dashboard) masa nyata, widget analitik, dan borang pengurusan data yang intuitif.

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Perkakasan

Pentadbir disarankan menggunakan komputer riba atau desktop dengan spesifikasi berikut untuk prestasi optimum panel pentadbiran:

- **Pemproses**: Intel Core i5 / AMD Ryzen 5 atau setara.
- **RAM**: Minimum 8GB.
- **Paparan**: Resolusi 1920x1080 disarankan.

### 2.2 Keperluan Perisian

| Perisian | Keperluan |
| -------- | --------- |
| Pelayar Web | Google Chrome, Microsoft Edge, atau Mozilla Firefox (Versi terkini). |
| Sambungan | Akses ke Intranet MOTAC (atau VPN jika akses luar dibenarkan). |

---

## 3. AKSES DAN KONFIGURASI

### 3.1 Log Masuk Pentadbir

1. Layari URL Panel Pentadbir: **<https://ictserve.motac.gov.my/admin>**
2. Masukkan **E-mel** dan **Kata Laluan** pentadbir anda.
3. Klik **Log Masuk**.
4. (Jika diaktifkan) Masukkan kod 2FA anda.

### 3.2 Dashboard Pentadbir

Setelah log masuk, anda akan melihat Dashboard utama yang memaparkan widget berikut:

- **Statistik Tiket**: Jumlah tiket Baru, Dalam Proses, dan Selesai.
- **Statistik Pinjaman**: Permohonan Menunggu Kelulusan, Aset Keluar, Aset Lewat.
- **Pematuhan SLA**: Graf prestasi masa tindak balas tiket.
- **Aktiviti Terkini**: Log ringkas tindakan pengguna dalam sistem.

---

## 4. PENGURUSAN PENGGUNA

### 4.1 Senarai Pengguna

1. Di menu sisi, klik **Pengguna** (Users).
2. Anda boleh melihat senarai semua pengguna berdaftar (Staf, Admin, Superuser).
3. Gunakan fungsi **Carian** untuk mencari pengguna berdasarkan nama atau e-mel.

### 4.2 Tambah/Kemaskini Pengguna

**Menambah Pengguna Baru:**

1. Klik butang **Cipta Pengguna** (Create User).
2. Isi maklumat: Nama, E-mel, Bahagian, Gred.
3. Tetapkan **Peranan** (Role):
   - `Staff`: Pengguna biasa.
   - `Admin`: Pengurus operasi.
   - `Superuser`: Pentadbir sistem penuh.
4. Klik **Cipta**.

**Mengemaskini Pengguna:**

1. Klik pada nama pengguna dalam senarai.
2. Kemaskini maklumat yang diperlukan.
3. Klik **Simpan**.

### 4.3 Pengurusan Peranan (Roles)

Hanya **Superuser** boleh mengubah konfigurasi peranan dan kebenaran (permissions) melalui menu **Roles & Permissions** (jika diaktifkan dalam UI) atau melalui konfigurasi kod.

---

## 5. PENGURUSAN HELPDESK

### 5.1 Senarai Tiket

1. Klik menu **Tiket Helpdesk**.
2. Tiket dipaparkan mengikut status. Anda boleh menapis (filter) mengikut:
   - Status (Baru, Dalam Proses, Selesai).
   - Kategori (Perkakasan, Perisian, dll).
   - Tarikh.

### 5.2 Kemaskini Status Tiket

1. Klik pada tiket untuk melihat butiran.
2. Anda boleh:
   - **Ubah Status**: Tukar dari 'Baru' ke 'Dalam Proses' atau 'Selesai'.
   - **Assign**: Tugaskan tiket kepada staf teknikal tertentu.
   - **Komen**: Tambah nota dalaman atau balasan kepada pengguna.
3. Klik **Simpan** untuk mengemaskini. Pengguna akan menerima notifikasi e-mel secara automatik.

### 5.3 Konfigurasi Kategori & SLA

1. Klik menu **Tetapan > Kategori Tiket**.
2. Anda boleh menambah kategori baru atau mengedit kategori sedia ada.
3. Tetapkan masa sasaran SLA (contoh: 4 jam untuk Kritikal) bagi setiap kategori.

---

## 6. PENGURUSAN ASET & PINJAMAN

### 6.1 Inventori Aset

1. Klik menu **Aset**.
2. **Tambah Aset**: Klik 'Cipta Aset', isi maklumat (Tag Aset, Nama, Kategori, Lokasi, Status).
3. **Status Aset**:
   - `Available`: Boleh dipinjam.
   - `In Use`: Sedang dipinjam.
   - `Maintenance`: Sedang diselenggara.
   - `Damaged`: Rosak.

### 6.2 Pengurusan Permohonan Pinjaman

1. Klik menu **Permohonan Pinjaman**.
2. Lihat permohonan dengan status `Pending Approval`.
3. Walaupun kelulusan biasanya dibuat oleh Pegawai Gred 41+, Admin boleh membuat **Override Approval** jika perlu.

### 6.3 Proses Serahan & Pemulangan (Check-in/Check-out)

**Serahan Aset (Check-out):**

1. Apabila pengguna datang mengambil aset, minta **OTP** mereka.
2. Buka permohonan berkaitan, klik butang **Sahkan Serahan** (Verify Handover).
3. Masukkan OTP. Status bertukar kepada `Active`.

**Pemulangan Aset (Check-in):**

1. Buka permohonan yang statusnya `Active` atau `Overdue`.
2. Klik **Proses Pemulangan**.
3. Semak kondisi aset. Pilih status: `Good` atau `Damaged`.
   - Jika `Damaged`, sistem akan automatik membuka tiket Helpdesk untuk aset tersebut.
4. Sahkan pemulangan. Aset kembali ke status `Available` (jika Good).

---

## 7. MODUL AI & AUTOMASI

### 7.1 Konfigurasi Chatbot FAQ

1. Klik menu **Pengurusan AI > FAQ**.
2. Tambah soalan lazim dan jawapan untuk melatih Chatbot (Ollama).
3. Soalan ini akan digunakan oleh bot untuk menjawab pertanyaan pengguna secara automatik.

### 7.2 Templat Auto-Reply

1. Klik menu **Pengurusan AI > Templat Auto-Reply**.
2. Cipta templat jawapan untuk kategori tiket tertentu.
3. Apabila tiket baru masuk, Admin boleh memilih untuk menggunakan draf jawapan yang dijana AI berdasarkan templat ini.

---

## 8. LAPORAN DAN ANALISIS

### 8.1 Penjanaan Laporan

1. Klik menu **Laporan**.
2. Pilih jenis laporan:
   - Statistik Tiket Bulanan.
   - Penggunaan Aset.
   - Pematuhan SLA.
3. Pilih julat tarikh.
4. Klik **Jana** atau **Eksport** (PDF/Excel).

### 8.2 Pemantauan Prestasi (Pulse)

1. Klik menu **Sistem > Pulse** (atau layari `/pulse`).
2. Dashboard ini memaparkan kesihatan teknikal sistem:
   - Beban pelayan (CPU/RAM).
   - Barisan kerja (Queue) yang perlahan.
   - Pertanyaan pangkalan data (Database Queries) yang lambat.

---

## 9. PENYELENGGARAAN DAN SOKONGAN

### 9.1 Log Audit

1. Klik menu **Sistem > Log Audit**.
2. Anda boleh melihat jejak audit terperinci:
   - Siapa yang log masuk.
   - Siapa yang mengubah status tiket/aset.
   - Perubahan data (nilai lama vs nilai baru).
3. Log ini penting untuk pematuhan keselamatan dan siasatan insiden.

### 9.2 Hubungi Sokongan Teknikal

Jika terdapat isu sistem yang tidak dapat diselesaikan:

- **E-mel**: <dev.team@ictserve.motac.gov.my>
- **Tiket JIRA**: [Pautan ke JIRA Projek]

---

## 10. PENYELESAIAN MASALAH

### 10.1 Masalah Lazim

**Masalah: E-mel notifikasi tidak dihantar.**

- **Penyelesaian**: Semak menu **Sistem > Pulse > Queues**. Pastikan 'worker' sedang berjalan. Jika terdapat 'failed jobs', cuba 'retry' melalui menu Failed Jobs.

**Masalah: Aset tidak boleh ditempah walaupun ada di pejabat.**

- **Penyelesaian**: Semak status aset dalam **Inventori Aset**. Pastikan status adalah `Available`. Jika status `Maintenance` atau `In Use` (tetapi aset ada), kemaskini status secara manual.

**Masalah: Pengguna tidak boleh log masuk.**

- **Penyelesaian**: Semak status pengguna di **Senarai Pengguna**. Pastikan akaun 'Aktif'. Jika pengguna terlupa kata laluan, minta mereka guna fungsi 'Lupa Kata Laluan' di halaman log masuk.

---

## 11. LAMPIRAN

### Lampiran A: Senarai Status Tiket

| Status | Keterangan |
| ------ | ---------- |
| **Open** | Tiket baru diterima. |
| **In Progress** | Sedang disiasat/dibaiki. |
| **Awaiting Info** | Menunggu maklum balas pengguna. |
| **Resolved** | Masalah selesai, menunggu pengesahan pengguna. |
| **Closed** | Tiket ditutup sepenuhnya. |

### Lampiran B: Senarai Status Aset

| Status | Keterangan |
| ------ | ---------- |
| **Available** | Sedia untuk dipinjam. |
| **Reserved** | Telah ditempah untuk tarikh akan datang. |
| **In Use** | Sedang dipinjam oleh pengguna. |
| **Maintenance** | Sedang diselenggara/rosak. |
| **Retired** | Aset dilupuskan. |

---

**NOTA PENTING:**

1. Manual ini disediakan mengikut piawaian KRISA.
2. Tangkapan skrin dan gambarajah mungkin berbeza sedikit mengikut kemaskini sistem semasa.
3. Sila laporkan sebarang kesilapan dalam manual ini kepada Pasukan Pembangunan BPM.

---

**Tarikh Terakhir Dikemaskini:** 15 Disember 2025
**Versi:** 3.6.1
**Status:** Aktif
