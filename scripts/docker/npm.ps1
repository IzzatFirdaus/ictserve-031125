#!/usr/bin/env pwsh
# Run npm commands in Docker container

param(
    [Parameter(ValueFromRemainingArguments)]
    [string[]]$Command
)

if (-not $Command) {
    Write-Host "Usage: .\npm.ps1 <command>" -ForegroundColor Yellow
    Write-Host "Example: .\npm.ps1 run build" -ForegroundColor Gray
    exit 1
}

$cmd = $Command -join " "
Write-Host "Running: npm $cmd" -ForegroundColor Cyan
docker compose exec app npm $cmd
