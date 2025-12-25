# ICTServe XAMPP Installation Guide

**Version**: 3.6.0  
**Target**: Non-Workspace XAMPP Development  
**Last Updated**: December 22, 2024

## Overview

This guide provides step-by-step instructions for installing ICTServe in a non-workspace XAMPP environment. This setup is ideal for developers who prefer traditional local development or cannot use Docker.

## Prerequisites

### Required Software

1. **XAMPP** (Latest version with PHP 8.4+)
   - Download: <https://www.apachefriends.org/>
   - Components needed: Apache, MySQL, PHP 8.4+

2. **Composer** (Latest version)
   - Download: <https://getcomposer.org/>
   - Ensure it's accessible from command line

3. **Node.js** (22.12+ with npm)
   - Download: <https://nodejs.org/>
   - Verify with: `node --version` and `npm --version`

4. **Git** (for cloning repository)
   - Download: <https://git-scm.com/>

### Optional Software

1. **Redis** (for enhanced performance)
   - Windows: <https://github.com/microsoftarchive/redis/releases>
   - WSL: `sudo apt install redis-server`

2. **Visual Studio Code** (recommended editor)
   - Download: <https://code.visualstudio.com/>

## Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from <https://www.apachefriends.org/>
2. Run the installer as Administrator
3. Install to default location (C:\xampp)
4. Start XAMPP Control Panel
5. Start Apache and MySQL services

### Step 2: Configure PHP

1. Open `C:\xampp\php\php.ini`
2. Add the settings from `deployment/xampp/config/php.ini.additions`
3. Restart Apache service

### Step 3: Clone ICTServe Repository

```bash
# Clone to your desired location
git clone <repository-url> ictserve
cd ictserve
```

### Step 4: Run Automated Setup

```powershell
# Run the automated setup script
.\deployment\xampp\setup-xampp.ps1

# Or with options
.\deployment\xampp\setup-xampp.ps1 -RedisSetup -Force
```

### Step 5: Manual Setup (Alternative)

If you prefer manual setup:

```powershell
# 1. Copy environment configuration
copy deployment\xampp\.env.xampp .env

# 2. Install dependencies
composer install
npm install

# 3. Generate application key
php artisan key:generate

# 4. Create database
mysql -u root -p -e "CREATE DATABASE ictserve;"

# 5. Run migrations
php artisan migrate --seed

# 6. Build assets
npm run build
```

### Step 6: Start Services

```powershell
# Option 1: Use service script
.\deployment\xampp\scripts\start-services.ps1

# Option 2: Manual start (separate terminals)
php artisan serve
npm run dev
php artisan reverb:start
```

## Configuration

### Database Configuration

Default database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=
```

### Web Server Configuration

**Option 1: Laravel Development Server**

- URL: <http://127.0.0.1:8000>
- Command: `php artisan serve`

**Option 2: Apache Virtual Host**

- Copy `deployment/xampp/config/apache-vhost.conf` to XAMPP
- Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
- Update DocumentRoot path
- Add to hosts file: `127.0.0.1 ictserve.local`

### Redis Configuration (Optional)

If Redis is installed:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Verification

### Health Check

Run the health check script:

```powershell
.\deployment\xampp\scripts\health-check.ps1
```

### Manual Verification

1. **PHP**: `php --version`
2. **Composer**: `composer --version`
3. **Node.js**: `node --version`
4. **MySQL**: `mysql -u root -e "SELECT VERSION();"`
5. **Laravel**: `php artisan about`

### Access URLs

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Telescope**: <http://127.0.0.1:8000/telescope>
- **Pulse**: <http://127.0.0.1:8000/pulse>

### Default Credentials

- **Superuser**: <superuser@motac.gov.my> / password
- **Admin**: <admin@motac.gov.my> / password
- **Staff**: <staff@motac.gov.my> / password

## Post-Installation

### Security Considerations

1. **Change MySQL root password**:

   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
   ```

2. **Update .env with new password**:

   ```env
   DB_PASSWORD=new_password
   ```

3. **Configure firewall** (if needed)

4. **Set proper file permissions** on storage directories

### Performance Optimization

1. **Enable OPcache** in php.ini
2. **Configure Redis** for caching
3. **Optimize MySQL** configuration
4. **Use production build** for assets: `npm run build`

### Development Tools

1. **Install IDE extensions**:
   - PHP Intelephense
   - Laravel Extension Pack
   - Tailwind CSS IntelliSense

2. **Configure debugging**:
   - Xdebug for PHP debugging
   - Laravel Telescope for application debugging

## Maintenance

### Updates

```powershell
# Pull latest changes
git pull origin main

# Update dependencies
composer update
npm update

# Run migrations
php artisan migrate

# Clear caches
php artisan optimize:clear
```

### Backup

```powershell
# Database backup
mysqldump -u root -p ictserve > backup_$(Get-Date -Format "yyyyMMdd").sql

# File backup
# Backup .env, storage/, and any custom files
```

## Troubleshooting

### Common Issues

1. **Port 80 in use**
   - Stop IIS or other web servers
   - Change Apache port in XAMPP

2. **MySQL won't start**
   - Check if port 3306 is in use
   - Review MySQL error logs

3. **PHP extensions missing**
   - Enable required extensions in php.ini
   - Restart Apache

4. **Permission errors**
   - Run command prompt as Administrator
   - Check file/folder permissions

### Getting Help

1. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
2. Review XAMPP documentation
3. Check Laravel documentation
4. Review application logs in `storage/logs/`

## Next Steps

After successful installation:

1. **Explore the application** at <http://127.0.0.1:8000>
2. **Review configuration** in `.env` file
3. **Set up development workflow** with your preferred tools
4. **Configure additional services** as needed (Redis, SSL, etc.)
5. **Read the main documentation** for feature details

---

**Note**: This installation is for development purposes. For production deployment, additional security and performance configurations are required.
