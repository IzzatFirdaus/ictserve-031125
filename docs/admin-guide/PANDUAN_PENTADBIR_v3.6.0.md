# Panduan Pentadbir ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Sistem**: ICTServe - Portal Perkhidmatan ICT MOTAC  
**Bahasa**: Bahasa Melayu Sahaja  
**Peranan**: Admin dan Superuser

---

## Pengenalan

Panduan ini ditujukan kepada kakitangan BPM MOTAC yang mempunyai peranan **Admin** atau **Superuser** dalam sistem ICTServe v3.6.0. Panduan ini merangkumi pengurusan sistem melalui panel pentadbiran Filament.

### Peranan dan Kebenaran

| Peranan | Kebenaran |
|---------|-----------|
| **Staff** | Akses portal pengguna, hantar tiket dan permohonan pinjaman |
| **Approver** | Kebenaran Staff + kelulusan permohonan pinjaman (Gred 41+) |
| **Admin** | Kebenaran Approver + pengurusan operasi melalui panel Filament |
| **Superuser** | Kebenaran Admin + konfigurasi sistem, Laravel Telescope, pengurusan pengguna penuh |

---

## Bahagian 1: Akses Panel Pentadbiran

### 1.1 Log Masuk ke Panel Filament

1. Layari **<https://ictserve.motac.gov.my/admin>**
2. Masukkan **E-mel** atau **Nama Pengguna**
3. Masukkan **Kata Laluan**
4. Klik **Log Masuk**

**Nota**: Hanya pengguna dengan peranan Admin atau Superuser boleh mengakses panel ini.

### 1.2 Gambaran Panel Pentadbiran

Panel pentadbiran Filament menyediakan:

- **Papan Pemuka** - Statistik dan metrik masa nyata
- **Pengurusan Tiket** - Urus tiket bantuan teknikal
- **Pengurusan Pinjaman** - Urus permohonan pinjaman aset
- **Pengurusan Aset** - Inventori dan penyelenggaraan aset
- **Pengurusan Pengguna** - Urus akaun pengguna (Superuser sahaja)
- **Laporan** - Jana dan eksport laporan
- **Tetapan** - Konfigurasi sistem (Superuser sahaja)

---

## Bahagian 2: Pengurusan Tiket Bantuan Teknikal

### 2.1 Senarai Tiket

1. Klik **Tiket Helpdesk** dari menu sisi
2. Lihat senarai semua tiket dengan maklumat:
   - Nombor tiket
   - Tajuk
   - Pemohon
   - Kategori
   - Status
   - Tarikh dicipta
   - SLA

### 2.2 Penapis dan Carian

**Penapis Tersedia:**

- Status (Baru, Ditugaskan, Dalam Proses, Selesai, Ditutup)
- Kategori (Perkakasan, Perisian, Rangkaian, E-mel, Lain-lain)
- Keutamaan (Rendah, Sederhana, Tinggi, Kritikal)
- Tarikh dicipta
- Kakitangan ditugaskan

**Carian:**

- Nombor tiket
- Tajuk
- Nama pemohon
- E-mel pemohon

### 2.3 Tugaskan Tiket

1. Klik pada tiket untuk membuka butiran
2. Klik butang **Tugaskan**
3. Pilih kakitangan ICT dari senarai
4. Masukkan nota penugasan (pilihan)
5. Klik **Simpan**

**Penugasan Automatik:**

- Sistem boleh dikonfigurasi untuk penugasan automatik berdasarkan kategori
- Beban kerja kakitangan dipertimbangkan dalam penugasan

### 2.4 Kemaskini Status Tiket

1. Buka butiran tiket
2. Klik butang **Kemaskini Status**
3. Pilih status baru:
   - **Ditugaskan** - Tiket telah ditugaskan
   - **Dalam Proses** - Sedang dikendalikan
   - **Menunggu Maklum Balas** - Memerlukan maklumat dari pemohon
   - **Selesai** - Masalah diselesaikan
   - **Ditutup** - Tiket ditutup
4. Masukkan nota kemaskini
5. Klik **Simpan**

### 2.5 Komen Dalaman

Komen dalaman hanya boleh dilihat oleh kakitangan ICT:

1. Buka butiran tiket
2. Pergi ke tab **Komen Dalaman**
3. Masukkan komen
4. Klik **Hantar**

### 2.6 Penjejakan SLA

Sistem menjejak SLA secara automatik:

| Keutamaan | Masa Respons | Masa Penyelesaian |
|-----------|--------------|-------------------|
| Kritikal | 1 jam | 4 jam |
| Tinggi | 4 jam | 24 jam |
| Sederhana | 8 jam | 48 jam |
| Rendah | 24 jam | 72 jam |

**Amaran SLA:**

- Amaran kuning: 75% masa SLA telah berlalu
- Amaran merah: SLA telah dilanggar
- Notifikasi automatik kepada penyelia

### 2.7 Eskalasi Tiket

Tiket boleh dieskalasi secara:

**Automatik:**

- Apabila SLA hampir dilanggar
- Apabila tiket tidak dikemaskini dalam tempoh tertentu

**Manual:**

1. Buka butiran tiket
2. Klik **Eskalasi**
3. Pilih penyelia
4. Masukkan sebab eskalasi
5. Klik **Hantar**

---

## Bahagian 3: Pengurusan Pinjaman Aset

### 3.1 Senarai Permohonan Pinjaman

1. Klik **Permohonan Pinjaman** dari menu sisi
2. Lihat senarai semua permohonan dengan maklumat:
   - Nombor permohonan
   - Pemohon
   - Aset dimohon
   - Tarikh pinjaman
   - Status
   - Pegawai pelulus

### 3.2 Penapis dan Carian

**Penapis Tersedia:**

- Status (Menunggu, Diluluskan, Ditolak, Dipinjam, Dipulangkan)
- Jenis aset
- Tarikh permohonan
- Bahagian pemohon

### 3.3 Proses Kelulusan (Admin)

Admin boleh meluluskan permohonan bagi pihak Pegawai Pelulus:

1. Buka butiran permohonan
2. Semak maklumat permohonan
3. Klik **Lulus** atau **Tolak**
4. Jika menolak, masukkan sebab
5. Klik **Simpan**

**Nota**: Tindakan ini direkodkan dalam log audit.

### 3.4 Proses Pengambilan Aset

1. Pemohon hadir dengan OTP
2. Buka permohonan yang berkaitan
3. Klik **Proses Pengambilan**
4. Masukkan OTP yang diberikan pemohon
5. Sahkan keadaan aset
6. Klik **Serahkan Aset**
7. Cetak resit pengambilan

### 3.5 Proses Pemulangan Aset

1. Pemohon hadir untuk memulangkan aset
2. Buka permohonan yang berkaitan
3. Klik **Proses Pemulangan**
4. Periksa keadaan aset:
   - **Baik** - Tiada kerosakan
   - **Rosak Ringan** - Kerosakan kecil
   - **Rosak Teruk** - Kerosakan besar
5. Jika rosak, tiket Helpdesk dibuat automatik
6. Klik **Terima Pemulangan**
7. Cetak resit pemulangan

### 3.6 Pengurusan Aset Tertunggak

1. Pergi ke **Aset Tertunggak** dari menu
2. Lihat senarai aset yang belum dipulangkan
3. Untuk setiap aset tertunggak:
   - Klik **Hantar Peringatan** untuk menghantar e-mel peringatan
   - Klik **Hubungi Pemohon** untuk melihat maklumat hubungan
   - Klik **Eskalasi** untuk melaporkan kepada penyelia

---

## Bahagian 4: Pengurusan Inventori Aset

### 4.1 Senarai Aset

1. Klik **Aset ICT** dari menu sisi
2. Lihat senarai semua aset dengan maklumat:
   - Kod aset
   - Nama aset
   - Kategori
   - Status
   - Lokasi
   - Keadaan

### 4.2 Tambah Aset Baru

1. Klik butang **Tambah Aset**
2. Isi maklumat aset:
   - **Kod Aset** - Kod unik aset
   - **Nama Aset** - Nama deskriptif
   - **Kategori** - Pilih dari senarai
   - **Jenama** - Jenama aset
   - **Model** - Model aset
   - **Nombor Siri** - Nombor siri pengeluar
   - **Tarikh Pembelian** - Tarikh aset dibeli
   - **Nilai** - Nilai pembelian (RM)
   - **Lokasi** - Lokasi penyimpanan
   - **Keadaan** - Keadaan semasa aset
3. Muat naik gambar aset (pilihan)
4. Klik **Simpan**

### 4.3 Kemaskini Maklumat Aset

1. Klik pada aset untuk membuka butiran
2. Klik **Kemaskini**
3. Ubah maklumat yang diperlukan
4. Klik **Simpan**

### 4.4 Status Aset

| Status | Penerangan |
|--------|------------|
| **Tersedia** | Aset boleh dipinjam |
| **Dipinjam** | Aset sedang dipinjam |
| **Penyelenggaraan** | Aset sedang diselenggara |
| **Rosak** | Aset rosak, tidak boleh dipinjam |
| **Dilupuskan** | Aset telah dilupuskan |

### 4.5 Sejarah Aset

Setiap aset mempunyai sejarah lengkap:

1. Buka butiran aset
2. Pergi ke tab **Sejarah**
3. Lihat rekod:
   - Sejarah pinjaman
   - Sejarah penyelenggaraan
   - Perubahan status
   - Tiket berkaitan

### 4.6 Penyelenggaraan Aset

1. Buka butiran aset
2. Klik **Jadualkan Penyelenggaraan**
3. Isi maklumat:
   - Jenis penyelenggaraan
   - Tarikh dijadualkan
   - Anggaran kos
   - Nota
4. Klik **Simpan**

---

## Bahagian 5: Pengurusan Pengguna (Superuser)

### 5.1 Senarai Pengguna

1. Klik **Pengguna** dari menu sisi
2. Lihat senarai semua pengguna dengan maklumat:
   - Nama
   - E-mel
   - Peranan
   - Bahagian
   - Status

### 5.2 Tambah Pengguna Baru

1. Klik butang **Tambah Pengguna**
2. Isi maklumat pengguna:
   - **Nama** - Nama penuh
   - **E-mel** - E-mel rasmi @motac.gov.my
   - **Nama Pengguna** - Nama pengguna untuk log masuk
   - **Kata Laluan** - Kata laluan sementara
   - **Peranan** - Pilih peranan (Staff, Approver, Admin, Superuser)
   - **Bahagian** - Pilih bahagian
   - **Gred** - Pilih gred jawatan
   - **Jawatan** - Pilih jawatan
3. Klik **Simpan**

### 5.3 Kemaskini Peranan Pengguna

1. Klik pada pengguna untuk membuka butiran
2. Klik **Kemaskini Peranan**
3. Pilih peranan baru
4. Masukkan sebab perubahan
5. Klik **Simpan**

**Nota**: Perubahan peranan direkodkan dalam log audit.

### 5.4 Nyahaktifkan Pengguna

1. Buka butiran pengguna
2. Klik **Nyahaktifkan**
3. Masukkan sebab
4. Klik **Sahkan**

**Nota**: Pengguna yang dinyahaktifkan tidak boleh log masuk tetapi rekod mereka dikekalkan.

### 5.5 Pautkan Penyerahan Tetamu

Untuk memautkan penyerahan tetamu kepada akaun pengguna:

1. Buka butiran pengguna
2. Pergi ke tab **Penyerahan Tetamu**
3. Lihat senarai penyerahan yang sepadan dengan e-mel
4. Klik **Pautkan** untuk setiap penyerahan
5. Sahkan tindakan

---

## Bahagian 6: Laporan dan Analitik

### 6.1 Papan Pemuka Analitik

Papan pemuka memaparkan metrik masa nyata:

**Metrik Helpdesk:**

- Jumlah tiket hari ini
- Tiket terbuka
- Purata masa penyelesaian
- Kadar pematuhan SLA

**Metrik Pinjaman:**

- Permohonan hari ini
- Pinjaman aktif
- Aset tertunggak
- Kadar kelulusan

### 6.2 Jana Laporan

1. Pergi ke **Laporan** dari menu
2. Pilih jenis laporan:
   - Laporan Tiket Helpdesk
   - Laporan Pinjaman Aset
   - Laporan Inventori Aset
   - Laporan Pengguna
   - Laporan Audit
3. Tetapkan parameter:
   - Julat tarikh
   - Penapis tambahan
4. Klik **Jana Laporan**

### 6.3 Eksport Laporan

Laporan boleh dieksport dalam format:

- **PDF** - Untuk cetakan dan arkib
- **Excel** - Untuk analisis lanjut
- **CSV** - Untuk import ke sistem lain

### 6.4 Laporan Berjadual

Untuk menjadualkan laporan automatik:

1. Pergi ke **Laporan Berjadual**
2. Klik **Tambah Jadual**
3. Pilih jenis laporan
4. Tetapkan kekerapan (Harian, Mingguan, Bulanan)
5. Masukkan e-mel penerima
6. Klik **Simpan**

---

## Bahagian 7: Log Audit

### 7.1 Sistem Audit Dwi

ICTServe v3.6.0 menggunakan sistem audit dwi:

**Audit Pematuhan (owen-it/laravel-auditing):**

- Perubahan peringkat medan
- Nilai lama dan baru
- Untuk keperluan pematuhan

**Log Aktiviti (spatie/laravel-activitylog):**

- Aktiviti pengguna
- Tindakan operasi
- Untuk pemantauan operasi

### 7.2 Lihat Log Audit

1. Pergi ke **Log Audit** dari menu
2. Pilih jenis log:
   - Log Pematuhan
   - Log Aktiviti
3. Gunakan penapis:
   - Tarikh
   - Pengguna
   - Jenis tindakan
   - Entiti terjejas

### 7.3 Eksport Log Audit

Log audit boleh dieksport untuk:

- Audit luaran
- Siasatan
- Arkib

**Nota**: Log audit disimpan selama minimum 7 tahun dan tidak boleh diubah.

---

## Bahagian 8: Pemantauan Prestasi (Superuser)

### 8.1 Laravel Pulse

Untuk mengakses Laravel Pulse:

1. Pergi ke **Pemantauan** > **Pulse**
2. Lihat metrik prestasi:
   - Masa respons
   - Pertanyaan pangkalan data
   - Penggunaan memori
   - Penggunaan CPU

### 8.2 Laravel Telescope

Untuk mengakses Laravel Telescope (Superuser sahaja):

1. Pergi ke **Pemantauan** > **Telescope**
2. Lihat maklumat penyahpepijatan:
   - Permintaan HTTP
   - Pertanyaan pangkalan data
   - Pengecualian
   - Log
   - Mel

**Nota**: Telescope hanya boleh diakses oleh Superuser untuk tujuan penyahpepijatan.

---

## Bahagian 9: Konfigurasi Sistem (Superuser)

### 9.1 Tetapan Umum

1. Pergi ke **Tetapan** > **Umum**
2. Konfigurasi:
   - Nama sistem
   - E-mel pentadbir
   - Zon masa
   - Format tarikh

### 9.2 Tetapan E-mel

1. Pergi ke **Tetapan** > **E-mel**
2. Konfigurasi:
   - Pelayan SMTP
   - Port
   - Pengesahan
   - Templat e-mel

### 9.3 Tetapan SLA

1. Pergi ke **Tetapan** > **SLA**
2. Konfigurasi masa SLA untuk setiap keutamaan
3. Konfigurasi amaran dan eskalasi

### 9.4 Tetapan Kelulusan

1. Pergi ke **Tetapan** > **Kelulusan**
2. Konfigurasi:
   - Matriks kelulusan
   - Tempoh sah pautan kelulusan
   - Pegawai pelulus lalai

---

## Bahagian 10: Penyelesaian Masalah

### 10.1 Masalah Biasa

**Pengguna tidak boleh log masuk:**

1. Semak status akaun (aktif/tidak aktif)
2. Semak peranan pengguna
3. Minta pengguna tetapkan semula kata laluan

**E-mel tidak dihantar:**

1. Semak konfigurasi SMTP
2. Semak log e-mel dalam Telescope
3. Semak baris gilir (queue)

**Prestasi perlahan:**

1. Semak Laravel Pulse untuk metrik
2. Semak pertanyaan pangkalan data dalam Telescope
3. Hubungi pasukan teknikal jika perlu

### 10.2 Hubungi Sokongan Teknikal

Untuk masalah teknikal yang tidak dapat diselesaikan:

- **E-mel**: <ict-support@motac.gov.my>
- **Telefon**: 03-8000 8000 ext. 1235
- **Waktu**: Isnin - Jumaat, 8:30 pagi - 5:30 petang

---

**Dokumen ini adalah sebahagian daripada sistem ICTServe v3.6.0**  
**Pematuhan**: D00-D17, WCAG 2.2 AA, PDPA 2010, MyGOV Digital Service Standards v2.1.0
