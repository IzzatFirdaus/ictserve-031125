# Panduan Pelaksanaan Integrasi AI Ollama (Ollama AI Integration Deployment Guide)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 12 Disember 2025  
**Status:** Sedia untuk Pengeluaran  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Pematuhi:** D11 v3.6.0 Technical Design Documentation

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                               |
| -------------------- | --------------------------------------------------- |
| **Versi**            | 3.6.0                                               |
| **Sasaran Persekitaran** | Pengeluaran, Ujian, Pembangunan                |
| **Pematuhi**         | D11 v3.6.0 Technical Design Documentation          |
| **Bahasa**           | Bahasa Melayu dengan istilah teknikal dalam English |
| **Keperluan Sistem** | PHP 8.2.12, MySQL 8.0, Redis 7.0, Ollama Server   |

---

## Keperluan Sistem (System Requirements)

### Keperluan Minimum Perkakasan

| Komponen | Minimum | Disyorkan | Catatan |
|----------|---------|-----------|---------|
| **CPU** | 4 cores (Intel i5/AMD Ryzen 5) | 8 cores (Intel i7/AMD Ryzen 7) | Untuk pemprosesan AI Ollama |
| **RAM** | 16GB | 32GB | Model AI memerlukan 8-16GB |
| **Storage** | 100GB SSD | 500GB NVMe SSD | Untuk model AI dan data |
| **Network** | 100 Mbps | 1 Gbps | Untuk akses API dan WebSocket |

### Keperluan Perisian

| Perisian | Versi | Keperluan | Catatan |
|----------|-------|-----------|---------|
| **PHP** | 8.2.12+ | Wajib | Dengan ekstensi: curl, json, mbstring, xml, zip |
| **MySQL** | 8.0+ | Wajib | Dengan InnoDB engine |
| **Redis** | 7.0+ | Wajib | Untuk cache dan queue |
| **Nginx/Apache** | Latest | Wajib | Web server dengan SSL |
| **Ollama** | Latest | Wajib | Local LLM server |
| **Node.js** | 22+ | Wajib | Untuk asset compilation |
| **Composer** | 2.6+ | Wajib | PHP dependency manager |

### Model AI Ollama

| Model | Saiz | RAM Diperlukan | Kegunaan |
|-------|------|----------------|----------|
| **llama3.1:8b-instruct-q4_K_M** | 4.7GB | 8GB | Pengeluaran (disyorkan) |
| **llama3.1:8b-instruct-fp16** | 16GB | 20GB | Kualiti tinggi |
| **llama3.1:7b-instruct-q4_K_M** | 4.1GB | 6GB | Pembangunan |

---

## Pemasangan Ollama Server

### 1. Pemasangan Ollama

#### Linux/Ubuntu

```bash
# Muat turun dan pasang Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Mulakan perkhidmatan Ollama
sudo systemctl start ollama
sudo systemctl enable ollama

# Semak status
sudo systemctl status ollama
```

#### Windows

```powershell
# Muat turun dari https://ollama.ai/download/windows
# Jalankan installer dan ikuti arahan

# Semak pemasangan
ollama --version
```

### 2. Konfigurasi Model AI

```bash
# Muat turun model yang disyorkan untuk pengeluaran
ollama pull llama3.1:8b-instruct-q4_K_M

# Semak model yang dipasang
ollama list

# Uji model
ollama run llama3.1:8b-instruct-q4_K_M "Halo, bagaimana anda hari ini?"
```

### 3. Konfigurasi Ollama untuk Pengeluaran

#### Fail Konfigurasi: `/etc/systemd/system/ollama.service`

```ini
[Unit]
Description=Ollama Service
After=network-online.target

[Service]
ExecStart=/usr/local/bin/ollama serve
User=ollama
Group=ollama
Restart=always
RestartSec=3
Environment="OLLAMA_HOST=127.0.0.1:11434"
Environment="OLLAMA_MODELS=/var/lib/ollama/models"
Environment="OLLAMA_KEEP_ALIVE=5m"
Environment="OLLAMA_MAX_LOADED_MODELS=1"

[Install]
WantedBy=default.target
```

#### Reload dan Restart

```bash
sudo systemctl daemon-reload
sudo systemctl restart ollama
sudo systemctl status ollama
```

---

## Konfigurasi Laravel ICTServe

### 1. Pembolehubah Persekitaran (.env)

```bash
# Konfigurasi Ollama AI
OLLAMA_MODEL=llama3.1:8b-instruct-q4_K_M
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_CONNECTION_TIMEOUT=300
OLLAMA_CACHE_ENABLED=true
OLLAMA_CACHE_TTL=3600
OLLAMA_CACHE_DRIVER=redis
OLLAMA_QUANTIZED_MODEL=true

# Konfigurasi Cache dan Queue (Redis)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Konfigurasi Broadcasting (Laravel Reverb)
BROADCAST_DRIVER=reverb
REVERB_APP_ID=ictserve-ai
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http

# Konfigurasi Laravel Pulse
PULSE_ENABLED=true
PULSE_DOMAIN=pulse.ictserve.motac.gov.my
PULSE_PATH=pulse

# Konfigurasi Laravel Telescope (Superuser sahaja)
TELESCOPE_ENABLED=true
TELESCOPE_DOMAIN=telescope.ictserve.motac.gov.my
TELESCOPE_PATH=telescope

# Konfigurasi Audit dan Logging
AUDIT_ENABLED=true
AUDIT_DRIVER=database
ACTIVITY_LOGGER_ENABLED=true
```

### 2. Konfigurasi Database

#### Migration untuk AI Tables

```bash
# Jalankan migration untuk jadual AI
php artisan migrate --path=database/migrations/ollama

# Semak jadual yang dicipta
php artisan db:show --table=faqs,documents,document_chunks,auto_reply_templates,auto_reply_drafts,message_logs
```

#### Seeding Data Awal

```bash
# Seed data FAQ dan templat auto-reply
php artisan db:seed --class=OllamaAISeeder

# Semak data yang dicipta
php artisan tinker
>>> App\Models\Faq::count()
>>> App\Models\AutoReplyTemplate::count()
```

### 3. Konfigurasi Queue dan Jobs

#### Laravel Horizon Setup

```bash
# Pasang Laravel Horizon (jika belum)
composer require laravel/horizon

# Terbitkan konfigurasi
php artisan horizon:install

# Konfigurasi Horizon untuk AI jobs
php artisan vendor:publish --tag=horizon-config
```

#### Konfigurasi Horizon: `config/horizon.php`

```php
'environments' => [
    'production' => [
        'supervisor-ai' => [
            'connection' => 'redis',
            'queue' => ['ai-processing', 'document-ingestion', 'auto-reply'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 3,
            'timeout' => 300,
        ],
    ],
],
```

#### Mulakan Horizon

```bash
# Mulakan Horizon untuk queue processing
php artisan horizon

# Atau gunakan supervisor untuk pengeluaran
sudo supervisorctl start horizon
```

### 4. Konfigurasi Laravel Reverb (WebSocket)

#### Mulakan Reverb Server

```bash
# Mulakan Reverb server
php artisan reverb:start --host=127.0.0.1 --port=6001

# Atau gunakan supervisor untuk pengeluaran
sudo supervisorctl start reverb
```

#### Konfigurasi Nginx untuk WebSocket

```nginx
# Tambah ke konfigurasi Nginx
location /app/ {
    proxy_pass http://127.0.0.1:6001;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

---

## Konfigurasi Pemantauan (Laravel Pulse + Telescope)

### 1. Laravel Pulse Setup

#### Konfigurasi: `config/pulse.php`

```php
'recorders' => [
    Recorders\CacheInteractions::class => [
        'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
    ],
    Recorders\Queues::class => [
        'enabled' => env('PULSE_QUEUES_ENABLED', true),
    ],
    Recorders\SlowQueries::class => [
        'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
        'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000),
    ],
    Recorders\UserRequests::class => [
        'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
    ],
],
```

#### Akses Pulse Dashboard

```php
// routes/web.php - Hanya admin dan superuser
Route::middleware(['auth', 'role:admin|superuser'])->group(function () {
    Route::get('/pulse', function () {
        return redirect('/pulse/dashboard');
    });
});
```

### 2. Laravel Telescope Setup

#### Konfigurasi: `config/telescope.php`

```php
'middleware' => [
    'web',
    'auth',
    'role:superuser', // Hanya superuser mengikut D00 v3.6.0
],

'watchers' => [
    Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
    Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
    Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
    Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
    Watchers\QueryWatcher::class => [
        'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
        'slow' => 500, // milliseconds
    ],
],
```

---

## Konfigurasi Keselamatan

### 1. SSL/TLS Configuration

#### Nginx SSL Setup

```nginx
server {
    listen 443 ssl http2;
    server_name ictserve.motac.gov.my;
    
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    root /var/www/ictserve/public;
    index index.php;
    
    # Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 2. Firewall Configuration

```bash
# UFW Firewall rules
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 6001/tcp  # Reverb WebSocket (internal only)
sudo ufw deny 11434/tcp  # Ollama (internal only)

# Aktifkan firewall
sudo ufw enable
```

### 3. Fail2Ban untuk Perlindungan

#### Konfigurasi: `/etc/fail2ban/jail.local`

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
logpath = /var/log/nginx/error.log
maxretry = 10
```

---

## Prosedur Backup dan Pemulihan

### 1. Backup Database

#### Script Backup Automatik

```bash
#!/bin/bash
# backup-ictserve-ai.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/ictserve-ai"
DB_NAME="ictserve"

# Backup MySQL
mysqldump -u backup_user -p$MYSQL_BACKUP_PASSWORD \
    --single-transaction \
    --routines \
    --triggers \
    $DB_NAME > $BACKUP_DIR/mysql_$DATE.sql

# Backup Ollama models
tar -czf $BACKUP_DIR/ollama_models_$DATE.tar.gz /var/lib/ollama/models/

# Backup Laravel storage
tar -czf $BACKUP_DIR/laravel_storage_$DATE.tar.gz /var/www/ictserve/storage/

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

#### Cron Job untuk Backup Harian

```bash
# Tambah ke crontab
0 2 * * * /usr/local/bin/backup-ictserve-ai.sh >> /var/log/backup.log 2>&1
```

### 2. Prosedur Pemulihan

#### Pemulihan Database

```bash
# Hentikan aplikasi
sudo systemctl stop nginx
sudo systemctl stop php8.2-fpm

# Pulihkan database
mysql -u root -p ictserve < /backup/ictserve-ai/mysql_20251212_020000.sql

# Pulihkan model Ollama
sudo systemctl stop ollama
tar -xzf /backup/ictserve-ai/ollama_models_20251212_020000.tar.gz -C /
sudo systemctl start ollama

# Pulihkan Laravel storage
tar -xzf /backup/ictserve-ai/laravel_storage_20251212_020000.tar.gz -C /var/www/ictserve/

# Mulakan semula perkhidmatan
sudo systemctl start php8.2-fpm
sudo systemctl start nginx
```

---

## Prosedur Rollback

### 1. Rollback Kod Aplikasi

```bash
#!/bin/bash
# rollback-ictserve-ai.sh

PREVIOUS_VERSION=$1
CURRENT_DIR="/var/www/ictserve"
BACKUP_DIR="/backup/releases"

if [ -z "$PREVIOUS_VERSION" ]; then
    echo "Usage: $0 <previous_version>"
    echo "Available versions:"
    ls -la $BACKUP_DIR/
    exit 1
fi

# Backup current version
cp -r $CURRENT_DIR $BACKUP_DIR/current_$(date +%Y%m%d_%H%M%S)

# Rollback to previous version
rm -rf $CURRENT_DIR
cp -r $BACKUP_DIR/$PREVIOUS_VERSION $CURRENT_DIR

# Set permissions
chown -R www-data:www-data $CURRENT_DIR
chmod -R 755 $CURRENT_DIR

# Run migrations down if needed
cd $CURRENT_DIR
php artisan migrate:rollback --step=1

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart horizon
sudo systemctl restart reverb

echo "Rollback to $PREVIOUS_VERSION completed"
```

### 2. Rollback Database

```bash
# Rollback migration tertentu
php artisan migrate:rollback --path=database/migrations/ollama

# Rollback ke batch tertentu
php artisan migrate:rollback --batch=5

# Rollback semua migration AI
php artisan migrate:reset --path=database/migrations/ollama
```

---

## Health Check dan Monitoring

### 1. Health Check Endpoints

#### Script Health Check

```bash
#!/bin/bash
# health-check-ictserve-ai.sh

API_URL="https://ictserve.motac.gov.my/api/v1/ollama"
LOG_FILE="/var/log/ictserve-health.log"

# Check API health
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/health)

if [ $HTTP_STATUS -eq 200 ]; then
    echo "$(date): API Health OK" >> $LOG_FILE
else
    echo "$(date): API Health FAILED - Status: $HTTP_STATUS" >> $LOG_FILE
    # Send alert email
    echo "ICTServe AI API health check failed" | mail -s "ICTServe Alert" admin@motac.gov.my
fi

# Check Ollama server
OLLAMA_STATUS=$(curl -s http://127.0.0.1:11434/api/tags | jq -r '.models | length')

if [ "$OLLAMA_STATUS" -gt 0 ]; then
    echo "$(date): Ollama Server OK - Models: $OLLAMA_STATUS" >> $LOG_FILE
else
    echo "$(date): Ollama Server FAILED" >> $LOG_FILE
    # Restart Ollama
    sudo systemctl restart ollama
fi
```

### 2. Monitoring dengan Nagios/Zabbix

#### Nagios Check Command

```bash
# /usr/local/nagios/libexec/check_ictserve_ai.sh
#!/bin/bash

API_RESPONSE=$(curl -s https://ictserve.motac.gov.my/api/v1/ollama/health)
STATUS=$(echo $API_RESPONSE | jq -r '.data.status')

case $STATUS in
    "healthy")
        echo "OK - ICTServe AI is healthy"
        exit 0
        ;;
    "degraded")
        echo "WARNING - ICTServe AI is degraded"
        exit 1
        ;;
    "unhealthy")
        echo "CRITICAL - ICTServe AI is unhealthy"
        exit 2
        ;;
    *)
        echo "UNKNOWN - Unable to determine ICTServe AI status"
        exit 3
        ;;
esac
```

---

## Performance Tuning

### 1. PHP-FPM Optimization

#### Konfigurasi: `/etc/php/8.2/fpm/pool.d/ictserve.conf`

```ini
[ictserve]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm-ictserve.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000

; Memory limits for AI processing
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 300
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
```

### 2. MySQL Optimization

#### Konfigurasi: `/etc/mysql/mysql.conf.d/ictserve-ai.cnf`

```ini
[mysqld]
# InnoDB settings for AI workload
innodb_buffer_pool_size = 4G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query cache for frequent AI queries
query_cache_type = 1
query_cache_size = 256M
query_cache_limit = 2M

# Connection settings
max_connections = 200
wait_timeout = 300
interactive_timeout = 300
```

### 3. Redis Optimization

#### Konfigurasi: `/etc/redis/redis.conf`

```ini
# Memory settings for AI cache
maxmemory 2gb
maxmemory-policy allkeys-lru

# Persistence for AI data
save 900 1
save 300 10
save 60 10000

# Network settings
bind 127.0.0.1
port 6379
timeout 300
```

---

## Troubleshooting Guide

### Masalah Biasa dan Penyelesaian

#### 1. Ollama Server Tidak Dapat Dihubungi
**Gejala:** API mengembalikan "SERVICE_UNAVAILABLE"
**Penyelesaian:**

```bash
# Semak status Ollama
sudo systemctl status ollama

# Semak log
sudo journalctl -u ollama -f

# Restart jika perlu
sudo systemctl restart ollama

# Test manual
curl http://127.0.0.1:11434/api/tags
```

#### 2. Model AI Kehabisan Memori
**Gejala:** Respons lambat atau timeout
**Penyelesaian:**

```bash
# Semak penggunaan memori
free -h
htop

# Tukar ke model yang lebih kecil
ollama pull llama3.1:7b-instruct-q4_K_M

# Update konfigurasi
# OLLAMA_MODEL=llama3.1:7b-instruct-q4_K_M
```

#### 3. Queue Jobs Tidak Diproses
**Gejala:** Auto-reply tidak dijana
**Penyelesaian:**

```bash
# Semak status Horizon
php artisan horizon:status

# Restart Horizon
php artisan horizon:terminate
php artisan horizon

# Semak failed jobs
php artisan queue:failed
php artisan queue:retry all
```

#### 4. WebSocket Tidak Berfungsi
**Gejala:** Notifikasi real-time tidak sampai
**Penyelesaian:**

```bash
# Semak status Reverb
ps aux | grep reverb

# Restart Reverb
php artisan reverb:restart

# Semak konfigurasi Nginx
nginx -t
sudo systemctl reload nginx
```

---

## Kontak Kecemasan (Emergency Contacts)

### Pasukan Sokongan Teknikal

| Peranan | Nama | Telefon | E-mel | Waktu Bertugas |
|---------|------|---------|-------|----------------|
| **Lead Developer** | Ahmad bin Ali | +603-8000-1234 | <ahmad.ali@motac.gov.my> | 24/7 |
| **System Administrator** | Siti binti Ahmad | +603-8000-1235 | <siti.ahmad@motac.gov.my> | 8AM-6PM |
| **Database Administrator** | Muhammad bin Hassan | +603-8000-1236 | <muhammad.hassan@motac.gov.my> | 8AM-6PM |
| **Network Administrator** | Fatimah binti Omar | +603-8000-1237 | <fatimah.omar@motac.gov.my> | 8AM-6PM |

### Prosedur Kecemasan

#### Tahap 1: Masalah Kecil (Response Time > 5s)

- Hubungi System Administrator
- Semak dashboard Laravel Pulse
- Lakukan restart perkhidmatan jika perlu

#### Tahap 2: Masalah Sederhana (Service Degraded)

- Hubungi Lead Developer
- Aktifkan graceful degradation
- Notifikasi pengguna melalui sistem

#### Tahap 3: Masalah Kritikal (Service Down)

- Hubungi semua ahli pasukan
- Aktifkan prosedur disaster recovery
- Notifikasi pengurusan atasan

---

**Dokumen ini mematuhi D11 v3.6.0 Technical Design Documentation dan menyediakan panduan lengkap untuk pelaksanaan sistem AI Ollama dalam persekitaran pengeluaran ICTServe.**
