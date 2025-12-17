# Panduan Pemantauan Sistem ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Sistem**: ICTServe - Portal Perkhidmatan ICT MOTAC

---

## Pengenalan

Panduan ini menerangkan sistem pemantauan dan amaran untuk ICTServe v3.6.0. Pemantauan yang berkesan memastikan sistem berfungsi dengan optimum dan masalah dapat dikesan awal.

---

## Bahagian 1: Gambaran Keseluruhan Pemantauan

### 1.1 Komponen Pemantauan

| Komponen | Alat | Tujuan |
|----------|------|--------|
| **Prestasi Aplikasi** | Laravel Pulse | Masa respons, pertanyaan DB, pengecualian |
| **Penyahpepijatan** | Laravel Telescope | Log, permintaan, pertanyaan (Superuser) |
| **Infrastruktur** | Skrip Bash | CPU, memori, cakera, perkhidmatan |
| **Log** | Laravel Log + Nginx | Ralat aplikasi dan pelayan web |
| **Baris Gilir** | Laravel Queue Monitor | Status kerja baris gilir |

### 1.2 Tahap Amaran

| Tahap | Warna | Tindakan |
|-------|-------|----------|
| **Kritikal** | 🔴 Merah | Tindakan segera diperlukan |
| **Amaran** | 🟡 Kuning | Perhatian diperlukan dalam 24 jam |
| **Maklumat** | 🔵 Biru | Untuk rujukan sahaja |
| **Normal** | 🟢 Hijau | Sistem berfungsi normal |

---

## Bahagian 2: Laravel Pulse

### 2.1 Akses Laravel Pulse

**URL**: `https://ictserve.motac.gov.my/pulse`

**Kebenaran**: Admin dan Superuser sahaja

### 2.2 Metrik Yang Dipantau

#### Permintaan (Requests)

| Metrik | Sasaran | Amaran | Kritikal |
|--------|---------|--------|----------|
| Purata masa respons | < 200ms | > 500ms | > 1000ms |
| Permintaan per minit | - | > 1000 | > 2000 |
| Kadar ralat | < 1% | > 5% | > 10% |

#### Pertanyaan Pangkalan Data (Queries)

| Metrik | Sasaran | Amaran | Kritikal |
|--------|---------|--------|----------|
| Purata masa pertanyaan | < 50ms | > 100ms | > 500ms |
| Pertanyaan perlahan | 0 | > 10/jam | > 50/jam |
| Pertanyaan N+1 | 0 | > 5 | > 20 |

#### Pengecualian (Exceptions)

| Metrik | Sasaran | Amaran | Kritikal |
|--------|---------|--------|----------|
| Pengecualian per jam | 0 | > 10 | > 50 |
| Pengecualian unik | 0 | > 5 | > 20 |

#### Baris Gilir (Queues)

| Metrik | Sasaran | Amaran | Kritikal |
|--------|---------|--------|----------|
| Kerja menunggu | < 100 | > 500 | > 1000 |
| Kerja gagal | 0 | > 10 | > 50 |
| Masa pemprosesan | < 30s | > 60s | > 120s |

### 2.3 Papan Pemuka Pulse

Papan pemuka Pulse memaparkan:

1. **Gambaran Keseluruhan** - Statistik ringkas sistem
2. **Permintaan** - Graf masa respons dan kadar ralat
3. **Pertanyaan** - Pertanyaan perlahan dan N+1
4. **Pengecualian** - Senarai pengecualian terkini
5. **Baris Gilir** - Status kerja baris gilir
6. **Cache** - Kadar hit/miss cache
7. **Pengguna** - Aktiviti pengguna aktif

---

## Bahagian 3: Laravel Telescope (Superuser)

### 3.1 Akses Laravel Telescope

**URL**: `https://ictserve.motac.gov.my/telescope`

**Kebenaran**: Superuser sahaja

### 3.2 Ciri-ciri Telescope

#### Permintaan (Requests)

- Semua permintaan HTTP masuk
- Header, parameter, dan respons
- Masa pemprosesan

#### Pertanyaan (Queries)

- Semua pertanyaan SQL
- Masa pelaksanaan
- Binding dan hasil

#### Pengecualian (Exceptions)

- Stack trace lengkap
- Konteks permintaan
- Pengguna yang terjejas

#### Log

- Semua entri log Laravel
- Tahap log (debug, info, warning, error)
- Konteks tambahan

#### E-mel (Mail)

- Semua e-mel yang dihantar
- Penerima dan kandungan
- Status penghantaran

#### Notifikasi

- Semua notifikasi yang dihantar
- Saluran (e-mel, pangkalan data, WebSocket)
- Status penghantaran

#### Baris Gilir (Jobs)

- Semua kerja baris gilir
- Status (menunggu, berjalan, selesai, gagal)
- Masa pemprosesan

---

## Bahagian 4: Pemantauan Infrastruktur

### 4.1 Skrip Pemantauan

Cipta fail `/opt/scripts/monitor-ictserve.sh`:

```bash
#!/bin/bash

# Konfigurasi
EMAIL="ict-alert@motac.gov.my"
SLACK_WEBHOOK="https://hooks.slack.com/services/xxx"
LOG_FILE="/var/log/ictserve-monitor.log"

# Ambang
THRESHOLD_DISK=80
THRESHOLD_MEMORY=80
THRESHOLD_CPU=80
THRESHOLD_LOAD=4

# Fungsi log
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> $LOG_FILE
}

# Fungsi hantar amaran
send_alert() {
    local level=$1
    local title=$2
    local message=$3
    
    # Log
    log_message "[$level] $title: $message"
    
    # E-mel
    echo "$message" | mail -s "[$level] ICTServe: $title" $EMAIL
    
    # Slack (pilihan)
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -s -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"[$level] ICTServe: $title\n$message\"}" \
            $SLACK_WEBHOOK
    fi
}

# Semak ruang cakera
check_disk() {
    DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
    if [ $DISK_USAGE -gt $THRESHOLD_DISK ]; then
        send_alert "KRITIKAL" "Ruang Cakera" "Penggunaan cakera: ${DISK_USAGE}%"
    fi
}

# Semak memori
check_memory() {
    MEMORY_USAGE=$(free | grep Mem | awk '{print int($3/$2 * 100)}')
    if [ $MEMORY_USAGE -gt $THRESHOLD_MEMORY ]; then
        send_alert "AMARAN" "Memori" "Penggunaan memori: ${MEMORY_USAGE}%"
    fi
}

# Semak CPU
check_cpu() {
    CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print int($2)}')
    if [ $CPU_USAGE -gt $THRESHOLD_CPU ]; then
        send_alert "AMARAN" "CPU" "Penggunaan CPU: ${CPU_USAGE}%"
    fi
}

# Semak beban sistem
check_load() {
    LOAD=$(cat /proc/loadavg | awk '{print int($1)}')
    if [ $LOAD -gt $THRESHOLD_LOAD ]; then
        send_alert "AMARAN" "Beban Sistem" "Beban sistem: $LOAD"
    fi
}

# Semak perkhidmatan
check_services() {
    SERVICES="nginx php8.2-fpm mysql redis-server ictserve-queue ictserve-reverb"
    
    for SERVICE in $SERVICES; do
        if ! systemctl is-active --quiet $SERVICE; then
            send_alert "KRITIKAL" "Perkhidmatan Gagal" "Perkhidmatan $SERVICE tidak aktif"
            
            # Cuba mulakan semula
            systemctl restart $SERVICE
            sleep 5
            
            if systemctl is-active --quiet $SERVICE; then
                send_alert "MAKLUMAT" "Perkhidmatan Dipulihkan" "Perkhidmatan $SERVICE berjaya dimulakan semula"
            fi
        fi
    done
}

# Semak laman web
check_website() {
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -m 10 https://ictserve.motac.gov.my)
    
    if [ $HTTP_CODE -ne 200 ]; then
        send_alert "KRITIKAL" "Laman Web Gagal" "Laman web tidak boleh diakses (HTTP $HTTP_CODE)"
    fi
}

# Semak sijil SSL
check_ssl() {
    EXPIRY=$(echo | openssl s_client -servername ictserve.motac.gov.my -connect ictserve.motac.gov.my:443 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2)
    EXPIRY_EPOCH=$(date -d "$EXPIRY" +%s)
    NOW_EPOCH=$(date +%s)
    DAYS_LEFT=$(( ($EXPIRY_EPOCH - $NOW_EPOCH) / 86400 ))
    
    if [ $DAYS_LEFT -lt 30 ]; then
        send_alert "AMARAN" "Sijil SSL" "Sijil SSL akan tamat dalam $DAYS_LEFT hari"
    fi
    
    if [ $DAYS_LEFT -lt 7 ]; then
        send_alert "KRITIKAL" "Sijil SSL" "Sijil SSL akan tamat dalam $DAYS_LEFT hari!"
    fi
}

# Semak baris gilir
check_queue() {
    cd /var/www/ictserve
    FAILED_JOBS=$(php artisan queue:failed --no-interaction 2>/dev/null | wc -l)
    
    if [ $FAILED_JOBS -gt 10 ]; then
        send_alert "AMARAN" "Baris Gilir" "$FAILED_JOBS kerja gagal dalam baris gilir"
    fi
}

# Jalankan semua pemeriksaan
main() {
    log_message "Memulakan pemeriksaan sistem..."
    
    check_disk
    check_memory
    check_cpu
    check_load
    check_services
    check_website
    check_ssl
    check_queue
    
    log_message "Pemeriksaan sistem selesai"
}

main
```

### 4.2 Jadualkan Pemantauan

```bash
# Edit crontab
sudo crontab -e

# Tambah baris berikut
*/5 * * * * /opt/scripts/monitor-ictserve.sh
```

---

## Bahagian 5: Pemantauan Log

### 5.1 Log Aplikasi Laravel

**Lokasi**: `/var/www/ictserve/storage/logs/laravel.log`

**Perintah Pemantauan**:

```bash
# Lihat log terkini
tail -f /var/www/ictserve/storage/logs/laravel.log

# Cari ralat
grep -i error /var/www/ictserve/storage/logs/laravel.log | tail -50

# Cari pengecualian
grep -i exception /var/www/ictserve/storage/logs/laravel.log | tail -50
```

### 5.2 Log Nginx

**Lokasi**:

- Akses: `/var/log/nginx/ictserve_access.log`
- Ralat: `/var/log/nginx/ictserve_error.log`

**Perintah Pemantauan**:

```bash
# Lihat ralat terkini
tail -f /var/log/nginx/ictserve_error.log

# Analisis kod status
awk '{print $9}' /var/log/nginx/ictserve_access.log | sort | uniq -c | sort -rn

# Cari ralat 500
grep " 500 " /var/log/nginx/ictserve_access.log | tail -20
```

### 5.3 Log Perkhidmatan

```bash
# Log baris gilir
sudo journalctl -u ictserve-queue -f

# Log Reverb
sudo journalctl -u ictserve-reverb -f

# Log MySQL
tail -f /var/log/mysql/error.log

# Log Redis
tail -f /var/log/redis/redis-server.log
```

---

## Bahagian 6: Papan Pemuka Pemantauan

### 6.1 Metrik Utama

Papan pemuka pemantauan harus memaparkan:

| Metrik | Sumber | Kekerapan Kemas Kini |
|--------|--------|---------------------|
| Status Perkhidmatan | systemctl | 1 minit |
| Penggunaan CPU | /proc/stat | 1 minit |
| Penggunaan Memori | free | 1 minit |
| Penggunaan Cakera | df | 5 minit |
| Masa Respons | Laravel Pulse | Masa nyata |
| Kadar Ralat | Laravel Pulse | Masa nyata |
| Kerja Baris Gilir | Laravel Queue | 1 minit |
| Sambungan WebSocket | Reverb | Masa nyata |

### 6.2 Visualisasi

Gunakan alat seperti:

- **Grafana** - Papan pemuka metrik
- **Prometheus** - Pengumpulan metrik
- **Laravel Pulse** - Pemantauan aplikasi terbina dalam

---

## Bahagian 7: Prosedur Respons Amaran

### 7.1 Amaran Kritikal (Merah)

**Masa Respons**: Segera (< 15 minit)

**Prosedur**:

1. Sahkan amaran adalah sah
2. Kenal pasti punca masalah
3. Laksanakan pemulihan segera
4. Maklumkan pihak berkepentingan
5. Dokumentasikan insiden

### 7.2 Amaran (Kuning)

**Masa Respons**: Dalam 24 jam

**Prosedur**:

1. Sahkan amaran adalah sah
2. Analisis trend dan punca
3. Rancang tindakan pembetulan
4. Laksanakan pembetulan
5. Pantau keputusan

### 7.3 Maklumat (Biru)

**Masa Respons**: Semasa penyelenggaraan berkala

**Prosedur**:

1. Rekod untuk rujukan
2. Analisis trend
3. Pertimbangkan pengoptimuman

---

## Bahagian 8: Laporan Pemantauan

### 8.1 Laporan Harian

Laporan harian harus mengandungi:

- Ringkasan status sistem
- Amaran yang diterima
- Metrik prestasi utama
- Isu yang diselesaikan

### 8.2 Laporan Mingguan

Laporan mingguan harus mengandungi:

- Trend prestasi
- Analisis kapasiti
- Isu berulang
- Cadangan penambahbaikan

### 8.3 Laporan Bulanan

Laporan bulanan harus mengandungi:

- Statistik ketersediaan (uptime)
- Analisis insiden
- Perbandingan dengan SLA
- Perancangan kapasiti

---

## Bahagian 9: Hubungi Sokongan

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
