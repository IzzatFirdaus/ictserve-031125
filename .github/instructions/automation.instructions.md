---
applyTo: ".github/workflows/**,scripts/**,bin/**"
description: "Automation standards: GitHub Actions, CI/CD pipelines, deployment scripts, and quality gates for ICTServe"
---

# Automation & CI/CD Instructions

**Purpose**
Defines mandatory standards for automation, CI/CD pipelines, and scripting in ICTServe. Ensures all code merges meet strict quality, security, and traceability gates (D11).

**Scope**
Applies to `.github/workflows`, `scripts/`, `bin/`, and any task automation tools.

## 1. Pipeline Gates (Mandatory)

All Pull Requests must pass these 5 gates before merging:

1.  **Linting & Style**:
    - PHP: `vendor/bin/pint --test` (PSR-12)
    - JS/CSS: `npm run lint`
2.  **Static Analysis**:
    - `vendor/bin/phpstan analyse` (Level 9 strictness)
3.  **Testing**:
    - Unit/Feature: `php artisan test` (100% pass required)
    - E2E: `npm run test:e2e` (Playwright)
4.  **Security**:
    - `composer audit` (PHP dependencies)
    - `npm audit` (JS dependencies)
5.  **Build Integrity**:
    - `npm run build` (Must compile without error)

## 2. GitHub Actions Workflows

### Standard CI (`.github/workflows/ci.yml`)
Triggers on `push` and `pull_request` to `main` and `develop`.

```yaml
name: CI
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
      redis:
        image: redis:alpine
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
          coverage: none

      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Static Analysis
        run: vendor/bin/phpstan analyse

      - name: Run Tests
        run: php artisan test
        env:
          DB_PORT: 3306
          DB_PASSWORD: root
````

### Security Scan (`.github/workflows/security.yml`)

Runs weekly and on critical branch pushes.

```yaml
name: Security Scan
on:
  schedule:
    - cron: '0 0 * * 1' # Weekly
  push:
    branches: [main]

jobs:
  audit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Composer Audit
        run: composer audit
      - name: NPM Audit
        run: npm audit --audit-level=high
```

## 3\. Automation Scripts

### Script Standards

  - **Location**: Store in `scripts/` or `bin/`.
  - **Permissions**: Must be executable (`chmod +x`).
  - **Error Handling**: Use `set -e` in Bash to fail fast.
  - **Logging**: Echo steps clearly (`echo "--> Migrating database..."`).

### Deployment Script (`scripts/deploy.sh`)

Atomic deployment pattern for zero-downtime updates.

```bash
#!/bin/bash
set -e

echo "--> Enabling Maintenance Mode"
php artisan down || true

echo "--> Pulling Code"
git pull origin main

echo "--> Installing Dependencies"
composer install --no-dev --optimize-autoloader
npm ci

echo "--> Building Assets"
npm run build

echo "--> Migrating Database"
php artisan migrate --force

echo "--> Clearing Caches"
php artisan optimize
php artisan view:cache
php artisan config:cache
php artisan event:cache

echo "--> Restarting Queues"
php artisan queue:restart

echo "--> Disabling Maintenance Mode"
php artisan up

echo "--> Deployment Complete"
```

## 4\. Secrets & Security

  - **Storage**: Never commit `.env` files or API keys. Use GitHub Secrets.
  - **Injection**: Inject secrets via environment variables in CI `env:` block.
  - **Validation**: Scripts should check for required env vars before running.

<!-- end list -->

```bash
if [ -z "$APP_KEY" ]; then
  echo "Error: APP_KEY is not set."
  exit 1
fi
```

## 5\. Traceability

All automation changes must reference a D11 (Technical Design) or D08 (Integration) section in the PR.

  * *Example*: `feat(ci): add accessibility scan step (Ref: D11 §7.2)`
