# Docker Container Specifications

## App Container

**Base Image**: `php:8.2-fpm-alpine`

### Installed Software

| Software | Version | Source |
|----------|---------|--------|
| **PHP** | 8.2-fpm | Base image |
| **Composer** | Latest stable | getcomposer.org installer |
| **Node.js** | Latest LTS | Alpine package (`nodejs`) |
| **npm** | Bundled with Node.js | Alpine package (`npm`) |

### PHP Extensions

- `pdo_mysql` - MySQL database driver
- `zip` - ZIP archive support
- `mbstring` - Multibyte string handling
- `intl` - Internationalization
- `bcmath` - Arbitrary precision mathematics
- `opcache` - PHP opcode cache

### System Packages

- `bash` - Shell
- `git` - Version control
- `curl` - HTTP client
- `zip/unzip` - Archive utilities
- `mysql-client` - MySQL CLI
- `netcat-openbsd` - Network utility

### Build Process

1. **Composer dependencies**: Installed during image build
2. **npm dependencies**: Installed during image build (`npm ci`)
3. **Asset compilation**: Built during image build (`npm run build`)
4. **Autoload optimization**: Generated during build

### Runtime

- **Port**: 8000
- **Command**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Working Directory**: `/var/www/html`
- **User**: `www-data`

## Database Container

**Base Image**: `mysql:8.0`

### Configuration

- **Port**: 3306
- **Database**: `ictserve`
- **User**: `laravel`
- **Password**: `secret`
- **Root Password**: `root`

## Nginx Container

**Base Image**: `nginx:alpine`

### Configuration

- **Port**: 80
- **Proxy Target**: `app:8000` (HTTP)
- **Config**: `/etc/nginx/nginx.conf`

## MCP Containers

### mcp-memory

- **Base**: `node:20-alpine`
- **Port**: 3100
- **Purpose**: Memory/knowledge graph server

### mcp-playwright

- **Base**: `mcr.microsoft.com/playwright:v1.49.1-noble`
- **Port**: 3101
- **Purpose**: Browser automation

### mcp-chrome-devtools

- **Base**: `node:20-alpine` + Chrome
- **Port**: 3102
- **Purpose**: Chrome DevTools Protocol

### mcp-sequential-thinking

- **Base**: `node:20-alpine`
- **Port**: 3103
- **Purpose**: Sequential reasoning

## Version Verification

Check installed versions inside containers:

```powershell
# PHP version
docker compose exec app php -v

# Composer version
docker compose exec app composer --version

# Node.js version
docker compose exec app node --version

# npm version
docker compose exec app npm --version

# MySQL version
docker compose exec db mysql --version
```
