# Docker Setup Guide

## Quick Start

```bash
# Clone repository
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# Start everything
make setup

# Initialize database
make migrate
make seed
```

## Common Commands

```bash
# Start containers
make up

# Stop containers
make stop

# Run artisan commands
make artisan cmd=migrate
make artisan cmd="cache:clear"

# Run composer
docker compose exec app composer install

# Run npm
docker compose exec app npm run dev

# View logs
make logs

# Open shell
make shell
```

## File Structure

- `Dockerfile` - PHP 8.2 FPM Alpine image
- `docker-compose.yml` - Services orchestration
- `nginx.conf` - Web server configuration
- `.env.docker` - Docker environment variables
- `Makefile` - Command shortcuts

## Troubleshooting

**Permission errors:**
```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**Database connection failed:**
Ensure `.env` uses `DB_HOST=db` (not `127.0.0.1`)

**Port 8000 already in use:**
```bash
# Stop XAMPP or change port in docker-compose.yml
ports:
  - "8080:8000"
```
