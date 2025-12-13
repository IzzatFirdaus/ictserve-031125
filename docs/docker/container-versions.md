# Container Software Versions

## App Container (`php:8.2-fpm-alpine`)

### Core Software

| Software | Version | Installation Method |
|----------|---------|-------------------|
| **PHP** | 8.2-fpm | Base image |
| **Composer** | Latest stable | getcomposer.org installer |
| **Node.js** | Recommended: 22.14.0 (LTS) | Alpine package (`nodejs`) |
| **npm** | Bundled with Node.js | Alpine package (`npm`) |

### PHP Extensions

- `pdo_mysql` - MySQL database driver
- `zip` - ZIP archive support
- `mbstring` - Multibyte string handling
- `intl` - Internationalization
- `bcmath` - Arbitrary precision mathematics
- `opcache` - PHP opcode cache

### System Packages

- bash, git, curl, zip/unzip
- mysql-client, netcat-openbsd

### Build Process

1. Composer dependencies installed during image build
2. npm dependencies installed during image build (`npm ci`)
3. Assets compiled during image build (`npm run build`)
4. Autoload optimization generated

### Runtime

- **Port**: 8000
- **Command**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Working Directory**: `/var/www/html`

## Database Container (`mysql:8.0`)

- **Port**: 3306
- **Database**: ictserve
- **User**: laravel
- **Password**: secret

## Nginx Container (`nginx:alpine`)

- **Port**: 80
- **Proxy**: HTTP to app:8000

## Version Verification

```powershell
# PHP version
docker compose exec app php -v

# Composer version
docker compose exec app composer --version

# Node.js version
docker compose exec app node --version

# npm version
docker compose exec app npm --version
```

If your image does not include Node v22, install Node 22 at build time or use a `node:22` runtime stage in your multi-stage build to ensure consistent builds and Vite compatibility.
