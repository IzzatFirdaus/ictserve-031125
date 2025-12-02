#!/usr/bin/env pwsh
# Run composer commands in Docker container

param(
    [Parameter(ValueFromRemainingArguments)]
    [string[]]$Command
)

if (-not $Command) {
    Write-Host "Usage: .\composer.ps1 <command>" -ForegroundColor Yellow
    Write-Host "Example: .\composer.ps1 install --no-dev" -ForegroundColor Gray
    exit 1
}

$cmd = $Command -join " "
Write-Host "Running: composer $cmd" -ForegroundColor Cyan
docker compose exec app composer $cmd
