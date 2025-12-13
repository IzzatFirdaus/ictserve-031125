# Persediaan Persekitaran Pengeluaran AI Ollama (Ollama AI Production Environment Setup)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 12 Disember 2025  
**Status:** Sedia untuk Pengeluaran  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Pematuhi:** D11 v3.6.0 Technical Design Documentation

---

## Senarai Semak Pelaksanaan Pengeluaran (Production Deployment Checklist)

### ✅ Pra-Pelaksanaan (Pre-Deployment)

#### Persediaan Infrastruktur

- [ ] **Server Setup**: Pelayan pengeluaran dikonfigurasi mengikut spesifikasi minimum
- [ ] **SSL Certificate**: Sijil SSL yang sah dipasang dan dikonfigurasi
- [ ] **Domain Configuration**: DNS dikonfigurasi untuk domain pengeluaran
- [ ] **Firewall Rules**: Peraturan firewall dikonfigurasi dengan betul
- [ ] **Backup System**: Sistem backup automatik dikonfigurasi dan diuji

#### Persediaan Perisian

- [ ] **PHP 8.2.12+**: Dipasang dengan semua ekstensi yang diperlukan
- [ ] **MySQL 8.0+**: Dikonfigurasi dengan optimisasi prestasi
- [ ] **Redis 7.0+**: Dikonfigurasi untuk cache dan queue
- [ ] **Nginx/Apache**: Web server dikonfigurasi dengan SSL
- [ ] **Ollama Server**: Dipasang dan model AI dimuat turun
- [ ] **Supervisor**: Dikonfigurasi untuk queue dan WebSocket management

#### Persediaan Keselamatan

- [ ] **Fail2Ban**: Dikonfigurasi untuk perlindungan brute force
- [ ] **UFW/iptables**: Firewall dikonfigurasi dengan port yang betul
- [ ] **SSH Keys**: Akses SSH menggunakan kunci sahaja (password disabled)
- [ ] **User Permissions**: Pengguna sistem dikonfigurasi dengan privilege minimum
- [ ] **Log Rotation**: Logrotate dikonfigurasi untuk semua log files

### ✅ Pelaksanaan Aplikasi (Application Deployment)

#### Kod Aplikasi

- [ ] **Git Repository**: Kod terkini dari branch production
- [ ] **Composer Dependencies**: Semua dependencies dipasang untuk production
- [ ] **NPM Dependencies**: Frontend assets dikompil untuk production
- [ ] **File Permissions**: Permissions yang betul untuk storage dan cache directories
- [ ] **Environment Configuration**: File .env.production dikonfigurasi dengan betul

#### Database Setup

- [ ] **Database Creation**: Database pengeluaran dicipta
- [ ] **User Privileges**: Database user dengan privileges yang sesuai
- [ ] **Migrations**: Semua migrations dijalankan
- [ ] **Seeders**: Data awal (FAQ, templates) di-seed
- [ ] **Indexes**: Semua indexes database dicipta dengan betul

#### Konfigurasi Perkhidmatan

- [ ] **Laravel Horizon**: Dikonfigurasi dan berjalan untuk queue processing
- [ ] **Laravel Reverb**: Dikonfigurasi dan berjalan untuk WebSocket
- [ ] **Laravel Pulse**: Dikonfigurasi untuk performance monitoring
- [ ] **Laravel Telescope**: Dikonfigurasi untuk superuser access sahaja
- [ ] **Cron Jobs**: Scheduled tasks dikonfigurasi dalam crontab

### ✅ Ujian Pengeluaran (Production Testing)

#### Ujian Fungsional

- [ ] **API Endpoints**: Semua endpoint API berfungsi dengan betul
- [ ] **FAQ Bot**: Sistem FAQ Bot memberikan respons yang betul
- [ ] **Document Analysis**: Upload dan analisis dokumen berfungsi
- [ ] **Auto-Reply**: Penjanaan dan approval auto-reply berfungsi
- [ ] **Authentication**: Laravel Sanctum authentication berfungsi
- [ ] **Authorization**: Role-based access control berfungsi dengan betul

#### Ujian Prestasi

- [ ] **Response Time**: API response time < 5 saat (95th percentile)
- [ ] **Concurrent Users**: Sistem boleh mengendalikan 100 pengguna serentak
- [ ] **Memory Usage**: Penggunaan memori dalam had yang ditetapkan
- [ ] **Cache Performance**: Cache hit rate > 80%
- [ ] **Database Performance**: Query time < 100ms untuk operasi biasa

#### Ujian Keselamatan

- [ ] **SSL/TLS**: HTTPS berfungsi dengan betul dan secure
- [ ] **Rate Limiting**: Had kadar API berfungsi dengan betul
- [ ] **Input Validation**: Semua input divalidasi dengan betul
- [ ] **PII Protection**: Data peribadi disanitasi dengan betul
- [ ] **Audit Logging**: Semua operasi dilog dengan betul

### ✅ Pasca-Pelaksanaan (Post-Deployment)

#### Pemantauan

- [ ] **Health Checks**: Endpoint health check berfungsi
- [ ] **Monitoring Setup**: Laravel Pulse dashboard boleh diakses
- [ ] **Log Monitoring**: Log files dipantau untuk errors
- [ ] **Performance Metrics**: Metrik prestasi dikumpul dengan betul
- [ ] **Alert System**: Sistem alert dikonfigurasi untuk issues kritikal

#### Dokumentasi

- [ ] **API Documentation**: Dokumentasi API terkini dan boleh diakses
- [ ] **User Guides**: Panduan pengguna dalam Bahasa Melayu
- [ ] **Admin Guides**: Panduan pentadbir untuk pengurusan sistem
- [ ] **Troubleshooting**: Panduan penyelesaian masalah
- [ ] **Emergency Contacts**: Senarai kontak kecemasan dikemaskini

---

## Konfigurasi Persekitaran Pengeluaran (.env.production)

```bash
# =============================================================================
# ICTServe Ollama AI Integration - Production Environment Configuration
# Versi: 3.6.0 (D11 v3.6.0 Technical Design Documentation)
# Tarikh: 12 Disember 2025
# =============================================================================

# Application Configuration
APP_NAME="ICTServe AI Integration"
APP_ENV=production
APP_KEY=base64:your-32-character-secret-key-here
APP_DEBUG=false
APP_URL=https://ictserve.motac.gov.my
APP_TIMEZONE=Asia/Kuala_Lumpur
APP_LOCALE=ms

# Database Configuration (MySQL 8.0)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve_production
DB_USERNAME=ictserve_user
DB_PASSWORD=your-secure-database-password

# Cache Configuration (Redis)
CACHE_DRIVER=redis
CACHE_PREFIX=ictserve_ai_cache

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.motac.gov.my
SESSION_SECURE_COOKIES=true
SESSION_SAME_SITE=lax

# Queue Configuration (Redis + Horizon)
QUEUE_CONNECTION=redis
QUEUE_PREFIX=ictserve_ai_queue

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your-secure-redis-password
REDIS_PORT=6379
REDIS_DB=0

# Broadcasting Configuration (Laravel Reverb)
BROADCAST_DRIVER=reverb
REVERB_APP_ID=ictserve-ai-production
REVERB_APP_KEY=your-reverb-app-key
REVERB_APP_SECRET=your-reverb-app-secret
REVERB_HOST=ictserve.motac.gov.my
REVERB_PORT=6001
REVERB_SCHEME=https

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.motac.gov.my
MAIL_PORT=587
MAIL_USERNAME=ictserve@motac.gov.my
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ictserve@motac.gov.my
MAIL_FROM_NAME="ICTServe AI System"

# Logging Configuration
LOG_CHANNEL=stack
LOG_STACK=single,daily
LOG_LEVEL=info
LOG_DAILY_DAYS=30

# =============================================================================
# Ollama AI Configuration
# =============================================================================

# Ollama Server Configuration
OLLAMA_MODEL=llama3.1:8b-instruct-q4_K_M
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_CONNECTION_TIMEOUT=300
OLLAMA_MAX_RETRIES=3
OLLAMA_RETRY_DELAY=1000

# AI Performance Configuration
OLLAMA_QUANTIZED_MODEL=true
OLLAMA_KEEP_ALIVE=5m
OLLAMA_MAX_LOADED_MODELS=1
OLLAMA_CONTEXT_WINDOW=4096

# AI Cache Configuration
OLLAMA_CACHE_ENABLED=true
OLLAMA_CACHE_TTL=3600
OLLAMA_CACHE_DRIVER=redis
OLLAMA_CACHE_PREFIX=ollama_ai

# AI Rate Limiting
OLLAMA_RATE_LIMIT_PER_USER=60
OLLAMA_RATE_LIMIT_PER_IP=1000
OLLAMA_RATE_LIMIT_BURST=10

# =============================================================================
# Laravel Pulse Configuration (Performance Monitoring)
# =============================================================================

PULSE_ENABLED=true
PULSE_DOMAIN=pulse.ictserve.motac.gov.my
PULSE_PATH=pulse
PULSE_CACHE_INTERACTIONS_ENABLED=true
PULSE_QUEUES_ENABLED=true
PULSE_SLOW_QUERIES_ENABLED=true
PULSE_SLOW_QUERIES_THRESHOLD=500
PULSE_USER_REQUESTS_ENABLED=true
PULSE_SYSTEM_ENABLED=true

# =============================================================================
# Laravel Telescope Configuration (Debugging - Superuser Only)
# =============================================================================

TELESCOPE_ENABLED=true
TELESCOPE_DOMAIN=telescope.ictserve.motac.gov.my
TELESCOPE_PATH=telescope
TELESCOPE_CACHE_WATCHER=true
TELESCOPE_COMMAND_WATCHER=true
TELESCOPE_JOB_WATCHER=true
TELESCOPE_LOG_WATCHER=true
TELESCOPE_QUERY_WATCHER=true
TELESCOPE_QUERY_SLOW_THRESHOLD=500

# =============================================================================
# Laravel Sanctum Configuration (API Authentication)
# =============================================================================

SANCTUM_STATEFUL_DOMAINS=ictserve.motac.gov.my,pulse.ictserve.motac.gov.my,telescope.ictserve.motac.gov.my
SANCTUM_GUARD=web
SANCTUM_EXPIRATION=null
SANCTUM_TOKEN_PREFIX=ictserve_

# =============================================================================
# Audit and Compliance Configuration (D09 v3.6.0 Dual Audit System)
# =============================================================================

# Owen-it Laravel Auditing (Compliance Audit)
AUDIT_ENABLED=true
AUDIT_DRIVER=database
AUDIT_QUEUE=true
AUDIT_THRESHOLD=500
AUDIT_STRICT=true

# Spatie Laravel Activity Log (Operational Logging)
ACTIVITY_LOGGER_ENABLED=true
ACTIVITY_LOGGER_DELETE_RECORDS_OLDER_THAN_DAYS=90
ACTIVITY_LOGGER_DEFAULT_LOG_NAME=ictserve_ai

# Data Retention Configuration
DATA_RETENTION_OPERATIONAL_DAYS=90
DATA_RETENTION_AUDIT_YEARS=7
DATA_RETENTION_CLEANUP_ENABLED=true

# =============================================================================
# Security Configuration
# =============================================================================

# PII Protection
PII_DETECTION_ENABLED=true
PII_SANITIZATION_ENABLED=true
PII_AUDIT_LOGGING=true

# Network Security
NETWORK_MONITORING_ENABLED=true
EXTERNAL_API_BLOCKING=true
SECURITY_ALERT_EMAIL=security@motac.gov.my

# Encryption
ENCRYPTION_KEY=your-32-character-encryption-key
HASH_ALGORITHM=sha256
HASH_SALT=your-unique-salt-string

# =============================================================================
# Performance Configuration
# =============================================================================

# Core Web Vitals Targets
PERFORMANCE_LCP_TARGET=2500
PERFORMANCE_FID_TARGET=100
PERFORMANCE_CLS_TARGET=0.1
PERFORMANCE_TTFB_TARGET=600

# Response Time Targets
PERFORMANCE_API_RESPONSE_TARGET=5000
PERFORMANCE_P95_TARGET=5000
PERFORMANCE_P99_TARGET=8000

# Resource Limits
PERFORMANCE_CPU_THRESHOLD=80
PERFORMANCE_MEMORY_THRESHOLD=90
PERFORMANCE_DISK_THRESHOLD=85

# =============================================================================
# Monitoring and Alerting Configuration
# =============================================================================

# Health Check Configuration
HEALTH_CHECK_ENABLED=true
HEALTH_CHECK_INTERVAL=60
HEALTH_CHECK_TIMEOUT=30

# Alert Configuration
ALERT_EMAIL_ENABLED=true
ALERT_EMAIL_RECIPIENTS=admin@motac.gov.my,ictserve-support@motac.gov.my
ALERT_SLACK_ENABLED=false
ALERT_SLACK_WEBHOOK=

# Metrics Collection
METRICS_ENABLED=true
METRICS_RETENTION_DAYS=30
METRICS_COLLECTION_INTERVAL=60

# =============================================================================
# Backup Configuration
# =============================================================================

BACKUP_ENABLED=true
BACKUP_DISK=s3
BACKUP_RETENTION_DAYS=30
BACKUP_NOTIFICATION_EMAIL=backup@motac.gov.my

# =============================================================================
# Third-Party Services (Optional)
# =============================================================================

# AWS S3 (for backups and file storage)
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=ictserve-ai-backups

# Google Workspace SSO (Optional - D00 v3.6.0)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://ictserve.motac.gov.my/auth/google/callback

# =============================================================================
# Feature Flags (D00-D17 v3.6.0 Compliance)
# =============================================================================

# D15 v3.6.0 - Bahasa Melayu sahaja (Language switcher disabled)
LANGUAGE_SWITCHER_ENABLED=false
DEFAULT_LOCALE=ms
SUPPORTED_LOCALES=ms

# D00 v3.6.0 - True Hybrid Architecture
HYBRID_ARCHITECTURE_ENABLED=true
SELF_REGISTRATION_ENABLED=true
ACCOUNT_LINKING_ENABLED=true
FLEXIBLE_LOGIN_ENABLED=true

# AI Feature Flags
FAQ_BOT_ENABLED=true
DOCUMENT_ANALYSIS_ENABLED=true
AUTO_REPLY_ENABLED=true
AI_APPROVAL_WORKFLOW_ENABLED=true

# Compliance Feature Flags
WCAG_COMPLIANCE_ENABLED=true
PDPA_COMPLIANCE_ENABLED=true
AUDIT_TRAIL_ENABLED=true
PII_PROTECTION_ENABLED=true

# =============================================================================
# Development and Debugging (Production: Disabled)
# =============================================================================

# Debug Configuration (DISABLED in production)
APP_DEBUG=false
LOG_LEVEL=info
TELESCOPE_ENABLED_FOR_ALL=false

# Query Logging (Limited in production)
DB_LOG_QUERIES=false
DB_LOG_SLOW_QUERIES=true
DB_SLOW_QUERY_THRESHOLD=1000

# Cache Debugging (DISABLED in production)
CACHE_DEBUG=false
QUEUE_DEBUG=false

# =============================================================================
# End of Configuration
# =============================================================================
```

---

## Supervisor Configuration untuk Perkhidmatan

### 1. Laravel Horizon (Queue Processing)

#### Fail: `/etc/supervisor/conf.d/ictserve-horizon.conf`

```ini
[program:ictserve-horizon]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan horizon
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/horizon.log
stopwaitsecs=3600
user=www-data
environment=HOME="/var/www/ictserve",USER="www-data"
```

### 2. Laravel Reverb (WebSocket Server)

#### Fail: `/etc/supervisor/conf.d/ictserve-reverb.conf`

```ini
[program:ictserve-reverb]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan reverb:start --host=127.0.0.1 --port=6001
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/reverb.log
stopwaitsecs=10
user=www-data
environment=HOME="/var/www/ictserve",USER="www-data"
```

### 3. Ollama Server

#### Fail: `/etc/supervisor/conf.d/ollama-server.conf`

```ini
[program:ollama-server]
process_name=%(program_name)s
command=/usr/local/bin/ollama serve
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/ollama/ollama.log
stopwaitsecs=30
user=ollama
environment=HOME="/var/lib/ollama",USER="ollama",OLLAMA_HOST="127.0.0.1:11434",OLLAMA_MODELS="/var/lib/ollama/models"
```

### 4. Reload Supervisor Configuration

```bash
# Reload supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start all services
sudo supervisorctl start ictserve-horizon
sudo supervisorctl start ictserve-reverb
sudo supervisorctl start ollama-server

# Check status
sudo supervisorctl status
```

---

## Cron Jobs untuk Maintenance

### Crontab Configuration: `/etc/cron.d/ictserve-ai`

```bash
# ICTServe AI Integration Maintenance Jobs
# Versi: 3.6.0 (D11 v3.6.0 Technical Design Documentation)

# Laravel Scheduler (every minute)
* * * * * www-data cd /var/www/ictserve && php artisan schedule:run >> /dev/null 2>&1

# Daily Backup (2:00 AM)
0 2 * * * root /usr/local/bin/backup-ictserve-ai.sh >> /var/log/backup.log 2>&1

# Weekly Log Cleanup (Sunday 3:00 AM)
0 3 * * 0 www-data cd /var/www/ictserve && php artisan log:clear --days=30

# Daily Cache Cleanup (4:00 AM)
0 4 * * * www-data cd /var/www/ictserve && php artisan cache:prune-stale-tags

# Weekly Database Optimization (Sunday 5:00 AM)
0 5 * * 0 root mysqlcheck -o ictserve_production -u root -p$MYSQL_ROOT_PASSWORD

# Daily Health Check (every 15 minutes during business hours)
*/15 8-18 * * 1-5 root /usr/local/bin/health-check-ictserve-ai.sh

# Monthly Security Audit (1st day of month, 6:00 AM)
0 6 1 * * root /usr/local/bin/security-audit-ictserve-ai.sh

# Daily Audit Log Archive (1:00 AM)
0 1 * * * www-data cd /var/www/ictserve && php artisan audit:archive --days=90

# Weekly Performance Report (Monday 7:00 AM)
0 7 * * 1 www-data cd /var/www/ictserve && php artisan performance:report --email=admin@motac.gov.my
```

---

## SSL Certificate Management

### 1. Let's Encrypt Setup (Certbot)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d ictserve.motac.gov.my -d pulse.ictserve.motac.gov.my -d telescope.ictserve.motac.gov.my

# Auto-renewal cron job
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

### 2. Custom SSL Certificate

```bash
# Copy certificate files
sudo cp ictserve.motac.gov.my.crt /etc/ssl/certs/
sudo cp ictserve.motac.gov.my.key /etc/ssl/private/
sudo cp ca-bundle.crt /etc/ssl/certs/

# Set proper permissions
sudo chmod 644 /etc/ssl/certs/ictserve.motac.gov.my.crt
sudo chmod 600 /etc/ssl/private/ictserve.motac.gov.my.key
sudo chmod 644 /etc/ssl/certs/ca-bundle.crt
```

---

## Database Optimization untuk Pengeluaran

### 1. MySQL Configuration Tuning

#### Fail: `/etc/mysql/mysql.conf.d/ictserve-ai-production.cnf`

```ini
[mysqld]
# Basic Settings
bind-address = 127.0.0.1
port = 3306
datadir = /var/lib/mysql
socket = /var/run/mysqld/mysqld.sock

# InnoDB Settings (Optimized for AI workload)
innodb_buffer_pool_size = 8G
innodb_log_file_size = 512M
innodb_log_buffer_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_open_files = 400

# Query Cache (for frequent AI queries)
query_cache_type = 1
query_cache_size = 512M
query_cache_limit = 4M

# Connection Settings
max_connections = 300
max_connect_errors = 1000000
wait_timeout = 600
interactive_timeout = 600

# Buffer Settings
key_buffer_size = 256M
max_allowed_packet = 64M
table_open_cache = 4000
sort_buffer_size = 4M
read_buffer_size = 2M
read_rnd_buffer_size = 16M
myisam_sort_buffer_size = 128M

# Logging
log_error = /var/log/mysql/error.log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 1
log_queries_not_using_indexes = 1

# Binary Logging (for replication/backup)
log_bin = /var/log/mysql/mysql-bin.log
binlog_format = ROW
expire_logs_days = 7
max_binlog_size = 100M
```

### 2. Database Indexes untuk AI Queries

```sql
-- Indexes untuk FAQ queries
CREATE INDEX idx_faqs_question_fulltext ON faqs (question) USING FULLTEXT;
CREATE INDEX idx_faqs_tags ON faqs (tags);
CREATE INDEX idx_faqs_match_score ON faqs (match_score);

-- Indexes untuk Document Analysis
CREATE INDEX idx_documents_status ON documents (status);
CREATE INDEX idx_documents_uploaded_by ON documents (uploaded_by);
CREATE INDEX idx_document_chunks_document_id ON document_chunks (document_id);
CREATE INDEX idx_document_chunks_embedding ON document_chunks (embedding(100));

-- Indexes untuk Auto-Reply
CREATE INDEX idx_auto_reply_drafts_status ON auto_reply_drafts (status);
CREATE INDEX idx_auto_reply_drafts_replyable ON auto_reply_drafts (replyable_type, replyable_id);
CREATE INDEX idx_auto_reply_drafts_created_at ON auto_reply_drafts (created_at);

-- Indexes untuk Audit Logs
CREATE INDEX idx_message_logs_operation_type ON message_logs (operation_type);
CREATE INDEX idx_message_logs_processed_at ON message_logs (processed_at);
CREATE INDEX idx_message_logs_user_id ON message_logs (user_id);
CREATE INDEX idx_message_logs_request_id ON message_logs (request_id);

-- Composite indexes untuk complex queries
CREATE INDEX idx_faqs_composite ON faqs (created_by, match_score, created_at);
CREATE INDEX idx_documents_composite ON documents (uploaded_by, status, created_at);
CREATE INDEX idx_drafts_composite ON auto_reply_drafts (status, generated_by, created_at);
```

---

## Prosedur Go-Live

### 1. Pre-Go-Live Checklist (24 jam sebelum)

```bash
#!/bin/bash
# pre-golive-checklist.sh

echo "=== ICTServe AI Integration Pre-Go-Live Checklist ==="
echo "Tarikh: $(date)"
echo

# Check system resources
echo "1. Checking system resources..."
free -h
df -h
uptime

# Check services
echo "2. Checking services..."
systemctl status nginx
systemctl status mysql
systemctl status redis
systemctl status ollama
supervisorctl status

# Check SSL certificates
echo "3. Checking SSL certificates..."
openssl x509 -in /etc/ssl/certs/ictserve.motac.gov.my.crt -text -noout | grep "Not After"

# Check database connectivity
echo "4. Checking database..."
mysql -u ictserve_user -p$DB_PASSWORD -e "SELECT COUNT(*) FROM faqs;" ictserve_production

# Check Ollama models
echo "5. Checking Ollama models..."
curl -s http://127.0.0.1:11434/api/tags | jq '.models[].name'

# Check API endpoints
echo "6. Checking API endpoints..."
curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq '.data.status'

echo "=== Pre-Go-Live Checklist Complete ==="
```

### 2. Go-Live Procedure

```bash
#!/bin/bash
# golive-procedure.sh

echo "=== ICTServe AI Integration Go-Live Procedure ==="
echo "Tarikh: $(date)"
echo

# Step 1: Final code deployment
echo "Step 1: Deploying final code..."
cd /var/www/ictserve
git pull origin production
composer install --no-dev --optimize-autoloader
npm run build

# Step 2: Database migrations
echo "Step 2: Running database migrations..."
php artisan migrate --force

# Step 3: Clear all caches
echo "Step 3: Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Step 4: Restart services
echo "Step 4: Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart all

# Step 5: Warm up application
echo "Step 5: Warming up application..."
curl -s https://ictserve.motac.gov.my/api/v1/ollama/health > /dev/null
sleep 5

# Step 6: Final health check
echo "Step 6: Final health check..."
./health-check-ictserve-ai.sh

# Step 7: Send go-live notification
echo "Step 7: Sending go-live notification..."
echo "ICTServe AI Integration v3.6.0 is now LIVE!" | mail -s "Go-Live Notification" admin@motac.gov.my

echo "=== Go-Live Procedure Complete ==="
echo "System Status: LIVE"
echo "Monitoring: https://pulse.ictserve.motac.gov.my"
echo "API Health: https://ictserve.motac.gov.my/api/v1/ollama/health"
```

### 3. Post-Go-Live Monitoring (24 jam pertama)

```bash
#!/bin/bash
# post-golive-monitoring.sh

echo "=== Post-Go-Live Monitoring (24 hours) ==="

# Monitor for 24 hours
for i in {1..144}; do
    echo "Check #$i at $(date)"
    
    # API Health Check
    API_STATUS=$(curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq -r '.data.status')
    echo "API Status: $API_STATUS"
    
    # System Resources
    CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)
    MEM_USAGE=$(free | grep Mem | awk '{printf("%.1f", $3/$2 * 100.0)}')
    echo "CPU: ${CPU_USAGE}%, Memory: ${MEM_USAGE}%"
    
    # Error Log Check
    ERROR_COUNT=$(tail -100 /var/www/ictserve/storage/logs/laravel.log | grep -c "ERROR")
    echo "Recent Errors: $ERROR_COUNT"
    
    # Alert if issues detected
    if [ "$API_STATUS" != "healthy" ] || [ "$ERROR_COUNT" -gt 5 ]; then
        echo "ALERT: Issues detected!" | mail -s "Post-GoLive Alert" admin@motac.gov.my
    fi
    
    echo "---"
    sleep 600  # Check every 10 minutes
done

echo "=== 24-hour monitoring complete ==="
```

---

**Dokumen ini mematuhi D11 v3.6.0 Technical Design Documentation dan menyediakan panduan lengkap untuk persediaan persekitaran pengeluaran sistem AI Ollama ICTServe.**
