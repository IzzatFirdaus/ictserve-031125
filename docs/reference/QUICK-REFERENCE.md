# Scripts Quick Reference

Quick command reference for common ICTServe development tasks.

## Development

```powershell
# Start all services
.\scripts\dev\start-dev.ps1

# Stop all services
.\scripts\dev\stop-dev.ps1

# Start Reverb only
.\scripts\dev\reverb-start.ps1

# Switch environment
.\scripts\dev\switch-env.ps1 -env production
```

## Testing

```powershell
# Run all tests
php artisan test

# Run specific test
.\scripts\testing\run-test.ps1 -filter "HelpdeskTest"

# Test changed files
.\scripts\testing\test-changed.ps1

# Static analysis
.\scripts\testing\check-larastan-ready.ps1
vendor\bin\phpstan analyse
```

## Translations

```powershell
# Check missing translations
.\scripts\translations\check-missing-translations.ps1

# Extract translations
php scripts\translations\extract-translations.php

# Scan hardcoded strings
php scripts\translations\scan-hardcoded-strings.php
```

## MCP

```powershell
# Health check
.\scripts\mcp\mcp-health-check.ps1

# Test servers
.\scripts\mcp\test-mcp-servers.ps1

# Verify config
.\scripts\mcp\verify-mcp-config.ps1
```

## Memory & Knowledge Graph

```bash
# Export memory
php scripts\memory\export-memory-graph.php

# Validate memory
.\scripts\memory\validate-memory-json.ps1

# Import to Neo4j
php scripts\neo4j\import-memory-to-neo4j.php
```

## Database

```bash
# Check admin
php scripts\database\check_admin.php

# Reset password
php scripts\database\reset-password.php user@example.com

# Check migrations
php scripts\database\check-migrations.php
```

## Setup

```powershell
# Setup virtual host
.\scripts\setup\setup-vhost.ps1

# Setup GitHub token
.\scripts\setup\setup-github-token.ps1

# Fix npm (Windows)
.\scripts\setup\fix-npm-windows.ps1
```

## Maintenance

```powershell
# Cleanup docs
.\scripts\maintenance\cleanup-docs.ps1

# Fix Filament issues
php scripts\maintenance\fix-filament-issues.php

# Fix markdown
php scripts\maintenance\fix-markdown-lint-rules.php
```

## Docker

```powershell
# Start Docker environment
.\scripts\docker\start-dev.ps1

# Run Artisan in Docker
.\scripts\docker\artisan.ps1 migrate

# Run Composer in Docker
.\scripts\docker\composer.ps1 install

# Run npm in Docker
.\scripts\docker\npm.ps1 run build
```

## Code Quality

```bash
# Format code (PSR-12)
vendor\bin\pint

# Static analysis
vendor\bin\phpstan analyse

# Run tests
php artisan test

# Lint JavaScript
npm run lint:js

# Lint CSS
npm run lint:css

# Format all
npm run format
```

## Common Artisan Commands

```bash
# Development
php artisan serve
php artisan reverb:start
php artisan queue:work

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ICTServe specific
php artisan ict:link-historical-submissions
php artisan ict:setup-dual-audit
php artisan ict:update-guest-counts
```

## Build Commands

```bash
# Development
npm run dev
composer run dev

# Production
npm run build

# Install
composer install
npm install
```

## Service Ports

- Laravel: <http://127.0.0.1:8000>
- Vite: <http://127.0.0.1:5173>
- Reverb: ws://127.0.0.1:6001
- Redis: 127.0.0.1:6379
- MySQL: 127.0.0.1:3306

## Troubleshooting

```powershell
# Check Redis
redis-cli ping

# Check ports
netstat -ano | findstr :8000

# Kill process
taskkill /PID <PID> /F

# Clear all caches
php artisan optimize:clear

# Rebuild assets
npm run build

# Fix permissions (Linux/Mac)
chmod -R 755 storage bootstrap/cache
```

## Environment Variables

```bash
# Copy example
cp .env.example .env

# Generate key
php artisan key:generate

# Common variables
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
REDIS_CLIENT=phpredis
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
```

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/new-feature

# Stage changes
git add .

# Commit with traceability
git commit -m "feat: implement feature (D03-FR-042)"

# Push
git push origin feature/new-feature

# Create PR
gh pr create --title "Feature: New Feature" --body "Description"
```

## Documentation References

- Full README: [scripts/README.md](./README.md)
- Dev Guide: [scripts/DEV-STARTUP-GUIDE.md](./DEV-STARTUP-GUIDE.md)
- Tech Stack: [.kiro/steering/tech.md](../.kiro/steering/tech.md)
- MCP Config: [.kiro/steering/mcp.md](../.kiro/steering/mcp.md)
