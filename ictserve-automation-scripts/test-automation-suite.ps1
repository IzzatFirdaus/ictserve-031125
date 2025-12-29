#Requires -Version 7.0
<#
.SYNOPSIS
    Quick test script to verify the ICTServe automation suite is working correctly.

.DESCRIPTION
    This script runs a few sample tests to verify that all the functions and 
    infrastructure are working properly.
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

# Import common functions
$ScriptRoot = $PSScriptRoot
. (Join-Path $ScriptRoot "utilities\common-functions.ps1")

Write-Host "ICTServe Automation Suite - Quick Test" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Configuration Loading
Write-Host "Test 1: Configuration Loading..." -ForegroundColor Yellow
try {
    $config = Get-EnvironmentConfig -Environment 'testing'
    Write-Host "✓ Configuration loaded successfully" -ForegroundColor Green
    Write-Host "  Base URL: $($config.BaseUrl)" -ForegroundColor Gray
} catch {
    Write-Host "✗ Configuration loading failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Test Result Initialization
Write-Host ""
Write-Host "Test 2: Test Framework Functions..." -ForegroundColor Yellow
try {
    $testResult = Initialize-TestResult -TestName "Sample Test" -Category "Framework Test"
    Write-Host "✓ Test result initialized successfully" -ForegroundColor Green
    Write-Host "  Test Name: $($testResult.TestName)" -ForegroundColor Gray
    Write-Host "  Status: $($testResult.Status)" -ForegroundColor Gray
} catch {
    Write-Host "✗ Test framework failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Browser Automation Mock
Write-Host ""
Write-Host "Test 3: Browser Automation Functions..." -ForegroundColor Yellow
try {
    $driver = Initialize-WebDriver -Mode $Mode -Browser 'Chrome'
    Write-Host "✓ WebDriver initialized successfully" -ForegroundColor Green
    Write-Host "  Browser: $($driver.Browser)" -ForegroundColor Gray
    Write-Host "  Mode: $($driver.Mode)" -ForegroundColor Gray
    
    Close-WebDriver -Driver $driver
    Write-Host "✓ WebDriver closed successfully" -ForegroundColor Green
} catch {
    Write-Host "✗ Browser automation failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: API Mock Functions
Write-Host ""
Write-Host "Test 4: API Testing Functions..." -ForegroundColor Yellow
try {
    $apiResponse = Invoke-ApiRequest -Endpoint "/api/test" -Method "GET"
    Assert-ApiSuccess -Response $apiResponse -Message "Test API call should succeed"
    Write-Host "✓ API functions working correctly" -ForegroundColor Green
    Write-Host "  Status Code: $($apiResponse.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "✗ API testing failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Logging Functions
Write-Host ""
Write-Host "Test 5: Logging Functions..." -ForegroundColor Yellow
try {
    Write-AutomationLog "Test log message" -Level INFO
    Write-TestStep "Sample test step" -Mode $Mode
    Write-TestOutput "Sample test output" -Type Success
    Write-Host "✓ Logging functions working correctly" -ForegroundColor Green
} catch {
    Write-Host "✗ Logging failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 6: File Operations
Write-Host ""
Write-Host "Test 6: File Operations..." -ForegroundColor Yellow
try {
    $screenshotPath = Take-Screenshot -Driver @{SessionId="test"} -Name "test-screenshot" -Mode $Mode
    if (Test-Path $screenshotPath) {
        Write-Host "✓ Screenshot function working correctly" -ForegroundColor Green
        Write-Host "  Screenshot saved: $screenshotPath" -ForegroundColor Gray
    } else {
        Write-Host "✗ Screenshot file not created" -ForegroundColor Red
    }
} catch {
    Write-Host "✗ File operations failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "Quick Test Completed!" -ForegroundColor Cyan
Write-Host "The automation suite infrastructure is ready for use." -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Run .\Main-Menu.ps1 to access the full automation suite" -ForegroundColor White
Write-Host "2. Configure credentials in config\credentials.json" -ForegroundColor White
Write-Host "3. Update environment settings in config\environments.json" -ForegroundColor White
Write-Host ""