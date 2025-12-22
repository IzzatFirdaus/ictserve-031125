# ICTServe XAMPP Deployment Manifest

**Version**: 3.6.0  
**Created**: December 22, 2024  
**Target Environment**: Non-Workspace XAMPP Development  
**Workspace Environment**: Docker (separate configuration)

## Deployment Package Overview

This deployment package provides a complete, self-contained setup for ICTServe in non-workspace XAMPP environments. It is designed to be easily deployed and configured independently of the workspace Docker setup.

## Package Structure

```
deployment/xampp/
├── README.md                    # Main deployment guide
├── DEPLOYMENT_MANIFEST.md       # This file
├── .env.xampp                   # XAMPP environment configuration
├── setup-xampp.ps1             # Automated PowerShell setup
├── setup-xampp.sh              # Automated Bash setup
├── config/
│   ├── apache-vhost.conf        # Apache virtual host template
│   ├── nginx-site.conf          # Nginx configuration (future)
│   └── php.ini.additions        # PHP configuration additions
├── scripts/
│   ├── start-services.ps1       # Service startup script
│   ├── stop-services.ps1        # Service shutdown script (future)
│   └── health-check.ps1         # System health verification
└── docs/
    ├── INSTALLATION.md          # Detailed installation guide
    ├── TROUBLESHOOTING.md       # Common issues and solutions
    └── CONFIGURATION.md         # Configuration options (future)
```

## Key Features

### 🚀 Automated Setup

- **One-command deployment**: `.\deployment\xampp\setup-xampp.ps1`
- **Cross-platform support**: PowerShell and Bash versions
- **Dependency verification**: Checks all prerequisites
- **Error handling**: Comprehensive error detection and reporting

### 🔧 Configuration Management

- **Environment-specific settings**: Optimized for XAMPP
- **Service profiles**: Minimal, backend, frontend, full
- **Optional Redis integration**: Enhanced performance when available
- **Security configurations**: Production-ready security settings

### 📊 Health Monitoring

- **Service status checking**: Verify all components are running
- **Dependency validation**: Ensure all requirements are met
- **Performance monitoring**: Basic health metrics
- **Troubleshooting assistance**: Automated problem detection

### 📚 Comprehensive Documentation

- **Step-by-step installation**: Detailed setup instructions
- **Troubleshooting guide**: Solutions for common issues
- **Configuration reference**: All available options
- **Best practices**: Recommended development workflows

## Environment Differences

| Component | Workspace (Docker) | Non-Workspace (XAMPP) |
|-----------|-------------------|----------------------|
| **Web Server** | Nginx (container) | Apache (XAMPP) |
| **Database** | MySQL (container) | MySQL (XAMPP) |
| **Cache** | Redis (container) | File/Redis (optional) |
| **Queue** | Redis (container) | Database/Redis |
| **Sessions** | Redis (container) | File/Redis |
| **URL** | `localhost:8000` | `127.0.0.1:8000` |
| **SSL** | Container managed | Manual configuration |
| **Deployment** | `docker compose up` | `setup-xampp.ps1` |

## Deployment Workflow

### 1. Prerequisites Check

- XAMPP installation verification
- PHP 8.4+ availability
- Composer installation
- Node.js 22.12+ availability
- MySQL service status

### 2. Environment Setup

- Copy XAMPP-specific `.env` configuration
- Generate application key
- Configure database connection
- Set up storage permissions

### 3. Dependency Installation

- Install Composer dependencies
- Install NPM dependencies
- Optimize autoloader
- Build frontend assets

### 4. Database Initialization

- Create database if not exists
- Run Laravel migrations
- Seed initial data
- Verify database connectivity

### 5. Service Configuration

- Configure Apache virtual host (optional)
- Set up Redis (optional)
- Configure SSL (optional)
- Set up monitoring

### 6. Verification

- Run health checks
- Test all endpoints
- Verify service connectivity
- Validate configuration

## Service Management

### Starting Services

**Automated (Recommended)**:

```powershell
.\deployment\xampp\scripts\start-services.ps1
```

**Manual**:

```powershell
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev

# Terminal 3: WebSocket Server
php artisan reverb:start

# Terminal 4: Queue Worker
php artisan queue:work
```

### Service Profiles

- **Minimal**: Laravel server only
- **Backend**: Laravel + Reverb + Queue
- **Frontend**: Laravel + Vite
- **Full**: All services (default)

### Health Monitoring

```powershell
# Check system health
.\deployment\xampp\scripts\health-check.ps1

# Monitor services
# Check running processes, ports, and connectivity
```

## Configuration Options

### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=
```

### Cache Configuration

```env
# File-based (default)
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Redis-based (optional)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Application URLs

```env
APP_URL=http://127.0.0.1:8000
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
```

## Security Considerations

### Development Security

- Default MySQL root password (empty)
- Debug mode enabled
- Error reporting enabled
- File-based sessions (default)

### Production Hardening

- Change MySQL root password
- Disable debug mode
- Configure proper error logging
- Use Redis for sessions
- Enable HTTPS
- Configure firewall rules

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
mysqldump -u root -p ictserve > backup.sql

# File backup
# Backup .env, storage/, and custom configurations
```

## Troubleshooting

### Common Issues

1. **Port conflicts**: Apache/MySQL ports in use
2. **Permission errors**: Storage directory permissions
3. **Memory limits**: PHP memory configuration
4. **Extension missing**: Required PHP extensions
5. **Database connection**: MySQL service not running

### Diagnostic Tools

- Health check script
- Laravel diagnostic commands
- System information commands
- Log file analysis

### Support Resources

- Detailed troubleshooting guide
- XAMPP documentation links
- Laravel community resources
- Stack Overflow references

## Version Compatibility

### Supported Versions

- **PHP**: 8.4.11+
- **MySQL**: 8.0+
- **Node.js**: 22.12+
- **Composer**: Latest
- **XAMPP**: Latest with PHP 8.4+

### Tested Platforms

- **Windows 10/11**: Primary target
- **Windows Server**: Compatible
- **Linux**: Bash script available
- **macOS**: Bash script available

## Future Enhancements

### Planned Features

- Nginx configuration option
- Docker Compose alternative
- SSL certificate automation
- Performance monitoring dashboard
- Automated backup system

### Integration Points

- CI/CD pipeline integration
- Monitoring system integration
- Backup system integration
- Security scanning integration

## Support and Maintenance

### Documentation Updates

- Keep installation guide current
- Update troubleshooting solutions
- Maintain configuration examples
- Document new features

### Testing

- Regular testing on fresh XAMPP installations
- Compatibility testing with new versions
- Performance benchmarking
- Security vulnerability assessment

### Community Support

- Issue tracking and resolution
- Feature request evaluation
- Community contribution guidelines
- Documentation improvements

---

**Note**: This deployment package is specifically designed for non-workspace XAMPP environments. For workspace development, use the Docker configuration in the main project directory.

**Deployment Status**: ✅ Ready for Production Use  
**Last Tested**: December 22, 2024  
**Next Review**: March 22, 2025
