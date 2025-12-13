# MCP Environment Variables Setup for Windows
# Sets User-level environment variables from .env file

param([switch]$Verify)

$envFile = ".\.env"
if (-not (Test-Path $envFile)) {
    Write-Host "ERROR: .env file not found" -ForegroundColor Red
    exit 1
}

Write-Host "`n=== MCP Environment Variables Setup ===" -ForegroundColor Cyan

$mcpKeys = @("FIRECRAWL_API_KEY", "CONTEXT7_API_KEY", "DEEPL_API_KEY", "PAT_GITHUB_ACCESS_TOKEN")

# Read .env file
$envVars = @{}
foreach ($line in (Get-Content $envFile)) {
    $line = $line.Trim()
    if ($line -and -not $line.StartsWith('#') -and $line -match '^([^=]+)=(.*)$') {
        $key = $Matches[1].Trim()
        $value = $Matches[2].Trim() -replace '^"(.+)"$', '$1'
        $envVars[$key] = $value
    }
}

if ($Verify) {
    Write-Host "Verifying environment variables...`n" -ForegroundColor Yellow
    $allSet = $true
    foreach ($key in $mcpKeys) {
        $value = [Environment]::GetEnvironmentVariable($key, "User")
        if ($value) {
            $masked = $value.Substring(0, [Math]::Min(20, $value.Length)) + "..."
            Write-Host "   [OK] $key = $masked" -ForegroundColor Green
        } else {
            Write-Host "   [MISSING] $key" -ForegroundColor Red
            $allSet = $false
        }
    }
    if ($allSet) {
        Write-Host "`nAll variables are set! Restart VS Code to use them." -ForegroundColor Green
    } else {
        Write-Host "`nSome variables missing. Run without -Verify to set them." -ForegroundColor Yellow
    }
    exit 0
}

# Set environment variables
Write-Host "Setting environment variables...`n" -ForegroundColor Yellow
$set = 0
foreach ($key in $mcpKeys) {
    if ($envVars.ContainsKey($key)) {
        $value = $envVars[$key]
        [Environment]::SetEnvironmentVariable($key, $value, "User")
        $masked = $value.Substring(0, [Math]::Min(20, $value.Length)) + "..."
        Write-Host "   [SET] $key = $masked" -ForegroundColor Green
        $set++
    } else {
        Write-Host "   [NOT FOUND] $key in .env" -ForegroundColor Red
    }
}

Write-Host "`nSet $set / $($mcpKeys.Count) variables" -ForegroundColor $(if ($set -eq $mcpKeys.Count) {"Green"} else {"Yellow"})
if ($set -eq $mcpKeys.Count) {
    Write-Host "`nSUCCESS! Restart VS Code for changes to take effect." -ForegroundColor Green
} else {
    Write-Host "`nWARNING: Some variables missing from .env" -ForegroundColor Yellow
}
exit 0
