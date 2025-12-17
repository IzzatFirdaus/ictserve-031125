# Panduan Penyelenggaraan Sistem ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Sistem**: ICTServe - Portal Perkhidmatan ICT MOTAC  
**Peranan**: Pentadbir Sistem

---

## Pengenalan

Panduan ini menerangkan prosedur penyelenggaraan rutin dan kecemasan untuk sistem ICTServe v3.6.0. Penyelenggaraan yang konsisten memastikan sistem berfungsi dengan optimum dan selamat.

---

## Bahagian 1: Penyelenggaraan Harian

### 1.1 Pemeriksaan Status Sistem

Jalankan pemeriksaan berikut setiap hari:

```bash
# Semak status perkhidmatan
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status redis-server
sudo systemctl status ictserve-queue
sudo systemctl status ictserve-reverb
```

### 1.2 Pemeriksaan Log Ralat

```bash
# Log aplikasi Laravel
tail -100 /var/www/ictserve/storage/logs/laravel.log | grep -i error

# Log Nginx
tail -100 /var/log/nginx/ictserve_error.log

# Log MySQL
tail -100 /var/log/mysql/error.log
```

### 1.3 Pemeriksaan Ruang Storan

```bash
# Semak penggunaan cakera
df -h

# Semak saiz direktori log
du -sh /var/www/ictserve/storage/logs/
du -sh /var/log/nginx/
du -sh /var/log/mysql/
```

### 1.4 Pemeriksaan Baris Gilir

```bash
# Semak bilangan kerja dalam baris gilir
cd /var/www/ictserve
php artisan queue:monitor redis:default

# Semak kerja yang gagal
php artisan queue:failed
```

---

## Bahagian 2: Penyelenggaraan Mingguan

### 2.1 Pembersihan Log

```bash
# Padam log lama (lebih 7 hari)
find /var/www/ictserve/storage/logs -name "*.log" -mtime +7 -delete

# Putar log Nginx
sudo logrotate -f /etc/logrotate.d/nginx
```

### 2.2 Pembersihan Cache

```bash
cd /var/www/ictserve

# Kosongkan cache aplikasi
sudo -u www-data php artisan cache:clear

# Kosongkan cache sesi tamat tempoh
sudo -u www-data php artisan session:gc

# Kosongkan cache pandangan
sudo -u www-data php artisan view:clear
```

### 2.3 Optimumkan Pangkalan Data

```bash
# Optimumkan jadual MySQL
sudo mysql -u root -p -e "OPTIMIZE TABLE ictserve.users, ictserve.helpdesk_tickets, ictserve.loan_applications, ictserve.assets;"

# Analisis jadual
sudo mysql -u root -p -e "ANALYZE TABLE ictserve.users, ictserve.helpdesk_tickets, ictserve.loan_applications, ictserve.assets;"
```

### 2.4 Pembersihan Kerja Gagal

```bash
cd /var/www/ictserve

# Lihat kerja gagal
php artisan queue:failed

# Cuba semula kerja gagal
php artisan queue:retry all

# Padam kerja gagal lama (lebih 7 hari)
php artisan queue:prune-failed --hours=168
```

### 2.5 Kemaskini Keselamatan

```bash
# Semak kemaskini sistem
sudo apt update
sudo apt list --upgradable

# Pasang kemaskini keselamatan
sudo apt upgrade -y
```

---

## Bahagian 3: Penyelenggaraan Bulanan

### 3.1 Semakan Prestasi

Akses Laravel Pulse untuk semakan prestasi:

```
https://ictserve.motac.gov.my/pulse
```

Metrik yang perlu disemak:

- Purata masa respons
- Pertanyaan pangkalan data perlahan
- Penggunaan memori
- Penggunaan CPU

### 3.2 Semakan Keselamatan

```bash
# Semak kebergantungan PHP yang terdedah
cd /var/www/ictserve
composer audit

# Semak kebergantungan Node.js yang terdedah
npm audit
```

### 3.3 Semakan Sandaran

```bash
# Sahkan sandaran wujud
ls -la /var/backups/ictserve/

# Uji pemulihan sandaran (dalam persekitaran ujian)
# JANGAN jalankan dalam pengeluaran!
```

### 3.4 Semakan Sijil SSL

```bash
# Semak tarikh tamat sijil SSL
echo | openssl s_client -servername ictserve.motac.gov.my -connect ictserve.motac.gov.my:443 2>/dev/null | openssl x509 -noout -dates
```

### 3.5 Pembersihan Data Lama

```bash
cd /var/www/ictserve

# Padam token tamat tempoh
php artisan sanctum:prune-expired

# Padam sesi tamat tempoh
php artisan session:gc

# Padam notifikasi lama (lebih 90 hari)
php artisan notifications:prune --days=90
```

### 3.6 Kemaskini Kebergantungan

```bash
cd /var/www/ictserve

# Mod penyelenggaraan
sudo -u www-data php artisan down

# Kemaskini kebergantungan PHP
sudo -u www-data composer update --no-dev

# Kemaskini kebergantungan Node.js
sudo -u www-data npm update

# Bina semula aset
sudo -u www-data npm run build

# Jalankan migrasi jika ada
sudo -u www-data php artisan migrate --force

# Kosongkan dan jana semula cache
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Mulakan semula perkhidmatan
sudo systemctl restart ictserve-queue
sudo systemctl restart ictserve-reverb

# Tamatkan mod penyelenggaraan
sudo -u www-data php artisan up
```

---

## Bahagian 4: Penyelenggaraan Tahunan

### 4.1 Semakan Kapasiti

Semak dan rancang keperluan kapasiti untuk tahun hadapan:

- Pertumbuhan data pangkalan data
- Pertumbuhan fail storan
- Pertumbuhan pengguna
- Keperluan prestasi

### 4.2 Semakan Pematuhan

Semak pematuhan dengan:

- PDPA 2010
- WCAG 2.2 AA
- ISO/IEC 27001
- MyGOV Digital Service Standards

### 4.3 Semakan Dokumentasi

Kemaskini dokumentasi sistem:

- Manual pengguna
- Panduan pentadbir
- Dokumentasi API
- Prosedur penyelenggaraan

### 4.4 Latihan Pemulihan Bencana

Jalankan latihan pemulihan bencana:

1. Simulasi kegagalan pelayan
2. Pemulihan dari sandaran
3. Failover ke pelayan sandaran
4. Dokumentasi masa pemulihan

---

## Bahagian 5: Prosedur Kecemasan

### 5.1 Sistem Tidak Boleh Diakses

**Langkah 1: Diagnosis**

```bash
# Semak status Nginx
sudo systemctl status nginx

# Semak status PHP-FPM
sudo systemctl status php8.2-fpm

# Semak log ralat
tail -50 /var/log/nginx/ictserve_error.log
tail -50 /var/www/ictserve/storage/logs/laravel.log
```

**Langkah 2: Pemulihan**

```bash
# Mulakan semula perkhidmatan
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Jika masih gagal, semak konfigurasi
sudo nginx -t
```

### 5.2 Pangkalan Data Tidak Boleh Diakses

**Langkah 1: Diagnosis**

```bash
# Semak status MySQL
sudo systemctl status mysql

# Semak log ralat
tail -50 /var/log/mysql/error.log

# Cuba sambung secara manual
mysql -u ictserve -p -e "SELECT 1;"
```

**Langkah 2: Pemulihan**

```bash
# Mulakan semula MySQL
sudo systemctl restart mysql

# Jika masih gagal, semak ruang cakera
df -h

# Semak proses MySQL
sudo mysqladmin processlist
```

### 5.3 Baris Gilir Tidak Berfungsi

**Langkah 1: Diagnosis**

```bash
# Semak status perkhidmatan
sudo systemctl status ictserve-queue

# Semak log
sudo journalctl -u ictserve-queue -n 50

# Semak sambungan Redis
redis-cli ping
```

**Langkah 2: Pemulihan**

```bash
# Mulakan semula perkhidmatan baris gilir
sudo systemctl restart ictserve-queue

# Jika Redis bermasalah
sudo systemctl restart redis-server
```

### 5.4 WebSocket Tidak Berfungsi

**Langkah 1: Diagnosis**

```bash
# Semak status Reverb
sudo systemctl status ictserve-reverb

# Semak port
sudo netstat -tlnp | grep 8080

# Semak log
sudo journalctl -u ictserve-reverb -n 50
```

**Langkah 2: Pemulihan**

```bash
# Mulakan semula Reverb
sudo systemctl restart ictserve-reverb
```

### 5.5 Ruang Cakera Penuh

**Langkah 1: Diagnosis**

```bash
# Semak penggunaan cakera
df -h

# Cari fail besar
sudo find / -type f -size +100M -exec ls -lh {} \;

# Semak direktori besar
sudo du -sh /var/log/*
sudo du -sh /var/www/ictserve/storage/*
```

**Langkah 2: Pemulihan**

```bash
# Padam log lama
sudo find /var/log -name "*.gz" -mtime +7 -delete
sudo find /var/www/ictserve/storage/logs -name "*.log" -mtime +3 -delete

# Kosongkan cache
cd /var/www/ictserve
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
```

---

## Bahagian 6: Pemantauan Automatik

### 6.1 Skrip Pemantauan

Cipta skrip `/opt/scripts/monitor-ictserve.sh`:

```bash
#!/bin/bash

# Konfigurasi
EMAIL="ict-alert@motac.gov.my"
THRESHOLD_DISK=80
THRESHOLD_MEMORY=80
THRESHOLD_CPU=80

# Fungsi hantar amaran
send_alert() {
    echo "$1" | mail -s "AMARAN ICTServe: $2" $EMAIL
}

# Semak ruang cakera
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt $THRESHOLD_DISK ]; then
    send_alert "Penggunaan cakera: ${DISK_USAGE}%" "Ruang Cakera Kritikal"
fi

# Semak memori
MEMORY_USAGE=$(free | grep Mem | awk '{print int($3/$2 * 100)}')
if [ $MEMORY_USAGE -gt $THRESHOLD_MEMORY ]; then
    send_alert "Penggunaan memori: ${MEMORY_USAGE}%" "Memori Kritikal"
fi

# Semak perkhidmatan
for SERVICE in nginx php8.2-fpm mysql redis-server ictserve-queue ictserve-reverb; do
    if ! systemctl is-active --quiet $SERVICE; then
        send_alert "Perkhidmatan $SERVICE tidak aktif" "Perkhidmatan Gagal"
    fi
done

# Semak laman web
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://ictserve.motac.gov.my)
if [ $HTTP_CODE -ne 200 ]; then
    send_alert "Laman web tidak boleh diakses (HTTP $HTTP_CODE)" "Laman Web Gagal"
fi
```

Jadualkan pemantauan setiap 5 minit:

```bash
sudo crontab -e
```

Tambah:

```cron
*/5 * * * * /opt/scripts/monitor-ictserve.sh
```

### 6.2 Pemantauan Laravel Pulse

Laravel Pulse menyediakan pemantauan masa nyata:

- **Permintaan** - Masa respons dan kadar ralat
- **Pertanyaan** - Pertanyaan pangkalan data perlahan
- **Pengecualian** - Ralat aplikasi
- **Baris Gilir** - Status kerja baris gilir
- **Cache** - Kadar hit/miss cache

Akses di: `https://ictserve.motac.gov.my/pulse`

---

## Bahagian 7: Senarai Semak Penyelenggaraan

### Senarai Semak Harian

- [ ] Semak status semua perkhidmatan
- [ ] Semak log ralat
- [ ] Semak ruang storan
- [ ] Semak baris gilir

### Senarai Semak Mingguan

- [ ] Pembersihan log lama
- [ ] Pembersihan cache
- [ ] Optimumkan pangkalan data
- [ ] Semak kerja gagal
- [ ] Kemaskini keselamatan

### Senarai Semak Bulanan

- [ ] Semakan prestasi (Laravel Pulse)
- [ ] Semakan keselamatan (audit kebergantungan)
- [ ] Sahkan sandaran
- [ ] Semak sijil SSL
- [ ] Pembersihan data lama
- [ ] Kemaskini kebergantungan

### Senarai Semak Tahunan

- [ ] Semakan kapasiti
- [ ] Semakan pematuhan
- [ ] Kemaskini dokumentasi
- [ ] Latihan pemulihan bencana

---

## Bahagian 8: Hubungi Sokongan

### Sokongan Dalaman

- **E-mel**: <ict-support@motac.gov.my>
- **Telefon**: 03-8000 8000 ext. 1235
- **Waktu**: Isnin - Jumaat, 8:30 pagi - 5:30 petang

### Sokongan Kecemasan (24/7)

- **Telefon**: 03-8000 8000 ext. 1999
- **E-mel**: <ict-emergency@motac.gov.my>

---

**Dokumen ini adalah sebahagian daripada sistem ICTServe v3.6.0**  
**Pematuhan**: D00-D17, ISO/IEC 27001, PDPA 2010
