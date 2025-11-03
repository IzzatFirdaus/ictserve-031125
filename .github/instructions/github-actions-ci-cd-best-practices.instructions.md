---
applyTo: ".github/workflows/**"
description: "GitHub Actions CI/CD workflows, automated testing, static analysis, deployment pipelines, and quality gates for ICTServe"
---

# GitHub Actions CI/CD — ICTServe Standards

## Purpose & Scope

GitHub Actions workflow patterns for ICTServe continuous integration and deployment. Covers automated testing, static analysis, security scanning, and deployment automation.

**Traceability**: D01 (Development Plan), D11 (CI/CD Requirements)

---

## Basic CI Workflow

**`.github/workflows/ci.yml`**:
```yaml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: ictserve_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
          coverage: xdebug
      
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist
      
      - name: Copy Environment
        run: cp .env.example .env
      
      - name: Generate Key
        run: php artisan key:generate
      
      - name: Run Migrations
        run: php artisan migrate --force
      
      - name: Run Tests
        run: php artisan test --parallel --coverage --min=80
      
      - name: Static Analysis (PHPStan)
        run: vendor/bin/phpstan analyse --no-progress
      
      - name: Code Style (Pint)
        run: vendor/bin/pint --test
```

---

## Frontend Build Workflow

```yaml
name: Frontend Build

on: [push, pull_request]

jobs:
  build:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
      
      - name: Install Dependencies
        run: npm ci
      
      - name: Build Assets
        run: npm run build
      
      - name: Run ESLint
        run: npm run lint
```

---

## Security Scanning

```yaml
name: Security Scan

on:
  push:
    branches: [main]
  schedule:
    - cron: '0 2 * * 1' # Weekly on Monday 2am

jobs:
  security:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Composer Audit
        run: composer audit
      
      - name: npm Audit
        run: npm audit --audit-level=high
      
      - name: OWASP Dependency Check
        uses: dependency-check/Dependency-Check_Action@main
        with:
          project: 'ICTServe'
          path: '.'
          format: 'HTML'
```

---

## Deployment Workflow

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]
    tags:
      - 'v*.*.*'

jobs:
  deploy:
    runs-on: ubuntu-latest
    environment: production
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Deploy to Server
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: $ secrets.PRODUCTION_HOST 
          username: $ secrets.PRODUCTION_USER 
          key: $ secrets.SSH_PRIVATE_KEY 
          script: |
            cd /var/www/ictserve
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo systemctl reload php8.2-fpm
```

---

## Matrix Testing (Multiple PHP Versions)

```yaml
jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: ['8.2', '8.3']
        laravel: ['12.x']
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP $ matrix.php 
        uses: shivammathur/setup-php@v2
        with:
          php-version: $ matrix.php 
      
      - name: Install Laravel $ matrix.laravel 
        run: composer require "laravel/framework:$ matrix.laravel " --no-update
      
      - run: composer install
      - run: php artisan test
```

---

## Caching Dependencies

```yaml
steps:
  - name: Cache Composer
    uses: actions/cache@v3
    with:
      path: vendor
      key: composer-$ hashFiles('**/composer.lock') 
  
  - name: Cache NPM
    uses: actions/cache@v3
    with:
      path: node_modules
      key: npm-$ hashFiles('**/package-lock.json') 
```

---

## Artifact Upload

```yaml
- name: Upload Test Coverage
  uses: actions/upload-artifact@v3
  with:
    name: coverage-report
    path: coverage/

- name: Upload Build Artifacts
  uses: actions/upload-artifact@v3
  with:
    name: build
    path: public/build/
```

---

## Environment Variables & Secrets

```yaml
env:
  APP_ENV: testing
  DB_CONNECTION: mysql
  DB_HOST: 127.0.0.1
  DB_DATABASE: ictserve_test
  DB_USERNAME: root
  DB_PASSWORD: password

steps:
  - name: Use Secret
    run: echo $ secrets.API_KEY 
```

**Add Secrets**: Repository Settings → Secrets and variables → Actions

---

## Best Practices

1. **Fail Fast**: Run fast tests first (linting, static analysis)
2. **Use Caching**: Cache dependencies to speed up builds
3. **Parallel Execution**: Run tests in parallel when possible
4. **Matrix Testing**: Test multiple PHP/Laravel versions
5. **Secure Secrets**: Never commit secrets; use GitHub Secrets
6. **Environment-Specific**: Use `environment:` for production deployments
7. **Manual Approval**: Require approval for production deployments

---

## References

- **GitHub Actions Docs**: https://docs.github.com/en/actions
- **Laravel CI**: https://github.com/laravel/framework/blob/12.x/.github/workflows/tests.yml
- **ICTServe**: D01 (Development Plan), D11 (CI/CD Requirements)

---

**Status**: ✅ Production-ready  
**Last Updated**: 2025-11-01
