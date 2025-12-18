# ICTServe Docker Configuration

This directory contains Docker configuration files for the ICTServe Laravel application with proper Node.js permission handling.

## Quick Start

### Development Mode (Recommended)

```bash
# Complete setup with hot reloading
.\scripts\docker\setup-ictserve.ps1 -Mode development

# Or using compose directly
docker compose -f compose.yaml -f compose.dev.yaml up -d
```

### Production Mode

```bash
# Production setup with built assets
.\scripts\docker\setup-ictserve.ps1 -Mode production

# Or using compose directly
docker compose up -d
```

## Node.js Permission Fix

If you encounter npm permission errors, use the quick fix script:

```bash
.\scripts\docker\npm-fix.ps1
```

## Architecture

### Multi-Stage Build

The Dockerfile uses a multi-stage build approach:

1. **Stage 1 (node-builder)**: Builds frontend assets with proper Node.js permissions
2. **Stage 2 (php-runtime)**: Creates the final PHP runtime with built assets

### Services

- **app**: Main Laravel application (PHP-FPM)
- **nginx**: Web server with development/production configs
- **db**: MySQL 8.0 database
- **redis**: Redis for caching, sessions, and queues
- **reverb**: Laravel Reverb WebSocket server
- **vite**: Development-only Vite server for hot reloading
- **mcp-***: MCP servers for AI integration

## Configuration Files

### Nginx Configurations

- `nginx/dev.conf`: Development configuration with Vite proxy
- `nginx/prod.conf`: Production configuration with security headers

### PHP Configuration

- `php/php.ini`: Optimized PHP settings for ICTServe

### Docker Compose Files

- `compose.yaml`: Base configuration for all environments
- `compose.dev.yaml`: Development overrides with volume mounts
- `compose.base.yaml`: Minimal base configuration

## Development Workflow

### Starting Development Environment

```bash
# Full setup (recommended for first time)
.\scripts\docker\setup-ictserve.ps1 -Mode development -Clean

# Quick start (if already set up)
docker compose -f compose.yaml -f compose.dev.yaml up -d
```

### Common Development Commands

```bash
# Run npm commands
docker compose exec --user www-data app npm run dev
docker compose exec --user www-data app npm run build

# Laravel commands
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# View logs
docker compose logs -f app
docker compose logs -f vite

# Shell access
docker compose exec app bash
docker compose exec --user www-data app bash
```

### Hot Reloading

In development mode, Vite runs on port 5173 and provides hot module replacement:

- Application: <http://localhost:8000>
- Vite Dev Server: <http://localhost:5173>

The nginx configuration automatically proxies Vite requests for seamless development.

## Production Deployment

### Building for Production

```bash
# Clean build
.\scripts\docker\setup-ictserve.ps1 -Mode production -Clean

# Build assets
docker compose exec --user www-data app npm run build

# Optimize Laravel
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### Environment Variables

Production deployments should use:

- `.env.docker` for container environment
- Proper database credentials
- Redis configuration
- SSL certificates for nginx

## Troubleshooting

### Node.js Permission Issues

**Problem**: `npm run build` fails with permission errors

**Solution**:

```bash
.\scripts\docker\npm-fix.ps1
```

### Container Won't Start

**Problem**: Services fail to start

**Solution**:

```bash
# Check logs
docker compose logs

# Clean restart
docker compose down --volumes
.\scripts\docker\setup-ictserve.ps1 -Clean
```

### Database Connection Issues

**Problem**: Laravel can't connect to database

**Solution**:

```bash
# Check database status
docker compose exec db mysqladmin ping -h localhost -u root -psecret

# Reset database
docker compose down db
docker volume rm ictserve_dbdata
docker compose up -d db
```

### Asset Build Failures

**Problem**: Vite build fails or assets not loading

**Solution**:

```bash
# Clear npm cache and rebuild
docker compose exec app rm -rf node_modules
docker compose exec --user www-data app npm ci
docker compose exec --user www-data app npm run build
```

## File Permissions

The Docker setup handles permissions automatically:

- **www-data (82:82)**: PHP-FPM and Laravel files
- **nodeuser (1001:1001)**: Node.js build process (build stage only)
- **npm cache**: `/var/www/.npm-cache` (owned by www-data)
- **npm global**: `/var/www/.npm-global` (owned by www-data)

## Security Considerations

### Development

- Uses HTTP for local development
- Debug mode enabled
- Relaxed security headers

### Production

- HTTPS recommended (configure nginx SSL)
- Security headers enabled
- OPcache optimizations
- Error logging only

## Performance Optimizations

### PHP

- OPcache enabled with optimized settings
- Realpath cache configured
- Memory limits appropriate for ICTServe

### Node.js

- Multi-stage build reduces final image size
- npm ci for faster, reliable installs
- Asset optimization via Vite

### Database

- MySQL 8.0 with health checks
- Persistent volumes for data
- Connection pooling ready

## Monitoring

### Health Checks

All services include health checks:

- **Database**: MySQL ping
- **Redis**: Redis ping
- **Application**: PHP version check
- **Nginx**: HTTP response check

### Logs

Centralized logging via Docker:

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f nginx
```

## Integration with ICTServe

### Laravel Features

- **Reverb**: WebSocket server for real-time features
- **Horizon**: Queue management (if enabled)
- **Telescope**: Debug assistant (development only)
- **Pulse**: Performance monitoring

### MCP Servers

- **Memory**: Knowledge graph storage
- **Sequential Thinking**: Problem decomposition
- **Playwright**: Browser automation
- **Chrome DevTools**: Browser debugging

### Compliance

- **PDPA 2010**: Data protection compliance
- **WCAG 2.2 AA**: Accessibility standards
- **PSR-12**: PHP coding standards
- **MyGOV**: Malaysian government standards

## Backup and Recovery

### Database Backup

```bash
# Create backup
docker compose exec db mysqldump -u laravel -psecret ictserve > backup.sql

# Restore backup
docker compose exec -T db mysql -u laravel -psecret ictserve < backup.sql
```

### Volume Backup

```bash
# Backup volumes
docker run --rm -v ictserve_dbdata:/data -v $(pwd):/backup alpine tar czf /backup/dbdata.tar.gz -C /data .

# Restore volumes
docker run --rm -v ictserve_dbdata:/data -v $(pwd):/backup alpine tar xzf /backup/dbdata.tar.gz -C /data
```

## Support

For issues specific to the Docker setup:

1. Check this README
2. Run the diagnostic scripts
3. Check Docker and container logs
4. Verify system requirements

For ICTServe application issues, refer to the main project documentation.
