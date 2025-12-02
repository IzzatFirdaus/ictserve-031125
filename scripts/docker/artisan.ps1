#!/usr/bin/env pwsh
# Run artisan commands in Docker container

param(
    [Parameter(ValueFromRemainingArguments)]
    [string[]]$Command
)

if (-not $Command) {
    Write-Host "Usage: .\artisan.ps1 <command>" -ForegroundColor Yellow
    Write-Host "Example: .\artisan.ps1 migrate --seed" -ForegroundColor Gray
    exit 1
}

$cmd = $Command -join " "
Write-Host "Running: php artisan $cmd" -ForegroundColor Cyan
docker compose exec app php artisan $cmd
