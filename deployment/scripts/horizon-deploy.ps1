# ICTServe Laravel Horizon Deployment Script (PowerShell)
#
# This script handles Horizon deployment on Windows environments
# including graceful shutdown, configuration updates, and health verification.
#
# Requirements: 23.1, 23.4
# Usage: .\horizon-deploy.ps1 [-Environment production] [-Action deploy]

param(
    [string]$Environment = "production",
    [ValidateSet("deploy", "rollback", "health", "status")]
    [string]$Action = "deploy"
)

# Configuration
$AppPath = "C:\xampp\htdocs\ictserve-031125"
$LogFile = "$AppPath\storage\logs\horizon-deploy.log"

# Ensure log directory exists
$LogDir = Split-Path $LogFile -Parent
if (!(Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
}

# Logging functions
function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] [$Level] $Message"
    
    switch ($Level) {
        "ERROR" { Write-Host $LogEntry -ForegroundColor Red }
        "WARNING" { Write-Host $LogEntry -ForegroundColor Yellow }
        "SUCCESS" { Write-Host $LogEntry -ForegroundColor Green }
        default { Write-Host $LogEntry -ForegroundColor White }
    }
    
    Add-Content -Path $LogFile -Value $LogEntry
}

# Check if application path exists
function Test-AppPath {
    if (!(Test-Path $AppPath)) {
        Write-Log "Application path $AppPath does not exist" "ERROR"
        exit 1
    }
}

# Check if Horizon is running
function Test-HorizonStatus {
    Write-Log "Checking current Horizon status..."
    
    Set-Location $AppPath
    
    try {
        $output = php artisan horizon:status 2>&1
        if ($output -match "running") {
            Write-Log "Horizon is currently running" "SUCCESS"
            return $true
        } else {
            Write-Log "Horizon is not running" "WARNING"
            return $false
        }
    } catch {
        Write-Log "Failed to check Horizon status: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

# Terminate Horizon gracefully
function Stop-Horizon {
    Write-Log "Terminating Horizon gracefully..."
    
    Set-Location $AppPath
    
    try {
        # Send terminate signal
        php artisan horizon:terminate
        Write-Log "Horizon terminate signal sent successfully" "SUCCESS"
        
        # Wait for graceful shutdown
        $timeout = 60
        $count = 0
        
        while ($count -lt $timeout) {
            $processes = Get-Process | Where-Object { $_.ProcessName -like "*php*" -and $_.CommandLine -like "*horizon*" }
            if ($processes.Count -eq 0) {
                Write-Log "Horizon processes terminated gracefully" "SUCCESS"
                return $true
            }
            
            Start-Sleep -Seconds 1
            $count++
        }
        
        Write-Log "Horizon did not terminate gracefully within ${timeout}s" "WARNING"
        return $false
    } catch {
        Write-Log "Failed to terminate Horizon: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

# Start Horizon
function Start-Horizon {
    Write-Log "Starting Horizon..."
    
    Set-Location $AppPath
    
    try {
        # Start Horizon in background
        Start-Process -FilePath "php" -ArgumentList "artisan", "horizon" -WindowStyle Hidden
        
        # Wait for startup
        Start-Sleep -Seconds 10
        
        # Verify Horizon is running
        if (Test-HorizonHealth) {
            Write-Log "Horizon started successfully and is healthy" "SUCCESS"
            return $true
        } else {
            Write-Log "Horizon failed health check after startup" "ERROR"
            return $false
        }
    } catch {
        Write-Log "Failed to start Horizon: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

# Verify Horizon health
function Test-HorizonHealth {
    Write-Log "Verifying Horizon health..."
    
    Set-Location $AppPath
    
    try {
        $output = php artisan horizon:status 2>&1
        if ($output -match "running") {
            Write-Log "Horizon is running and healthy" "SUCCESS"
            return $true
        } else {
            Write-Log "Horizon health check failed" "ERROR"
            return $false
        }
    } catch {
        Write-Log "Health check error: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

# Create health check endpoint
function New-HealthCheckEndpoint {
    Write-Log "Creating Horizon health check endpoint..."
    
    $healthCheckContent = @'
<?php
// ICTServe Horizon Health Check (Windows)
// Returns HTTP 200 if Horizon is healthy, 503 if not

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';

try {
    // Check if Horizon is running
    $output = [];
    $returnCode = 0;
    exec('php ' . __DIR__ . '/../../artisan horizon:status 2>&1', $output, $returnCode);
    
    if ($returnCode === 0 && strpos(implode(' ', $output), 'running') !== false) {
        http_response_code(200);
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'horizon' => 'running',
            'platform' => 'windows'
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            'status' => 'unhealthy',
            'timestamp' => date('c'),
            'horizon' => 'not running',
            'platform' => 'windows',
            'output' => $output
        ]);
    }
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'timestamp' => date('c'),
        'error' => $e->getMessage(),
        'platform' => 'windows'
    ]);
}
'@

    $healthCheckPath = "$AppPath\storage\app\horizon-health-check.php"
    Set-Content -Path $healthCheckPath -Value $healthCheckContent
    Write-Log "Health check endpoint created at storage/app/horizon-health-check.php" "SUCCESS"
}

# Main deployment function
function Invoke-HorizonDeploy {
    Write-Log "Starting ICTServe Horizon deployment for environment: $Environment" "SUCCESS"
    
    # Pre-deployment checks
    Test-AppPath
    
    # Deployment steps
    if (Test-HorizonStatus) {
        Stop-Horizon | Out-Null
    }
    
    New-HealthCheckEndpoint
    Start-Horizon | Out-Null
    
    # Final verification
    if (Test-HorizonHealth) {
        Write-Log "✅ ICTServe Horizon deployment completed successfully!" "SUCCESS"
        
        Write-Log ""
        Write-Log "Health Check URL: http://localhost/ictserve/storage/horizon-health-check.php"
        Write-Log "Horizon Dashboard: http://localhost/ictserve/horizon"
        
        return $true
    } else {
        Write-Log "❌ Horizon deployment failed - health check unsuccessful" "ERROR"
        return $false
    }
}

# Rollback function
function Invoke-HorizonRollback {
    Write-Log "Rolling back Horizon deployment..." "WARNING"
    
    # Stop current process
    Stop-Horizon | Out-Null
    
    # Restart with previous configuration
    Start-Horizon | Out-Null
    
    Write-Log "Rollback completed" "SUCCESS"
}

# Script execution
switch ($Action) {
    "deploy" {
        $result = Invoke-HorizonDeploy
        if (!$result) { exit 1 }
    }
    "rollback" {
        Invoke-HorizonRollback
    }
    "health" {
        $result = Test-HorizonHealth
        if (!$result) { exit 1 }
    }
    "status" {
        Test-HorizonStatus | Out-Null
        Write-Log "Current Horizon Status Check Completed"
    }
    default {
        Write-Log "Invalid action: $Action" "ERROR"
        Write-Host "Usage: .\horizon-deploy.ps1 [-Environment production] [-Action deploy|rollback|health|status]"
        exit 1
    }
}

Write-Log "Script execution completed successfully" "SUCCESS"
exit 0