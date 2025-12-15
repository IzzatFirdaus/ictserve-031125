# MANUAL PENGGUNA SISTEM

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

Seksyen ini adalah ruangan untuk menyatakan secara ringkas keterangan berkenaan dokumen Manual Pengguna Sistem yang disediakan. Manual ini menyediakan panduan lengkap untuk pengguna (Staf, Tetamu, dan Pegawai Kelulusan) dalam menggunakan sistem ICTServe bagi tujuan pengurusan aduan kerosakan ICT dan permohonan pinjaman aset.

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
| 3.6.1     | 15 Disember 2025  | Kemaskini manual pengguna selaras dengan versi sistem 3.6.1 (Hybrid AI & Access). | Pasukan Pembangunan BPM |
| 3.0.0     | 31 Oktober 2025   | Versi awal manual pengguna untuk sistem ICTServe v3.0.                            | Pasukan Pembangunan BPM |

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
   - 3.1 Akses Sistem
   - 3.2 Pendaftaran dan Log Masuk
   - 3.3 Konfigurasi Profil

4. **PENGENALAN ANTARA MUKA PENGGUNA**
   - 4.1 Dashboard Utama
   - 4.2 Menu Navigasi
   - 4.3 Pintasan Papan Kekunci

5. **FUNGSI-FUNGSI SISTEM**
   - 5.1 Modul Helpdesk (Aduan Kerosakan)
   - 5.2 Modul Pinjaman Aset ICT
   - 5.3 Modul Kelulusan (Pegawai Gred 41+)
   - 5.4 Bantuan AI (Chatbot & FAQ)

6. **PENYELENGGARAAN DAN SOKONGAN**
   - 6.1 Hubungi Sokongan Teknikal

7. **PENYELESAIAN MASALAH**
   - 7.1 Soalan Lazim (FAQ)

8. **LAMPIRAN**

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
| OTP     | One-Time Password (Kata Laluan Sekali Guna)     |
| SLA     | Service Level Agreement (Perjanjian Tahap Perkhidmatan) |
| AI      | Artificial Intelligence (Kecerdasan Buatan)     |
| URL     | Uniform Resource Locator                        |

### b. Definisi

| Terma/Istilah           | Definisi                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| **Hybrid Access**       | Kebolehan menggunakan sistem sama ada sebagai tetamu (tanpa log masuk) atau pengguna berdaftar.                            |
| **Dashboard**           | Halaman utama yang memaparkan ringkasan status tiket dan pinjaman.                                                         |
| **Pegawai Pelulus**     | Pegawai Gred 41 ke atas yang bertanggungjawab meluluskan permohonan pinjaman aset.                                         |
| **Tiket**               | Rekod aduan kerosakan yang didaftarkan dalam sistem.                                                                       |

---

## viii. Sumber Rujukan

1. D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md (v3.6.1)
2. D18_AI_CHATBOT_OLLAMA_BEDROCK.md (v1.0.0)
3. USER_MANUAL_MS.md (v3.0.0)

---

## 1. PENGENALAN

### 1.1 Tujuan Manual

Manual ini bertujuan untuk menyediakan panduan lengkap kepada pengguna dalam menggunakan sistem ICTServe. Ia merangkumi langkah-langkah penggunaan modul Helpdesk, Pinjaman Aset, dan fungsi-fungsi lain yang berkaitan.

### 1.2 Skop Manual

Manual ini merangkumi:

- Panduan akses dan log masuk (termasuk akses tetamu).
- Arahan penggunaan modul Helpdesk dan Pinjaman Aset.
- Panduan bagi Pegawai Pelulus.
- Penggunaan ciri bantuan AI.
- Penyelesaian masalah lazim.

### 1.3 Sasaran Pengguna

| Kategori Pengguna | Keterangan |
| ----------------- | ---------- |
| **Staf MOTAC**    | Pengguna yang membuat aduan atau memohon pinjaman aset. |
| **Tetamu**        | Pengguna yang mengakses sistem tanpa log masuk untuk aduan pantas. |
| **Pegawai Pelulus**| Pegawai Gred 41+ yang meluluskan permohonan pinjaman. |
| **Pentadbir**     | Pegawai BPM yang menguruskan sistem (rujuk Manual Pentadbir untuk fungsi lanjut). |

### 1.4 Gambaran Keseluruhan Sistem

ICTServe adalah sistem pengurusan perkhidmatan ICT dalaman untuk kakitangan MOTAC. Sistem ini membolehkan pengguna menghantar tiket bantuan teknikal, memohon pinjaman aset ICT, menjejak status permohonan secara masa nyata, dan berinteraksi dengan Chatbot AI untuk bantuan pantas.

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Perkakasan

Sistem ini boleh diakses menggunakan komputer peribadi, komputer riba, atau peranti mudah alih (telefon pintar/tablet) dengan sambungan internet.

### 2.2 Keperluan Perisian

| Perisian | Keperluan |
| -------- | --------- |
| Pelayar Web | Google Chrome (Disyorkan), Mozilla Firefox, Microsoft Edge, Safari (Versi terkini) |
| Pemaparan PDF | Adobe Acrobat Reader atau pelayar web dengan sokongan PDF |

### 2.3 Keperluan Rangkaian

Sistem memerlukan sambungan internet atau intranet MOTAC yang stabil untuk beroperasi sepenuhnya.

---

## 3. AKSES DAN KONFIGURASI

### 3.1 Akses Sistem

1. Buka pelayar web.
2. Layari alamat URL: **<https://ictserve.motac.gov.my>**
3. Anda akan dibawa ke halaman utama sistem.

### 3.2 Pendaftaran dan Log Masuk

Sistem menyokong **Akses Hibrid**:

- **Akses Tetamu**: Anda boleh terus menggunakan fungsi "Tiket Baru" atau "Pinjaman Baru" tanpa log masuk.
- **Log Masuk Staf**:
  1. Klik butang **Log Masuk** di bahagian atas kanan.
  2. Masukkan **E-mel** dan **Kata Laluan**.
  3. Klik **Log Masuk**.
  4. Jika terlupa kata laluan, klik pautan "Lupa Kata Laluan".

### 3.3 Konfigurasi Profil (Pengguna Berdaftar)

1. Klik nama anda di bahagian atas kanan dan pilih **Profil**.
2. Anda boleh mengemaskini:
   - Nombor telefon.
   - Tetapan notifikasi.
   - Pilihan bahasa (Bahasa Melayu adalah bahasa utama).
3. Maklumat seperti Nama, Jawatan, dan Bahagian adalah baca-sahaja. Jika terdapat kesilapan, sila hubungi pentadbir atau klik "Minta Pembetulan".

---

## 4. PENGENALAN ANTARA MUKA PENGGUNA

### 4.1 Dashboard Utama

Dashboard memaparkan ringkasan aktiviti anda:

| Kad Statistik | Penerangan |
| ------------- | ---------- |
| **Tiket Terbuka** | Bilangan tiket bantuan anda yang sedang diproses. |
| **Pinjaman Menunggu** | Permohonan pinjaman yang belum diluluskan. |
| **Kelulusan Saya** | (Untuk Pegawai Pelulus) Permohonan yang perlu tindakan anda. |
| **Boleh Dituntut** | Tiket yang dibuat sebagai tetamu yang boleh dikaitkan ke akaun anda. |

### 4.2 Menu Navigasi

Menu utama terletak di bahagian atas atau sisi (bergantung pada peranti):

- **Utama**: Kembali ke Dashboard.
- **Tiket Baru**: Borang aduan kerosakan.
- **Pinjaman Baru**: Borang permohonan aset.
- **Sejarah**: Senarai permohonan terdahulu.
- **Kelulusan**: (Jika berkenaan) Senarai permohonan untuk diluluskan.

### 4.3 Pintasan Papan Kekunci

| Pintasan | Fungsi |
| -------- | ------ |
| `Alt+N`  | Tiket Baru |
| `Alt+L`  | Pinjaman Baru |
| `Alt+D`  | Kembali ke Dashboard |
| `?`      | Papar senarai pintasan |

---

## 5. FUNGSI-FUNGSI SISTEM

### 5.1 Modul Helpdesk (Aduan Kerosakan)

#### 5.1.1 Menghantar Tiket Baru

1. Klik **Tiket Baru** dari menu atau Dashboard.
2. **Langkah 1: Maklumat Asas**
   - Pilih **Kategori** (Perkakasan, Perisian, Rangkaian, dll).
   - Masukkan **Tajuk** ringkas masalah.
3. **Langkah 2: Butiran Masalah**
   - Terangkan masalah di ruangan **Penerangan**.
   - Sahkan **Bahagian** dan **Lokasi** anda.
4. **Langkah 3: Lampiran** (Pilihan)
   - Muat naik gambar atau dokumen berkaitan (Maks 5 fail, 10MB/fail).
5. **Langkah 4: Pengesahan**
   - Semak maklumat dan tandakan kotak perakuan.
   - Klik **Hantar**.
6. Anda akan menerima **Nombor Tiket** dan e-mel pengesahan.

#### 5.1.2 Menjejak Status Tiket

1. Pergi ke menu **Sejarah**.
2. Cari tiket menggunakan nombor tiket.
3. Status tiket:
   - **Baru**: Diterima sistem.
   - **Dalam Proses**: Sedang disemak juruteknik.
   - **Selesai**: Masalah telah diselesaikan.

#### 5.1.3 Menuntut Tiket Tetamu

Jika anda menghantar tiket tanpa log masuk, anda boleh menuntutnya kemudian:

1. Di Dashboard, lihat kad **Boleh Dituntut**.
2. Klik **Tuntut Penyerahan**.
3. Masukkan OTP yang dihantar ke e-mel anda untuk pengesahan.

### 5.2 Modul Pinjaman Aset ICT

#### 5.2.1 Memohon Pinjaman

1. Klik **Pinjaman Baru**.
2. **Langkah 1: Pilih Aset**
   - Pilih jenis aset (Projektor, Laptop, dll).
   - Sistem akan memaparkan ketersediaan aset.
3. **Langkah 2: Tarikh**
   - Pilih **Tarikh Pengambilan** dan **Pemulangan**.
   - Tempoh maksimum adalah 14 hari.
4. **Langkah 3: Tujuan**
   - Nyatakan tujuan dan lokasi penggunaan.
5. **Langkah 4: Hantar**
   - Baca syarat pinjaman dan hantar permohonan.

#### 5.2.2 Proses Pengambilan dan Pemulangan

- **Pengambilan**: Selepas lulus, anda terima OTP melalui e-mel. Hadir ke kaunter BPM dan berikan OTP untuk pengambilan aset.
- **Pemulangan**: Pulangkan aset sebelum tarikh tamat. Kakitangan akan memeriksa aset dan mengesahkan pemulangan dalam sistem.

### 5.3 Modul Kelulusan (Pegawai Gred 41+)

Pegawai Pelulus akan menerima e-mel apabila terdapat permohonan yang memerlukan kelulusan.

1. Klik pautan dalam e-mel notifikasi, ATAU log masuk ke sistem dan pergi ke menu **Kelulusan**.
2. Semak butiran permohonan.
3. Klik **Lulus** atau **Tolak**.
4. Jika menolak, masukkan sebab penolakan.

### 5.4 Bantuan AI (Chatbot & FAQ)

Sistem dilengkapi dengan Chatbot AI (Ollama/Bedrock) untuk bantuan pantas.

1. Klik ikon **Chat** di sudut bawah kanan.
2. Taip soalan anda (contoh: "Macam mana nak reset password?").
3. AI akan memberikan jawapan serta-merta berdasarkan pangkalan pengetahuan sistem.

---

## 6. PENYELENGGARAAN DAN SOKONGAN

### 6.1 Hubungi Sokongan Teknikal

Jika anda menghadapi masalah yang tidak dapat diselesaikan melalui panduan ini:

**Bahagian Pengurusan Maklumat (BPM)**

- **E-mel**: <ict@motac.gov.my>
- **Telefon**: 03-8000 8000 ext. 1234
- **Waktu Operasi**: Isnin - Jumaat, 8:30 pagi - 5:30 petang

---

## 7. PENYELESAIAN MASALAH

### 7.1 Soalan Lazim (FAQ)

**S: Saya terlupa kata laluan. Apa perlu buat?**
J: Klik pautan "Lupa Kata Laluan" di halaman log masuk dan ikut arahan yang dihantar ke e-mel anda.

**S: Berapa lama tempoh kelulusan pinjaman?**
J: Kelulusan bergantung kepada Pegawai Pelulus (biasanya 1-2 hari bekerja).

**S: Bolehkah saya membatalkan tiket yang telah dihantar?**
J: Anda boleh membatalkan tiket yang berstatus "Baru" melalui menu Sejarah. Jika status "Dalam Proses", sila hubungi BPM.

**S: Mengapa saya tidak menerima e-mel notifikasi?**
J: Semak folder Spam/Junk e-mel anda. Pastikan e-mel yang didaftarkan adalah betul.

---

## 8. LAMPIRAN

### Lampiran A: Senarai Pintasan Papan Kekunci

| Pintasan | Fungsi |
| -------- | ------ |
| `Alt+N`  | Tiket Baru |
| `Alt+L`  | Pinjaman Baru |
| `Alt+D`  | Dashboard |
| `Alt+H`  | Bantuan |
| `Esc`    | Tutup Modal/Dialog |

---

**NOTA PENTING:**

1. Manual ini disediakan mengikut piawaian KRISA.
2. Tangkapan skrin dan gambarajah mungkin berbeza sedikit mengikut kemaskini sistem semasa.
3. Sila laporkan sebarang kesilapan dalam manual ini kepada BPM.

---

**Tarikh Terakhir Dikemaskini:** 15 Disember 2025
**Versi:** 3.6.1
**Status:** Aktif
