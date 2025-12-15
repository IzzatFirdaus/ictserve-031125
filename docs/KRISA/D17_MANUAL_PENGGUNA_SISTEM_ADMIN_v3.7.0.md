# MANUAL PENGGUNA SISTEM (PENTADBIR)

## SISTEM ICTSERVE

**(Modul Pentadbiran & Operasi Back-Office)**

| Medan | Nilai |
| :--- | :--- |
| **NAMA AGENSI** | Bahagian Pengurusan Maklumat (BPM) |
| **NAMA AGENSI INDUK** | Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **TARIKH DOKUMEN** | 15 Disember 2025 |
| **VERSI DOKUMEN** | 3.7.0 |

-----

## i. Keterangan Dokumen

Dokumen ini adalah Manual Pengguna bagi Sistem ICTServe versi 3.7.0, khusus untuk peranan **Pentadbir Sistem (Admin)**. Ia menyediakan panduan lengkap bagi menguruskan operasi harian sistem termasuk pengurusan tiket aduan, pemprosesan pinjaman aset ICT, pengurusan inventori, serta pemantauan prestasi sistem melalui panel pentadbiran.

-----

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| [Nama Pegawai] | Ketua Pasukan Pembangunan | | 15-12-2025 |
| [Nama Pegawai] | Pengurus Projek ICT | | 15-12-2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
| :--- | :--- | :--- | :--- |
| [Nama Pegawai] | Pengarah BPM | | |

-----

## iii. Kawalan Dokumen

Seksyen ini adalah ruangan untuk mencatatkan maklumat-maklumat penyediaan dokumen termasuk maklumat pindaan yang telah dilakukan ke atas dokumen ini.

### KAWALAN DOKUMEN

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0.0 | Sept 2025 | Versi awal manual pengguna pentadbir | Pasukan BPM |
| 3.0.0 | Okt 2025 | Kemaskini Panel Filament dan Aliran Kerja Hibrid | Pasukan BPM |
| 3.5.0 | Nov 2025 | Penambahan fungsi *Tracking* Aksesori & Pemantauan Pulse | Pasukan BPM |
| 3.7.0 | 15 Dis 2025 | Kemaskini Pengurusan AI Chatbot & Auto-Reply | Pasukan BPM |

-----

## iv. Kandungan

- i. Keterangan Dokumen
- ii. Semakan dan Pengesahan Dokumen
- iii. Kawalan Dokumen
- iv. Kandungan
- v. Senarai Gambarajah
- vi. Senarai Jadual
- vii. Definisi dan Akronim
- viii. Sumber Rujukan

<!-- end list -->

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

3. **PEMASANGAN DAN KONFIGURASI**

      - 3.1 Akses ke Panel Pentadbir

4. **PENGENALAN ANTARA MUKA PENGGUNA (FILAMENT)**

      - 4.1 Papan Pemuka (Dashboard)
      - 4.2 Menu Sisi
      - 4.3 Widget Prestasi (Laravel Pulse)

5. **PENGURUSAN PENGGUNA DAN KESELAMATAN**

      - 5.1 Log Masuk Admin
      - 5.2 Peranan dan Kebenaran (RBAC)

6. **FUNGSI-FUNGSI SISTEM (OPERASI)**

      - 6.1 Pengurusan Tiket Helpdesk
      - 6.2 Pengurusan Pinjaman Aset & Pegawai Bertanggungjawab
      - 6.3 Pengurusan Inventori Aset
      - 6.4 Pengurusan AI & Auto-Reply

7. **LAPORAN DAN ANALISIS**

      - 7.1 Laporan SLA & Statistik
      - 7.2 Jejak Audit (Dual Audit)

8. **PENYELENGGARAAN DAN SOKONGAN**

      - 8.1 Pemantauan Baris Gilir (Queue)

9. **PENYELESAIAN MASALAH**

10. **LAMPIRAN**

-----

## v. Senarai Gambarajah

*(Ruangan disediakan untuk tangkapan skrin Panel Filament, Dashboard Pulse, dan Borang Operasi)*

-----

## vi. Senarai Jadual

| No. | Tajuk Jadual | Muka Surat |
| :--- | :--- | :--- |
| 1 | Spesifikasi Aksesori | 12 |
| 2 | Kategori Kerosakan | 15 |

-----

## vii. Definisi dan Akronim

| Akronim | Keterangan |
| :--- | :--- |
| **BPM** | Bahagian Pengurusan Maklumat |
| **SLA** | *Service Level Agreement* (Perjanjian Tahap Perkhidmatan) |
| **RBAC** | *Role-Based Access Control* (Kawalan Akses Berasaskan Peranan) |
| **AI** | *Artificial Intelligence* (Kecerdasan Buatan) |
| **QR** | *Quick Response Code* |

| Terma | Definisi |
| :--- | :--- |
| **Admin** | Pegawai yang menguruskan operasi harian sistem (tiket/aset). |
| **Superuser** | Pegawai dengan akses penuh konfigurasi dan audit sistem. |
| **Check-out** | Proses menyerahkan aset kepada pemohon. |
| **True Hybrid** | Seni bina yang menerima data dari pengguna berdaftar dan tetamu. |

-----

## viii. Sumber Rujukan

1. D00 System Overview v3.6.1
2. D03 Software Requirements Specification v3.6.1
3. D12 UI/UX Design Guide v3.6.0
4. D18 AI Chatbot Ollama-Bedrock v1.0.0

-----

## 1\. PENGENALAN

### 1.1. Tujuan Manual

Manual ini disediakan sebagai panduan rujukan bagi **Pentadbir Sistem (Admin)** untuk mengendalikan fungsi *back-office* Sistem ICTServe. Ia merangkumi proses kerja pengurusan tiket, aset, dan pemantauan sistem.

### 1.2. Skop Manual

Manual ini meliputi penggunaan Panel Pentadbiran (berasaskan Filament) bagi:

- Menguruskan Tiket Aduan (Triage, Assign, Resolve).
- Menguruskan Pinjaman Aset (Kelulusan Manual, Serahan, Pemulangan).
- Memantau prestasi AI dan sistem melalui Dashboard.

### 1.3. Sasaran Pengguna

- **Admin**: Pegawai BPM yang menjalankan operasi harian.
- **Superuser**: Pegawai yang memantau konfigurasi dan audit (rujukan tambahan).

### 1.4. Gambaran Keseluruhan Sistem

Sistem ICTServe adalah platform bersepadu yang menggunakan arkitektur hibrid untuk menerima aduan dan permohonan daripada staf (log masuk) dan tetamu (tanpa log masuk). Semua data disatukan ke dalam pangkalan data pusat untuk tindakan Pentadbir.

-----

## 2\. KEPERLUAN SISTEM

### 2.1. Keperluan Perkakasan

- Komputer Desktop/Laptop dengan resolusi skrin minimum 1024x768.
- Pengimbas Kod QR (untuk proses Check-out/Check-in aset).

### 2.2. Keperluan Perisian

- Pelayar Web Moden (Google Chrome, Microsoft Edge, Firefox).
- Perisian PDF Reader.

### 2.3. Keperluan Rangkaian

- Sambungan Intranet MOTAC atau Internet stabil (untuk akses Cloud AI).

-----

## 3\. PEMASANGAN DAN KONFIGURASI

### 3.1. Akses ke Panel Pentadbir

Sistem ini berasaskan web sepenuhnya. Tiada pemasangan perisian diperlukan pada komputer klien.

1. Buka pelayar web.
2. Layari URL: `https://ictserve.motac.gov.my/admin`
3. Pastikan anda mempunyai akaun dengan peranan `admin` atau `superuser`.

-----

## 4\. PENGENALAN ANTARA MUKA PENGGUNA

### 4.1. Struktur Antara Muka (Filament)

Sistem menggunakan kerangka Filament v4 yang seragam dan responsif.

- **Bar Atas (Top Bar)**: Carian global, notifikasi sistem, dan profil pengguna.
- **Bar Sisi (Sidebar)**: Navigasi utama ke modul-modul sistem.
- **Ruang Kandungan**: Memaparkan jadual data, borang, atau widget statistik.

### 4.2. Papan Pemuka (Dashboard)

Halaman utama Admin memaparkan widget penting:

- **Statistik Tiket**: Jumlah Tiket Terbuka, Dalam Proses, Selesai.
- **Status Aset**: Aset Sedang Dipinjam, Lewat Dipulangkan.
- **Pematuhan SLA**: Peratusan tiket yang mematuhi tempoh masa ditetapkan.

### 4.3. Widget Prestasi (Laravel Pulse)

Admin boleh melihat ringkasan kesihatan sistem melalui widget Laravel Pulse yang memaparkan:

- Beban pelayan (CPU/Memori).
- Status baris gilir (Queue Jobs) bagi penghantaran e-mel dan pemprosesan AI.

-----

## 5\. PENGURUSAN PENGGUNA DAN KESELAMATAN

### 5.1. Log Masuk Admin

1. Masukkan e-mel rasmi (`@motac.gov.my`).
2. Masukkan kata laluan.
3. *(Jika diaktifkan)* Masukkan kod 2FA.
4. Klik "Log Masuk".

### 5.2. Peranan dan Kebenaran (RBAC)

- **Admin**: Boleh mengurus tiket, aset, dan pinjaman.
- **Superuser**: Akses tambahan kepada log audit, konfigurasi sistem, dan *Telescope*.

-----

## 6\. FUNGSI-FUNGSI SISTEM (OPERASI)

### 6.1. Pengurusan Tiket Helpdesk

Admin bertanggungjawab menyaring dan menyelesaikan tiket aduan.

**Langkah Mengurus Tiket:**

1. Klik menu **"Tiket Aduan"**.
2. Pilih tiket berstatus `OPEN`.
3. **Tugasan (Assign)**: Pilih staf teknikal untuk menyelesaikan masalah.
4. **Kemas Kini Status**: Tukar status kepada `IN_PROGRESS` atau `AWAITING_INFO`.
5. **Tambah Komen**: Masukkan catatan dalaman (Internal Note) atau balasan kepada pengguna.
6. **Selesai**: Tukar status kepada `RESOLVED` apabila masalah selesai.

### 6.2. Pengurusan Pinjaman Aset & Pegawai Bertanggungjawab

Admin menguruskan proses penyerahan dan pemulangan aset. Sistem kini menyokong penjejakan "Pegawai Bertanggungjawab" dan aksesori.

**Proses Pengeluaran (Check-Out):**

1. Buka permohonan berstatus `APPROVED`.
2. Klik butang **"Check-Out"**.
3. Imbas kod QR aset yang akan diserahkan.
4. Semak senarai aksesori (Beg, Tetikus, Kabel) dan tandakan `Hadir` (Present) dalam jadual `loan_transaction_accessories`.
5. Sahkan nama **Pegawai Bertanggungjawab** (jika berbeza dari pemohon).
6. Klik "Simpan". Status bertukar kepada `ON_LOAN`.

**Proses Pemulangan (Check-In):**

1. Cari permohonan pinjaman.
2. Klik butang **"Check-In"**.
3. Semak keadaan fizikal aset. Jika rosak, sistem akan automatik menjana tiket aduan.
4. Semak pemulangan semua aksesori. Jika tidak lengkap, sistem akan merekodkan percanggahan (*discrepancy*).
5. Klik "Selesai".

### 6.3. Pengurusan Inventori Aset

Admin boleh menambah, mengemaskini, atau melupuskan aset.

1. Klik menu **"Inventori Aset"**.
2. Gunakan fungsi **"Tambah Aset Baru"** untuk mendaftar peralatan.
3. Jana dan cetak label QR untuk aset baharu.

### 6.4. Pengurusan AI & Auto-Reply

Sistem menggunakan AI Hibrid (Ollama + Bedrock) untuk membantu menjawab pertanyaan. Admin berperanan melatih dan memantau AI.

**Meluluskan Draf Auto-Reply:**

1. Sistem AI mungkin menjana draf jawapan untuk tiket lazim.
2. Admin akan melihat notifikasi **"Auto-Reply Draft Available"**.
3. Semak kandungan draf.
4. Klik **"Approve & Send"** untuk hantar kepada pengguna, atau **"Edit"** untuk membaiki jawapan.

-----

## 7\. LAPORAN DAN ANALISIS

### 7.1. Penjanaan Laporan

Admin boleh menjana laporan berkala melalui menu **"Laporan"**.

- Pilih jenis laporan (Contoh: Statistik Tiket Bulanan).
- Tetapkan julat tarikh.
- Klik "Eksport ke PDF" atau "Eksport ke Excel".

### 7.2. Jejak Audit (Dual Audit)

Untuk tujuan pematuhan, Admin (terutamanya Superuser) boleh menyemak:

- **Log Aktiviti**: Siapa buat apa dan bila.
- **Log Audit**: Perubahan data (nilai lama vs nilai baru) untuk pematuhan integriti data.

-----

## 8\. PENYELENGGARAAN DAN SOKONGAN

### 8.1. Pemantauan Baris Gilir (Queue)

Admin perlu memastikan sistem notifikasi berjalan lancar.

- Lihat widget **"Failed Jobs"** di Dashboard.
- Jika terdapat e-mel gagal dihantar, klik **"Retry"** untuk cuba semula penghantaran.

-----

## 9\. PENYELESAIAN MASALAH

**Masalah**: E-mel kelulusan tidak diterima oleh Ketua Bahagian.
**Penyelesaian**: Semak log e-mel dalam sistem. Jika status "Sent", minta pengguna semak folder Spam. Jika "Failed", klik Retry pada Queue Manager.

**Masalah**: AI memberikan jawapan yang salah.
**Penyelesaian**: Admin boleh mengemaskini Pangkalan Pengetahuan (FAQ Knowledge Base) melalui menu Admin untuk memperbetulkan fakta bagi rujukan AI masa hadapan.

**Masalah**: Sistem lambat.
**Penyelesaian**: Semak Dashboard Laravel Pulse. Jika penggunaan CPU tinggi, laporkan kepada pasukan teknikal pelayan.

-----

## 10\. LAMPIRAN

*(Ruangan ini boleh diisi dengan templat e-mel rasmi dan senarai kod rujukan borang)*
