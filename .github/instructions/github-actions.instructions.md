---
applyTo: ".github/workflows/**"
description: "GitHub Actions standards: Mandatory CI gates, security scanning workflows, and deployment pipeline requirements for ICTServe."
---

# GitHub Actions & CI/CD Instructions

**Purpose**
Defines the mandatory Continuous Integration and Continuous Deployment (CI/CD) standards for ICTServe. Ensures code quality, security, and stability through automated gates.

**Scope**
Applies to all workflows in `.github/workflows`, action configurations, and deployment scripts.

## 1. Pipeline Quality Gates

Every Pull Request **MUST** pass these 5 gates before merging to `develop` or `main`.

| Gate | Command | Failure Criteria |
| :--- | :--- | :--- |
| **1. Linting** | `vendor/bin/pint --test` | Any code style violation. |
| **2. Analysis** | `vendor/bin/phpstan analyse` | Any Level 9 static analysis error. |
| **3. Testing** | `php artisan test` | Any failed unit or feature test. |
| **4. Security** | `composer audit` | Any Critical/High vulnerability. |
| **5. Build** | `npm run build` | Asset compilation failure. |

## 2. Workflow Standards

### Naming & Triggers
- **File Name**: `kebab-case.yml` (e.g., `ci-pipeline.yml`, `security-scan.yml`).
- **Triggers**:
  - `push`: Branches `main`, `develop`.
  - `pull_request`: Branches `main`, `develop`.
  - `schedule`: Cron for security audits (weekly).

### Secrets Management
- **Never** commit secrets to code.
- Use `secrets.KEY_NAME` context.
- **Required Secrets**:
  - `APP_KEY`
  - `DB_PASSWORD` (if using external DB)
  - `PROD_SSH_KEY` (for deployment)

## 3. Standard Workflows

### Continuous Integration (`ci.yml`)
Standard pipeline for PHP 8.4 and Laravel 12.

```yaml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  validate:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: true
      matrix:
        php: [8.4]

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
          php-version: ${{ matrix.php }}
          extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, bcmath
          coverage: none

      - name: Install Dependencies
        run: composer install --no-progress --prefer-dist

      - name: Check Code Style
        run: vendor/bin/pint --test

      - name: Static Analysis
        run: vendor/bin/phpstan analyse --memory-limit=2G

      - name: Run Tests
        run: php artisan test --parallel
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: root
          CACHE_STORE: redis
````

### Security Audit (`security.yml`)

Runs weekly to check for new vulnerabilities in dependencies.

```yaml
name: Security Audit
on:
  schedule:
    - cron: '0 8 * * 1' # Weekly on Monday
  workflow_dispatch:

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

## 4\. Deployment Strategy

### Atomic Deployment

Use a deployment script via SSH (e.g., `appleboy/ssh-action`) that executes an atomic swap or zero-downtime procedure.

**Deployment Script (`scripts/deploy.sh`)**:

```bash
#!/bin/bash
set -e

# 1. Pull changes
git pull origin main

# 2. Install Deps
composer install --no-dev --optimize-autoloader

# 3. Migrate (Force for prod)
php artisan migrate --force

# 4. Optimize
php artisan optimize
php artisan view:cache
npm run build

# 5. Restart Workers
php artisan queue:restart
```

## 5\. Traceability

Every workflow modification PR must reference the relevant **Technical Design (D11)** section regarding CI/CD requirements.

*Example Commit*: `ci: add redis service to test pipeline (Ref: D11-CICD-04)`
