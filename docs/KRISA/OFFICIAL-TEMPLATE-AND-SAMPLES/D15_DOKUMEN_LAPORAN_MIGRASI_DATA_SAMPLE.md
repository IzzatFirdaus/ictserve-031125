# D15: Dokumen Laporan Migrasi Data (Data Migration Report)

**Judul Dokumen:** Dokumen Laporan Migrasi Data (LMD)  
**Rujukan:** SMPBM / LMD  
**Mukasurat:** Vii

---

## Sumber Rujukan

Sumber rujukan yang digunakan dalam dokumen ini adalah:

1. Panduan Keruteraan Sistem Aplikasi Sektor Awam (KRISA) 2019
2. Pelan Pembangunan Sistem
3. Spesifikasi Keperluan Bisnes
4. Spesifikasi Keperluan Sistem
5. Spesifikasi Rekabentuk Sistem
6. Dokumen Spesifikasi Rekabentuk Sistem bagi Sistem Tempahan Bilik Mesyuarat

---

## 1. Objektif Laporan Migrasi Data

Laporan Migrasi Data bertujuan untuk:

- Mendokumentasikan proses perpindahan data dari sistem lama ke sistem baru
- Memastikan integriti dan konsistensi data semasa proses migrasi
- Menyediakan bukti bahawa semua data telah berjaya dimigrasikan
- Mendokumentasikan isu-isu dan penyelesaian yang terlibat dalam migrasi
- Memberikan panduan untuk proses rollback jika diperlukan

---

## 2. Skop Migrasi Data

### 2.1 Data yang Dimigrasikan

Dokumen ini merangkumi migrasi data berikut:

- Data pengguna sistem
- Data maklumat bilik
- Data tempahan
- Data konfigurasi sistem
- Data log sistem

### 2.2 Data yang Tidak Dimigrasikan

Data berikut tidak dimigrasikan:

- Data log transaksi bersejarah (lebih dari 2 tahun)
- Data sementara sistem lama
- Data cache yang tidak diperlukan

---

## 3. Strategi Migrasi Data

### 3.1 Fasa Migrasi

Migrasi data dilaksanakan dalam tiga fasa:

#### Fasa 1: Penyediaan

- Persiapan persekitaran target
- Validasi skema pangkalan data
- Pembuatan rekod sandaran
- Ujian ekstrak-transformasi-muatan (ETL)

#### Fasa 2: Pelaksanaan

- Pelaksanaan skrip migrasi
- Pengesahan data dalam sistem target
- Penyesuaian jika diperlukan

#### Fasa 3: Pengesahan

- Pengesahan lengkap data dimigrasikan
- Pengujian fungsionalitas sistem
- Pengesahan oleh pengguna akhir

### 3.2 Jadual Migrasi

| Fasa | Aktiviti | Tarikh Mula | Tarikh Akhir | Durasi |
|------|----------|-------------|--------------|--------|
| 1 | Penyediaan | - | - | 5 hari |
| 2 | Pelaksanaan | - | - | 3 hari |
| 3 | Pengesahan | - | - | 2 hari |

---

## 4. Rancangan Teknikal Migrasi

### 4.1 Sumber Data

- **Sistem Sumber:** Sistem Lama Tempahan Bilik Mesyuarat
- **Format Data:** SQL Database (.mdf, .ldf)
- **Kuantiti Data:** Lebih kurang 50 MB
- **Jenis Koneksi:** Direct Database Connection

### 4.2 Sistem Sasaran

- **Sistem Sasaran:** Sistem Baru Tempahan Bilik Mesyuarat
- **Platform:** Laravel PHP with MySQL
- **Format Database:** MySQL 8.0+
- **Persekitaran:** Production Server

### 4.3 Alat Migrasi

Alat dan teknologi yang digunakan:

```
┌─────────────────────────────────────────┐
│      Alat dan Teknologi Migrasi        │
├─────────────────────────────────────────┤
│ 1. SQL Migration Tools                  │
│ 2. Laravel Migration Framework           │
│ 3. ETL Scripts (PHP/Python)             │
│ 4. Data Validation Tools                │
│ 5. Backup & Recovery Tools              │
└─────────────────────────────────────────┘
```

---

## 5. Proses Migrasi Data Terperinci

### 5.1 Ekstrak Data Sumber

**Langkah 1:** Kumpul Data dari Sistem Lama

```
Query: SELECT * FROM [Old_System].[dbo].[Users]
       SELECT * FROM [Old_System].[dbo].[Rooms]
       SELECT * FROM [Old_System].[dbo].[Bookings]
```

**Langkah 2:** Eksport ke Format Perantaraan

- Format: CSV/Excel atau SQL Script
- Lokasi: Staging Area
- Saiz Fail: Bergantung kepada jadual

### 5.2 Transformasi Data

**Langkah 1:** Data Cleansing

- Keluarkan rekod duplikat
- Isi nilai-nilai kosong dengan nilai lalai
- Tukar format tarikh dan masa

**Langkah 2:** Data Mapping

```
Old System Column  →  New System Column
UserId             →  user_id
UserName           →  username
UserEmail          →  email
CreatedDate        →  created_at
ModifiedDate       →  updated_at
```

**Langkah 3:** Validasi Logik

- Semak integriti rujukan asing (FK)
- Sahkan peraturan keunikan (Unique Constraints)
- Pengesahan bentuk (Format Validation)

### 5.3 Muatkan Data ke Sistem Baru

**Langkah 1:** Sediakan Persekitaran Target

- Bersihkan jadual sasaran
- Semulakan counter autoinkremen
- Kurangkan kekangan indeks sementara

**Langkah 2:** Muatkan Data

```sql
INSERT INTO users (user_id, username, email, created_at)
SELECT UserId, UserName, UserEmail, CreatedDate
FROM staging_users
WHERE is_valid = 1;
```

**Langkah 3:** Periksa Keputusan Muatan

- Bilangan rekod dimuat
- Masa pemprosesan
- Catatan ralat (Error Log)

---

## 6. Pengesahan dan Pengujian

### 6.1 Pengesahan Data

**Pemeriksaan Kuantiti:**

```
Jadual          | Rekod Sumber | Rekod Dimuat | Status
----------------|--------------|--------------|--------
Users           | 150          | 150          | ✓ OK
Rooms           | 25           | 25           | ✓ OK
Bookings        | 2,340        | 2,340        | ✓ OK
Room_Features   | 75           | 75           | ✓ OK
Users_Roles     | 180          | 180          | ✓ OK
```

**Pemeriksaan Integriti:**

- Sampel acak 10% data untuk semakan manual
- Sahkan perhubungan jadual
- Pengesahan rujukan asing

### 6.2 Pengujian Fungsional

**Pengujian Modul:**

1. Autentikasi Pengguna - LULUS ✓
2. Carian Bilik - LULUS ✓
3. Tempahan Bilik - LULUS ✓
4. Laporan Tempahan - LULUS ✓
5. Pengurusan Pengguna - LULUS ✓

**Pengujian Prestasi:**

```
Metrik Prestasi       | Sasaran    | Keputusan | Status
--------------------|------------|-----------|--------
Masa Muatan Data     | < 10 min   | 8 min     | ✓ OK
Masa Pertanyaan      | < 2 sec    | 1.5 sec   | ✓ OK
Kadar Kesalahan      | < 0.1%     | 0.05%     | ✓ OK
Ketersediaan Sistem  | 99.5%      | 99.7%     | ✓ OK
```

---

## 7. Pengurusan Risiko dan Kontingensi

### 7.1 Risiko yang Mungkin

| Risiko | Kemungkinan | Kesan | Penyelesaian |
|--------|------------|-------|-------------|
| Kehilangan Data | Rendah | Tinggi | Sandaran Penuh |
| Keruntuhan Sistem | Rendah | Tinggi | Rollback Plan |
| Ketidakpadanan Data | Sederhana | Sederhana | Validasi Lengkap |
| Ralat Transformasi | Sederhana | Sederhana | Pengujian ETL |

### 7.2 Rancangan Rollback

Jika migrasi gagal:

1. Pemulihan Sandaran Data Sistem Lama
2. Pemulihan Sistem Lama dari Titik Sandaran
3. Analisis Kegagalan
4. Rancang Semula Proses Migrasi
5. Percubaan Migrasi Semula

**Masa Pemulihan Objektif (RTO):** 2 jam  
**Titik Pemulihan Objektif (RPO):** 30 minit

---

## 8. Kesimpulan

Laporan Migrasi Data ini menunjukkan bahawa:

✓ Semua data telah berjaya dimigrasikan dari sistem lama ke sistem baru
✓ Integriti data telah disahkan melalui pelbagai pemeriksaan validasi
✓ Sistem baru berfungsi dengan baik dengan semua data dimigrasikan
✓ Pengguna akhir telah mengesahkan ketepatan data

**Status Keseluruhan:** BERJAYA SELESAI

---

## 9. Lampiran

### Lampiran A: Skrip Migrasi ETL

- `migrate_users.php`
- `migrate_rooms.php`
- `migrate_bookings.php`
- `validate_data.php`

### Lampiran B: Log Migrasi

- Tarikh Pelaksanaan: [Tarikh Migrasi]
- Bilangan Rekod Dimigrasikan: 2,770
- Masa Keseluruhan: 18 minit
- Catatan Ralat: Tiada

### Lampiran C: Pengesahan Pengguna
Disahkan oleh:

- Pentadbir Sistem: _________________ (Tandatangan)
- Pengurus Projek: _________________ (Tandatangan)
- Ketua Unit TI: _________________ (Tandatangan)

---

**Dokumen Dijana:** [Tarikh Semasa]  
**Versi:** 1.0  
**Status:** Selesai

---

*Dokumen ini adalah sebahagian daripada Panduan Keruteraan Sistem Aplikasi Sektor Awam (KRISA) 2019*
