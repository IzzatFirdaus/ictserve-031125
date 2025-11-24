#!/usr/bin/env pwsh
<#
Run first-time initialization tasks inside the running app container.

This script assumes containers are running via `docker compose up -d` and runs:
 - composer install (inside container)
 - artisan migrate --seed
 - storage:link
 - vendor/bin/pint --dirty (optional: format code in container)

Use from project root: `./scripts/docker/init-dev.ps1`
#>

function Exec-Command {
    param($cmd)
    Write-Host "> $cmd" -ForegroundColor Cyan
    & cmd /c "$cmd"
}

try {
    Exec-Command 'docker compose ps'
} catch {
    Write-Host "docker CLI not available or Docker Compose not configured. Start Docker Desktop and try again." -ForegroundColor Red
    exit 1
}

Write-Host "Running installation and migrations inside ictserve-app container..." -ForegroundColor Cyan

Exec-Command 'docker compose exec app sh -lc "composer install --no-interaction --prefer-dist"'
# Run migrations first (force) then seed — tolerate failures so re-running won't break on duplicate data
Exec-Command 'docker compose exec app sh -lc "php artisan migrate --force || true"'
Exec-Command 'docker compose exec app sh -lc "php artisan db:seed --force || true"'
Exec-Command 'docker compose exec app sh -lc "php artisan storage:link || true"'

Write-Host "Initialization finished. You can visit http://localhost:8000" -ForegroundColor Green
