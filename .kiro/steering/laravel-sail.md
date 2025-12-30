---
inclusion:
  fileMatchPattern:
    - 'docker-compose.yml'
    - 'docker-compose.*.yml'
    - '.env.sail'
  applyWhen:
    - Docker-based development with Laravel Sail
    - Container orchestration
    - Local development environment setup
---

# Laravel Sail Docker Environment Guidelines

Laravel Sail provides Docker-based local development environment for Laravel.

**CRITICAL**: ICTServe does NOT use Laravel Sail. The project uses:

1. **Laragon** (Windows local development)
2. **Custom Docker setup** (production deployment)
3. **XAMPP** (alternative local development)

## ICTServe Development Stack

- **Local**: Laragon with PHP 8.4.11, MySQL 8.0, Redis 7.0
- **Production**: Custom Docker containers (see `docs/docker/`)
- **CI/CD**: GitHub Actions with native PHP/MySQL

## If Sail is Required

```bash
# Install Sail
composer require laravel/sail --dev
php artisan sail:install

# Start services
./vendor/bin/sail up -d

# Common commands
sail artisan migrate
sail composer install
sail npm run dev
sail test
```

## Why Not Sail for ICTServe

1. **Windows Compatibility**: Laragon provides better Windows integration
2. **Performance**: Native PHP faster than Docker on Windows
3. **Existing Setup**: Team familiar with Laragon/XAMPP
4. **Production Parity**: Custom Docker setup matches production

Do not implement Sail unless explicitly requested for cross-platform development needs.
