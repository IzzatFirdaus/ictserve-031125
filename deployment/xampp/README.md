# ICTServe XAMPP Deployment Package

**Version**: 3.6.0  
**Target Environment**: Non-Workspace XAMPP Development  
**Last Updated**: December 22, 2024

## Overview

This deployment package contains everything needed to set up ICTServe in a non-workspace XAMPP environment. It provides a complete, self-contained setup that differs from the workspace Docker configuration.

## Package Contents

```
deployment/xampp/
├── README.md                 # This file
├── .env.xampp               # XAMPP environment configuration
├── setup-xampp.ps1         # Automated XAMPP setup script
├── setup-xampp.sh          # Bash version of setup script
├── config/                  # XAMPP-specific configuration files
│   ├── apache-vhost.conf    # Apache virtual host configuration
│   ├── nginx-site.conf      # Nginx site configuration (alternative)
│   └── php.ini.additions    # PHP configuration additions
├── scripts/                 # XAMPP-specific scripts
│   ├── start-services.ps1   # Start XAMPP services
│   ├── stop-services.ps1    # Stop XAMPP services
│   └── health-check.ps1     # Service health check
└── docs/                    # XAMPP-specific documentation
    ├── INSTALLATION.md      # Step-by-step installation guide
    ├── TROUBLESHOOTING.md   # XAMPP-specific troubleshooting
    └── CONFIGURATION.md     # Configuration options
```

## Quick Start

### Prerequisites

- **XAMPP**: Latest version with PHP 8.4+, MySQL 8.0+, Apache 2.4+
- **Composer**: Latest version
- **Node.js**: 22.12+ with npm
- **Git**: For cloning the repository

### Automated Setup

```powershell
# 1. Navigate to ICTServe root directory
cd path\to\ictserve

# 2. Run XAMPP deployment script
.\deployment\xampp\setup-xampp.ps1

# 3. Start XAMPP services
.\deployment\xampp\scripts\start-services.ps1
```

### Manual Setup

```powershell
# 1. Copy XAMPP environment configuration
copy deployment\xampp\.env.xampp .env

# 2. Install dependencies
composer install
npm install

# 3. Generate application key
php artisan key:generate

# 4. Setup database
mysql -u root -p -e "CREATE DATABASE ictserve;"
php artisan migrate --seed

# 5. Start services
php artisan serve
npm run dev
```

## Environment Differences

| Feature | Workspace (Docker) | Non-Workspace (XAMPP) |
|---------|-------------------|----------------------|
| **Database** | MySQL container (`db`) | Local MySQL (`127.0.0.1`) |
| **Web Server** | Nginx container | Apache/Nginx local |
| **Cache** | Redis container | File-based cache |
| **Queue** | Redis container | Database queue |
| **Sessions** | Redis container | File-based sessions |
| **URL** | `http://localhost:8000` | `http://127.0.0.1:8000` |
| **SSL** | Container managed | XAMPP SSL setup |

## Services Configuration

### Database

- **Host**: `127.0.0.1`
- **Port**: `3306`
- **Database**: `ictserve`
- **Username**: `root`
- **Password**: (empty by default)

### Web Server

- **Apache**: Port 80/443 (XAMPP default)
- **Laravel**: Port 8000 (`php artisan serve`)
- **Vite**: Port 5173 (development)

### Optional Services

- **Redis**: Port 6379 (if installed)
- **Reverb**: Port 8080 (WebSocket)
- **Horizon**: Available if Redis is configured

## Deployment Steps

### 1. Prepare Target Environment

```powershell
# Install XAMPP
# Download from: https://www.apachefriends.org/

# Install Composer
# Download from: https://getcomposer.org/

# Install Node.js
# Download from: https://nodejs.org/
```

### 2. Deploy ICTServe

```powershell
# Clone repository
git clone <repository-url> ictserve
cd ictserve

# Run deployment script
.\deployment\xampp\setup-xampp.ps1
```

### 3. Configure Services

```powershell
# Start XAMPP Control Panel
# Enable Apache and MySQL

# Configure virtual host (optional)
copy deployment\xampp\config\apache-vhost.conf C:\xampp\apache\conf\extra\

# Restart Apache
```

### 4. Verify Installation

```powershell
# Check services
.\deployment\xampp\scripts\health-check.ps1

# Access application
# http://127.0.0.1:8000
```

## Configuration Options

### Environment Variables

Key XAMPP-specific environment variables in `.env`:

```env
# Database (XAMPP MySQL)
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=

# Cache (File-based)
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# URLs (Local)
APP_URL=http://127.0.0.1:8000
REVERB_HOST=127.0.0.1
```

### Optional Redis Setup

If you want to use Redis with XAMPP:

```powershell
# Install Redis for Windows
# Download from: https://github.com/microsoftarchive/redis/releases

# Or use WSL Redis
wsl --install
wsl
sudo apt update && sudo apt install redis-server

# Update .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

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
mysqldump -u root -p ictserve > backup_$(Get-Date -Format "yyyyMMdd_HHmmss").sql

# File backup
# Backup storage/ and .env files
```

## Troubleshooting

### Common Issues

1. **Port 80 in use**: Change Apache port or stop conflicting services
2. **MySQL connection failed**: Check XAMPP MySQL service is running
3. **Permission errors**: Run as administrator or fix file permissions
4. **Composer memory limit**: Set `COMPOSER_MEMORY_LIMIT=-1`

### Support Resources

- [XAMPP Documentation](https://www.apachefriends.org/docs/)
- [Laravel Documentation](https://laravel.com/docs/12.x)
- [ICTServe Troubleshooting](docs/TROUBLESHOOTING.md)

## Security Considerations

### Development Security

- Change default MySQL root password
- Configure proper file permissions
- Use HTTPS in production
- Keep XAMPP updated

### Production Deployment

This package is designed for development environments. For production:

1. Use proper web server (Apache/Nginx)
2. Configure SSL certificates
3. Set up proper database users
4. Enable security headers
5. Configure firewall rules

## Support

For issues specific to XAMPP deployment:

1. Check [docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)
2. Review XAMPP logs in `C:\xampp\apache\logs\`
3. Check Laravel logs in `storage/logs/`
4. Verify service status with health-check script

---

**Note**: This deployment package is specifically designed for non-workspace XAMPP environments. For workspace development, use the Docker configuration in the main project directory.
