#Requires -Version 7.0
<#
.SYNOPSIS
    Test specific menu options that might have issues.
#>

# Import common functions
$ScriptRoot = $PSScriptRoot
. (Join-Path $ScriptRoot "utilities\common-functions.ps1")

Write-Host "Testing Specific Menu Options" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

# Test Option 1: Guest User Workflows (by running a specific script)
Write-Host "Testing Option 1: Guest User Workflows" -ForegroundColor Yellow
try {
    $scriptPath = Join-Path $ScriptRoot "scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1"
    if (Test-Path $scriptPath) {
        Write-Host "✅ Guest workflow script exists: submit-basic-ticket.ps1" -ForegroundColor Green
    } else {
        Write-Host "⚠️ Guest workflow script missing, but will be auto-created" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error testing guest workflows: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Option 15: Run Critical Path Tests
Write-Host "Testing Option 15: Run Critical Path Tests" -ForegroundColor Yellow
try {
    $endToEndDir = Join-Path $ScriptRoot "scripts\end-to-end"
    if (Test-Path $endToEndDir) {
        Write-Host "✅ End-to-end directory exists" -ForegroundColor Green
    } else {
        Write-Host "⚠️ End-to-end directory missing, will be auto-created" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error testing critical path: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Option 21: Configuration Settings
Write-Host "Testing Option 21: Configuration Settings" -ForegroundColor Yellow
try {
    $configFiles = @("environments.json", "settings.json", "credentials.json")
    $allConfigsExist = $true
    
    foreach ($configFile in $configFiles) {
        $configPath = Join-Path $ScriptRoot "config\$configFile"
        if (Test-Path $configPath) {
            Write-Host "✅ Config file exists: $configFile" -ForegroundColor Green
        } else {
            Write-Host "❌ Config file missing: $configFile" -ForegroundColor Red
            $allConfigsExist = $false
        }
    }
    
    if ($allConfigsExist) {
        Write-Host "✅ All configuration files present" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Error testing configuration: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Option 25: System Health Check
Write-Host "Testing Option 25: System Health Check" -ForegroundColor Yellow
try {
    $prereqs = Test-Prerequisites
    if ($prereqs.PowerShell) {
        Write-Host "✅ PowerShell version check passed" -ForegroundColor Green
    } else {
        Write-Host "❌ PowerShell version check failed" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error testing system health: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Option 29: Troubleshooting Guide
Write-Host "Testing Option 29: Troubleshooting Guide" -ForegroundColor Yellow
try {
    $troubleshootingPath = Join-Path $ScriptRoot "docs\troubleshooting-guide.md"
    if (Test-Path $troubleshootingPath) {
        Write-Host "✅ Troubleshooting guide exists" -ForegroundColor Green
    } else {
        Write-Host "❌ Troubleshooting guide missing" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error testing troubleshooting guide: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test directory structure
Write-Host "Testing Directory Structure" -ForegroundColor Yellow
$requiredDirs = @(
    "config",
    "reports",
    "reports\execution-logs",
    "reports\screenshots", 
    "reports\test-results",
    "reports\analytics",
    "scripts",
    "utilities",
    "docs"
)

$allDirsExist = $true
foreach ($dir in $requiredDirs) {
    $dirPath = Join-Path $ScriptRoot $dir
    if (Test-Path $dirPath) {
        Write-Host "✅ Directory exists: $dir" -ForegroundColor Green
    } else {
        Write-Host "❌ Directory missing: $dir" -ForegroundColor Red
        $allDirsExist = $false
    }
}

if ($allDirsExist) {
    Write-Host "✅ All required directories present" -ForegroundColor Green
}

Write-Host ""

# Test common functions availability
Write-Host "Testing Common Functions" -ForegroundColor Yellow
$requiredFunctions = @(
    "Initialize-TestResult",
    "Save-TestResult", 
    "Write-TestStep",
    "Initialize-WebDriver",
    "Navigate-ToUrl",
    "Fill-FormField",
    "Take-Screenshot",
    "Invoke-ApiRequest",
    "Get-ConfigValue"
)

$allFunctionsExist = $true
foreach ($func in $requiredFunctions) {
    try {
        Get-Command $func -ErrorAction Stop | Out-Null
        Write-Host "✅ Function available: $func" -ForegroundColor Green
    } catch {
        Write-Host "❌ Function missing: $func" -ForegroundColor Red
        $allFunctionsExist = $false
    }
}

if ($allFunctionsExist) {
    Write-Host "✅ All required functions available" -ForegroundColor Green
}

Write-Host ""
Write-Host "Specific Menu Options Test Completed!" -ForegroundColor Cyan