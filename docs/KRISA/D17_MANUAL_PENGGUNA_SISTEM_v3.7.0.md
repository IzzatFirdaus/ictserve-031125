# DOKUMEN MANUAL PENGGUNA SISTEM

## SISTEM ICTSERVE
**(Sistem Pengurusan Helpdesk & Pinjaman Aset ICT)**

| Medan | Nilai |
| :--- | :--- |
| **NAMA AGENSI** | Bahagian Pengurusan Maklumat (BPM) |
| **NAMA AGENSI INDUK** | Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **TARIKH DOKUMEN** | 15 Disember 2025 |
| **VERSI DOKUMEN** | 3.7.0 |

---

## i. Keterangan Dokumen

Dokumen ini adalah Manual Pengguna bagi Sistem ICTServe versi 3.7.0. Ia menyediakan panduan lengkap mengenai cara penggunaan sistem, bermula daripada pendaftaran akaun, penggunaan fungsi hibrid (Helpdesk dan Pinjaman Aset), interaksi dengan Pembantu AI (AI Chatbot), sehinggalah kepada pengendalian ralat.

Dokumen ini mematuhi piawaian pendokumentasian KRISA dan merangkumi ciri-ciri terkini sistem termasuk *True Hybrid Architecture* dan *Cloud Hybrid AI*.

---

## ii. Semakan dan Pengesahan Dokumen

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| [Nama Pegawai] | Ketua Pasukan Pembangunan | | 15-12-2025 |
| [Nama Pegawai] | Pegawai UI/UX | | 15-12-2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
| :--- | :--- | :--- | :--- |
| [Nama Pegawai] | Pengarah BPM | | |

---

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0.0 | Sept 2025 | Versi awal manual pengguna | Pasukan BPM |
| 3.0.0 | Okt 2025 | Kemaskini seni bina dalaman dan aksesibiliti | Pasukan BPM |
| 3.5.0 | Nov 2025 | Penambahan Pendaftaran Kendiri & Arkitektur Hibrid | Pasukan BPM |
| 3.7.0 | 15 Dis 2025 | Kemaskini integrasi AI Chatbot & Bahasa Melayu Sahaja | Pasukan BPM |

---

## iv. Kandungan

1. **PENGENALAN**
    * 1.1 Tujuan dan Skop
    * 1.2 Organisasi Manual
    * 1.3 Maklumat Untuk Dihubungi
    * 1.4 Rujukan Dokumen
    * 1.5 Tanggungjawab Pengguna
    * 1.6 Glosari

2. **OVERVIEW SISTEM**
    * 2.1 Tujuan
    * 2.2 Keterangan Sistem (Arkitektur Hibrid)

3. **KETERANGAN FUNGSI SISTEM**
    * 3.1 Senarai Fungsi Sistem
    * 3.2 Perincian Keterangan bagi Fungsi Sistem

4. **ARAHAN PENGGUNAAN SISTEM**
    * 4.1 Log Masuk & Pendaftaran (Mod Authenticated)
    * 4.2 Akses Tetamu (Mod Guest)
    * 4.3 Proses Pengoperasian Sistem (Langkah-demi-Langkah)
        * 4.3.1 Menghantar Tiket Aduan (Helpdesk)
        * 4.3.2 Memohon Pinjaman Aset ICT
        * 4.3.3 Menggunakan Pembantu AI (FAQ Bot)
        * 4.3.4 Proses Kelulusan (Pegawai Gred 41+)
    * 4.4 Pautan Akaun (Account Linking)
    * 4.5 Penamatan Sesi

5. **PENGENDALIAN RALAT**
    * 5.1 Kod dan Mesej Ralat
    * 5.2 Bantuan Helpdesk

---

## v. Senarai Gambarajah

*(Ruangan ini disediakan untuk senarai tangkapan skrin sistem)*

---

## vi. Senarai Jadual

1. Jadual 1: Peranan Pengguna
2. Jadual 2: Klasifikasi Masalah AI

---

## 1. PENGENALAN

### 1.1. Tujuan dan Skop
Manual Pengguna ini bertujuan memberi panduan kepada warga kerja MOTAC (Staf dan Pegawai) dalam menggunakan Sistem ICTServe v3.7.0. Skop manual merangkumi penggunaan modul Helpdesk, Pinjaman Aset, dan interaksi dengan Pembantu AI. Sistem ini adalah untuk kegunaan dalaman sahaja.

### 1.2. Organisasi Manual
Manual ini disusun mengikut aliran kerja pengguna, bermula daripada kaedah akses (Log Masuk atau Tetamu), diikuti dengan fungsi utama, dan diakhiri dengan penyelesaian masalah.

### 1.3. Maklumat Untuk Dihubungi
Sekiranya terdapat sebarang masalah teknikal yang tidak dapat diselesaikan melalui manual ini, sila hubungi:

* **Unit Helpdesk BPM MOTAC**
* Emel: <helpdesk@motac.gov.my>
* Telefon: Sambungan 1234

### 1.4. Rujukan Dokumen
Manual ini merujuk kepada spesifikasi teknikal berikut:

* D00 System Overview v3.6.1
* D03 Software Requirements Specification v3.6.1
* D18 AI Chatbot Ollama-Bedrock v1.0.0

### 1.5. Tanggungjawab Pengguna
Pengguna bertanggungjawab untuk:

* Memastikan maklumat aduan/permohonan adalah benar.
* Tidak berkongsi kata laluan (bagi pengguna berdaftar).
* Mematuhi dasar penggunaan AI dan tidak memuat naik data rahsia (SULIT) ke dalam ruang sembang AI.

### 1.6. Glosari

* **True Hybrid Architecture**: Konsep di mana pengguna boleh memilih untuk menggunakan sistem sebagai Tetamu (tanpa log masuk) atau Staf Berdaftar (dengan log masuk).
* **Pembantu AI**: Chatbot pintar yang menjawab soalan FAQ dan membantu mendraf respons.
* **Token**: Kod keselamatan unik yang dihantar melalui e-mel untuk menyemak status atau membuat kelulusan tanpa log masuk.

---

## 2. OVERVIEW SISTEM

### 2.1. Tujuan
Sistem ICTServe dibangunkan untuk memusatkan pengurusan perkhidmatan ICT di MOTAC, mempercepatkan proses aduan kerosakan, dan mendigitalkan permohonan pinjaman aset dengan bantuan teknologi AI.

### 2.2. Keterangan Sistem
Sistem ini berasaskan web dan boleh diakses melalui rangkaian intranet MOTAC. Ia mempunyai ciri unik **"Guest-First"**, di mana staf boleh menghantar aduan dengan pantas tanpa perlu log masuk, namun digalakkan mendaftar untuk ciri tambahan seperti papan pemuka peribadi (My Dashboard).

Antara muka sistem menggunakan **Bahasa Melayu sepenuhnya** selaras dengan dasar v3.6.0.

---

## 3. KETERANGAN FUNGSI SISTEM

### 3.1. Senarai Fungsi Sistem
Sistem ICTServe menawarkan fungsi berikut:

1. **Pengurusan Akaun**: Pendaftaran sendiri, log masuk fleksibel, dan pautan akaun.
2. **Helpdesk**: Pelaporan kerosakan ICT dan semakan status tiket.
3. **Pinjaman Aset**: Tempahan peralatan ICT dan semakan ketersediaan.
4. **Pembantu AI (AI Chatbot)**: Bantuan automatik untuk pertanyaan lazim.
5. **Kelulusan E-mel**: Fungsi khas untuk Pegawai Gred 41+ meluluskan pinjaman melalui e-mel.

### 3.2. Perincian Keterangan bagi Fungsi Sistem

| Fungsi | Keterangan | Input | Output |
| :--- | :--- | :--- | :--- |
| **Pendaftaran Sendiri** | Membolehkan staf mencipta akaun menggunakan e-mel rasmi. | E-mel (@motac.gov.my), Kata Laluan | Akaun Pengguna Aktif |
| **Tiket Aduan (Guest)** | Menghantar aduan tanpa log masuk. | Nama, E-mel, Kategori, Masalah | Nombor Tiket & Pautan Status |
| **Pinjaman Aset** | Memohon peralatan ICT. | Tarikh, Tujuan, Jenis Aset | E-mel Kelulusan kepada Ketua Jabatan |
| **Pembantu AI** | Menjawab soalan pengguna secara masa nyata. | Teks Pertanyaan | Jawapan Teks / Cadangan FAQ |

---

## 4. ARAHAN PENGGUNAAN SISTEM

### 4.1. Log Masuk & Pendaftaran (Mod Authenticated)

Pengguna yang ingin menyimpan rekod sejarah dan mengakses *Dashboard* perlu mendaftar.

**Langkah Pendaftaran:**

1. Klik pautan **"Daftar"** pada halaman utama.
2. Masukkan Nama Penuh dan E-mel rasmi (`@motac.gov.my` sahaja).
3. Cipta kata laluan yang kukuh.
4. Sistem akan menghantar pautan pengesahan ke e-mel anda. Klik pautan tersebut untuk mengaktifkan akaun.

**Langkah Log Masuk:**

1. Klik **"Log Masuk"**.
2. Masukkan **E-mel Penuh** ATAU **Nama Pengguna Pendek** (contoh: `ali.abu`).
3. Masukkan kata laluan dan klik butang Log Masuk.
4. *(Opsyenal)*: Anda juga boleh log masuk menggunakan **Google Workspace** jika akaun Google anda adalah `@motac.gov.my`.

### 4.2. Akses Tetamu (Mod Guest)

Bagi akses pantas, pengguna **TIDAK PERLU** log masuk.

1. Terus ke halaman utama.
2. Pilih butang **"Hantar Tiket"** atau **"Mohon Pinjaman"**.
3. Sistem akan meminta maklumat asas (Nama & E-mel) untuk tujuan rekod bagi setiap transaksi.

### 4.3. Proses Pengoperasian Sistem

#### 4.3.1. Menghantar Tiket Aduan (Helpdesk)

1. Pilih menu **Aduan Kerosakan**.
2. Isi maklumat peribadi (jika Tetamu) atau semak maklumat (jika Log Masuk).
3. Pilih **Kategori Kerosakan** dan isi **Keterangan Masalah**.
4. Muat naik gambar bukti jika perlu.
5. Klik **Hantar**.
6. Anda akan menerima e-mel mengandungi **Nombor Tiket** dan pautan semakan status.

#### 4.3.2. Memohon Pinjaman Aset ICT

1. Pilih menu **Pinjaman Aset**.
2. Pilih tarikh mula dan tamat pinjaman. Sistem akan menyemak ketersediaan aset secara automatik.
3. Pilih jenis aset dan kuantiti.
4. Isi tujuan pinjaman.
5. *(Penting)* Sistem akan menghantar e-mel automatik kepada Pegawai Pelulus (Ketua Bahagian) anda.

#### 4.3.3. Menggunakan Pembantu AI (FAQ Bot)

1. Klik ikon **Pembantu AI** di penjuru bawah kanan skrin.
2. Taip soalan anda dalam Bahasa Melayu (contoh: *"Bagaimana cara mohon laptop?"*).
3. AI akan memproses soalan menggunakan pangkalan pengetahuan dalaman (Ollama) atau analisis kompleks (Bedrock) secara automatik.
4. Jawapan akan dipaparkan serta-merta.

#### 4.3.4. Proses Kelulusan (Pegawai Gred 41+)
Fungsi ini khusus untuk pegawai yang meluluskan pinjaman.

1. Pegawai menerima e-mel bertajuk **"Permohonan Kelulusan Pinjaman Aset"**.
2. E-mel mengandungi butiran pemohon dan aset.
3. Klik butang **"LULUS"** atau **"TOLAK"** di dalam e-mel tersebut.
4. Pautan akan membuka halaman pengesahan ringkas (tidak perlu log masuk). Masukkan catatan jika perlu dan sahkan keputusan.

### 4.4. Pautan Akaun (Account Linking)
Jika anda baru mendaftar akaun tetapi pernah membuat aduan sebagai Tetamu sebelum ini:

1. Log masuk ke sistem.
2. Sistem akan mengesan jika terdapat tiket lama yang menggunakan e-mel yang sama.
3. Satu paparan *pop-up* akan muncul: **"Adakah anda ingin pautkan transaksi lama?"**.
4. Klik **"Ya, Pautkan"** untuk menyatukan rekod lama ke dalam *Dashboard* anda.

### 4.5. Penamatan Sesi

* Bagi pengguna berdaftar: Klik menu profil dan pilih **"Log Keluar"**.
* Bagi pengguna tetamu: Cukup sekadar menutup pelayar web.

---

## 5. PENGENDALIAN RALAT

### 5.1. Kod dan Mesej Ralat Biasa

| Mesej Ralat / Simptom | Maksud | Tindakan |
| :--- | :--- | :--- |
| **"E-mel tidak sah"** | Domain e-mel bukan `@motac.gov.my`. | Gunakan e-mel rasmi jabatan sahaja. |
| **"Token Tamat Tempoh"** | Pautan e-mel (kelulusan/status) sudah luput (lebih 72 jam). | Hubungi Helpdesk untuk jana semula pautan. |
| **"Aset Tidak Tersedia"** | Aset habis ditempah pada tarikh tersebut. | Pilih tarikh lain atau hubungi Unit Aset. |
| **"AI tidak dapat menjawab"** | Pembantu AI gagal memproses soalan kompleks atau pelayan sibuk. | Sila taip soalan dengan lebih ringkas atau hubungi talian manusia. |

### 5.2. Bantuan Helpdesk
Jika masalah berlanjutan, sila laporkan ralat melalui fungsi **Chatbot** (taip "Masalah Teknikal") atau hubungi talian **1234**. Sila nyatakan Mesej Ralat yang terpapar untuk tindakan pantas pasukan teknikal.
