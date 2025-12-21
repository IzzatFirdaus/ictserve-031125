#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Windows-compatible queue worker management for ICTServe
    
.DESCRIPTION
    Since Laravel Horizon requires pcntl extension (not available on Windows),
    this script provides an alternative queue management solution using
    multiple queue:work processes.
    
.PARAMETER Action
    Action to perform: start, stop, status, restart
    
.PARAMETER Environment
    Environment to run in: local, staging, production
    
.EXAMPLE
    .\scripts\dev\start-queue-workers.ps1 -Action start
    .\scripts\dev\start-queue-workers.ps1 -Action status
#>

param(
    [Parameter(Mandatory=$false)]
    [ValidateSet("start", "stop", "status", "restart")]
    [string]$Action = "start",
    
    [Parameter(Mandatory=$false)]
    [ValidateSet("local", "staging", "production")]
    [string]$Environment = "local"
)

# Configuration
$WorkerProcesses = @{
    "default" = @{
        "queues" = "default"
        "processes" = 1
        "timeout" = 60
        "memory" = 128
        "tries" = 3
    }
    "helpdesk" = @{
        "queues" = "helpdesk,notifications"
        "processes" = 2
        "timeout" = 300
        "memory" = 128
        "tries" = 3
    }
    "asset-loan" = @{
        "queues" = "asset-loan,approvals"
        "processes" = 1
        "timeout" = 180
        "memory" = 128
        "tries" = 5
    }
    "ai-chatbot" = @{
        "queues" = "ai-chatbot,document-processing"
        "processes" = 1
        "timeout" = 600
        "memory" = 256
        "tries" = 3
    }
    "reports" = @{
        "queues" = "reports,exports"
        "processes" = 1
        "timeout" = 600
        "memory" = 256
        "tries" = 3
    }
}

$ProcessPrefix = "ictserve-queue"

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-Host "=" * 60 -ForegroundColor Cyan
    Write-Host $Title -ForegroundColor Yellow
    Write-Host "=" * 60 -ForegroundColor Cyan
}

function Get-QueueWorkerProcesses {
    return Get-Process | Where-Object { 
        $_.ProcessName -eq "php" -and 
        $_.CommandLine -like "*queue:work*" -and
        $_.CommandLine -like "*$ProcessPrefix*"
    }
}

function Start-QueueWorkers {
    Write-Header "Starting ICTServe Queue Workers ($Environment)"
    
    $startedWorkers = 0
    
    foreach ($workerName in $WorkerProcesses.Keys) {
        $config = $WorkerProcesses[$workerName]
        
        Write-Host "[WORKER] Starting $workerName workers..." -ForegroundColor Green
        Write-Host "  Queues: $($config.queues)" -ForegroundColor Gray
        Write-Host "  Processes: $($config.processes)" -ForegroundColor Gray
        
        for ($i = 1; $i -le $config.processes; $i++) {
            $processName = "$ProcessPrefix-$workerName-$i"
            
            $arguments = @(
                "artisan", "queue:work", "redis",
                "--name=$processName",
                "--queue=$($config.queues)",
                "--tries=$($config.tries)",
                "--timeout=$($config.timeout)",
                "--memory=$($config.memory)",
                "--sleep=3",
                "--max-jobs=1000",
                "--max-time=3600"
            )
            
            try {
                $process = Start-Process -FilePath "php" -ArgumentList $arguments -WindowStyle Hidden -PassThru
                Write-Host "    ✓ Started $processName (PID: $($process.Id))" -ForegroundColor Green
                $startedWorkers++
            }
            catch {
                Write-Host "    ✗ Failed to start $processName : $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }
    
    Write-Host ""
    Write-Host "Started $startedWorkers queue worker processes" -ForegroundColor Green
    Write-Host "Monitor with: .\scripts\dev\start-queue-workers.ps1 -Action status" -ForegroundColor Yellow
}

function Stop-QueueWorkers {
    Write-Header "Stopping ICTServe Queue Workers"
    
    $processes = Get-QueueWorkerProcesses
    
    if ($processes.Count -eq 0) {
        Write-Host "No queue worker processes found" -ForegroundColor Yellow
        return
    }
    
    foreach ($process in $processes) {
        try {
            Write-Host "Stopping PID $($process.Id)..." -ForegroundColor Yellow
            $process.Kill()
            Write-Host "  ✓ Stopped PID $($process.Id)" -ForegroundColor Green
        }
        catch {
            Write-Host "  ✗ Failed to stop PID $($process.Id): $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

function Show-QueueWorkerStatus {
    Write-Header "ICTServe Queue Worker Status"
    
    # Check running processes
    $processes = Get-QueueWorkerProcesses
    
    if ($processes.Count -eq 0) {
        Write-Host "No queue worker processes running" -ForegroundColor Yellow
    } else {
        Write-Host "Running queue worker processes:" -ForegroundColor Green
        foreach ($process in $processes) {
            $runtime = (Get-Date) - $process.StartTime
            Write-Host "  PID $($process.Id) - Runtime: $($runtime.ToString('hh\:mm\:ss'))" -ForegroundColor Gray
        }
    }
    
    Write-Host ""
    
    # Check queue status
    Write-Host "Queue Status:" -ForegroundColor Cyan
    try {
        $queueStatus = & php artisan queue:monitor redis:default,redis:helpdesk,redis:asset-loan,redis:ai-chatbot,redis:reports 2>&1
        Write-Host $queueStatus -ForegroundColor Gray
    }
    catch {
        Write-Host "Failed to get queue status: $($_.Exception.Message)" -ForegroundColor Red
    }
}

function Restart-QueueWorkers {
    Write-Header "Restarting ICTServe Queue Workers"
    Stop-QueueWorkers
    Start-Sleep -Seconds 2
    Start-QueueWorkers
}

# Main execution
switch ($Action) {
    "start" { Start-QueueWorkers }
    "stop" { Stop-QueueWorkers }
    "status" { Show-QueueWorkerStatus }
    "restart" { Restart-QueueWorkers }
}

Write-Host ""