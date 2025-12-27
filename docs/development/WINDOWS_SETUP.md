# Windows Development Setup Guide

This document explains the Windows-specific setup configuration for ICTServe and how to handle platform-specific PHP extensions.

## Platform Requirements Configuration

### The Issue

On Windows systems, Laravel Horizon (v5.41.0) and Laravel Reverb require PHP extensions that only exist on Unix/Linux systems:

- `ext-pcntl` (Process Control) - Unix/Linux only
- `ext-posix` (POSIX functions) - Unix/Linux only

These extensions cannot be installed on Windows, as Windows uses a different process model than Unix.

### The Solution

ICTServe is configured to gracefully handle this by:

1. **`composer.json` Configuration**:

   ```json
   "config": {
       "platform-check": false,
       "ignore-platform-req": ["ext-pcntl", "ext-posix"],
       "platform": {"php": "8.4.11"}
   }
   ```

   - `platform-check: false` — Disables strict platform requirement validation
   - `ignore-platform-req` — Explicitly ignores unavailable Unix extensions
   - `platform: php 8.4.11` — Sets the platform to the actual PHP version available

2. **Composer Scripts**:
   The `setup` and `install-dependencies` scripts are configured to use `--ignore-platform-reqs` flag automatically when running on Windows.

## Installation Instructions

### Fresh Installation

```bash
# Clone the repository
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# Install dependencies (handles platform requirements automatically)
composer install --no-interaction --prefer-dist

# Or use the setup script
composer run setup
```

### Using the Setup Script

The simplest way to set up the development environment:

```bash
composer run setup
```

This script will:

1. Install PHP dependencies (with platform requirements ignored)
2. Create `.env` from `.env.example`
3. Generate Laravel application key
4. Run database migrations
5. Install Node dependencies
6. Build frontend assets

### Using the Install Script

For just installing dependencies:

```bash
composer run install-dependencies
```

This is equivalent to:

```bash
composer install --no-interaction --prefer-dist --ignore-platform-reqs
```

## Troubleshooting

### "ext-pcntl is missing" Error

If you see this error:

```
laravel/horizon v5.41.0 requires ext-pcntl * -> it is missing from your system.
```

**Solution**: Use the explicit flag:

```bash
composer install --no-interaction --prefer-dist --ignore-platform-reqs
```

Or use the configured scripts:

```bash
composer run install-dependencies
composer run setup
```

### "Platform mismatch" Error

If Composer complains about PHP version mismatch:

1. Check your actual PHP version:

   ```bash
   php --version
   ```

2. Update `composer.json` `platform` setting to match:

   ```json
   "config": {
       "platform": {"php": "X.Y.Z"}
   }
   ```

3. Regenerate lock file:

   ```bash
   composer update --no-interaction --prefer-dist --ignore-platform-reqs
   ```

### Frontend Assets Not Updating

If changes to Vue/Livewire components aren't appearing:

```bash
# Rebuild frontend assets
npm run build

# Or watch for changes during development
npm run dev
```

## Docker Alternative

If you prefer a Linux-based development environment, use Docker Compose:

```bash
docker-compose -f compose.dev.yaml up --build
```

This provides a complete Linux environment with all extensions available.

## Architecture on Windows

The application is designed to work on Windows while maintaining compatibility with Linux deployments:

- **Production**: Runs on Linux servers with all extensions available
- **Development (Windows)**: Gracefully ignores unavailable Unix extensions
- **Development (Linux/Docker)**: Full feature set with all extensions

Laravel Horizon and Reverb are optional features:

- **Windows**: Can be installed but some features may not work
- **Production/Linux**: Full functionality

## Advanced: Conditional Configuration

If you need different configurations for Windows vs. Linux, you can use environment-specific `.env` files:

```bash
# For Windows-specific settings
cp .env.example .env.windows
# Edit .env.windows with Windows-specific values

# Use it with:
php --ini
# Set PHP_ENV=windows in your environment
```

Or use Docker Compose for consistent Linux environments on Windows.

## Related Files

- [`composer.json`](./composer.json) - Dependency and script configuration
- [`package.json`](./package.json) - Frontend build configuration
- [`.env.example`](./.env.example) - Environment template
- [`compose.dev.yaml`](./compose.dev.yaml) - Docker dev environment

## Support

For issues specific to Windows development:

1. Check this guide first
2. Ensure PHP 8.4+ is installed and available in PATH
3. Verify Composer is installed: `composer --version`
4. Try the Docker Compose alternative for a guaranteed Linux environment

---

**Last Updated**: 2025-12-25  
**PHP Version**: 8.4.11  
**Composer**: 2.x
