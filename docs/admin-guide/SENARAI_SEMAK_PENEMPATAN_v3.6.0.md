# Senarai Semak Penempatan ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Sistem**: ICTServe - Portal Perkhidmatan ICT MOTAC

---

## Pengenalan

Senarai semak ini memastikan penempatan sistem ICTServe v3.6.0 dilaksanakan dengan lengkap dan selamat. Tandakan setiap item selepas selesai.

---

## Bahagian 1: Pra-Penempatan

### 1.1 Keperluan Pelayan

- [ ] Pelayan memenuhi keperluan minimum (4 teras CPU, 8GB RAM, 100GB SSD)
- [ ] Sistem operasi Ubuntu 22.04 LTS atau lebih tinggi dipasang
- [ ] Akses SSH dikonfigurasi dengan kunci awam
- [ ] Firewall dikonfigurasi (port 80, 443, 8080)

### 1.2 Perisian Asas

- [ ] PHP 8.2.12 atau lebih tinggi dipasang
- [ ] Sambungan PHP yang diperlukan dipasang
- [ ] MySQL 8.0 atau lebih tinggi dipasang
- [ ] Redis 7.0 atau lebih tinggi dipasang
- [ ] Nginx 1.24 atau lebih tinggi dipasang
- [ ] Node.js 20 LTS dipasang
- [ ] Composer 2.6 atau lebih tinggi dipasang

### 1.3 Sijil SSL

- [ ] Sijil SSL diperoleh untuk domain
- [ ] Sijil SSL dipasang di pelayan
- [ ] Konfigurasi HTTPS disahkan

### 1.4 Pangkalan Data

- [ ] Pangkalan data MySQL dicipta
- [ ] Pengguna pangkalan data dicipta dengan kebenaran yang sesuai
- [ ] Sambungan pangkalan data diuji

### 1.5 Redis

- [ ] Redis dipasang dan berjalan
- [ ] Kata laluan Redis dikonfigurasi
- [ ] Sambungan Redis diuji

---

## Bahagian 2: Penempatan Aplikasi

### 2.1 Kod Sumber

- [ ] Direktori aplikasi dicipta (/var/www/ictserve)
- [ ] Kod sumber dimuat turun dari repositori
- [ ] Kebenaran fail ditetapkan dengan betul

### 2.2 Kebergantungan

- [ ] Kebergantungan PHP dipasang (composer install --no-dev)
- [ ] Kebergantungan Node.js dipasang (npm ci)
- [ ] Aset frontend dibina (npm run build)

### 2.3 Konfigurasi Persekitaran

- [ ] Fail .env dicipta dari .env.example
- [ ] APP_ENV ditetapkan kepada "production"
- [ ] APP_DEBUG ditetapkan kepada "false"
- [ ] APP_URL ditetapkan dengan betul
- [ ] Kunci aplikasi dijana (php artisan key:generate)

### 2.4 Konfigurasi Pangkalan Data

- [ ] DB_CONNECTION ditetapkan kepada "mysql"
- [ ] DB_HOST, DB_PORT, DB_DATABASE dikonfigurasi
- [ ] DB_USERNAME, DB_PASSWORD dikonfigurasi
- [ ] Sambungan pangkalan data diuji

### 2.5 Konfigurasi Cache dan Sesi

- [ ] CACHE_DRIVER ditetapkan kepada "redis"
- [ ] SESSION_DRIVER ditetapkan kepada "redis"
- [ ] QUEUE_CONNECTION ditetapkan kepada "redis"
- [ ] REDIS_HOST, REDIS_PASSWORD dikonfigurasi

### 2.6 Konfigurasi E-mel

- [ ] MAIL_MAILER dikonfigurasi
- [ ] MAIL_HOST, MAIL_PORT dikonfigurasi
- [ ] MAIL_USERNAME, MAIL_PASSWORD dikonfigurasi
- [ ] MAIL_FROM_ADDRESS, MAIL_FROM_NAME dikonfigurasi
- [ ] Penghantaran e-mel diuji

### 2.7 Konfigurasi WebSocket (Reverb)

- [ ] REVERB_APP_ID dikonfigurasi
- [ ] REVERB_APP_KEY dikonfigurasi
- [ ] REVERB_APP_SECRET dikonfigurasi
- [ ] REVERB_HOST, REVERB_PORT dikonfigurasi

---

## Bahagian 3: Migrasi dan Data

### 3.1 Migrasi Pangkalan Data

- [ ] Migrasi dijalankan (php artisan migrate --force)
- [ ] Semua jadual dicipta dengan betul
- [ ] Indeks pangkalan data disahkan

### 3.2 Data Awal (Seeder)

- [ ] Seeder dijalankan jika diperlukan
- [ ] Pengguna pentadbir awal dicipta
- [ ] Data rujukan dimuat

### 3.3 Pengoptimuman

- [ ] Cache konfigurasi dijana (php artisan config:cache)
- [ ] Cache laluan dijana (php artisan route:cache)
- [ ] Cache pandangan dijana (php artisan view:cache)
- [ ] Cache acara dijana (php artisan event:cache)

---

## Bahagian 4: Konfigurasi Pelayan Web

### 4.1 Nginx

- [ ] Konfigurasi laman Nginx dicipta
- [ ] Konfigurasi SSL dikonfigurasi
- [ ] Header keselamatan ditambah
- [ ] Gzip dikonfigurasi
- [ ] Laman diaktifkan (symlink ke sites-enabled)
- [ ] Konfigurasi Nginx diuji (nginx -t)
- [ ] Nginx dimuat semula

### 4.2 PHP-FPM

- [ ] PHP-FPM dikonfigurasi untuk www-data
- [ ] Pool PHP-FPM dioptimumkan
- [ ] PHP-FPM berjalan

---

## Bahagian 5: Perkhidmatan Latar Belakang

### 5.1 Baris Gilir (Queue Worker)

- [ ] Fail perkhidmatan systemd dicipta
- [ ] Perkhidmatan diaktifkan (systemctl enable)
- [ ] Perkhidmatan dimulakan (systemctl start)
- [ ] Status perkhidmatan disahkan

### 5.2 WebSocket (Reverb)

- [ ] Fail perkhidmatan systemd dicipta
- [ ] Perkhidmatan diaktifkan
- [ ] Perkhidmatan dimulakan
- [ ] Sambungan WebSocket diuji

### 5.3 Penjadual (Scheduler)

- [ ] Cron job ditambah untuk penjadual Laravel
- [ ] Penjadual berjalan dengan betul

---

## Bahagian 6: Keselamatan

### 6.1 Kebenaran Fail

- [ ] Pemilik fail ditetapkan kepada www-data
- [ ] Kebenaran direktori ditetapkan kepada 755
- [ ] Kebenaran storage dan cache ditetapkan kepada 775
- [ ] Fail .env dilindungi (chmod 600)

### 6.2 Firewall

- [ ] UFW atau firewall lain dikonfigurasi
- [ ] Hanya port yang diperlukan dibuka (80, 443, 22)
- [ ] Port 8080 (Reverb) dilindungi

### 6.3 Keselamatan Aplikasi

- [ ] APP_DEBUG ditetapkan kepada false
- [ ] Kunci aplikasi dijana dan selamat
- [ ] Kata laluan pangkalan data kuat
- [ ] Kata laluan Redis kuat

---

## Bahagian 7: Pemantauan

### 7.1 Log

- [ ] Log aplikasi boleh diakses
- [ ] Log Nginx dikonfigurasi
- [ ] Putaran log dikonfigurasi

### 7.2 Pemantauan Prestasi

- [ ] Laravel Pulse dikonfigurasi
- [ ] Akses Pulse dihadkan kepada admin/superuser
- [ ] Metrik prestasi boleh dilihat

### 7.3 Pemantauan Sistem

- [ ] Skrip pemantauan dipasang
- [ ] Amaran e-mel dikonfigurasi
- [ ] Pemantauan dijadualkan (cron)

---

## Bahagian 8: Sandaran

### 8.1 Sandaran Pangkalan Data

- [ ] Skrip sandaran pangkalan data dicipta
- [ ] Sandaran dijadualkan (harian)
- [ ] Lokasi sandaran dikonfigurasi
- [ ] Sandaran diuji

### 8.2 Sandaran Fail

- [ ] Skrip sandaran fail dicipta
- [ ] Direktori storage disandarkan
- [ ] Sandaran dijadualkan

### 8.3 Pengekalan Sandaran

- [ ] Polisi pengekalan ditetapkan (30 hari)
- [ ] Pembersihan sandaran lama dijadualkan

---

## Bahagian 9: Ujian Akhir

### 9.1 Ujian Fungsi

- [ ] Halaman utama boleh diakses
- [ ] Borang tetamu berfungsi
- [ ] Pendaftaran pengguna berfungsi
- [ ] Log masuk berfungsi
- [ ] Tiket helpdesk boleh dihantar
- [ ] Permohonan pinjaman boleh dihantar
- [ ] Panel pentadbiran boleh diakses

### 9.2 Ujian E-mel

- [ ] E-mel pengesahan dihantar
- [ ] E-mel notifikasi dihantar
- [ ] E-mel kelulusan dihantar

### 9.3 Ujian WebSocket

- [ ] Notifikasi masa nyata berfungsi
- [ ] Kemas kini status masa nyata berfungsi

### 9.4 Ujian Prestasi

- [ ] Masa muat halaman < 3 saat
- [ ] Core Web Vitals memenuhi sasaran
- [ ] Tiada ralat dalam log

---

## Bahagian 10: Dokumentasi

### 10.1 Dokumentasi Penempatan

- [ ] Prosedur penempatan didokumenkan
- [ ] Konfigurasi pelayan didokumenkan
- [ ] Kata laluan disimpan dengan selamat

### 10.2 Dokumentasi Pemulihan

- [ ] Prosedur pemulihan didokumenkan
- [ ] Prosedur rollback didokumenkan
- [ ] Kenalan kecemasan didokumenkan

---

## Pengesahan Akhir

| Item | Status | Tarikh | Disahkan Oleh |
|------|--------|--------|---------------|
| Pra-Penempatan | ☐ | | |
| Penempatan Aplikasi | ☐ | | |
| Migrasi dan Data | ☐ | | |
| Konfigurasi Pelayan | ☐ | | |
| Perkhidmatan Latar Belakang | ☐ | | |
| Keselamatan | ☐ | | |
| Pemantauan | ☐ | | |
| Sandaran | ☐ | | |
| Ujian Akhir | ☐ | | |
| Dokumentasi | ☐ | | |

---

**Pengesahan Penempatan**

Saya mengesahkan bahawa semua item dalam senarai semak ini telah dilengkapkan dan sistem ICTServe v3.6.0 sedia untuk pengeluaran.

| | |
|---|---|
| **Nama** | |
| **Jawatan** | |
| **Tarikh** | |
| **Tandatangan** | |

---

**Dokumen ini adalah sebahagian daripada sistem ICTServe v3.6.0**  
**Pematuhan**: D00-D17, ISO/IEC 27001, PDPA 2010
