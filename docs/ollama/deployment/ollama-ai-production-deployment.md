# Panduan Deployment Produksi Integrasi AI Ollama (Ollama AI Production Deployment Guide)

**Sistem ICTServe v3.6.0**  
**Tarikh:** 12 Disember 2025  
**Pematuhan:** D11 v3.6.0 Technical Design Documentation  
**Bahasa:** Bahasa Melayu sahaja (D15 v3.6.0)

---

## Keperluan Sistem (System Requirements)

### Keperluan Minimum (Minimum Requirements)

- **Pelayan (Server)**: Ubuntu 22.04 LTS atau CentOS 8+
- **PHP**: 8.2.12 atau lebih tinggi
- **MySQL**: 8.0 atau lebih tinggi
- **Redis**: 7.0 atau lebih tinggi
- **Nginx**: 1.20+ atau Apache 2.4+
- **RAM**: 16GB minimum (untuk model Ollama)
- **Storage**: 100GB SSD minimum
- **CPU**: 8 cores minimum (untuk AI processing)

### Keperluan Ollama Server

- **Model**: llama3.1 (quantized Q4_K_M)
- **RAM**: 8GB untuk model
- **Port**: 11434 (internal only)
- **Network**: Localhost access sahaja (tiada external connections)

---

## Langkah Deployment (Deployment Steps)

### 1. Persediaan Pelayan (Server Preparation)

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install keperluan asas
sudo apt install -y nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Install Ollama Server

```bash
# Download dan install Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Start Ollama service
sudo systemctl enable ollama
sudo systemctl start ollama

# Download model llama3.1
ollama pull llama3.1

# Verify installation
ollama list
curl http://localhost:11434/api/version
```

### 3. Konfigurasi Database (Database Configuration)

```sql
-- Cipta database dan pengguna
CREATE DATABASE ictserve_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ictserve_user'@'localhost' IDENTIFIED BY 'SECURE_DATABASE_PASSWORD';
GRANT ALL PRIVILEGES ON ictserve_production.* TO 'ictserve_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Deploy Aplikasi Laravel (Laravel Application Deployment)

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/motac/ictserve.git ictserve-ai
cd ictserve-ai

# Set permissions
sudo chown -R www-data:www-data /var/www/ictserve-ai
sudo chmod -R 755 /var/www/ictserve-ai
sudo chmod -R 775 /var/www/ictserve-ai/storage
sudo chmod -R 775 /var/www/ictserve-ai/bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Konfigurasi environment
cp .env.production.example .env
# Edit .env dengan nilai production yang sebenar

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed data awal (jika diperlukan)
php artisan db:seed --class=AdminUserSeeder

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create symbolic link untuk storage
php artisan storage:link
```

### 5. Konfigurasi Nginx

```nginx
# /etc/nginx/sites-available/ictserve-ai
server {
    listen 80;
    server_name ictserve.motac.gov.my;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ictserve.motac.gov.my;
    root /var/www/ictserve-ai/public;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/ictserve.motac.gov.my.crt;
    ssl_certificate_key /etc/ssl/private/ictserve.motac.gov.my.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSocket support untuk Laravel Reverb
    location /app {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 6. Konfigurasi Laravel Reverb (WebSocket Server)

```bash
# Install Laravel Reverb
php artisan reverb:install

# Start Reverb server
php artisan reverb:start --host=0.0.0.0 --port=6001 --hostname=ictserve.motac.gov.my

# Create systemd service untuk Reverb
sudo tee /etc/systemd/system/laravel-reverb.service > /dev/null <<EOF
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/ictserve-ai
ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=6001
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl enable laravel-reverb
sudo systemctl start laravel-reverb
```

### 7. Konfigurasi Laravel Horizon (Queue Management)

```bash
# Install Laravel Horizon
php artisan horizon:install

# Publish Horizon configuration
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

# Create systemd service untuk Horizon
sudo tee /etc/systemd/system/laravel-horizon.service > /dev/null <<EOF
[Unit]
Description=Laravel Horizon Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/ictserve-ai
ExecStart=/usr/bin/php artisan horizon
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl enable laravel-horizon
sudo systemctl start laravel-horizon
```

---

## Pemantauan dan Alerting (Monitoring & Alerting)

### 1. Health Check Endpoints

```bash
# Test health check endpoints
curl https://ictserve.motac.gov.my/health
curl https://ictserve.motac.gov.my/api/health/ollama
curl http://localhost:11434/api/version
```

### 2. Laravel Pulse Dashboard

- **URL**: <https://ictserve.motac.gov.my/admin/pulse>
- **Access**: Admin dan Superuser sahaja
- **Metrics**: Response times, queue jobs, server health

### 3. Laravel Telescope (Superuser Only)

- **URL**: <https://ictserve.motac.gov.my/admin/telescope>
- **Access**: Superuser sahaja
- **Features**: Request debugging, query monitoring, job tracking

### 4. Log Monitoring

```bash
# Monitor Laravel logs
sudo tail -f /var/www/ictserve-ai/storage/logs/laravel.log

# Monitor Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Monitor Ollama logs
sudo journalctl -u ollama -f
```

---

## Backup dan Disaster Recovery

### 1. Database Backup

```bash
# Daily database backup
sudo tee /etc/cron.d/ictserve-backup > /dev/null <<EOF
0 2 * * * www-data mysqldump -u ictserve_user -p'SECURE_DATABASE_PASSWORD' ictserve_production | gzip > /backup/ictserve-\$(date +\%Y\%m\%d).sql.gz
EOF
```

### 2. Application Backup

```bash
# Weekly application backup
sudo tee /etc/cron.d/ictserve-app-backup > /dev/null <<EOF
0 3 * * 0 www-data tar -czf /backup/ictserve-app-\$(date +\%Y\%m\%d).tar.gz -C /var/www ictserve-ai --exclude=node_modules --exclude=vendor
EOF
```

### 3. Rollback Procedures

```bash
# Rollback ke versi sebelumnya
cd /var/www/ictserve-ai
git checkout previous-stable-tag
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback --step=1
php artisan config:cache
sudo systemctl restart php8.2-fpm nginx
```

---

## Security Configuration

### 1. Firewall Rules

```bash
# Configure UFW firewall
sudo ufw enable
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 6001/tcp  # Laravel Reverb (internal)
sudo ufw deny 11434/tcp  # Block external Ollama access
```

### 2. SSL/TLS Configuration

- **Certificate**: Gunakan certificate dari CA yang sah
- **Protocols**: TLS 1.2 dan 1.3 sahaja
- **HSTS**: Enabled dengan max-age 1 tahun
- **OCSP Stapling**: Enabled untuk performance

### 3. Network Security

```bash
# Block external access ke Ollama
sudo iptables -A INPUT -p tcp --dport 11434 ! -s 127.0.0.1 -j DROP
sudo iptables-save > /etc/iptables/rules.v4
```

---

## Performance Tuning

### 1. PHP-FPM Configuration

```ini
; /etc/php/8.2/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000

; Memory limits
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 300
```

### 2. MySQL Optimization

```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 4G
innodb_log_file_size = 1G
query_cache_size = 256M
max_connections = 200
```

### 3. Redis Configuration

```ini
# /etc/redis/redis.conf
maxmemory 2gb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

---

## Troubleshooting

### 1. Ollama Connection Issues

```bash
# Check Ollama status
sudo systemctl status ollama
curl http://localhost:11434/api/version

# Restart Ollama
sudo systemctl restart ollama
```

### 2. Queue Job Failures

```bash
# Check Horizon status
php artisan horizon:status

# Restart Horizon
php artisan horizon:terminate
sudo systemctl restart laravel-horizon
```

### 3. WebSocket Connection Issues

```bash
# Check Reverb status
sudo systemctl status laravel-reverb

# Test WebSocket connection
wscat -c ws://localhost:6001/app/ictserve-ai
```

### 4. Performance Issues

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check resource usage
htop
iostat -x 1
```

---

## Emergency Contacts

- **Admin Sistem**: <admin@motac.gov.my>
- **Superuser**: <superuser@motac.gov.my>  
- **Sokongan Teknikal**: <support@motac.gov.my>
- **Telefon Kecemasan**: +603-XXXX-XXXX

---

## Compliance Checklist (D00-D17 v3.6.0)

- [ ] **D00 v3.6.0**: True Hybrid Architecture dengan Self-Registration
- [ ] **D09 v3.6.0**: Dual Audit System (owen-it + spatie) configured
- [ ] **D11 v3.6.0**: Laravel Pulse + Telescope + Sanctum + Reverb integrated
- [ ] **D15 v3.6.0**: Bahasa Melayu sahaja interface (no language switcher)
- [ ] **D16 v3.6.0**: Laravel Reverb WebSocket server configured
- [ ] **D17 v3.6.0**: Laravel Horizon queue management configured
- [ ] **WCAG 2.2 AA**: Accessibility compliance verified
- [ ] **PDPA 2010**: Data protection measures implemented
- [ ] **Core Web Vitals**: Performance targets met (LCP <2.5s, FID <100ms, CLS <0.1)
- [ ] **Security**: All external connections blocked, local Ollama only
- [ ] **Monitoring**: Health checks, alerting, and logging configured
