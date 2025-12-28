#Requires -Version 7.0
<#
.SYNOPSIS
    Executes all ICTServe automation scripts sequentially with reporting.

.DESCRIPTION
    This script runs all 347+ automation scripts in the ICTServe suite,
    generating comprehensive reports and tracking execution results.

.PARAMETER Mode
    The demonstration mode to use (Headless, Visual, Demo, Interactive, Recording).

.PARAMETER Environment
    The target environment (development, testing, staging, production).

.PARAMETER Category
    Optional: Run only scripts from a specific category.

.PARAMETER ReportFormat
    The format for the execution report (JSON, HTML, CSV).

.EXAMPLE
    .\Run-All.ps1 -Mode Headless -Environment testing
    Runs all scripts in headless mode against the testing environment.

.EXAMPLE
    .\Run-All.ps1 -Category guest-workflows -Mode Visual
    Runs only guest workflow scripts in visual mode.
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Headless',
    
    [Parameter()]
    [ValidateSet('development', 'testing', 'staging', 'production')]
    [string]$Environment = 'testing',
    
    [Parameter()]
    [ValidateSet('guest-workflows', 'authenticated-workflows', 'admin-operations',
                 'ai-integration', 'api-backend', 'performance-accessibility',
                 'security-compliance', 'monitoring-health', 'end-to-end', 'all')]
    [string]$Category = 'all',
    
    [Parameter()]
    [ValidateSet('JSON', 'HTML', 'CSV')]
    [string]$ReportFormat = 'HTML',
    
    [Parameter()]
    [switch]$StopOnFailure,
    
    [Parameter()]
    [int]$MaxParallel = 1
)

$ScriptRoot = $PSScriptRoot

# Import utilities
. (Join-Path $ScriptRoot "utilities\common-functions.ps1")

# Initialize execution
$executionId = New-UniqueId -Prefix "RUN"
$startTime = Get-Date
$results = @()

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "  ICTServe Automation Suite - Full Execution" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Execution ID: $executionId" -ForegroundColor White
Write-Host "  Environment:  $Environment" -ForegroundColor White
Write-Host "  Mode:         $Mode" -ForegroundColor White
Write-Host "  Category:     $Category" -ForegroundColor White
Write-Host "  Started:      $($startTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor White
Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Define categories
$categories = if ($Category -eq 'all') {
    @('guest-workflows', 'authenticated-workflows', 'admin-operations',
      'ai-integration', 'api-backend', 'performance-accessibility',
      'security-compliance', 'monitoring-health', 'end-to-end')
} else {
    @($Category)
}

# Execute scripts by category
foreach ($cat in $categories) {
    $categoryPath = Join-Path $ScriptRoot "scripts\$cat"
    
    Write-Host "Category: $cat" -ForegroundColor Yellow
    Write-Host "─────────────────────────────────────────" -ForegroundColor Gray
    
    if (-not (Test-Path $categoryPath)) {
        Write-Host "  [SKIP] Category not yet implemented" -ForegroundColor DarkGray
        continue
    }
    
    $scripts = Get-ChildItem -Path $categoryPath -Filter "*.ps1" -Recurse | 
               Where-Object { $_.Name -ne 'menu.ps1' }
    
    foreach ($script in $scripts) {
        $scriptStart = Get-Date
        $relativePath = $script.FullName.Replace($ScriptRoot, '').TrimStart('\')
        
        Write-Host "  Running: $($script.BaseName)..." -NoNewline
        
        try {
            # Get environment config for BaseUrl
            $envConfig = Get-EnvironmentConfig -Environment $Environment
            $baseUrl = if ($envConfig.BaseUrl) { $envConfig.BaseUrl } else { "http://localhost:8000" }
            
            & $script.FullName -Mode $Mode -BaseUrl $baseUrl -ErrorAction Stop
            $status = 'Passed'
            Write-Host " ✓" -ForegroundColor Green
        }
        catch {
            $status = 'Failed'
            $errorMsg = $_.Exception.Message
            Write-Host " ✗" -ForegroundColor Red
            
            if ($StopOnFailure) {
                Write-Host ""
                Write-Host "Execution stopped due to failure: $errorMsg" -ForegroundColor Red
                break
            }
        }
        
        $results += @{
            Name = $script.BaseName
            Path = $relativePath
            Category = $cat
            Status = $status
            Duration = ((Get-Date) - $scriptStart).ToString('mm\:ss\.fff')
            Error = if ($status -eq 'Failed') { $errorMsg } else { $null }
        }
    }
    
    Write-Host ""
}

# Calculate summary
$endTime = Get-Date
$totalDuration = $endTime - $startTime
$passed = ($results | Where-Object { $_.Status -eq 'Passed' }).Count
$failed = ($results | Where-Object { $_.Status -eq 'Failed' }).Count
$total = $results.Count
$passRate = if ($total -gt 0) { [math]::Round(($passed / $total) * 100, 2) } else { 0 }

# Display summary
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "  Execution Summary" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Total Scripts: $total" -ForegroundColor White
Write-Host "  Passed:        $passed" -ForegroundColor Green
Write-Host "  Failed:        $failed" -ForegroundColor $(if ($failed -gt 0) { 'Red' } else { 'Green' })
Write-Host "  Pass Rate:     $passRate%" -ForegroundColor $(if ($passRate -ge 90) { 'Green' } elseif ($passRate -ge 70) { 'Yellow' } else { 'Red' })
Write-Host "  Duration:      $($totalDuration.ToString('hh\:mm\:ss'))" -ForegroundColor White
Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan

# Generate report
$reportData = @{
    ExecutionId = $executionId
    Environment = $Environment
    Mode = $Mode
    Category = $Category
    StartTime = $startTime.ToString('yyyy-MM-dd HH:mm:ss')
    EndTime = $endTime.ToString('yyyy-MM-dd HH:mm:ss')
    Duration = $totalDuration.ToString('hh\:mm\:ss')
    TotalScripts = $total
    Passed = $passed
    Failed = $failed
    PassRate = $passRate
    Results = $results
}

$reportPath = New-TestReport -ReportName "full-execution-$executionId" -TestResults $results -Format $ReportFormat

Write-Host ""
Write-Host "Report generated: $reportPath" -ForegroundColor Cyan
Write-Host ""

# Return exit code based on results
if ($failed -gt 0) {
    exit 1
}
exit 0
