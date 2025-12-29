#Requires -Version 7.0
<#
.SYNOPSIS
    Test specific menu functions to verify they work correctly.
#>

# Import the Main-Menu script to get access to its functions
. (Join-Path $PSScriptRoot "Main-Menu.ps1")

Write-Host "Testing Menu Functions" -ForegroundColor Cyan
Write-Host "======================" -ForegroundColor Cyan
Write-Host ""

# Test System Health Check
Write-Host "Testing System Health Check..." -ForegroundColor Yellow
try {
    Show-SystemHealthCheck
    Write-Host "✅ System Health Check function works" -ForegroundColor Green
} catch {
    Write-Host "❌ System Health Check failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Configuration Menu
Write-Host "Testing Configuration Menu..." -ForegroundColor Yellow
try {
    # We can't actually run this interactively, but we can test if the function exists
    $configFunction = Get-Command Show-ConfigurationMenu -ErrorAction Stop
    Write-Host "✅ Configuration Menu function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Configuration Menu function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Quick Start Guide
Write-Host "Testing Quick Start Guide..." -ForegroundColor Yellow
try {
    Show-QuickStartGuide
    Write-Host "✅ Quick Start Guide function works" -ForegroundColor Green
} catch {
    Write-Host "❌ Quick Start Guide failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Search Scripts
Write-Host "Testing Search Scripts..." -ForegroundColor Yellow
try {
    $searchFunction = Get-Command Search-Scripts -ErrorAction Stop
    Write-Host "✅ Search Scripts function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Search Scripts function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Guest Workflows Menu
Write-Host "Testing Guest Workflows Menu..." -ForegroundColor Yellow
try {
    $guestFunction = Get-Command Show-GuestWorkflowsMenu -ErrorAction Stop
    Write-Host "✅ Guest Workflows Menu function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Guest Workflows Menu function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Authenticated Workflows Menu
Write-Host "Testing Authenticated Workflows Menu..." -ForegroundColor Yellow
try {
    $authFunction = Get-Command Show-AuthenticatedWorkflowsMenu -ErrorAction Stop
    Write-Host "✅ Authenticated Workflows Menu function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Authenticated Workflows Menu function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Category Scripts Function
Write-Host "Testing Category Scripts Function..." -ForegroundColor Yellow
try {
    $categoryFunction = Get-Command Invoke-CategoryScripts -ErrorAction Stop
    Write-Host "✅ Category Scripts function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Category Scripts function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Execution History
Write-Host "Testing Execution History..." -ForegroundColor Yellow
try {
    $historyFunction = Get-Command Show-ExecutionHistory -ErrorAction Stop
    Write-Host "✅ Execution History function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Execution History function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test Handle Guest Workflow Choice
Write-Host "Testing Handle Guest Workflow Choice..." -ForegroundColor Yellow
try {
    $handleFunction = Get-Command Handle-GuestWorkflowChoice -ErrorAction Stop
    Write-Host "✅ Handle Guest Workflow Choice function exists" -ForegroundColor Green
} catch {
    Write-Host "❌ Handle Guest Workflow Choice function missing: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "Menu Function Tests Completed!" -ForegroundColor Cyan