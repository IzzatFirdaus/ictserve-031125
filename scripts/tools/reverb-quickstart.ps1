#!/usr/bin/env pwsh
# Reverb WebSocket System Quick Start (Windows PowerShell)
# This script starts all required services for Laravel Reverb broadcasting

param(
    [ValidateSet('start', 'status', 'stop')]
    [string]$Action = 'start'
)

$BaseDir = 'c:\XAMPP\htdocs\ictserve-031125'
$RedisPort = 6379
$ReverBPort = 8080
$LaravelPort = 8000

function Test-Service {
    param([string]$Name, [int]$Port)
    try {
        $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
        return $result.TcpTestSucceeded
    } catch {
        return $false
    }
}

function Show-Status {
    Write-Host "`n=== Reverb Broadcasting System Status ===" -ForegroundColor Cyan
    Write-Host ""

    $redis = Test-Service -Name "Redis" -Port $RedisPort
    Write-Host "Redis (127.0.0.1:$RedisPort): $(if ($redis) { 'RUNNING ✓' } else { 'STOPPED ✗' })" -ForegroundColor $(if ($redis) { 'Green' } else { 'Red' })

    $reverb = Test-Service -Name "Reverb" -Port $ReverBPort
    Write-Host "Reverb (127.0.0.1:$ReverBPort): $(if ($reverb) { 'RUNNING ✓' } else { 'STOPPED ✗' })" -ForegroundColor $(if ($reverb) { 'Green' } else { 'Red' })

    Write-Host "Laravel App (127.0.0.1:$LaravelPort): Check by opening browser" -ForegroundColor Yellow
    Write-Host ""
}

function Start-Services {
    Write-Host "`n=== Starting Reverb Broadcasting Services ===" -ForegroundColor Cyan
    Write-Host "`nYou need to start these in separate PowerShell windows:`n" -ForegroundColor Yellow

    Write-Host "Terminal 1 - Redis Server:" -ForegroundColor Green
    Write-Host 'C:\xampp\redis\redis-server.exe' -ForegroundColor White
    Write-Host ""

    Write-Host "Terminal 2 - Reverb WebSocket Server:" -ForegroundColor Green
    Write-Host "cd $BaseDir" -ForegroundColor White
    Write-Host "php artisan reverb:serve --host=127.0.0.1 --port=8080 --scheme=http" -ForegroundColor White
    Write-Host ""

    Write-Host "Terminal 3 - Queue Worker:" -ForegroundColor Green
    Write-Host "cd $BaseDir" -ForegroundColor White
    Write-Host "php artisan queue:work redis --queue=default,broadcast" -ForegroundColor White
    Write-Host ""

    Write-Host "Terminal 4 - Laravel Development Server:" -ForegroundColor Green
    Write-Host "cd $BaseDir" -ForegroundColor White
    Write-Host "php artisan serve --host=127.0.0.1 --port=8000" -ForegroundColor White
    Write-Host ""

    Write-Host "Once all are running, open: http://127.0.0.1:8000" -ForegroundColor Cyan
    Write-Host ""
}

switch ($Action) {
    'start' { Start-Services }
    'status' { Show-Status }
    'stop' {
        Write-Host "To stop services, terminate the PowerShell windows running each service" -ForegroundColor Yellow
        Show-Status
    }
}
