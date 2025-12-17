# Panduan Penempatan ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Sistem**: ICTServe - Portal Perkhidmatan ICT MOTAC  
**Peranan**: Pentadbir Sistem / DevOps

---

## Pengenalan

Panduan ini menerangkan prosedur penempatan (deployment) sistem ICTServe v3.6.0 ke persekitaran pengeluaran (production). Pastikan semua keperluan dipenuhi sebelum memulakan penempatan.

---

## Bahagian 1: Keperluan Sistem

### 1.1 Keperluan Pelayan

| Komponen | Keperluan Minimum | Disyorkan |
|----------|-------------------|-----------|
| **CPU** | 4 teras | 8 teras |
| **RAM** | 8 GB | 16 GB |
| **Storan** | 100 GB SSD | 250 GB SSD |
| **Sistem Operasi** | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |

### 1.2 Keperluan Perisian

| Perisian | Versi |
|----------|-------|
| **PHP** | 8.2.12 atau lebih tinggi |
| **MySQL** | 8.0 atau lebih tinggi |
| **Redis** | 7.0 atau lebih tinggi |
| **Nginx** | 1.24 atau lebih tinggi |
| **Node.js** | 20 LTS atau lebih tinggi |
| **Composer** | 2.6 atau lebih tinggi |

### 1.3 Sambungan PHP Yang Diperlukan

```
php-bcmath
php-ctype
php-curl
php-dom
php-fileinfo
php-gd
php-intl
php-json
php-mbstring
php-mysql
php-openssl
php-pdo
php-redis
php-tokenizer
php-xml
php-zip
```

---

## Bahagian 2: Persediaan Pelayan

### 2.1 Kemaskini Sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### 2.2 Pasang PHP 8.2

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring \
    php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl \
    php8.2-redis php8.2-tokenizer -y
```

### 2.3 Pasang MySQL 8.0

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 2.4 Pasang Redis 7.0

```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### 2.5 Pasang Nginx

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 2.6 Pasang Node.js 20 LTS

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y
```

### 2.7 Pasang Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## Bahagian 3: Konfigurasi Pangkalan Data

### 3.1 Cipta Pangkalan Data

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ictserve'@'localhost' IDENTIFIED BY 'kata_laluan_selamat';
GRANT ALL PRIVILEGES ON ictserve.* TO 'ictserve'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Konfigurasi Redis

Edit `/etc/redis/redis.conf`:

```conf
# Tetapan keselamatan
requirepass kata_laluan_redis_selamat

# Tetapan memori
maxmemory 2gb
maxmemory-policy allkeys-lru

# Tetapan persistensi
appendonly yes
appendfsync everysec
```

Mulakan semula Redis:

```bash
sudo systemctl restart redis-server
```

---

## Bahagian 4: Penempatan Aplikasi

### 4.1 Cipta Direktori Aplikasi

```bash
sudo mkdir -p /var/www/ictserve
sudo chown -R www-data:www-data /var/www/ictserve
```

### 4.2 Muat Turun Kod Sumber

```bash
cd /var/www/ictserve
sudo -u www-data git clone https://github.com/motac/ictserve.git .
```

### 4.3 Pasang Kebergantungan PHP

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 4.4 Pasang Kebergantungan Node.js

```bash
sudo -u www-data npm ci
sudo -u www-data npm run build
```

### 4.5 Konfigurasi Persekitaran

Salin fail `.env.example` ke `.env`:

```bash
sudo -u www-data cp .env.example .env
```

Edit fail `.env`:

```env
APP_NAME="ICTServe"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://ictserve.motac.gov.my

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=ictserve
DB_PASSWORD=kata_laluan_selamat

BROADCAST_DRIVER=reverb
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=kata_laluan_redis_selamat
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.motac.gov.my
MAIL_PORT=587
MAIL_USERNAME=ictserve@motac.gov.my
MAIL_PASSWORD=kata_laluan_mel
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ictserve@motac.gov.my
MAIL_FROM_NAME="ICTServe MOTAC"

REVERB_APP_ID=ictserve
REVERB_APP_KEY=kunci_reverb_selamat
REVERB_APP_SECRET=rahsia_reverb_selamat
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 4.6 Jana Kunci Aplikasi

```bash
sudo -u www-data php artisan key:generate
```

### 4.7 Jalankan Migrasi Pangkalan Data

```bash
sudo -u www-data php artisan migrate --force
```

### 4.8 Jalankan Seeder (Pilihan)

```bash
sudo -u www-data php artisan db:seed --force
```

### 4.9 Optimumkan Aplikasi

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

### 4.10 Tetapkan Kebenaran

```bash
sudo chown -R www-data:www-data /var/www/ictserve
sudo chmod -R 755 /var/www/ictserve
sudo chmod -R 775 /var/www/ictserve/storage
sudo chmod -R 775 /var/www/ictserve/bootstrap/cache
```

---

## Bahagian 5: Konfigurasi Nginx

### 5.1 Cipta Konfigurasi Laman

Cipta fail `/etc/nginx/sites-available/ictserve`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name ictserve.motac.gov.my;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ictserve.motac.gov.my;

    root /var/www/ictserve/public;
    index index.php;

    # Sijil SSL
    ssl_certificate /etc/ssl/certs/ictserve.motac.gov.my.crt;
    ssl_certificate_key /etc/ssl/private/ictserve.motac.gov.my.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;

    # Tetapan keselamatan
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml;

    # Lokasi utama
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Aset statik
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Halang akses ke fail tersembunyi
    location ~ /\. {
        deny all;
    }

    # Log
    access_log /var/log/nginx/ictserve_access.log;
    error_log /var/log/nginx/ictserve_error.log;
}
```

### 5.2 Aktifkan Laman

```bash
sudo ln -s /etc/nginx/sites-available/ictserve /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Bahagian 6: Konfigurasi Perkhidmatan

### 6.1 Perkhidmatan Baris Gilir (Queue Worker)

Cipta fail `/etc/systemd/system/ictserve-queue.service`:

```ini
[Unit]
Description=ICTServe Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/var/www/ictserve
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Aktifkan perkhidmatan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable ictserve-queue
sudo systemctl start ictserve-queue
```

### 6.2 Perkhidmatan Laravel Reverb (WebSocket)

Cipta fail `/etc/systemd/system/ictserve-reverb.service`:

```ini
[Unit]
Description=ICTServe Reverb WebSocket Server
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/var/www/ictserve
ExecStart=/usr/bin/php artisan reverb:start --host=127.0.0.1 --port=8080

[Install]
WantedBy=multi-user.target
```

Aktifkan perkhidmatan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable ictserve-reverb
sudo systemctl start ictserve-reverb
```

### 6.3 Perkhidmatan Penjadual (Scheduler)

Tambah ke crontab:

```bash
sudo crontab -u www-data -e
```

Tambah baris:

```cron
* * * * * cd /var/www/ictserve && php artisan schedule:run >> /dev/null 2>&1
```

---

## Bahagian 7: Pemantauan dan Penyelenggaraan

### 7.1 Pemantauan Log

```bash
# Log aplikasi
tail -f /var/www/ictserve/storage/logs/laravel.log

# Log Nginx
tail -f /var/log/nginx/ictserve_error.log

# Log baris gilir
sudo journalctl -u ictserve-queue -f

# Log Reverb
sudo journalctl -u ictserve-reverb -f
```

### 7.2 Pemantauan Prestasi

Akses Laravel Pulse di:

```
https://ictserve.motac.gov.my/pulse
```

### 7.3 Sandaran Automatik

Cipta skrip sandaran `/opt/scripts/backup-ictserve.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/ictserve"

# Sandaran pangkalan data
mysqldump -u ictserve -p'kata_laluan_selamat' ictserve | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Sandaran fail
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/ictserve/storage/app

# Padam sandaran lama (lebih 30 hari)
find $BACKUP_DIR -type f -mtime +30 -delete
```

Jadualkan sandaran harian:

```bash
sudo crontab -e
```

Tambah:

```cron
0 2 * * * /opt/scripts/backup-ictserve.sh
```

### 7.4 Kemaskini Aplikasi

```bash
cd /var/www/ictserve

# Mod penyelenggaraan
sudo -u www-data php artisan down

# Tarik kod terkini
sudo -u www-data git pull origin main

# Kemaskini kebergantungan
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci
sudo -u www-data npm run build

# Jalankan migrasi
sudo -u www-data php artisan migrate --force

# Kosongkan cache
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

## Bahagian 8: Penyelesaian Masalah

### 8.1 Masalah Biasa

**Ralat 500 Internal Server Error:**

1. Semak log: `tail -f /var/www/ictserve/storage/logs/laravel.log`
2. Semak kebenaran: `sudo chown -R www-data:www-data /var/www/ictserve`
3. Kosongkan cache: `sudo -u www-data php artisan optimize:clear`

**Baris gilir tidak berfungsi:**

1. Semak status: `sudo systemctl status ictserve-queue`
2. Semak log: `sudo journalctl -u ictserve-queue -f`
3. Mulakan semula: `sudo systemctl restart ictserve-queue`

**WebSocket tidak berfungsi:**

1. Semak status: `sudo systemctl status ictserve-reverb`
2. Semak port: `sudo netstat -tlnp | grep 8080`
3. Mulakan semula: `sudo systemctl restart ictserve-reverb`

### 8.2 Hubungi Sokongan

Untuk bantuan teknikal:

- **E-mel**: <ict-support@motac.gov.my>
- **Telefon**: 03-8000 8000 ext. 1235

---

**Dokumen ini adalah sebahagian daripada sistem ICTServe v3.6.0**  
**Pematuhan**: D00-D17, ISO/IEC 27001, PDPA 2010
