# Docker Troubleshooting

Common issues and solutions for ICTServe Docker deployment.

## Build Issues

### Build Context Too Large

**Symptom**: `transferring context: 10.46GB` causing timeout

**Solution**: Verify `.dockerignore` excludes large directories

```bash
# Check build context size
docker build --no-cache -t test . 2>&1 | grep "transferring context"

# Should show ~72MB, not 10GB
```

**Fix**: Ensure `.dockerignore` contains:

```
vendor/
node_modules/
Mimir/
docs/
tests/
storage/logs/
```

### Build Timeout

**Symptom**: Build hangs or times out

**Solution**: Increase Docker build timeout

```bash
# Set timeout to 30 minutes
DOCKER_BUILDKIT=1 docker build --progress=plain --no-cache -t ictserve-app .
```

### Composer Install Fails

**Symptom**: `composer install` fails in Dockerfile

**Solution**: Clear composer cache and retry

```dockerfile
RUN composer clear-cache && \
    composer install --no-interaction --prefer-dist --optimize-autoloader
```

## Runtime Issues

### Database Connection Failed

**Symptom**: `SQLSTATE[HY000] [2002] Connection refused`

**Cause**: App container trying to connect before DB is ready

**Solution**: Use wait-for-db.sh script (already configured)

```bash
# Verify wait script exists
docker compose exec app ls -la /usr/local/bin/wait-for-db.sh

# Check DB health
docker compose ps db
# Should show "healthy"
```

### Port Already in Use

**Symptom**: `Error starting userland proxy: listen tcp4 0.0.0.0:8000: bind: address already in use`

**Solution**: Stop conflicting service or change port

```bash
# Find process using port 8000
netstat -ano | findstr :8000

# Kill process (Windows)
taskkill /PID <PID> /F

# Or change port in compose.yaml
ports:
  - "8001:80"  # Use 8001 instead
```

### Permission Denied

**Symptom**: `Permission denied` when accessing files

**Solution**: Fix file permissions

```bash
# Fix storage permissions
docker compose exec app chmod -R 775 storage bootstrap/cache

# Fix ownership
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Artisan Commands Fail

**Symptom**: `php artisan` commands not working

**Solution**: Run commands inside container

```bash
# Correct way
docker compose exec app php artisan migrate

# Not: php artisan migrate (runs on host)
```

### Make Command Not Found (Windows)

**Symptom**: `make: The term 'make' is not recognized`

**Solution**: Use PowerShell scripts instead

```powershell
# Instead of: make build
.\scripts\docker\build.ps1

# Instead of: make artisan cmd="migrate"
.\scripts\docker\artisan.ps1 migrate

# Instead of: make composer cmd="install"
.\scripts\docker\composer.ps1 install
```

See [WINDOWS.md](WINDOWS.md) for complete guide.

### npm Not Found in Container

**Symptom**: `npm: executable file not found in $PATH`

**Cause**: Old image without Node.js

**Solution**: Rebuild container

```bash
# Rebuild with Node.js
docker compose build app
docker compose up -d app

# Verify npm installed
docker compose exec app npm --version
```

## MCP Service Issues

### MCP Container Not Starting

**Symptom**: MCP service exits immediately

**Solution**: Check logs for errors

```bash
# View logs
docker compose logs mcp-memory

# Restart service
docker compose restart mcp-memory
```

### MCP Storage Permission Denied

**Symptom**: MCP memory server can't write to storage

**Solution**: Fix storage permissions

```bash
# Create MCP storage directory
mkdir -p storage/mcp

# Fix permissions
chmod -R 775 storage/mcp
```

## Network Issues

### Cannot Access Application

**Symptom**: `curl http://localhost:8000` fails

**Solution**: Verify nginx and app are running

```bash
# Check all services
docker compose ps

# Check nginx logs
docker compose logs nginx

# Check app logs
docker compose logs app

# Test from inside nginx container
docker compose exec nginx curl http://app:8000
```

### DNS Resolution Failed

**Symptom**: `db` hostname not resolving

**Solution**: Verify containers on same network

```bash
# List networks
docker network ls

# Inspect network
docker network inspect ictserve-031125_default

# Verify all containers listed
```

## Performance Issues

### Slow Build Times

**Solution**: Use BuildKit and layer caching

```bash
# Enable BuildKit
export DOCKER_BUILDKIT=1

# Build with cache
docker compose build --parallel
```

### High Memory Usage

**Solution**: Set resource limits

```yaml
# compose.yaml
services:
  app:
    deploy:
      resources:
        limits:
          memory: 2G
```

### Slow File Sync (Windows/Mac)

**Solution**: Use cached or delegated mounts

```yaml
volumes:
  - ./:/var/www/html:cached  # Faster on Mac/Windows
```

## Database Issues

### Database Not Initialized

**Symptom**: `Unknown database 'ictserve'`

**Solution**: Recreate database volume

```bash
# Stop services
docker compose down

# Remove database volume
docker volume rm ictserve-031125_db-data

# Start services (will recreate DB)
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --seed
```

### Migration Fails

**Symptom**: Migration errors

**Solution**: Reset database

```bash
# Fresh migration
docker compose exec app php artisan migrate:fresh --seed

# Or rollback and retry
docker compose exec app php artisan migrate:rollback
docker compose exec app php artisan migrate
```

## Cleanup

### Remove All Containers

```bash
# Stop and remove containers
docker compose down

# Remove volumes too
docker compose down -v
```

### Clean Docker System

```bash
# Remove unused containers, networks, images
docker system prune -a

# Remove volumes
docker volume prune
```

### Reset Everything

```bash
# Nuclear option: remove everything
docker compose down -v
docker system prune -a --volumes
docker compose build --no-cache
docker compose up -d
```

## Debugging

### Shell into Container

```bash
# App container
docker compose exec app sh

# Database container
docker compose exec db sh

# Nginx container
docker compose exec nginx sh
```

### View Real-time Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app

# Last 100 lines
docker compose logs --tail=100 app
```

### Inspect Container

```bash
# Container details
docker inspect ictserve-app

# Network details
docker network inspect ictserve-031125_default

# Volume details
docker volume inspect ictserve-031125_db-data
```

## Getting Help

If issues persist:

1. Check [GitHub Issues](https://github.com/IzzatFirdaus/ictserve-031125/issues)
2. Review [Docker logs](#view-real-time-logs)
3. Verify [environment configuration](SETUP.md#environment-configuration)
4. Try [reset everything](#reset-everything)

## Next Steps

- [Setup Guide](SETUP.md) - Installation instructions
- [Architecture](ARCHITECTURE.md) - Container design
- [Development](DEVELOPMENT.md) - Development workflow
