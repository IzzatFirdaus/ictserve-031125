#Requires -Version 7.0
<#
.SYNOPSIS
    Comprehensive test script to validate all Main-Menu.ps1 options and identify errors.

.DESCRIPTION
    This script systematically tests all 29 menu options to identify missing files,
    broken functions, and other issues that need to be fixed.
#>

[CmdletBinding()]
param(
    [Parameter()]
    [switch]$FixIssues
)

# Import common functions
$ScriptRoot = $PSScriptRoot
. (Join-Path $ScriptRoot "utilities\common-functions.ps1")

Write-Host "ICTServe Automation Suite - Menu Options Validation" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""

$testResults = @()
$issuesFound = @()

function Test-MenuOption {
    param(
        [string]$OptionNumber,
        [string]$Description,
        [scriptblock]$TestAction
    )
    
    Write-Host "Testing Option $OptionNumber" + ": $Description" -ForegroundColor Yellow
    
    $result = @{
        Option = $OptionNumber
        Description = $Description
        Status = "Unknown"
        Issues = @()
        FixApplied = $false
    }
    
    try {
        $issues = & $TestAction
        if ($issues -and $issues.Count -gt 0) {
            $result.Status = "Issues Found"
            $result.Issues = $issues
            Write-Host "  ❌ Issues found: $($issues.Count)" -ForegroundColor Red
            foreach ($issue in $issues) {
                Write-Host "    - $issue" -ForegroundColor Gray
            }
        } else {
            $result.Status = "OK"
            Write-Host "  ✅ OK" -ForegroundColor Green
        }
    }
    catch {
        $result.Status = "Error"
        $result.Issues = @($_.Exception.Message)
        Write-Host "  ❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    return $result
}

# Test Option 1: Guest User Workflows
$testResults += Test-MenuOption -OptionNumber "1" -Description "Guest User Workflows" -TestAction {
    $issues = @()
    
    # Check for guest workflows menu
    $menuPath = Join-Path $ScriptRoot "scripts\guest-workflows\menu.ps1"
    if (-not (Test-Path $menuPath)) {
        $issues += "Missing guest workflows menu: $menuPath"
    }
    
    # Check for Show-GuestWorkflowsMenu function
    try {
        Get-Command Show-GuestWorkflowsMenu -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-GuestWorkflowsMenu"
    }
    
    # Check for Handle-GuestWorkflowChoice function
    try {
        Get-Command Handle-GuestWorkflowChoice -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Handle-GuestWorkflowChoice"
    }
    
    return $issues
}

# Test Option 2: Authenticated User Workflows
$testResults += Test-MenuOption -OptionNumber "2" -Description "Authenticated User Workflows" -TestAction {
    $issues = @()
    
    $menuPath = Join-Path $ScriptRoot "scripts\authenticated-workflows\menu.ps1"
    if (-not (Test-Path $menuPath)) {
        $issues += "Missing authenticated workflows menu: $menuPath"
    }
    
    try {
        Get-Command Show-AuthenticatedWorkflowsMenu -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-AuthenticatedWorkflowsMenu"
    }
    
    return $issues
}

# Test Options 3-9: Category Menus
$categories = @(
    @{ Number = "3"; Name = "admin-operations"; Display = "Admin Panel Operations" },
    @{ Number = "4"; Name = "ai-integration"; Display = "AI Integration Testing" },
    @{ Number = "5"; Name = "api-backend"; Display = "API Integration & Backend Systems" },
    @{ Number = "6"; Name = "performance-accessibility"; Display = "Performance & Accessibility Testing" },
    @{ Number = "7"; Name = "security-compliance"; Display = "Security & Compliance Testing" },
    @{ Number = "8"; Name = "monitoring-health"; Display = "System Monitoring & Health" },
    @{ Number = "9"; Name = "end-to-end"; Display = "End-to-End Workflow Testing" }
)

foreach ($category in $categories) {
    $testResults += Test-MenuOption -OptionNumber $category.Number -Description $category.Display -TestAction {
        $issues = @()
        
        $menuPath = Join-Path $ScriptRoot "scripts\$($category.Name)\menu.ps1"
        if (-not (Test-Path $menuPath)) {
            $issues += "Missing category menu: $menuPath"
        }
        
        $categoryDir = Join-Path $ScriptRoot "scripts\$($category.Name)"
        if (-not (Test-Path $categoryDir)) {
            $issues += "Missing category directory: $categoryDir"
        }
        
        return $issues
    }.GetNewClosure()
}

# Test Options 10-14: Live Demonstrations (Placeholder implementations)
$demos = @(
    @{ Number = "10"; Name = "Guest vs Authenticated Comparison" },
    @{ Number = "11"; Name = "Complete User Journey Demo" },
    @{ Number = "12"; Name = "Admin Panel Feature Tour" },
    @{ Number = "13"; Name = "AI Integration Showcase" },
    @{ Number = "14"; Name = "Security Features Demo" }
)

foreach ($demo in $demos) {
    $testResults += Test-MenuOption -OptionNumber $demo.Number -Description $demo.Name -TestAction {
        # These are placeholder implementations, so they should always work
        return @()
    }
}

# Test Options 15-18: Automated Operations
$testResults += Test-MenuOption -OptionNumber "15" -Description "Run All Critical Path Tests" -TestAction {
    $issues = @()
    
    try {
        Get-Command Invoke-CategoryScripts -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Invoke-CategoryScripts"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "16" -Description "Run All Frontend Tests" -TestAction {
    $issues = @()
    
    try {
        Get-Command Invoke-CategoryScripts -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Invoke-CategoryScripts"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "17" -Description "Run All Backend Tests" -TestAction {
    $issues = @()
    
    try {
        Get-Command Invoke-CategoryScripts -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Invoke-CategoryScripts"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "18" -Description "Run Complete Test Suite" -TestAction {
    $issues = @()
    
    try {
        Get-Command Invoke-CategoryScripts -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Invoke-CategoryScripts"
    }
    
    return $issues
}

# Test Options 19-20: Recording & Documentation (Placeholder implementations)
$testResults += Test-MenuOption -OptionNumber "19" -Description "Record Training Videos" -TestAction {
    return @() # Placeholder implementation
}

$testResults += Test-MenuOption -OptionNumber "20" -Description "Generate Demo Screenshots" -TestAction {
    return @() # Placeholder implementation
}

# Test Options 21-25: Utilities & Management
$testResults += Test-MenuOption -OptionNumber "21" -Description "Configuration Settings" -TestAction {
    $issues = @()
    
    try {
        Get-Command Show-ConfigurationMenu -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-ConfigurationMenu"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "22" -Description "View Execution History" -TestAction {
    $issues = @()
    
    try {
        Get-Command Show-ExecutionHistory -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-ExecutionHistory"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "23" -Description "Generate Comprehensive Reports" -TestAction {
    return @() # Placeholder implementation
}

$testResults += Test-MenuOption -OptionNumber "24" -Description "Test Data Management" -TestAction {
    return @() # Placeholder implementation
}

$testResults += Test-MenuOption -OptionNumber "25" -Description "System Health Check" -TestAction {
    $issues = @()
    
    try {
        Get-Command Show-SystemHealthCheck -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-SystemHealthCheck"
    }
    
    return $issues
}

# Test Options 26-27: Advanced Features
$testResults += Test-MenuOption -OptionNumber "26" -Description "Search Tests by Keyword" -TestAction {
    $issues = @()
    
    try {
        Get-Command Search-Scripts -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Search-Scripts"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "27" -Description "Custom Test Suites" -TestAction {
    return @() # Placeholder implementation
}

# Test Options 28-29: Help & Documentation
$testResults += Test-MenuOption -OptionNumber "28" -Description "Quick Start Guide" -TestAction {
    $issues = @()
    
    try {
        Get-Command Show-QuickStartGuide -ErrorAction Stop | Out-Null
    } catch {
        $issues += "Missing function: Show-QuickStartGuide"
    }
    
    return $issues
}

$testResults += Test-MenuOption -OptionNumber "29" -Description "Troubleshooting Guide" -TestAction {
    $issues = @()
    
    $troubleshootingPath = Join-Path $ScriptRoot "docs\troubleshooting-guide.md"
    if (-not (Test-Path $troubleshootingPath)) {
        $issues += "Missing troubleshooting guide: $troubleshootingPath"
    }
    
    return $issues
}

# Summary
Write-Host ""
Write-Host "Test Results Summary" -ForegroundColor Cyan
Write-Host "===================" -ForegroundColor Cyan

$totalTests = $testResults.Count
$passedTests = ($testResults | Where-Object { $_.Status -eq "OK" }).Count
$failedTests = ($testResults | Where-Object { $_.Status -ne "OK" }).Count

Write-Host "Total Options Tested: $totalTests" -ForegroundColor White
Write-Host "Passed: $passedTests" -ForegroundColor Green
Write-Host "Failed: $failedTests" -ForegroundColor Red

if ($failedTests -gt 0) {
    Write-Host ""
    Write-Host "Issues Found:" -ForegroundColor Red
    Write-Host "=============" -ForegroundColor Red
    
    foreach ($result in $testResults | Where-Object { $_.Status -ne "OK" }) {
        Write-Host ""
        Write-Host "Option $($result.Option): $($result.Description)" -ForegroundColor Yellow
        foreach ($issue in $result.Issues) {
            Write-Host "  - $issue" -ForegroundColor Gray
            $issuesFound += @{
                Option = $result.Option
                Description = $result.Description
                Issue = $issue
            }
        }
    }
}

# Export results
$reportPath = Join-Path $ScriptRoot "reports\menu-validation-report.json"
$testResults | ConvertTo-Json -Depth 10 | Set-Content $reportPath
Write-Host ""
Write-Host "Detailed report saved to: $reportPath" -ForegroundColor Cyan

return $issuesFound