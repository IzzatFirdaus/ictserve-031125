# ICTServe Deployment Checklist

## Pre-Deployment Requirements

### System Requirements

- [ ] **PHP**: 8.2.12 or higher
- [ ] **Web Server**: Nginx 1.18+ or Apache 2.4+
- [ ] **Database**: MySQL 8.0+ or MariaDB 10.6+
- [ ] **Node.js**: 18.x or higher (for asset compilation)
- [ ] **Composer**: 2.x
- [ ] **SSL Certificate**: Valid TLS certificate for HTTPS

### Server Configuration

- [ ] **Memory**: Minimum 2GB RAM, recommended 4GB+
- [ ] **Storage**: Minimum 10GB free space, recommended 50GB+
- [ ] **CPU**: Minimum 2 cores, recommended 4+ cores
- [ ] **Network**: Stable internet connection for email delivery

---

## Environment Setup

### 1. Server Preparation

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd php8.2-intl

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Database Setup

```sql
-- Create database
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user
CREATE USER 'ictserve_user'@'localhost' IDENTIFIED BY 'secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON ictserve.* TO 'ictserve_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Application Deployment

```bash
# Clone repository
git clone https://github.com/your-org/ictserve.git /var/www/ictserve
cd /var/www/ictserve

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --production

# Build frontend assets
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/ictserve
sudo chmod -R 755 /var/www/ictserve
sudo chmod -R 775 /var/www/ictserve/storage
sudo chmod -R 775 /var/www/ictserve/bootstrap/cache
```

---

## Configuration

### 1. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Environment Variables

Edit `.env` file with production values:

```env
# Application
APP_NAME=ICTServe
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=ictserve_user
DB_PASSWORD=secure_password_here

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error

# Security
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### 3. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Create superuser account
php artisan make:superuser
```

---

## Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/ictserve`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/ictserve/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # File Upload Limits
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/ictserve /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Security Configuration

### 1. File Permissions

```bash
# Set secure permissions
sudo find /var/www/ictserve -type f -exec chmod 644 {} \;
sudo find /var/www/ictserve -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/ictserve/storage
sudo chmod -R 775 /var/www/ictserve/bootstrap/cache
sudo chmod 600 /var/www/ictserve/.env
```

### 2. Firewall Configuration

```bash
# Configure UFW firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

### 3. Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Create jail configuration
sudo tee /etc/fail2ban/jail.local << EOF
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
EOF

sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## Performance Optimization

### 1. PHP-FPM Configuration

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
; Process management
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

; Memory limits
php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
```

### 2. Laravel Optimization

```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize Composer autoloader
composer dump-autoload --optimize --classmap-authoritative
```

### 3. Database Optimization

```sql
-- Add indexes for performance
ALTER TABLE helpdesk_tickets ADD INDEX idx_status_priority (status, priority);
ALTER TABLE loan_applications ADD INDEX idx_status_created (status, created_at);
ALTER TABLE assets ADD INDEX idx_status_availability (status, availability);
ALTER TABLE audits ADD INDEX idx_user_created (user_id, created_at);
```

---

## Monitoring & Logging

### 1. Log Rotation

Create `/etc/logrotate.d/ictserve`:

```text
/var/www/ictserve/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### 2. System Monitoring

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Setup log monitoring
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/ictserve/storage/logs/laravel.log
```

### 3. Health Check Script

Create `/usr/local/bin/ictserve-health-check.sh`:

```bash
#!/bin/bash

# Check application health
curl -f -s https://your-domain.com/health > /dev/null
if [ $? -ne 0 ]; then
    echo "Application health check failed" | mail -s "ICTServe Alert" admin@your-domain.com
fi

# Check database connectivity
php /var/www/ictserve/artisan tinker --execute="DB::connection()->getPdo();"
if [ $? -ne 0 ]; then
    echo "Database connection failed" | mail -s "ICTServe Alert" admin@your-domain.com
fi

# Check disk space
DISK_USAGE=$(df /var/www/ictserve | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "Disk usage is ${DISK_USAGE}%" | mail -s "ICTServe Alert" admin@your-domain.com
fi
```

Make executable and add to cron:

```bash
sudo chmod +x /usr/local/bin/ictserve-health-check.sh
echo "*/5 * * * * /usr/local/bin/ictserve-health-check.sh" | sudo crontab -
```

---

## Queue Management

### 1. Queue Worker Setup

Create systemd service `/etc/systemd/system/ictserve-worker.service`:

```ini
[Unit]
Description=ICTServe Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/ictserve/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable ictserve-worker
sudo systemctl start ictserve-worker
```

### 2. Queue Monitoring

```bash
# Check queue status
php artisan queue:monitor

# Restart queue workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush
```

---

## Backup Configuration

### 1. Database Backup Script

Create `/usr/local/bin/ictserve-backup.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/ictserve"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="ictserve"
DB_USER="ictserve_user"
DB_PASS="secure_password_here"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/database_$DATE.sql.gz

# Application files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www/ictserve \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='node_modules' \
    .

# Remove backups older than 30 days
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

Make executable and schedule:

```bash
sudo chmod +x /usr/local/bin/ictserve-backup.sh
echo "0 2 * * * /usr/local/bin/ictserve-backup.sh" | sudo crontab -
```

---

## SSL Certificate Management

### 1. Let's Encrypt Setup

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com

# Test renewal
sudo certbot renew --dry-run
```

### 2. Certificate Monitoring

```bash
# Check certificate expiry
openssl x509 -in /etc/letsencrypt/live/your-domain.com/cert.pem -noout -dates

# Auto-renewal cron job (already set up by certbot)
sudo crontab -l | grep certbot
```

---

## Post-Deployment Verification

### 1. Functionality Tests

- [ ] **Login**: Admin and superuser login works
- [ ] **Dashboard**: Widgets load correctly
- [ ] **Helpdesk**: Ticket creation and management
- [ ] **Asset Loans**: Application processing
- [ ] **Email**: Notifications are sent
- [ ] **Reports**: Export functionality works
- [ ] **Search**: Global search functions
- [ ] **Mobile**: Responsive design works

### 2. Security Tests

- [ ] **HTTPS**: SSL certificate valid and enforced
- [ ] **Headers**: Security headers present
- [ ] **Authentication**: Unauthorized access blocked
- [ ] **CSRF**: Form protection active
- [ ] **File Upload**: Malicious files rejected
- [ ] **SQL Injection**: Input sanitization works

### 3. Performance Tests

- [ ] **Page Load**: < 3 seconds for main pages
- [ ] **Database**: Query performance acceptable
- [ ] **Memory**: Usage within limits
- [ ] **Cache**: Caching mechanisms active

---

## Rollback Procedures

### 1. Application Rollback

```bash
# Stop services
sudo systemctl stop ictserve-worker
sudo systemctl stop nginx

# Restore from backup
cd /var/www
sudo mv ictserve ictserve-failed
sudo tar -xzf /var/backups/ictserve/files_YYYYMMDD_HHMMSS.tar.gz
sudo mv ictserve-backup ictserve

# Restore database
mysql -u ictserve_user -p ictserve < /var/backups/ictserve/database_YYYYMMDD_HHMMSS.sql

# Restart services
sudo systemctl start nginx
sudo systemctl start ictserve-worker
```

### 2. Database Rollback

```bash
# Create current backup before rollback
mysqldump -u ictserve_user -p ictserve > /tmp/current_backup.sql

# Restore from backup
mysql -u ictserve_user -p ictserve < /var/backups/ictserve/database_YYYYMMDD_HHMMSS.sql

# Clear application cache
php artisan cache:clear
php artisan config:clear
```

---

## Maintenance Procedures

### 1. Regular Maintenance Tasks

**Daily**:

- [ ] Check system logs for errors
- [ ] Monitor disk space usage
- [ ] Verify backup completion
- [ ] Check queue worker status

**Weekly**:

- [ ] Review security logs
- [ ] Update system packages
- [ ] Check SSL certificate status
- [ ] Analyze performance metrics

**Monthly**:

- [ ] Update application dependencies
- [ ] Review and rotate logs
- [ ] Test backup restoration
- [ ] Security audit and updates

### 2. Update Procedures

```bash
# Application updates
cd /var/www/ictserve
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart ictserve-worker
```

---

## Support Contacts

- **System Administrator**: <admin@motac.gov.my>
- **Database Administrator**: <dba@motac.gov.my>
- **Security Officer**: <security@motac.gov.my>
- **Emergency Contact**: +603-1234-5678

---

*Last Updated: January 6, 2025*
*Version: 3.0.0*
