# ICTServe XAMPP Troubleshooting Guide

**Version**: 3.6.0  
**Target**: Non-Workspace XAMPP Development  
**Last Updated**: December 22, 2024

## Common Issues and Solutions

### Installation Issues

#### XAMPP Installation Problems

**Issue**: XAMPP installer fails or services won't start

**Solutions**:

1. **Run as Administrator**: Right-click installer and select "Run as administrator"
2. **Disable antivirus temporarily** during installation
3. **Check Windows Defender**: Add XAMPP folder to exclusions
4. **Port conflicts**: Change default ports if 80/443/3306 are in use

**Issue**: Apache won't start - Port 80 in use

**Solutions**:

```powershell
# Check what's using port 80
netstat -ano | findstr :80

# Stop IIS if running
net stop iisadmin
net stop w3svc

# Or change Apache port in httpd.conf
# Listen 8080 instead of Listen 80
```

**Issue**: MySQL won't start - Port 3306 in use

**Solutions**:

```powershell
# Check what's using port 3306
netstat -ano | findstr :3306

# Stop conflicting MySQL services
net stop mysql
net stop mysql80

# Or change MySQL port in my.ini
# port = 3307
```

#### PHP Configuration Issues

**Issue**: PHP extensions not loading

**Solutions**:

1. **Check php.ini location**: `php --ini`
2. **Enable extensions**: Remove semicolon from extension lines
3. **Required extensions for ICTServe**:

   ```ini
   extension=curl
   extension=fileinfo
   extension=gd
   extension=intl
   extension=mbstring
   extension=openssl
   extension=pdo_mysql
   extension=tokenizer
   extension=xml
   extension=zip
   ```

4. **Restart Apache** after changes

**Issue**: Memory limit errors

**Solutions**:

```ini
; In php.ini
memory_limit = 512M
max_execution_time = 300
```

### Database Issues

#### MySQL Connection Problems

**Issue**: "Connection refused" or "Access denied"

**Solutions**:

1. **Check MySQL service**: Ensure MySQL is running in XAMPP Control Panel
2. **Test connection**:

   ```bash
   mysql -u root -p
   ```

3. **Reset root password**:

   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY '';
   FLUSH PRIVILEGES;
   ```

4. **Update .env file** with correct credentials

**Issue**: Database doesn't exist

**Solutions**:

```sql
-- Create database manually
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant permissions
GRANT ALL PRIVILEGES ON ictserve.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

#### Migration Issues

**Issue**: Migration fails with foreign key constraints

**Solutions**:

1. **Run migrations in order**:

   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Check database engine**: Ensure InnoDB is used
3. **Disable foreign key checks temporarily**:

   ```sql
   SET FOREIGN_KEY_CHECKS=0;
   -- Run migrations
   SET FOREIGN_KEY_CHECKS=1;
   ```

### Composer Issues

#### Dependency Installation Problems

**Issue**: Composer install fails with memory errors

**Solutions**:

```bash
# Increase memory limit
COMPOSER_MEMORY_LIMIT=-1 composer install

# Or update php.ini
memory_limit = 512M
```

**Issue**: SSL certificate problems

**Solutions**:

```bash
# Disable SSL verification (development only)
composer config --global disable-tls true

# Or update certificates
composer self-update --update-keys
```

**Issue**: Vendor directory permissions

**Solutions**:

```powershell
# Windows - Run as Administrator
# Or check folder permissions in Properties > Security
```

### Node.js and NPM Issues

#### NPM Installation Problems

**Issue**: npm install fails with permission errors

**Solutions**:

```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and package-lock.json
rm -rf node_modules package-lock.json
npm install

# Or use npm ci for clean install
npm ci
```

**Issue**: Node version compatibility

**Solutions**:

```bash
# Check Node version (require 22.12+)
node --version

# Update Node.js from https://nodejs.org/
# Or use nvm for Windows
```

**Issue**: Vite build fails

**Solutions**:

```bash
# Clear Vite cache
rm -rf node_modules/.vite

# Rebuild
npm run build

# Check for port conflicts
netstat -ano | findstr :5173
```

### Laravel Application Issues

#### Environment Configuration

**Issue**: APP_KEY not set

**Solutions**:

```bash
# Generate new key
php artisan key:generate

# Or manually set in .env
APP_KEY=base64:your-generated-key-here
```

**Issue**: Storage permissions

**Solutions**:

```powershell
# Windows - Check folder permissions
# Right-click storage folder > Properties > Security
# Ensure IUSR and IIS_IUSRS have full control

# Or run as Administrator
```

**Issue**: Cache issues

**Solutions**:

```bash
# Clear all caches
php artisan optimize:clear

# Individual cache clearing
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### Service Startup Issues

**Issue**: Laravel server won't start

**Solutions**:

```bash
# Check if port 8000 is in use
netstat -ano | findstr :8000

# Use different port
php artisan serve --port=8001

# Check for errors
php artisan serve --verbose
```

**Issue**: Queue worker stops

**Solutions**:

```bash
# Restart queue worker
php artisan queue:restart

# Check for failed jobs
php artisan queue:failed

# Monitor queue
php artisan queue:monitor
```

### Redis Issues (Optional)

#### Redis Connection Problems

**Issue**: Redis connection refused

**Solutions**:

1. **Check Redis service**:

   ```bash
   redis-cli ping
   # Should return PONG
   ```

2. **Start Redis service**:

   ```bash
   # WSL
   sudo systemctl start redis-server
   
   # Windows Redis
   redis-server.exe
   ```

3. **Update .env configuration**:

   ```env
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   REDIS_PASSWORD=null
   ```

**Issue**: Redis not installed

**Solutions**:

1. **Install via WSL**:

   ```bash
   wsl --install
   wsl
   sudo apt update && sudo apt install redis-server
   ```

2. **Or use file-based cache**:

   ```env
   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=database
   ```

### Performance Issues

#### Slow Application Response

**Solutions**:

1. **Enable OPcache** in php.ini:

   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=4000
   ```

2. **Use Redis for caching**:

   ```env
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   ```

3. **Optimize database**:

   ```sql
   -- Add indexes for frequently queried columns
   -- Optimize MySQL configuration
   ```

#### High Memory Usage

**Solutions**:

1. **Increase PHP memory limit**:

   ```ini
   memory_limit = 512M
   ```

2. **Optimize Composer autoloader**:

   ```bash
   composer dump-autoload --optimize
   ```

3. **Use production asset build**:

   ```bash
   npm run build
   ```

### Security Issues

#### File Permissions

**Issue**: Storage directories not writable

**Solutions**:

```powershell
# Windows - Set permissions via GUI
# Right-click folder > Properties > Security
# Add IUSR with Full Control

# Or use icacls command
icacls storage /grant IUSR:F /T
icacls bootstrap\cache /grant IUSR:F /T
```

#### SSL Certificate Issues

**Issue**: HTTPS not working

**Solutions**:

1. **Generate self-signed certificate**:

   ```bash
   # In XAMPP Apache folder
   makecert -r -pe -n "CN=localhost" -ss my -sr LocalMachine -a sha256 -sky exchange -sp "Microsoft RSA SChannel Cryptographic Provider" -sy 12 localhost.cer
   ```

2. **Configure Apache SSL**:
   - Enable SSL module in httpd.conf
   - Configure virtual host for HTTPS
   - Update certificate paths

## Diagnostic Commands

### System Information

```powershell
# Check PHP configuration
php --ini
php -m  # List loaded modules
php -v  # PHP version

# Check Composer
composer --version
composer diagnose

# Check Node.js
node --version
npm --version
npm doctor

# Check MySQL
mysql --version
mysql -u root -e "SELECT VERSION();"

# Check services
netstat -ano | findstr :80    # Apache
netstat -ano | findstr :3306  # MySQL
netstat -ano | findstr :8000  # Laravel
netstat -ano | findstr :5173  # Vite
```

### Laravel Diagnostics

```bash
# Application information
php artisan about

# Check environment
php artisan env

# Test database connection
php artisan db:show

# Check routes
php artisan route:list

# Check configuration
php artisan config:show

# Check queue status
php artisan queue:monitor

# View logs
php artisan log:show
```

## Getting Additional Help

### Log Files to Check

1. **Apache Error Log**: `C:\xampp\apache\logs\error.log`
2. **MySQL Error Log**: `C:\xampp\mysql\data\mysql_error.log`
3. **PHP Error Log**: `C:\xampp\php\logs\php_error.log`
4. **Laravel Log**: `storage/logs/laravel.log`

### Useful Resources

1. **XAMPP Documentation**: <https://www.apachefriends.org/docs/>
2. **Laravel Documentation**: <https://laravel.com/docs/12.x>
3. **Composer Documentation**: <https://getcomposer.org/doc/>
4. **Node.js Documentation**: <https://nodejs.org/docs/>

### Support Channels

1. **ICTServe Documentation**: Check main project documentation
2. **Laravel Community**: <https://laravel.com/community>
3. **XAMPP Forums**: <https://community.apachefriends.org/>
4. **Stack Overflow**: Tag questions with `laravel`, `xampp`, `php`

---

**Note**: If you continue to experience issues, consider using the Docker setup for a more consistent development environment.
