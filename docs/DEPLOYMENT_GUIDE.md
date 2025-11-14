# ICTServe Deployment and Maintenance Guide

**Version**: 3.0.0
**Last Updated**: 2025-01-06
**Status**: Production Ready

## Table of Contents

1. [Production Deployment](#production-deployment)
2. [System Administration](#system-administration)
3. [Maintenance Procedures](#maintenance-procedures)
4. [Troubleshooting Guide](#troubleshooting-guide)
5. [Performance Monitoring](#performance-monitoring)
6. [Security Hardening](#security-hardening)
7. [Backup and Recovery](#backup-and-recovery)
8. [Scaling Guidelines](#scaling-guidelines)


---

## Production Deployment

### Prerequisites

**Server Requirements**:

- **OS**: Ubuntu 22.04 LTS / CentOS 8+ / Debian 11+
- **PHP**: 8.2.12+ with extensions (mbstring, xml, pdo, openssl, tokenizer, json, bcmath, gd)
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Redis**: 6.0+ for cache and queue
- **Node.js**: 18.x+ for asset compilation
- **Composer**: 2.x
- **Git**: 2.x


**Network Requirements**:

- HTTPS/TLS 1.3 certificate
- Firewall rules: 80 (HTTP), 443 (HTTPS), 3306 (MySQL - internal only)
- CDN configuration for static assets (optional)


### Step 1: Server Setup

```bash

# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip \
    php8.2-redis php8.2-intl

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2: Application Deployment

```bash

# Clone repository
cd /var/www
sudo git clone https://github.com/IzzatFirdaus/ictserve-031125.git ictserve
cd ictserve

# Set ownership
sudo chown -R www-data:www-data /var/www/ictserve

# Install PHP dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
sudo -u www-data npm install
sudo -u www-data npm run build

# Configure environment
sudo -u www-data cp .env.production .env
sudo -u www-data php artisan key:generate

# Set permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Step 3: Database Setup

```bash

# Create database
sudo mysql -u root -p <<EOF
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ictserve_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON ictserve.* TO 'ictserve_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Configure .env database settings
sudo -u www-data nano .env
# Update:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ictserve
# DB_USERNAME=ictserve_user
# DB_PASSWORD=STRONG_PASSWORD_HERE

# Run migrations
sudo -u www-data php artisan migrate --force

# Seed initial data
sudo -u www-data php artisan db:seed --force
```

### Step 4: Web Server Configuration

**Nginx Configuration** (`/etc/nginx/sites-available/ictserve`):

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
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/ictserve.crt;
    ssl_certificate_key /etc/ssl/private/ictserve.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';" always;

    # Logging
    access_log /var/log/nginx/ictserve-access.log;
    error_log /var/log/nginx/ictserve-error.log;

    # PHP-FPM Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Static asset caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash

sudo ln -s /etc/nginx/sites-available/ictserve /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: Queue Worker Setup

**Supervisor Configuration** (`/etc/supervisor/conf.d/ictserve-worker.conf`):

```ini

[program:ictserve-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/worker.log
stopwaitsecs=3600
```

Start worker:
```bash

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ictserve-worker:*
```

### Step 6: Scheduler Setup

Add to crontab (`sudo crontab -e -u www-data`):

```cron

* * * * * cd /var/www/ictserve && php artisan schedule:run >> /dev/null 2>&1
```

### Step 7: Optimization

```bash

# Cache configuration
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Create storage link
sudo -u www-data php artisan storage:link

# Set OPcache (edit /etc/php/8.2/fpm/php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## System Administration

### User Management

**Create Admin User**:
```bash

php artisan tinker
>>> $user = User::create(['name' => 'Admin', 'email' => 'admin@motac.gov.my', 'password' => Hash::make('password')]);
>>> $user->assignRole('admin');
```

**Assign Roles**:
```bash

php artisan tinker
>>> $user = User::where('email', 'user@motac.gov.my')->first();
>>> $user->assignRole('staff'); // or 'approver', 'admin', 'superuser'
```

### Database Management

**Backup Database**:
```bash

mysqldump -u ictserve_user -p ictserve > backup_$(date +%Y%m%d_%H%M%S).sql
```

**Restore Database**:
```bash

mysql -u ictserve_user -p ictserve < backup_20250106_120000.sql
```

### Log Management

**View Application Logs**:
```bash

tail -f storage/logs/laravel.log
```

**View Nginx Logs**:
```bash

tail -f /var/log/nginx/ictserve-access.log
tail -f /var/log/nginx/ictserve-error.log
```

**View Queue Worker Logs**:
```bash

tail -f storage/logs/worker.log
```

---

## Maintenance Procedures

### Regular Maintenance Tasks

**Daily**:

- Monitor application logs for errors
- Check queue worker status
- Verify backup completion


**Weekly**:

- Review security logs
- Check disk space usage
- Update dependencies (security patches)


**Monthly**:

- Database optimization
- Log rotation and cleanup
- Performance audit


### Update Procedure

```bash

# 1. Enable maintenance mode
php artisan down

# 2. Pull latest changes
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Clear and rebuild cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart services
sudo supervisorctl restart ictserve-worker:*
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

# 7. Disable maintenance mode
php artisan up
```

### Database Maintenance

**Optimize Tables**:
```bash

php artisan db:optimize
```

**Clean Old Audit Logs** (older than 7 years):
```bash

php artisan audit:clean --days=2555
```

**Prune Expired Data**:
```bash

php artisan model:prune
```

---

## Troubleshooting Guide

### Common Issues

**Issue: 500 Internal Server Error**

**Solution**:
```bash

# Check logs
tail -n 50 storage/logs/laravel.log

# Verify permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

**Issue: Queue Jobs Not Processing**

**Solution**:
```bash

# Check worker status
sudo supervisorctl status ictserve-worker:*

# Restart workers
sudo supervisorctl restart ictserve-worker:*

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

**Issue: Database Connection Error**

**Solution**:
```bash

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Verify credentials in .env
cat .env | grep DB_

# Check MySQL service
sudo systemctl status mysql
```

**Issue: High Memory Usage**

**Solution**:
```bash

# Check PHP memory limit
php -i | grep memory_limit

# Increase in php.ini
memory_limit = 512M

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## Performance Monitoring

### Monitoring Tools

**Laravel Telescope** (Development Only):
```bash

composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Application Performance Monitoring**:

- Use New Relic or Datadog for production monitoring
- Monitor Core Web Vitals with Google Analytics
- Track queue metrics with Horizon


### Performance Metrics

**Target Metrics**:

- **Response Time**: < 200ms (average)
- **Database Queries**: < 10 per page
- **Memory Usage**: < 128MB per request
- **Queue Processing**: < 60s per job
- **Core Web Vitals**: LCP < 2.5s, FID < 100ms, CLS < 0.1


**Monitoring Commands**:
```bash

# Check queue size
php artisan queue:monitor redis:default --max=100

# View cache statistics
php artisan cache:stats

# Database query logging
php artisan db:monitor --max=100
```

---

## Security Hardening

### Security Checklist

- [ ] HTTPS/TLS 1.3 enabled
- [ ] Security headers configured
- [ ] CSRF protection enabled
- [ ] Rate limiting configured
- [ ] File upload validation
- [ ] SQL injection prevention (Eloquent ORM)
- [ ] XSS protection (Blade escaping)
- [ ] Audit logging enabled
- [ ] Two-factor authentication (for admins)
- [ ] Regular security updates


### Security Commands

**Check for Vulnerabilities**:
```bash

composer audit
npm audit
```

**Update Dependencies**:
```bash

composer update --with-dependencies
npm update
```

**Review Security Logs**:
```bash

php artisan security:review
```

---

## Backup and Recovery

### Automated Backup Script

Create `/usr/local/bin/ictserve-backup.sh`:

```bash

#!/bin/bash
BACKUP_DIR="/var/backups/ictserve"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u ictserve_user -p'PASSWORD' ictserve | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup application files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/ictserve/storage

# Backup .env
cp /var/www/ictserve/.env $BACKUP_DIR/env_$DATE

# Delete backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Schedule daily backup (crontab):
```cron

0 2 * * * /usr/local/bin/ictserve-backup.sh >> /var/log/ictserve-backup.log 2>&1
```

### Recovery Procedure

```bash

# 1. Restore database
gunzip < /var/backups/ictserve/db_20250106_020000.sql.gz | mysql -u ictserve_user -p ictserve

# 2. Restore files
tar -xzf /var/backups/ictserve/files_20250106_020000.tar.gz -C /

# 3. Restore .env
cp /var/backups/ictserve/env_20250106_020000 /var/www/ictserve/.env

# 4. Clear cache
php artisan cache:clear
php artisan config:cache

# 5. Restart services
sudo supervisorctl restart ictserve-worker:*
sudo systemctl reload php8.2-fpm
```

---

## Scaling Guidelines

### Horizontal Scaling

**Load Balancer Configuration** (Nginx):

```nginx

upstream ictserve_backend {
    least_conn;
    server 192.168.1.10:80 weight=1;
    server 192.168.1.11:80 weight=1;
    server 192.168.1.12:80 weight=1;
}

server {
    listen 443 ssl http2;
    server_name ictserve.motac.gov.my;

    location / {
        proxy_pass http://ictserve_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Database Scaling

**Read Replicas**:
```php

// config/database.php
'mysql' => [
    'read' => [
        'host' => ['192.168.1.20', '192.168.1.21'],
    ],
    'write' => [
        'host' => ['192.168.1.10'],
    ],
    // ... other config
],
```

### Cache Scaling

**Redis Cluster**:
```php

// config/database.php
'redis' => [
    'client' => 'phpredis',
    'cluster' => true,
    'clusters' => [
        'default' => [
            ['host' => '192.168.1.30', 'port' => 6379],
            ['host' => '192.168.1.31', 'port' => 6379],
            ['host' => '192.168.1.32', 'port' => 6379],
        ],
    ],
],
```

---

## Support and Contacts

**Technical Support**:

- Email: ict@bpm.gov.my
- Phone: +603-1234-5678
- Hours: Monday-Friday, 8:00 AM - 5:00 PM


**Emergency Contacts**:

- System Administrator: admin@motac.gov.my
- Database Administrator: dba@motac.gov.my
- Security Team: security@motac.gov.my


**Documentation**:

- System Documentation: `/docs/` folder
- API Documentation: `/docs/api/`
- User Guides: `/docs/guides/`


---

**Document Version**: 1.0.0
**Last Reviewed**: 2025-01-06
**Next Review**: 2025-07-06
