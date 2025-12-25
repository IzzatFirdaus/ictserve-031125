# Simple test to validate the main script syntax
param([switch]$TestOnly)

Write-Host "Testing ICTServe development script..." -ForegroundColor Cyan

try {
    # Test basic PowerShell syntax by loading the script without executing
    $scriptContent = Get-Content "scripts\dev\start-dev.ps1" -Raw
    $scriptBlock = [ScriptBlock]::Create($scriptContent)
    
    Write-Host "[OK] Script syntax is valid" -ForegroundColor Green
    
    if ($TestOnly) {
        Write-Host "[INFO] Test completed successfully" -ForegroundColor Blue
        exit 0
    }
    
    # Test WSL availability
    if (Get-Command wsl.exe -ErrorAction SilentlyContinue) {
        Write-Host "[OK] WSL is available" -ForegroundColor Green
        
        # Test WSL Redis
        $redisTest = & wsl.exe -e bash -c "command -v redis-server >/dev/null && echo 'installed' || echo 'missing'" 2>$null
        if ($redisTest -eq 'installed') {
            Write-Host "[OK] WSL Redis is installed" -ForegroundColor Green
        } else {
            Write-Host "[WARN] WSL Redis not installed" -ForegroundColor Yellow
        }
    } else {
        Write-Host "[WARN] WSL not available" -ForegroundColor Yellow
    }
    
    # Test required commands
    $commands = @('php', 'node', 'npm', 'composer')
    foreach ($cmd in $commands) {
        if (Get-Command $cmd -ErrorAction SilentlyContinue) {
            Write-Host "[OK] $cmd is available" -ForegroundColor Green
        } else {
            Write-Host "[WARN] $cmd not found in PATH" -ForegroundColor Yellow
        }
    }
    
    Write-Host "[INFO] Environment test completed" -ForegroundColor Blue
    
} catch {
    Write-Host "[ERROR] Script validation failed: $_" -ForegroundColor Red
    exit 1
}
